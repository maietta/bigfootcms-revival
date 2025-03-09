<?php

namespace BigfootCMS\Template;

use DOMDocument;
use DOMNode;
use DOMXPath;
use DOMDocumentFragment;

/**
 * PureHtml - A modern template processing library
 * 
 * @author Nicholas Maietta <nick@icode4u.com>
 * @license GPLv3
 */
class PureHtml {
    private array $index = [];
    private array $metatags = [];
    
    public function __construct(
        private readonly object $stylesheets = new class() {
            public array $head = [];
            public array $body = [];
        },
        private object $javascripts = new class {
            public array $head = [];
            public array $body = [];
        }
    ) {}
    
    /**
     * Set the title of an HTML document
     */
    public function title(string|DOMDocument $html, string $title): DOMDocument {
        $dom = $this->getInstanceOfDom($html);
        $titleNode = $dom->getElementsByTagName("title")->item(0);
        if ($titleNode) {
            $titleNode->nodeValue = $title;
        }
        return $dom;
    }
    
    /**
     * Scan HTML for assets (stylesheets, scripts, meta tags)
     */
    public function scan(string|DOMDocument $html, string $target = ''): self {
        $dom = $this->getInstanceOfDom($html);
        $blocks = match($target) {
            'body' => ['body'],
            'head' => ['head'],
            default => ['body', 'head']
        };
        
        foreach ($blocks as $blockName) {
            $block = $dom->getElementsByTagName($blockName)->item(0);
            if (!$block) continue;
            
            $this->scanStylesheets($block, $blockName);
            $this->scanMetaTags($block);
            $this->scanScripts($block, $blockName);
            $this->scanInlineStyles($block, $blockName);
        }
        
        return $this;
    }
    
    /**
     * Remove specified elements from HTML
     */
    public function scrub(string|DOMDocument $html, string $whatToStrip = ''): string {
        $dom = $this->getInstanceOfDom($html);
        
        $nodesToRemove = match($whatToStrip) {
            'styles' => ['link', 'style'],
            'scripts' => ['script'],
            default => ['link', 'style', 'script']
        };
        
        foreach ($nodesToRemove as $tagName) {
            $nodes = $dom->getElementsByTagName($tagName);
            while ($nodes->length > 0) {
                $this->removeNode($nodes->item(0));
            }
        }
        
        return $dom->saveHTML() ?: '';
    }
    
    /**
     * Splice new content into an existing HTML document
     */
    public function splice(string|DOMDocument $html, string $new, string $tag): string {
        $dom = $this->getInstanceOfDom($html);
        
        $newDoc = new DOMDocument();
        $newDoc->loadHTML('<?xml encoding="UTF-8">' . $new, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        $element = $dom->getElementById($tag);
        if (!$element) {
            return $dom->saveHTML() ?: '';
        }
        
        // Remove existing content
        while ($element->firstChild) {
            $element->removeChild($element->firstChild);
        }
        
        // Insert new content
        $xpath = new DOMXPath($newDoc);
        $body = $xpath->query('/html/body')->item(0);
        if ($body) {
            $fragment = $dom->createDocumentFragment();
            $fragment->appendXML($newDoc->saveXML($body));
            $element->appendChild($fragment);
        }
        
        return $dom->saveHTML($dom->documentElement) ?: '';
    }
    
    /**
     * Rebuild HTML document with collected assets
     */
    public function rebuild(string|DOMDocument $html): string {
        $dom = $this->getInstanceOfDom($html);
        
        // Remove existing meta tags
        $nodes = $dom->getElementsByTagName("meta");
        while ($nodes->length > 0) {
            $this->removeNode($nodes->item(0));
        }
        
        // Add meta tags
        $this->rebuildMetaTags($dom);
        
        // Add stylesheets
        $this->rebuildStylesheets($dom);
        
        // Add scripts
        $this->rebuildScripts($dom);
        
        return $this->beautifyDOM($dom);
    }
    
    private function getInstanceOfDom(string|DOMDocument $dom): DOMDocument {
        if ($dom instanceof DOMDocument) {
            return $dom;
        }
        
        libxml_use_internal_errors(true);
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->loadHTML($dom, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        
        return $document;
    }
    
    private function removeNode(DOMNode $node): void {
        $parent = $node->parentNode;
        if ($parent) {
            $this->removeChildren($node);
            $parent->removeChild($node);
        }
    }
    
    private function removeChildren(DOMNode $node): void {
        while ($node->firstChild) {
            while ($node->firstChild->firstChild) {
                $this->removeChildren($node->firstChild);
            }
            $node->removeChild($node->firstChild);
        }
    }
    
    private function scanStylesheets(DOMNode $block, string $location): void {
        $assets = $block->getElementsByTagName('link');
        foreach ($assets as $resource) {
            $attributes = $this->getElementAttributes($resource);
            if (!$attributes) continue;
            
            $key = $this->generateResourceKey($attributes);
            if (!in_array($key, $this->index)) {
                $target = $resource->hasAttribute('body') ? 'body' : 'head';
                $this->stylesheets->$target[] = $attributes;
                $this->index[] = $key;
            }
        }
    }
    
    private function scanMetaTags(DOMNode $block): void {
        $assets = $block->getElementsByTagName('meta');
        foreach ($assets as $resource) {
            $attributes = $this->getElementAttributes($resource);
            if (!$attributes) continue;
            
            $key = $this->generateResourceKey($attributes);
            if (!in_array($key, $this->index)) {
                $this->metatags[] = $attributes;
                $this->index[] = $key;
            }
        }
    }
    
    private function scanScripts(DOMNode $block, string $location): void {
        $assets = $block->getElementsByTagName('script');
        foreach ($assets as $resource) {
            $content = trim($resource->textContent);
            
            if (strlen($content) > 12) {
                $key = $this->generateResourceKey($content);
                if (!in_array($key, $this->index)) {
                    $target = $resource->hasAttribute('body') ? 'body' : 'head';
                    $this->javascripts->$target[] = rtrim($content);
                    $this->index[] = $key;
                }
            } else {
                $attributes = $this->getElementAttributes($resource);
                if (!$attributes) continue;
                
                $key = $this->generateResourceKey($attributes);
                if (!in_array($key, $this->index)) {
                    $target = $resource->hasAttribute('body') ? 'body' : 'head';
                    $this->javascripts->$target[] = $attributes;
                    $this->index[] = $key;
                }
            }
        }
    }
    
    private function scanInlineStyles(DOMNode $block, string $location): void {
        $assets = $block->getElementsByTagName('style');
        foreach ($assets as $resource) {
            $content = trim($resource->textContent);
            if (strlen($content) <= 6) continue;
            
            $key = $this->generateResourceKey($content);
            if (!in_array($key, $this->index)) {
                $target = $resource->hasAttribute('body') ? 'body' : 'head';
                $this->stylesheets->$target[] = rtrim($content);
                $this->index[] = $key;
            }
        }
    }
    
    private function getElementAttributes(DOMNode $element): ?array {
        $attributes = [];
        foreach ($element->attributes as $attribute) {
            if (strlen($attribute->nodeValue) > 0) {
                $attributes[$attribute->nodeName] = trim($attribute->nodeValue);
            }
        }
        return $attributes ?: null;
    }
    
    private function generateResourceKey(array|string $resource): string {
        return sha1(is_array($resource) ? serialize($resource) : $resource);
    }
    
    private function rebuildMetaTags(DOMDocument $dom): void {
        $head = $dom->getElementsByTagName('head')->item(0);
        if (!$head) return;
        
        foreach ($this->metatags as $meta) {
            $element = $dom->createElement('meta');
            foreach ($meta as $name => $value) {
                $element->setAttribute($name, $value);
            }
            $head->appendChild($element);
        }
    }
    
    private function rebuildStylesheets(DOMDocument $dom): void {
        foreach (['head', 'body'] as $location) {
            $container = $dom->getElementsByTagName($location)->item(0);
            if (!$container) continue;
            
            foreach ($this->stylesheets->$location as $resource) {
                if (is_array($resource)) {
                    $element = $dom->createElement('link');
                    $this->setElementAttributes($element, $resource, [
                        'href', 'integrity', 'crossorigin', 'hreflang', 
                        'defer', 'rel', 'media', 'sizes', 'type'
                    ]);
                    $container->appendChild($element);
                } else {
                    $element = $dom->createElement('style');
                    $element->textContent = $resource;
                    $container->appendChild($element);
                }
            }
        }
    }
    
    private function rebuildScripts(DOMDocument $dom): void {
        foreach (['head', 'body'] as $location) {
            $container = $dom->getElementsByTagName($location)->item(0);
            if (!$container) continue;
            
            foreach ($this->javascripts->$location as $resource) {
                $element = $dom->createElement('script');
                if (is_array($resource)) {
                    $this->setElementAttributes($element, $resource, [
                        'src', 'integrity', 'crossorigin', 'async', 
                        'defer', 'charset', 'type'
                    ]);
                } else {
                    $element->textContent = $resource;
                }
                $container->appendChild($element);
            }
        }
    }
    
    private function setElementAttributes(DOMNode $element, array $attributes, array $order): void {
        $sorted = array_merge(array_flip($order), $attributes);
        foreach ($sorted as $name => $value) {
            if (!isset($attributes[$name])) continue;
            $element->setAttribute($name, $value);
        }
    }
    
    private function beautifyDOM(DOMDocument $doc, int $depth = 1): string {
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        return $doc->saveHTML() ?: '';
    }
} 
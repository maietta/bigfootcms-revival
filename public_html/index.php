<?php

require_once __DIR__ . '/lib/Database.php';
require_once __DIR__ . '/lib/Framework.php';
require_once __DIR__ . '/purehtml.php';

try {
    // Initialize framework and PureHTML
    $framework = new Framework();
    $template = new PureHTML();
    
    // Get the requested path
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Default to index.html if no path specified
    if ($path === '/') {
        $path = '/index.html';
    }
    
    // Get content for the current path
    $content = $framework->getContent($path);
    
    if ($content === null) {
        http_response_code(404);
        echo '<h1>404 Not Found</h1>';
        echo '<p>The requested page could not be found.</p>';
        exit;
    }
    
    // Get navigation
    $topNav = $framework->navigation('top');
    
    // Load and process the template
    $templatePath = __DIR__ . '/templates/clean/default.html';
    $templateHtml = file_get_contents($templatePath);
    
    if ($templateHtml === false) {
        throw new Exception('Could not load template file');
    }
    
    // Create DOM from template
    $dom = $template->getInstanceOfDom($templateHtml);
    
    // Set the page title
    $dom = $template->title($dom, htmlspecialchars($content->page_title) . ' - BigfootCMS');
    
    // Prepare navigation HTML
    $navHtml = '';
    foreach ($topNav as $link) {
        $navHtml .= sprintf(
            '<a href="%s">%s</a>',
            htmlspecialchars($link['virtual_path']),
            htmlspecialchars($link['page_title'])
        );
    }
    
    // Prepare main content
    $mainContent = base64_decode($content->encoded_content);
    
    // Splice content into template
    $html = $dom->saveHTML();
    $html = $template->splice($html, $navHtml, 'main-nav');
    $html = $template->splice($html, $mainContent, 'main-content');
    $dom = $template->getInstanceOfDom($html);
    
    // Add default styles
    $defaultStyles = '
        body { font-family: system-ui, -apple-system, sans-serif; line-height: 1.6; max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .navigation { margin-bottom: 2rem; padding: 1rem 0; border-bottom: 1px solid #eee; }
        .navigation a { margin-right: 1rem; color: #333; text-decoration: none; }
        .navigation a:hover { text-decoration: underline; }
        .content { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .sidebar { padding: 1rem; }
        .footer { margin-top: 2rem; padding: 1rem 0; border-top: 1px solid #eee; text-align: center; }
    ';
    
    // Scan template for existing assets
    $template->scan($dom);
    
    // Add our styles to the template
    $template->stylesheets->head[] = $defaultStyles;
    
    // Rebuild the template with all assets
    $finalDom = $template->rebuild($dom);
    
    // Output the final HTML
    echo $finalDom->saveHTML();
    
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo '<h1>500 Internal Server Error</h1>';
    echo '<p>Something went wrong. Please try again later.</p>';
} 
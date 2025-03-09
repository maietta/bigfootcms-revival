<?php
declare(strict_types=1);

require_once __DIR__ . '/../lib/Api.php';
require_once __DIR__ . '/../lib/Database.php';

class ContentApi extends Api {
    protected array $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE'];
    
    public function handle(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        
        switch ($method) {
            case 'GET':
                $this->handleGet();
                break;
            case 'POST':
                $this->handlePost();
                break;
            case 'PUT':
                $this->handlePut();
                break;
            case 'DELETE':
                $this->handleDelete();
                break;
            default:
                $this->sendError('Method not allowed', 405);
        }
    }
    
    private function handleGet(): void {
        try {
            $path = $this->data['path'] ?? null;
            
            if ($path) {
                // Get specific content
                $stmt = Database::query(
                    'SELECT * FROM commnetivity_content WHERE virtual_path = ?',
                    [$path]
                );
                $content = $stmt->fetch();
                
                if (!$content) {
                    $this->sendError('Content not found', 404);
                }
                
                $this->sendResponse($content);
            } else {
                // List all content
                $stmt = Database::query('SELECT * FROM commnetivity_content ORDER BY weight, virtual_path');
                $content = $stmt->fetchAll();
                $this->sendResponse($content);
            }
        } catch (Exception $e) {
            $this->sendError('Database error: ' . $e->getMessage(), 500);
        }
    }
    
    private function handlePost(): void {
        try {
            if (empty($this->data['virtual_path']) || empty($this->data['encoded_content'])) {
                $this->sendError('Missing required fields');
            }
            
            $stmt = Database::query(
                'INSERT INTO commnetivity_content (
                    virtual_path, internal_path, page_title, encoded_content,
                    encoded_javascript, encoded_stylesheet, cleartext_excerpts
                ) VALUES (?, ?, ?, ?, ?, ?, ?)',
                [
                    $this->data['virtual_path'],
                    $this->data['internal_path'] ?? '',
                    $this->data['page_title'] ?? 'Untitled Document',
                    $this->data['encoded_content'],
                    $this->data['encoded_javascript'] ?? '',
                    $this->data['encoded_stylesheet'] ?? '',
                    $this->data['cleartext_excerpts'] ?? ''
                ]
            );
            
            $this->sendResponse([
                'id' => Database::lastInsertId(),
                'message' => 'Content created successfully'
            ], 201);
            
        } catch (Exception $e) {
            $this->sendError('Database error: ' . $e->getMessage(), 500);
        }
    }
    
    private function handlePut(): void {
        try {
            if (empty($this->data['virtual_path'])) {
                $this->sendError('Missing virtual_path');
            }
            
            $fields = [];
            $params = [];
            
            // Build dynamic update query
            foreach ([
                'page_title', 'encoded_content', 'encoded_javascript',
                'encoded_stylesheet', 'cleartext_excerpts'
            ] as $field) {
                if (isset($this->data[$field])) {
                    $fields[] = "$field = ?";
                    $params[] = $this->data[$field];
                }
            }
            
            if (empty($fields)) {
                $this->sendError('No fields to update');
            }
            
            $params[] = $this->data['virtual_path'];
            
            $stmt = Database::query(
                'UPDATE commnetivity_content SET ' . implode(', ', $fields) . 
                ' WHERE virtual_path = ?',
                $params
            );
            
            if ($stmt->rowCount() === 0) {
                $this->sendError('Content not found', 404);
            }
            
            $this->sendResponse(['message' => 'Content updated successfully']);
            
        } catch (Exception $e) {
            $this->sendError('Database error: ' . $e->getMessage(), 500);
        }
    }
    
    private function handleDelete(): void {
        try {
            if (empty($this->data['virtual_path'])) {
                $this->sendError('Missing virtual_path');
            }
            
            $stmt = Database::query(
                'DELETE FROM commnetivity_content WHERE virtual_path = ?',
                [$this->data['virtual_path']]
            );
            
            if ($stmt->rowCount() === 0) {
                $this->sendError('Content not found', 404);
            }
            
            $this->sendResponse(['message' => 'Content deleted successfully']);
            
        } catch (Exception $e) {
            $this->sendError('Database error: ' . $e->getMessage(), 500);
        }
    }
}

$api = new ContentApi();
$api->handle(); 
<?php
declare(strict_types=1);

namespace App\Controllers;

use Database;

class ContentController extends Controller {
    public function list(): void {
        try {
            $stmt = Database::query('SELECT * FROM commnetivity_content ORDER BY weight, virtual_path');
            $content = $stmt->fetchAll();
            $this->json($content);
        } catch (\Exception $e) {
            $this->error('Database error: ' . $e->getMessage(), 500);
        }
    }
    
    public function show(array $params): void {
        try {
            $path = $params['path'] ?? null;
            if (!$path) {
                $this->error('Path parameter is required', 400);
                return;
            }
            
            $stmt = Database::query(
                'SELECT * FROM commnetivity_content WHERE virtual_path = ?',
                [$path]
            );
            $content = $stmt->fetch();
            
            if (!$content) {
                $this->error('Content not found', 404);
                return;
            }
            
            $this->json($content);
        } catch (\Exception $e) {
            $this->error('Database error: ' . $e->getMessage(), 500);
        }
    }
    
    public function create(): void {
        $user = $this->requireAuth();
        $data = $this->getRequestData();
        
        if (!isset($data['virtual_path']) || !isset($data['content'])) {
            $this->error('Missing required fields', 400);
            return;
        }
        
        try {
            Database::query(
                'INSERT INTO commnetivity_content (virtual_path, encoded_content, created_by) VALUES (?, ?, ?)',
                [$data['virtual_path'], base64_encode($data['content']), $user['id']]
            );
            
            $this->json(['message' => 'Content created successfully'], 201);
        } catch (\Exception $e) {
            $this->error('Database error: ' . $e->getMessage(), 500);
        }
    }
    
    public function update(array $params): void {
        $user = $this->requireAuth();
        $data = $this->getRequestData();
        
        try {
            $stmt = Database::query(
                'UPDATE commnetivity_content SET encoded_content = ?, updated_by = ? WHERE id = ?',
                [base64_encode($data['content']), $user['id'], $params['id']]
            );
            
            if ($stmt->rowCount() === 0) {
                $this->error('Content not found', 404);
                return;
            }
            
            $this->json(['message' => 'Content updated successfully']);
        } catch (\Exception $e) {
            $this->error('Database error: ' . $e->getMessage(), 500);
        }
    }
    
    public function delete(array $params): void {
        $user = $this->requireAuth();
        
        try {
            $stmt = Database::query(
                'DELETE FROM commnetivity_content WHERE id = ?',
                [$params['id']]
            );
            
            if ($stmt->rowCount() === 0) {
                $this->error('Content not found', 404);
                return;
            }
            
            $this->json(['message' => 'Content deleted successfully']);
        } catch (\Exception $e) {
            $this->error('Database error: ' . $e->getMessage(), 500);
        }
    }
} 
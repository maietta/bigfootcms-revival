<?php
declare(strict_types=1);

namespace App\Controllers;

abstract class Controller {
    protected function json(mixed $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
    
    protected function error(string $message, int $status = 400): void {
        $this->json(['error' => $message], $status);
    }
    
    protected function getRequestData(): array {
        $input = file_get_contents('php://input');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
            return json_decode($input, true) ?? [];
        }
        return $_GET;
    }
    
    protected function requireAuth(): ?array {
        session_start();
        if (!isset($_SESSION['user'])) {
            $this->error('Unauthorized', 401);
            exit;
        }
        return $_SESSION['user'];
    }
} 
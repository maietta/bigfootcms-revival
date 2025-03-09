<?php
declare(strict_types=1);

abstract class Api {
    protected array $allowedMethods = ['GET'];
    protected array $data = [];
    
    public function __construct() {
        header('Content-Type: application/json');
        $this->handleCors();
        $this->loadRequestData();
    }
    
    protected function handleCors(): void {
        // Allow from any origin during development
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header('Access-Control-Allow-Methods: ' . implode(', ', $this->allowedMethods));
            }
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }
            
            exit(0);
        }
    }
    
    protected function loadRequestData(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
            $input = file_get_contents('php://input');
            $this->data = json_decode($input, true) ?? [];
        } else {
            $this->data = $_GET;
        }
    }
    
    protected function sendResponse(mixed $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
    
    protected function sendError(string $message, int $statusCode = 400): void {
        $this->sendResponse(['error' => $message], $statusCode);
    }
    
    abstract public function handle(): void;
} 
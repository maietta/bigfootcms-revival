<?php
declare(strict_types=1);

class Framework {
    private PDO $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function query(string $path, string $table): ?object {
        $stmt = $this->db->prepare(
            "SELECT * FROM commnetivity_$table WHERE virtual_path = ?"
        );
        $stmt->execute([$path]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result === false ? null : $result;
    }
    
    public function navigation(string $position, int $limit = 5): array {
        $stmt = $this->db->prepare(
            "SELECT n.virtual_path, c.page_title, c.nav_title 
             FROM commnetivity_navigation n
             JOIN commnetivity_content c ON n.virtual_path = c.virtual_path
             WHERE n.position = ?
             ORDER BY n.weight
             LIMIT ?"
        );
        $stmt->execute([$position, $limit]);
        
        $links = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            if ($row === false) break;
            $title = $row->nav_title ?: $row->page_title;
            $links[] = [
                'virtual_path' => $row->virtual_path,
                'page_title' => $title
            ];
        }
        return $links;
    }
    
    public function updateContent(string $virtualPath, string $content): bool {
        try {
            // Start transaction
            $this->db->beginTransaction();
            
            // Archive current content
            $stmt = $this->db->prepare(
                "INSERT INTO commnetivity_content_hist 
                 SELECT id, virtual_path, page_title, nav_title, parent_id, 
                        cleartext_excerpts, encoded_content, CURRENT_TIMESTAMP
                 FROM commnetivity_content 
                 WHERE virtual_path = ?"
            );
            $stmt->execute([$virtualPath]);
            
            // Update content
            $stmt = $this->db->prepare(
                "UPDATE commnetivity_content 
                 SET encoded_content = ?, updated_at = CURRENT_TIMESTAMP
                 WHERE virtual_path = ?"
            );
            $stmt->execute([base64_encode($content), $virtualPath]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error updating content: " . $e->getMessage());
            return false;
        }
    }
    
    public function getContent(string $virtualPath): ?object {
        $stmt = $this->db->prepare(
            "SELECT * FROM commnetivity_content WHERE virtual_path = ?"
        );
        $stmt->execute([$virtualPath]);
        
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        if ($result === false) {
            return null;
        }
        
        if ($result->encoded_content) {
            $result->decoded_content = base64_decode($result->encoded_content);
        }
        
        return $result;
    }
    
    public function listContent(?string $parentId = null, int $limit = 50): array {
        $sql = "SELECT * FROM commnetivity_content";
        $params = [];
        
        if ($parentId) {
            $sql .= " WHERE parent_id = ?";
            $params[] = $parentId;
        }
        
        $sql .= " ORDER BY weight, virtual_path LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ) ?: [];
    }
} 
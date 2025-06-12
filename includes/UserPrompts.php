<?php
// User prompts management class
// includes/UserPrompts.php

require_once __DIR__ . '/Database.php';

class UserPrompts {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all prompts for a user
     */
    public function getUserPrompts($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, title, content, category, is_public, created_at, updated_at 
                FROM user_prompts 
                WHERE user_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                error_log("Error fetching user prompts: " . $e->getMessage());
            }
            return [];
        }
    }
    
    /**
     * Create a new prompt for a user
     */
    public function createPrompt($userId, $title, $content, $category = 'custom', $isPublic = false) {
        try {
            // Validate input
            $title = trim($title);
            $content = trim($content);
            
            if (empty($title) || empty($content)) {
                return ['success' => false, 'message' => 'Title and content are required'];
            }
            
            if (strlen($title) > 255) {
                return ['success' => false, 'message' => 'Title is too long (max 255 characters)'];
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO user_prompts (user_id, title, content, category, is_public) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([$userId, $title, $content, $category, $isPublic ? 1 : 0]);
            $promptId = $this->db->lastInsertId();
            
            // Return the created prompt
            return [
                'success' => true, 
                'message' => 'Prompt created successfully',
                'prompt' => [
                    'id' => $promptId,
                    'title' => $title,
                    'content' => $content,
                    'category' => $category,
                    'is_public' => $isPublic,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ];
            
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
            } else {
                return ['success' => false, 'message' => 'Failed to create prompt. Please try again.'];
            }
        }
    }
    
    /**
     * Update an existing prompt
     */
    public function updatePrompt($userId, $promptId, $title, $content, $category = 'custom', $isPublic = false) {
        try {
            // Validate input
            $title = trim($title);
            $content = trim($content);
            
            if (empty($title) || empty($content)) {
                return ['success' => false, 'message' => 'Title and content are required'];
            }
            
            if (strlen($title) > 255) {
                return ['success' => false, 'message' => 'Title is too long (max 255 characters)'];
            }
            
            // Check if prompt exists and belongs to user
            $stmt = $this->db->prepare("SELECT id FROM user_prompts WHERE id = ? AND user_id = ?");
            $stmt->execute([$promptId, $userId]);
            
            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'Prompt not found or access denied'];
            }
            
            // Update the prompt
            $stmt = $this->db->prepare("
                UPDATE user_prompts 
                SET title = ?, content = ?, category = ?, is_public = ?, updated_at = NOW() 
                WHERE id = ? AND user_id = ?
            ");
            
            $stmt->execute([$title, $content, $category, $isPublic ? 1 : 0, $promptId, $userId]);
            
            return [
                'success' => true, 
                'message' => 'Prompt updated successfully',
                'prompt' => [
                    'id' => $promptId,
                    'title' => $title,
                    'content' => $content,
                    'category' => $category,
                    'is_public' => $isPublic,
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ];
            
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
            } else {
                return ['success' => false, 'message' => 'Failed to update prompt. Please try again.'];
            }
        }
    }
    
    /**
     * Delete a prompt
     */
    public function deletePrompt($userId, $promptId) {
        try {
            // Check if prompt exists and belongs to user
            $stmt = $this->db->prepare("SELECT id FROM user_prompts WHERE id = ? AND user_id = ?");
            $stmt->execute([$promptId, $userId]);
            
            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'Prompt not found or access denied'];
            }
            
            // Delete the prompt
            $stmt = $this->db->prepare("DELETE FROM user_prompts WHERE id = ? AND user_id = ?");
            $stmt->execute([$promptId, $userId]);
            
            return ['success' => true, 'message' => 'Prompt deleted successfully'];
            
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
            } else {
                return ['success' => false, 'message' => 'Failed to delete prompt. Please try again.'];
            }
        }
    }
    
    /**
     * Get a specific prompt (with ownership check)
     */
    public function getPrompt($userId, $promptId) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, title, content, category, is_public, created_at, updated_at 
                FROM user_prompts 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$promptId, $userId]);
            $prompt = $stmt->fetch();
            
            if ($prompt) {
                return ['success' => true, 'prompt' => $prompt];
            } else {
                return ['success' => false, 'message' => 'Prompt not found or access denied'];
            }
            
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
            } else {
                return ['success' => false, 'message' => 'Failed to retrieve prompt. Please try again.'];
            }
        }
    }
    
    /**
     * Import prompts from localStorage data
     * Used when user logs in and we want to merge their local prompts with server prompts
     */
    public function importPromptsFromLocal($userId, $localPrompts) {
        try {
            $imported = 0;
            $skipped = 0;
            
            foreach ($localPrompts as $prompt) {
                if (!isset($prompt['title']) || !isset($prompt['content'])) {
                    $skipped++;
                    continue;
                }
                
                // Check if a prompt with the same title already exists for this user
                $stmt = $this->db->prepare("SELECT id FROM user_prompts WHERE user_id = ? AND title = ?");
                $stmt->execute([$userId, $prompt['title']]);
                
                if ($stmt->fetch()) {
                    $skipped++; // Skip if already exists
                    continue;
                }
                
                // Create the prompt
                $result = $this->createPrompt($userId, $prompt['title'], $prompt['content']);
                if ($result['success']) {
                    $imported++;
                } else {
                    $skipped++;
                }
            }
            
            return [
                'success' => true,
                'message' => "Import completed: {$imported} prompts imported, {$skipped} skipped",
                'imported' => $imported,
                'skipped' => $skipped
            ];
            
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                return ['success' => false, 'message' => 'Import error: ' . $e->getMessage()];
            } else {
                return ['success' => false, 'message' => 'Failed to import prompts. Please try again.'];
            }
        }
    }
}
?>

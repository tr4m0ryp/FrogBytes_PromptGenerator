<?php
// API endpoint for managing user prompts
// api/prompts.php

require_once __DIR__ . '/../includes/User.php';
require_once __DIR__ . '/../includes/UserPrompts.php';

header('Content-Type: application/json');

// Enable CORS if needed (for local development)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Start session and check authentication
session_start();

$user = new User();
if (!$user->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$currentUser = $user->getCurrentUser();
$userId = $currentUser['id'];

$userPrompts = new UserPrompts();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get all prompts for the user
            $prompts = $userPrompts->getUserPrompts($userId);
            echo json_encode(['success' => true, 'prompts' => $prompts]);
            break;
            
        case 'POST':
            // Create new prompt or handle special actions
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                $input = $_POST; // Fallback to form data
            }
            
            $action = $input['action'] ?? 'create';
            
            if ($action === 'import') {
                // Import prompts from localStorage
                $localPrompts = $input['prompts'] ?? [];
                $result = $userPrompts->importPromptsFromLocal($userId, $localPrompts);
                echo json_encode($result);
            } else {
                // Create new prompt
                $title = $input['title'] ?? '';
                $content = $input['content'] ?? '';
                $category = $input['category'] ?? 'custom';
                $isPublic = isset($input['is_public']) ? (bool)$input['is_public'] : false;
                
                $result = $userPrompts->createPrompt($userId, $title, $content, $category, $isPublic);
                echo json_encode($result);
            }
            break;
            
        case 'PUT':
            // Update existing prompt
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Prompt ID is required']);
                break;
            }
            
            $promptId = (int)$input['id'];
            $title = $input['title'] ?? '';
            $content = $input['content'] ?? '';
            $category = $input['category'] ?? 'custom';
            $isPublic = isset($input['is_public']) ? (bool)$input['is_public'] : false;
            
            $result = $userPrompts->updatePrompt($userId, $promptId, $title, $content, $category, $isPublic);
            echo json_encode($result);
            break;
            
        case 'DELETE':
            // Delete prompt
            $promptId = null;
            
            // Check if ID is in URL path or query parameter
            if (isset($_GET['id'])) {
                $promptId = (int)$_GET['id'];
            } else {
                // Parse from URL path like /api/prompts.php/123
                $pathInfo = $_SERVER['PATH_INFO'] ?? '';
                if (preg_match('/\/(\d+)$/', $pathInfo, $matches)) {
                    $promptId = (int)$matches[1];
                }
            }
            
            if (!$promptId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Prompt ID is required']);
                break;
            }
            
            $result = $userPrompts->deletePrompt($userId, $promptId);
            echo json_encode($result);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    if (DEBUG_MODE) {
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Internal server error']);
    }
}
?>

<?php
// User management class
// includes/User.php

require_once __DIR__ . '/Database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Register a new user
     */
    public function register($email, $password) {
        try {
            // Check if user already exists
            if ($this->getUserByEmail($email)) {
                return ['success' => false, 'message' => 'Email already registered'];
            }
            
            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Invalid email format'];
            }
            
            // Validate password (minimum 8 characters)
            if (strlen($password) < 8) {
                return ['success' => false, 'message' => 'Password must be at least 8 characters long'];
            }
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_HASH_ALGO, PASSWORD_HASH_OPTIONS);
            
            // Generate verification token
            $verificationToken = bin2hex(random_bytes(32));
            
            // Insert user
            $stmt = $this->db->prepare("
                INSERT INTO users (email, password_hash, verification_token) 
                VALUES (?, ?, ?)
            ");
            
            $stmt->execute([$email, $hashedPassword, $verificationToken]);
            
            return [
                'success' => true, 
                'message' => 'Account created successfully',
                'user_id' => $this->db->lastInsertId(),
                'verification_token' => $verificationToken
            ];
            
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
            } else {
                return ['success' => false, 'message' => 'Registration failed. Please try again.'];
            }
        }
    }
    
    /**
     * Login user
     */
    public function login($email, $password, $rememberMe = false) {
        try {
            $user = $this->getUserByEmail($email);
            
            if (!$user) {
                return ['success' => false, 'message' => 'Invalid email or password'];
            }
            
            if (!password_verify($password, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Invalid email or password'];
            }
            
            // Start session
            session_start();
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            
            // Create session record
            $sessionToken = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + ($rememberMe ? REMEMBER_ME_LIFETIME : SESSION_LIFETIME));
            
            $stmt = $this->db->prepare("
                INSERT INTO user_sessions (user_id, session_token, expires_at, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $user['id'],
                $sessionToken,
                $expiresAt,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
            
            if ($rememberMe) {
                setcookie('remember_token', $sessionToken, time() + REMEMBER_ME_LIFETIME, '/', '', true, true);
            }
            
            return ['success' => true, 'message' => 'Login successful', 'user' => $user];
            
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                return ['success' => false, 'message' => 'Login failed: ' . $e->getMessage()];
            } else {
                return ['success' => false, 'message' => 'Login failed. Please try again.'];
            }
        }
    }
    
    /**
     * Get user by email
     */
    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    /**
     * Get user by ID
     */
    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Create password reset token
     */
    public function createPasswordResetToken($email) {
        try {
            $user = $this->getUserByEmail($email);
            
            if (!$user) {
                return ['success' => false, 'message' => 'Email not found'];
            }
            
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour
            
            // Delete existing tokens for this email
            $stmt = $this->db->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt->execute([$email]);
            
            // Insert new token
            $stmt = $this->db->prepare("
                INSERT INTO password_resets (email, token, expires_at) 
                VALUES (?, ?, ?)
            ");
            
            $stmt->execute([$email, $token, $expiresAt]);
            
            return [
                'success' => true, 
                'message' => 'Password reset token created',
                'token' => $token
            ];
            
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                return ['success' => false, 'message' => 'Token creation failed: ' . $e->getMessage()];
            } else {
                return ['success' => false, 'message' => 'Failed to create reset token. Please try again.'];
            }
        }
    }
    
    /**
     * Reset password using token
     */
    public function resetPassword($token, $newPassword) {
        try {
            // Validate password
            if (strlen($newPassword) < 8) {
                return ['success' => false, 'message' => 'Password must be at least 8 characters long'];
            }
            
            // Check if token is valid and not expired
            $stmt = $this->db->prepare("
                SELECT * FROM password_resets 
                WHERE token = ? AND expires_at > NOW() AND used = FALSE
            ");
            $stmt->execute([$token]);
            $resetRecord = $stmt->fetch();
            
            if (!$resetRecord) {
                return ['success' => false, 'message' => 'Invalid or expired reset token'];
            }
            
            // Hash new password
            $hashedPassword = password_hash($newPassword, PASSWORD_HASH_ALGO, PASSWORD_HASH_OPTIONS);
            
            // Update user password
            $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
            $stmt->execute([$hashedPassword, $resetRecord['email']]);
            
            // Mark token as used
            $stmt = $this->db->prepare("UPDATE password_resets SET used = TRUE WHERE token = ?");
            $stmt->execute([$token]);
            
            return ['success' => true, 'message' => 'Password reset successfully'];
            
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                return ['success' => false, 'message' => 'Password reset failed: ' . $e->getMessage()];
            } else {
                return ['success' => false, 'message' => 'Password reset failed. Please try again.'];
            }
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        session_start();
        
        // Remove session from database if exists
        if (isset($_SESSION['user_id'])) {
            if (isset($_COOKIE['remember_token'])) {
                $stmt = $this->db->prepare("DELETE FROM user_sessions WHERE session_token = ?");
                $stmt->execute([$_COOKIE['remember_token']]);
                setcookie('remember_token', '', time() - 3600, '/', '', true, true);
            }
        }
        
        // Destroy session
        session_destroy();
        
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        session_start();
        
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            return true;
        }
        
        // Check remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            $stmt = $this->db->prepare("
                SELECT u.* FROM users u 
                JOIN user_sessions s ON u.id = s.user_id 
                WHERE s.session_token = ? AND s.expires_at > NOW()
            ");
            $stmt->execute([$_COOKIE['remember_token']]);
            $user = $stmt->fetch();
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['logged_in'] = true;
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get current logged in user
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return $this->getUserById($_SESSION['user_id']);
    }
}
?>

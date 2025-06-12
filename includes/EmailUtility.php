<?php
// Email utility for sending emails
// includes/EmailUtility.php

require_once __DIR__ . '/../config/database.php';

class EmailUtility {
    
    /**
     * Send password reset email
     */
    public static function sendPasswordResetEmail($email, $resetToken) {
        $resetLink = SITE_URL . "/reset_password.php?token=" . $resetToken;
        
        $subject = "Password Reset - " . SITE_NAME;
        
        $message = self::getPasswordResetEmailTemplate($resetLink);
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . SMTP_FROM_NAME . ' <' . SMTP_FROM_EMAIL . '>',
            'Reply-To: ' . SMTP_FROM_EMAIL,
            'X-Mailer: PHP/' . phpversion()
        ];
        
        // Try to send using mail() function first (simplest approach)
        if (mail($email, $subject, $message, implode("\r\n", $headers))) {
            return ['success' => true, 'message' => 'Email sent successfully'];
        }
        
        // If mail() fails, you can implement SMTP here using PHPMailer or similar
        return ['success' => false, 'message' => 'Failed to send email'];
    }
    
    /**
     * Get password reset email template
     */
    private static function getPasswordResetEmailTemplate($resetLink) {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Password Reset - ' . SITE_NAME . '</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0D1117; color: #39FF14; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .button { 
                    display: inline-block; 
                    padding: 12px 24px; 
                    background: #39FF14; 
                    color: #0D1117; 
                    text-decoration: none; 
                    border-radius: 4px; 
                    font-weight: bold;
                    margin: 20px 0;
                }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>' . SITE_NAME . '</h1>
                </div>
                <div class="content">
                    <h2>Password Reset Request</h2>
                    <p>You have requested to reset your password. Click the button below to set a new password:</p>
                    
                    <a href="' . $resetLink . '" class="button">Reset Password</a>
                    
                    <p>If the button doesn\'t work, copy and paste this link into your browser:</p>
                    <p><a href="' . $resetLink . '">' . $resetLink . '</a></p>
                    
                    <p><strong>Important:</strong> This link will expire in 1 hour for security reasons.</p>
                    
                    <p>If you did not request this password reset, please ignore this email.</p>
                </div>
                <div class="footer">
                    <p>&copy; ' . date('Y') . ' ' . SITE_NAME . '. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
    }
    
    /**
     * Send verification email (for future use)
     */
    public static function sendVerificationEmail($email, $verificationToken) {
        $verificationLink = SITE_URL . "/verify_email.php?token=" . $verificationToken;
        
        $subject = "Verify Your Email - " . SITE_NAME;
        
        $message = self::getVerificationEmailTemplate($verificationLink);
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . SMTP_FROM_NAME . ' <' . SMTP_FROM_EMAIL . '>',
            'Reply-To: ' . SMTP_FROM_EMAIL,
            'X-Mailer: PHP/' . phpversion()
        ];
        
        if (mail($email, $subject, $message, implode("\r\n", $headers))) {
            return ['success' => true, 'message' => 'Verification email sent successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to send verification email'];
    }
    
    /**
     * Get verification email template
     */
    private static function getVerificationEmailTemplate($verificationLink) {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Verify Your Email - ' . SITE_NAME . '</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0D1117; color: #39FF14; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .button { 
                    display: inline-block; 
                    padding: 12px 24px; 
                    background: #39FF14; 
                    color: #0D1117; 
                    text-decoration: none; 
                    border-radius: 4px; 
                    font-weight: bold;
                    margin: 20px 0;
                }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>' . SITE_NAME . '</h1>
                </div>
                <div class="content">
                    <h2>Welcome to ' . SITE_NAME . '!</h2>
                    <p>Thank you for signing up. Please verify your email address by clicking the button below:</p>
                    
                    <a href="' . $verificationLink . '" class="button">Verify Email</a>
                    
                    <p>If the button doesn\'t work, copy and paste this link into your browser:</p>
                    <p><a href="' . $verificationLink . '">' . $verificationLink . '</a></p>
                    
                    <p>If you did not create this account, please ignore this email.</p>
                </div>
                <div class="footer">
                    <p>&copy; ' . date('Y') . ' ' . SITE_NAME . '. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
    }
}
?>

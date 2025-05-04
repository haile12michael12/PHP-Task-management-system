<?php
/**
 * Session Management Class
 * 
 * Handles user sessions, flash messages, and session security
 */
class Session {
    /**
     * Constructor
     * Starts the session if it's not already started
     */
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Check if a user is logged in
     * 
     * @return bool True if user is logged in, false otherwise
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Set the user ID in the session
     * 
     * @param int $userId The user ID to store in the session
     */
    public function setUserId($userId) {
        $_SESSION['user_id'] = $userId;
    }
    
    /**
     * Get the user ID from the session
     * 
     * @return int|null The user ID or null if not set
     */
    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Set the user name in the session
     * 
     * @param string $userName The user name to store in the session
     */
    public function setUserName($userName) {
        $_SESSION['user_name'] = $userName;
    }
    
    /**
     * Get the user name from the session
     * 
     * @return string|null The user name or null if not set
     */
    public function getUserName() {
        return $_SESSION['user_name'] ?? null;
    }
    
    /**
     * Set a flash message that will be displayed once and then removed
     * 
     * @param string $key The key to store the message under
     * @param mixed $value The value to store
     */
    public function setFlash($key, $value) {
        $_SESSION['flash_' . $key] = $value;
    }
    
    /**
     * Get a flash message by key
     * 
     * @param string $key The key of the flash message
     * @return mixed|null The flash message value or null if not set
     */
    public function getFlash($key) {
        if (isset($_SESSION['flash_' . $key])) {
            $value = $_SESSION['flash_' . $key];
            unset($_SESSION['flash_' . $key]);
            return $value;
        }
        return null;
    }
    
    /**
     * Check if a flash message exists
     * 
     * @param string $key The key of the flash message
     * @return bool True if the flash message exists, false otherwise
     */
    public function hasFlash($key) {
        return isset($_SESSION['flash_' . $key]);
    }
    
    /**
     * Set a session variable
     * 
     * @param string $key The key to store the variable under
     * @param mixed $value The value to store
     */
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get a session variable
     * 
     * @param string $key The key of the session variable
     * @param mixed $default The default value to return if the key doesn't exist
     * @return mixed The session variable value or the default value
     */
    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Check if a session variable exists
     * 
     * @param string $key The key of the session variable
     * @return bool True if the session variable exists, false otherwise
     */
    public function has($key) {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remove a session variable
     * 
     * @param string $key The key of the session variable to remove
     */
    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Destroy the session and all data
     */
    public function destroy() {
        session_unset();
        session_destroy();
    }
    
    /**
     * Regenerate the session ID to prevent session fixation
     * 
     * @param bool $deleteOldSession Whether to delete the old session data
     */
    public function regenerate($deleteOldSession = true) {
        session_regenerate_id($deleteOldSession);
    }
    
    /**
     * Clear all flash messages
     */
    public function clearFlash() {
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, 'flash_') === 0) {
                unset($_SESSION[$key]);
            }
        }
    }
}

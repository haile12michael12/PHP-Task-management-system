<?php
/**
 * Form Validation Class
 * 
 * Provides methods for validating form inputs
 */
class Validator {
    /**
     * Validate an email address
     * 
     * @param string $email The email address to validate
     * @return bool True if the email is valid, false otherwise
     */
    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate a date string
     * 
     * @param string $date The date string to validate (YYYY-MM-DD format)
     * @return bool True if the date is valid, false otherwise
     */
    public function validateDate($date) {
        $format = 'Y-m-d';
        $dateTime = DateTime::createFromFormat($format, $date);
        return $dateTime && $dateTime->format($format) === $date;
    }
    
    /**
     * Validate a hexadecimal color code
     * 
     * @param string $color The color code to validate (#RRGGBB format)
     * @return bool True if the color code is valid, false otherwise
     */
    public function validateHexColor($color) {
        return preg_match('/^#([A-Fa-f0-9]{6})$/', $color) === 1;
    }
    
    /**
     * Validate a password strength
     * 
     * @param string $password The password to validate
     * @param int $minLength The minimum length required (default: 6)
     * @return bool True if the password meets the requirements, false otherwise
     */
    public function validatePassword($password, $minLength = 6) {
        return strlen($password) >= $minLength;
    }
    
    /**
     * Validate that two strings match (e.g., password confirmation)
     * 
     * @param string $str1 The first string
     * @param string $str2 The second string
     * @return bool True if the strings match, false otherwise
     */
    public function validateMatch($str1, $str2) {
        return $str1 === $str2;
    }
    
    /**
     * Validate that a string is not empty
     * 
     * @param string $value The string to validate
     * @return bool True if the string is not empty, false otherwise
     */
    public function validateRequired($value) {
        return !empty(trim($value));
    }
    
    /**
     * Validate a number is within a given range
     * 
     * @param int|float $value The number to validate
     * @param int|float $min The minimum allowed value
     * @param int|float $max The maximum allowed value
     * @return bool True if the number is within the range, false otherwise
     */
    public function validateRange($value, $min, $max) {
        return $value >= $min && $value <= $max;
    }
    
    /**
     * Validate a string length is within a given range
     * 
     * @param string $value The string to validate
     * @param int $min The minimum allowed length
     * @param int $max The maximum allowed length
     * @return bool True if the string length is within the range, false otherwise
     */
    public function validateLength($value, $min, $max) {
        $length = strlen(trim($value));
        return $length >= $min && $length <= $max;
    }
    
    /**
     * Validate that a value is one of a set of allowed values
     * 
     * @param mixed $value The value to validate
     * @param array $allowedValues The array of allowed values
     * @return bool True if the value is in the allowed values, false otherwise
     */
    public function validateInArray($value, array $allowedValues) {
        return in_array($value, $allowedValues);
    }
    
    /**
     * Sanitize a string by removing HTML and PHP tags
     * 
     * @param string $value The string to sanitize
     * @return string The sanitized string
     */
    public function sanitizeString($value) {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitize an integer
     * 
     * @param mixed $value The value to sanitize
     * @return int The sanitized integer
     */
    public function sanitizeInt($value) {
        return (int) $value;
    }
    
    /**
     * Sanitize a float
     * 
     * @param mixed $value The value to sanitize
     * @return float The sanitized float
     */
    public function sanitizeFloat($value) {
        return (float) $value;
    }
}

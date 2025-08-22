<?php

namespace App\Libraries;

use CodeIgniter\Database\Exceptions\DatabaseException;
use Exception;

class DatabaseErrorHandler
{
    /**
     * Handle database exceptions safely
     */
    public static function handle(Exception $e, string $context = ''): array
    {
        $errorCode = 'DB_ERROR';
        $userMessage = 'A database error occurred. Please try again later.';
        $logMessage = $e->getMessage();
        
        // Categorize different types of database errors
        if ($e instanceof DatabaseException) {
            $errorCode = self::categorizeError($e);
            $userMessage = self::getUserFriendlyMessage($errorCode);
        }
        
        // Log the actual error for developers
        log_message('error', "Database Error [{$errorCode}] in {$context}: {$logMessage}");
        
        // Return sanitized error information
        return [
            'error' => true,
            'code' => $errorCode,
            'message' => $userMessage,
            'context' => $context
        ];
    }

    /**
     * Categorize database errors
     */
    private static function categorizeError(DatabaseException $e): string
    {
        $message = strtolower($e->getMessage());
        
        // Connection errors
        if (strpos($message, 'connection') !== false || strpos($message, 'connect') !== false) {
            return 'CONNECTION_ERROR';
        }
        
        // Constraint violations
        if (strpos($message, 'duplicate') !== false || strpos($message, 'unique') !== false) {
            return 'DUPLICATE_ENTRY';
        }
        
        if (strpos($message, 'foreign key') !== false || strpos($message, 'constraint') !== false) {
            return 'CONSTRAINT_VIOLATION';
        }
        
        // Permission errors
        if (strpos($message, 'access denied') !== false || strpos($message, 'permission') !== false) {
            return 'ACCESS_DENIED';
        }
        
        // Table/column errors
        if (strpos($message, 'table') !== false && strpos($message, 'exist') !== false) {
            return 'TABLE_NOT_FOUND';
        }
        
        if (strpos($message, 'column') !== false && strpos($message, 'unknown') !== false) {
            return 'COLUMN_NOT_FOUND';
        }
        
        // Syntax errors
        if (strpos($message, 'syntax') !== false) {
            return 'SYNTAX_ERROR';
        }
        
        // Timeout errors
        if (strpos($message, 'timeout') !== false || strpos($message, 'lock') !== false) {
            return 'TIMEOUT_ERROR';
        }
        
        return 'GENERAL_ERROR';
    }

    /**
     * Get user-friendly error messages
     */
    private static function getUserFriendlyMessage(string $errorCode): string
    {
        return match($errorCode) {
            'CONNECTION_ERROR' => 'Unable to connect to the database. Please check your connection and try again.',
            'DUPLICATE_ENTRY' => 'This record already exists. Please use a different identifier.',
            'CONSTRAINT_VIOLATION' => 'This operation cannot be completed due to data relationships. Please check related records.',
            'ACCESS_DENIED' => 'Database access denied. Please contact your administrator.',
            'TABLE_NOT_FOUND' => 'Required data table not found. Please contact your administrator.',
            'COLUMN_NOT_FOUND' => 'Data structure error. Please contact your administrator.',
            'SYNTAX_ERROR' => 'Database query error. Please contact your administrator.',
            'TIMEOUT_ERROR' => 'Database operation timed out. Please try again.',
            default => 'A database error occurred. Please try again later.'
        };
    }

    /**
     * Check if error is recoverable
     */
    public static function isRecoverable(string $errorCode): bool
    {
        $recoverableErrors = [
            'CONNECTION_ERROR',
            'TIMEOUT_ERROR',
            'GENERAL_ERROR'
        ];
        
        return in_array($errorCode, $recoverableErrors);
    }

    /**
     * Get suggested action for error
     */
    public static function getSuggestedAction(string $errorCode): string
    {
        return match($errorCode) {
            'CONNECTION_ERROR' => 'Check your internet connection and try again.',
            'DUPLICATE_ENTRY' => 'Use a different ID or update the existing record.',
            'CONSTRAINT_VIOLATION' => 'Remove or update related records first.',
            'ACCESS_DENIED' => 'Contact your system administrator.',
            'TIMEOUT_ERROR' => 'Try again with a smaller data set.',
            default => 'Contact technical support if the problem persists.'
        };
    }

    /**
     * Log security-related database events
     */
    public static function logSecurityEvent(string $event, array $context = []): void
    {
        $securityLog = [
            'event' => $event,
            'timestamp' => date('Y-m-d H:i:s'),
            'user_id' => session('id_user') ?? 'anonymous',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'context' => $context
        ];
        
        log_message('critical', 'Database Security Event: ' . json_encode($securityLog));
    }

    /**
     * Validate query for potential security issues
     */
    public static function validateQuery(string $query): array
    {
        $issues = [];
        $query = strtolower(trim($query));
        
        // Check for dangerous operations
        $dangerousPatterns = [
            '/drop\s+table/i' => 'DROP TABLE detected',
            '/drop\s+database/i' => 'DROP DATABASE detected',
            '/truncate/i' => 'TRUNCATE detected',
            '/delete\s+from\s+\w+\s*$/i' => 'DELETE without WHERE clause',
            '/update\s+\w+\s+set\s+.*\s*$/i' => 'UPDATE without WHERE clause',
            '/union\s+select/i' => 'Potential SQL injection (UNION)',
            '/;\s*drop/i' => 'Potential SQL injection (stacked queries)',
            '/\/\*.*\*\//i' => 'SQL comments detected',
            '/--/i' => 'SQL comments detected',
            '/xp_cmdshell/i' => 'System command execution attempt',
            '/sp_executesql/i' => 'Dynamic SQL execution attempt'
        ];
        
        foreach ($dangerousPatterns as $pattern => $description) {
            if (preg_match($pattern, $query)) {
                $issues[] = $description;
            }
        }
        
        return $issues;
    }

    /**
     * Sanitize error message for display
     */
    public static function sanitizeErrorMessage(string $message): string
    {
        // Remove sensitive information from error messages
        $sensitivePatterns = [
            '/password[^\'\"]*[\'\"]\w+[\'\"]/' => 'password [REDACTED]',
            '/user[^\'\"]*[\'\"]\w+[\'\"]/' => 'user [REDACTED]',
            '/host[^\'\"]*[\'\"]\w+[\'\"]/' => 'host [REDACTED]',
            '/database[^\'\"]*[\'\"]\w+[\'\"]/' => 'database [REDACTED]',
            '/table[^\'\"]*[\'\"]\w+[\'\"]/' => 'table [REDACTED]'
        ];
        
        foreach ($sensitivePatterns as $pattern => $replacement) {
            $message = preg_replace($pattern, $replacement, $message);
        }
        
        return $message;
    }
}
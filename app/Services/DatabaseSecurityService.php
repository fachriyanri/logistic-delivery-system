<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Query;
use Config\Database;
use Exception;

class DatabaseSecurityService
{
    protected BaseConnection $db;
    protected array $sensitiveFields = [
        'password', 'token', 'secret', 'key', 'hash'
    ];

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Execute a secure query with prepared statements
     */
    public function secureQuery(string $sql, array $binds = []): Query
    {
        try {
            // Log query for debugging (without sensitive data)
            $this->logQuery($sql, $binds);
            
            // Execute with prepared statement
            return $this->db->query($sql, $binds);
        } catch (Exception $e) {
            // Log error without exposing sensitive information
            $this->logError($e, $sql);
            throw new Exception('Database operation failed');
        }
    }

    /**
     * Secure select query with automatic escaping
     */
    public function secureSelect(string $table, array $where = [], array $select = ['*']): array
    {
        $builder = $this->db->table($table);
        
        if (!empty($select)) {
            $builder->select(implode(', ', $select));
        }
        
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                $builder->whereIn($field, $value);
            } else {
                $builder->where($field, $value);
            }
        }
        
        try {
            return $builder->get()->getResultArray();
        } catch (Exception $e) {
            $this->logError($e, "SELECT from {$table}");
            throw new Exception('Database query failed');
        }
    }

    /**
     * Secure insert with data validation
     */
    public function secureInsert(string $table, array $data): bool
    {
        // Sanitize data
        $cleanData = $this->sanitizeData($data);
        
        try {
            return $this->db->table($table)->insert($cleanData);
        } catch (Exception $e) {
            $this->logError($e, "INSERT into {$table}");
            throw new Exception('Database insert failed');
        }
    }

    /**
     * Secure update with data validation
     */
    public function secureUpdate(string $table, array $data, array $where): bool
    {
        // Sanitize data
        $cleanData = $this->sanitizeData($data);
        
        $builder = $this->db->table($table);
        
        foreach ($where as $field => $value) {
            $builder->where($field, $value);
        }
        
        try {
            return $builder->update($cleanData);
        } catch (Exception $e) {
            $this->logError($e, "UPDATE {$table}");
            throw new Exception('Database update failed');
        }
    }

    /**
     * Secure delete with confirmation
     */
    public function secureDelete(string $table, array $where): bool
    {
        if (empty($where)) {
            throw new Exception('Delete operation requires WHERE conditions');
        }
        
        $builder = $this->db->table($table);
        
        foreach ($where as $field => $value) {
            $builder->where($field, $value);
        }
        
        try {
            return $builder->delete();
        } catch (Exception $e) {
            $this->logError($e, "DELETE from {$table}");
            throw new Exception('Database delete failed');
        }
    }

    /**
     * Execute transaction safely
     */
    public function secureTransaction(callable $callback): bool
    {
        $this->db->transStart();
        
        try {
            $result = $callback($this);
            
            if ($result === false) {
                $this->db->transRollback();
                return false;
            }
            
            $this->db->transComplete();
            return $this->db->transStatus();
        } catch (Exception $e) {
            $this->db->transRollback();
            $this->logError($e, 'Transaction');
            throw new Exception('Transaction failed');
        }
    }

    /**
     * Get database statistics safely
     */
    public function getDatabaseStats(): array
    {
        try {
            $stats = [];
            
            // Get table counts
            $tables = ['user', 'kategori', 'barang', 'pelanggan', 'kurir', 'pengiriman', 'detail_pengiriman'];
            
            foreach ($tables as $table) {
                $count = $this->db->table($table)->countAllResults();
                $stats[$table] = $count;
            }
            
            return $stats;
        } catch (Exception $e) {
            $this->logError($e, 'Database stats');
            return [];
        }
    }

    /**
     * Sanitize input data
     */
    private function sanitizeData(array $data): array
    {
        $cleanData = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                // Remove null bytes
                $value = str_replace(chr(0), '', $value);
                
                // Trim whitespace
                $value = trim($value);
                
                // For sensitive fields, don't log the value
                if (in_array(strtolower($key), $this->sensitiveFields)) {
                    // Hash passwords if not already hashed
                    if ($key === 'password' && !password_get_info($value)['algo']) {
                        $value = password_hash($value, PASSWORD_ARGON2ID);
                    }
                }
            }
            
            $cleanData[$key] = $value;
        }
        
        return $cleanData;
    }

    /**
     * Log query for debugging (without sensitive data)
     */
    private function logQuery(string $sql, array $binds = []): void
    {
        if (ENVIRONMENT === 'development') {
            $sanitizedBinds = [];
            
            foreach ($binds as $key => $value) {
                if (is_string($key) && in_array(strtolower($key), $this->sensitiveFields)) {
                    $sanitizedBinds[$key] = '[REDACTED]';
                } else {
                    $sanitizedBinds[$key] = $value;
                }
            }
            
            log_message('debug', 'Database Query: ' . $sql . ' | Binds: ' . json_encode($sanitizedBinds));
        }
    }

    /**
     * Log database errors safely
     */
    private function logError(Exception $e, string $context): void
    {
        $errorMessage = "Database Error in {$context}: " . $e->getMessage();
        
        // Don't expose sensitive information in production
        if (ENVIRONMENT === 'production') {
            $errorMessage = "Database Error in {$context}: Operation failed";
        }
        
        log_message('error', $errorMessage);
    }

    /**
     * Validate table name to prevent injection
     */
    private function validateTableName(string $table): bool
    {
        // Only allow alphanumeric characters and underscores
        return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table) === 1;
    }

    /**
     * Validate column name to prevent injection
     */
    private function validateColumnName(string $column): bool
    {
        // Only allow alphanumeric characters and underscores
        return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column) === 1;
    }

    /**
     * Get connection info safely
     */
    public function getConnectionInfo(): array
    {
        return [
            'database' => $this->db->getDatabase(),
            'platform' => $this->db->getPlatform(),
            'version' => $this->db->getVersion(),
            'charset' => $this->db->getCharset(),
        ];
    }

    /**
     * Check database connection health
     */
    public function checkConnection(): bool
    {
        try {
            $this->db->query('SELECT 1');
            return true;
        } catch (Exception $e) {
            $this->logError($e, 'Connection check');
            return false;
        }
    }

    /**
     * Backup critical data (for security purposes)
     */
    public function backupCriticalData(): array
    {
        try {
            $backup = [];
            
            // Backup user data (without passwords)
            $users = $this->secureSelect('user', [], ['id_user', 'username', 'level']);
            $backup['users'] = $users;
            
            // Backup system configuration if exists
            // This would be extended based on actual requirements
            
            return $backup;
        } catch (Exception $e) {
            $this->logError($e, 'Data backup');
            return [];
        }
    }

    /**
     * Audit database operations
     */
    public function auditOperation(string $operation, string $table, array $data = []): void
    {
        try {
            $auditData = [
                'operation' => $operation,
                'table_name' => $table,
                'user_id' => session('id_user') ?? 'system',
                'timestamp' => date('Y-m-d H:i:s'),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ];
            
            // Don't audit sensitive data
            if (!in_array($table, ['user']) || $operation !== 'SELECT') {
                $auditData['affected_records'] = count($data);
            }
            
            log_message('info', 'Database Audit: ' . json_encode($auditData));
        } catch (Exception $e) {
            // Don't throw exception for audit failures
            log_message('error', 'Audit logging failed: ' . $e->getMessage());
        }
    }
}
<?php

namespace App\Services;

use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;
use InvalidArgumentException;
use RuntimeException;

class FileUploadService
{
    /**
     * Allowed file types for uploads
     */
    private array $allowedTypes = [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
        'general' => ['txt', 'csv']
    ];

    /**
     * Maximum file size in bytes (5MB default)
     */
    private int $maxFileSize = 5242880;

    /**
     * Upload directory
     */
    private string $uploadPath = WRITEPATH . 'uploads/';

    public function __construct()
    {
        // Ensure upload directory exists
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }

    /**
     * Validate uploaded file
     */
    public function validateFile(UploadedFile $file, string $type = 'general'): bool
    {
        // Check if file was uploaded
        if (!$file->isValid()) {
            throw new InvalidArgumentException('Invalid file upload: ' . $file->getErrorString());
        }

        // Check file size
        if ($file->getSize() > $this->maxFileSize) {
            throw new InvalidArgumentException('File size exceeds maximum allowed size of ' . $this->formatBytes($this->maxFileSize));
        }

        // Check file type
        $extension = strtolower($file->getClientExtension());
        if (!isset($this->allowedTypes[$type]) || !in_array($extension, $this->allowedTypes[$type])) {
            throw new InvalidArgumentException('File type not allowed. Allowed types: ' . implode(', ', $this->allowedTypes[$type] ?? []));
        }

        // Check MIME type
        if (!$this->isValidMimeType($file, $extension)) {
            throw new InvalidArgumentException('File MIME type does not match extension');
        }

        return true;
    }

    /**
     * Sanitize filename
     */
    public function sanitizeFilename(string $filename): string
    {
        // Remove any path information
        $filename = basename($filename);
        
        // Remove special characters and spaces
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Remove multiple underscores
        $filename = preg_replace('/_+/', '_', $filename);
        
        // Trim underscores from start and end
        $filename = trim($filename, '_');
        
        // Ensure filename is not empty
        if (empty($filename)) {
            $filename = 'file_' . time();
        }

        return $filename;
    }

    /**
     * Store uploaded file securely
     */
    public function storeFile(UploadedFile $file, string $subfolder = '', string $type = 'general'): string
    {
        $this->validateFile($file, $type);

        // Create subfolder if specified
        $targetPath = $this->uploadPath;
        if (!empty($subfolder)) {
            $targetPath .= rtrim($subfolder, '/') . '/';
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0755, true);
            }
        }

        // Generate unique filename
        $originalName = $this->sanitizeFilename($file->getClientName());
        $extension = $file->getClientExtension();
        $filename = pathinfo($originalName, PATHINFO_FILENAME) . '_' . time() . '.' . $extension;

        // Move file to target location
        if (!$file->move($targetPath, $filename)) {
            throw new RuntimeException('Failed to move uploaded file');
        }

        // Return relative path from writable directory
        return 'uploads/' . ($subfolder ? $subfolder . '/' : '') . $filename;
    }

    /**
     * Delete uploaded file
     */
    public function deleteFile(string $filePath): bool
    {
        $fullPath = WRITEPATH . $filePath;
        
        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }

    /**
     * Get file URL for display
     */
    public function getFileUrl(string $filePath): string
    {
        return base_url('uploads/' . ltrim($filePath, '/'));
    }

    /**
     * Validate MIME type against file extension
     */
    private function isValidMimeType(UploadedFile $file, string $extension): bool
    {
        $mimeType = $file->getClientMimeType();
        
        $validMimeTypes = [
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'webp' => ['image/webp'],
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'xls' => ['application/vnd.ms-excel'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'txt' => ['text/plain'],
            'csv' => ['text/csv', 'application/csv']
        ];

        return isset($validMimeTypes[$extension]) && in_array($mimeType, $validMimeTypes[$extension]);
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Set maximum file size
     */
    public function setMaxFileSize(int $bytes): self
    {
        $this->maxFileSize = $bytes;
        return $this;
    }

    /**
     * Add allowed file type
     */
    public function addAllowedType(string $category, array $extensions): self
    {
        if (!isset($this->allowedTypes[$category])) {
            $this->allowedTypes[$category] = [];
        }
        
        $this->allowedTypes[$category] = array_merge($this->allowedTypes[$category], $extensions);
        return $this;
    }
}
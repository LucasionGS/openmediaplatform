<?php

namespace App\Traits;

trait HandlesUploadLimits
{
    /**
     * Get the maximum upload file size from PHP configuration
     */
    public function getMaxUploadSize()
    {
        // Get values from PHP configuration
        $uploadMax = $this->parseSize(ini_get('upload_max_filesize'));
        $postMax = $this->parseSize(ini_get('post_max_size'));
        $memoryLimit = $this->parseSize(ini_get('memory_limit'));
        
        // Return the smallest of the three (most restrictive)
        return min($uploadMax, $postMax, $memoryLimit);
    }
    
    /**
     * Parse PHP size values (like "500M", "2G", "1024K")
     */
    public function parseSize($size) 
    {
        $size = trim($size);
        $last = strtolower($size[strlen($size)-1]);
        $size = (int) $size;
        
        switch($last) {
            case 'g':
                $size *= 1024 * 1024 * 1024;
                break;
            case 'm':
                $size *= 1024 * 1024;
                break;
            case 'k':
                $size *= 1024;
                break;
        }
        
        return $size;
    }
    
    /**
     * Get human readable file size
     */
    public function formatBytes($bytes, $precision = 2) 
    {
        $units = array('B', 'KB', 'MB', 'GB');
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Get maximum upload size in KB for Laravel validation
     */
    public function getMaxUploadSizeInKB()
    {
        return floor($this->getMaxUploadSize() / 1024);
    }
}

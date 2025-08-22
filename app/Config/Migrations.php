<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Migrations extends BaseConfig
{
    /**
     * Enable/Disable Migrations
     */
    public bool $enabled = true;

    /**
     * Migrations Table
     */
    public string $table = 'migrations';

    /**
     * Timestamp Format
     */
    public string $timestampFormat = 'Y-m-d-His_';

    /**
     * Migration Type
     * 
     * Migration file names may be based on a sequential identifier or on a timestamp.
     * Options are:
     *   'sequential' = Default migration naming (001, 002, 003...)
     *   'timestamp'  = Timestamp migration naming (YYYYMMDDHHIISS)
     */
    public string $type = 'timestamp';

    /**
     * Migrations version
     */
    public string $version = '2024-01-01-000007';
}
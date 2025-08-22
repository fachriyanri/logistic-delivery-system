<?php

namespace Config;

use CodeIgniter\Config\AutoloadConfig;

class Autoload extends AutoloadConfig
{
    // PSR-4 namespaces
    public $psr4 = [
        'App'    => APPPATH,
        'Config' => APPPATH . 'Config',
    ];

    // Optional autoloads
    public $classmap = [];
    public $files    = [];
    public $helpers  = [];
}

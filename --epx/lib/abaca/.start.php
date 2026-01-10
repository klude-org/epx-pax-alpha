<?php 

namespace { return (function(){
    try {
        
        \define('_\START_FILE', \str_replace('\\','/', __FILE__));
        \define('_\START_DIR', \str_replace('\\','/', __DIR__));
        \define('_\DATA_DIR', $_SERVER['_']['DATA_DIR']);
        
        $this->i->dx;
        
        0 AND $this->i->dx->trap();    
        
        return $this->i->nav->route();
        
    } finally {
        
        \define('_\INTFC', $_SERVER['_']['INTFC']);
        \define('_\KEY', \md5($_SERVER['SCRIPT_FILENAME']));
        \define('_\SITE_DIR', $_SERVER['_']['SITE_DIR']);
        \define('_\LOCAL_DIR', $_SERVER['_']['LOCAL_DIR']);
        
        \define('_\ROOT_DIR', $_SERVER['_']['ROOT_DIR'] ?? '');
        \define('_\ROOT_URL', $_SERVER['_']['ROOT_URL'] ?? '');
        \define('_\IS_CLI', $_SERVER['_']['IS_CLI']);
        \define('_\IS_WEB', ($_SERVER['_']['INTFC'] === 'web'));
        \define('_\IS_HTTP', ($_SERVER['_']['INTFC'] !== 'cli'));
        \define('_\IS_API', (!\_\IS_CLI && !\_\IS_WEB));
        \define('_\IS_HTML', (\strpos(($_SERVER['HTTP_ACCEPT'] ?? ''),'text/html') !== false));
        
    } 
})->bindTo(\_::_(),\_::class)(); }

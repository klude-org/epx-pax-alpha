<?php

namespace { return (function(){
    
    \define('_\START_FILE', \str_replace('\\','/', __FILE__));
    \define('_\START_DIR', \str_replace('\\','/', __DIR__));
    \define('_\DATA_DIR', $_SERVER['_']['DATA_DIR']);
    \define('_\SITE_DIR', $_SERVER['_']['SITE_DIR']);
    \define('_\LOCAL_DIR', $_SERVER['_']['LOCAL_DIR']);
    \define('_\ROOT_DIR', $_SERVER['_']['ROOT_DIR'] ?? '');
    \define('_\ROOT_URL', $_SERVER['_']['ROOT_URL'] ?? '');
    \define('_\INTFC', $intfc = $_SERVER['_']['INTFC']);
    \define('_\OB_OUT', $_SERVER['_']['OB_OUT']);
    \define('_\OB_TOP', $_SERVER['_']['OB_TOP']);
    \define('_\IS_CLI', $_SERVER['_']['IS_CLI']);
    \define('_\IS_WEB', ($_SERVER['_']['INTFC'] === 'web'));
    \define('_\IS_HTTP', ($_SERVER['_']['INTFC'] !== 'cli'));
    \define('_\IS_API', (!\_\IS_CLI && !\_\IS_WEB));
    \define('_\IS_HTML', (\strpos(($_SERVER['HTTP_ACCEPT'] ?? ''),'text/html') !== false));
    \define('_\KEY', \md5($_SERVER['SCRIPT_FILENAME']));
    \define('_\SPFX', '/$~');
    \define('_\RURP',(function(){
        if(\_\IS_CLI){
            if(!\str_starts_with(($s = $_SERVER['argv'][1] ?? ''),'-')){
                $parsed = \parse_url('/'.\ltrim($s,'/'));
                !empty($parsed['query']) AND \parse_str($parsed['query'], $_GET);
                return $parsed['path'];
            } else {
                return '/';
            }
            \define('_\RSSN','');
        } else {
            $p = \strtok($_SERVER['REQUEST_URI'],'?');
            if((\php_sapi_name() == 'cli-server')){
                $rurp = $p;
            } else if((\str_starts_with($p, $n = $_SERVER['SCRIPT_NAME']))){
                $rurp = \substr($p,\strlen($n));
            } else if((($d = \dirname($n = $_SERVER['SCRIPT_NAME'])) == DIRECTORY_SEPARATOR)){
                $rurp = $p;
            } else {
                $rurp = \substr($p, \strlen($d));
            }
            
            if(\str_starts_with($rurp, \_\SPFX)){
                $token = \trim(\strtok($rurp,'/'), \_\SPFX);
                $rurp = '/'.\strtok('');
                $_SERVER['FW__INTFC'] = 'frame';
            } else {
                $token = '';
            }
            \define('_\RSSN',$token);
            return $rurp;
        }
    })());
    
    $x = \get_include_path();
    0 AND \set_include_path($x = \_\START_DIR.PATH_SEPARATOR.\get_include_path());
    0 AND \spl_autoload_extensions("-#{$intfc}.php,/-#{$intfc}.php,-#.php,/-#.php");
    0 AND \spl_autoload_register();

    try {
        function i(){ static $I; return $I ?? $I = \_\i::_();  }
        return i()->nav->route();
        
    } finally {
        if(0){
            \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', -1);
            \header('Content-Type: application/json');
            echo \json_encode([
                '$_ENV' => $_ENV,
                'nav' => (array) i()->nav,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); 
            exit();
        }
    } 
    
})(); }

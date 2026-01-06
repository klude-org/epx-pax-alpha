<?php 
#####################################################################################################################
#region
    /* 
                                               EPX-PAX-START
    PROVIDER : KLUDE PTY LTD
    PACKAGE  : EPX-PAX
    AUTHOR   : BRIAN PINTO
    RELEASED : 2026-01-06
    
    */
#endregion
# ###################################################################################################################
# i'd like to be a tree - pilu (._.) // please keep this line in all versions - BP
namespace { return include (function(){
    \defined('_\MSTART') OR \define('_\MSTART', \microtime(true));
    $_SERVER['_']['PLEX_DIR'] = \strtr(__DIR__,'\\','/');
    $_SERVER['_']['SITE_DIR'] = \strtr($_SERVER['FW__SITE_DIR'] ?? (empty($_SERVER['HTTP_HOST'])
        ? realpath($_SERVER['FW__SITE_DIR'] ?? \getcwd())
        : realpath(\dirname($_SERVER['SCRIPT_FILENAME']))
    ),'\\','/');
    if(\is_file($f = "{$_SERVER['_']['SITE_DIR']}/.local-start.php")){
        return $f;
    } else if(\is_file($f = "{$_SERVER['_']['PLEX_DIR']}/.local/vnd/epx.php")){
        return $f;
    } else if($c = \file_get_contents("https://raw.githubusercontent.com/klude-org/epx-pax-alpha/main/--epx/lib/vnd/epx.php")){
        \is_dir($d = \dirname($f)) OR \mkdir($d, 0777, true);
        \file_put_contents($f, $c);
        return $f;
    } else {
        throw new \Exception("Failed: Unable to locate or install epx.php");
    }
})();}


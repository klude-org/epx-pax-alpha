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
namespace { return \is_callable($c = (function(){
    \defined('_\MSTART') OR \define('_\MSTART', \microtime(true));
    \set_error_handler(function($severity, $message, $file, $line){
        throw new \ErrorException(
            $message, 
            0,
            $severity, 
            $file, 
            $line
        );
    });
    \set_exception_handler(function($ex){
        switch($_SERVER['_']['INTFC'] ?? 'web'){
            case 'cli':{
                echo "\033[91m\n"
                    .$ex::class.": {$ex->getMessage()}\n"
                    ."File: {$ex->getFile()}\n"
                    ."Line: {$ex->getLine()}\n"
                    ."\033[31m{$ex}\033[0m\n"
                ;
                exit(1);
            } break;
            case 'web':{
                \http_response_code(500);
                while(\ob_get_level() > 0){ @\ob_end_clean(); }
                \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', -1);
                if (preg_match('#^application/json\b#i', $_SERVER['CONTENT_TYPE'] ?? '')) {
                    // It's JSON
                    \header('Content-Type: application/json');
                    echo \json_encode([
                        'status' => "error",
                        'message' => $ex->getMessage(),
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); 
                } else {
                    echo <<<HTML
                        <style>
                            body{ background-color: #121212; color: #e0e0e0; font-family: sans-serif; margin: 0; padding: 20px;}
                            pre{ overflow:auto; color:red;border:1px solid red;padding:5px; background-color: #1e1e1e; max-height: calc(100vh-25px); }
                            /* Scrollbar styles for WebKit (Chrome, Edge, Safari) */
                            ::-webkit-scrollbar { width: 12px; height: 12px;}
                            ::-webkit-scrollbar-track { background: #1e1e1e; }
                            ::-webkit-scrollbar-thumb { background-color: #555; border-radius: 6px; border: 2px solid #1e1e1e; }
                            ::-webkit-scrollbar-thumb:hover { background-color: #777; }
                            /* Firefox scrollbar (limited support) */
                            * { scrollbar-width: thin; scrollbar-color: #555 #1e1e1e;}
                        </style>
                        <pre>{$ex}</pre>
                        HTML;
                }
                exit(1);
            } break;
            default:{
                \http_response_code(500);
                while(\ob_get_level() > 0){ @\ob_end_clean(); }
                \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', -1);
                \header('Content-Type: application/json');
                echo \json_encode([
                    'status' => "error",
                    'message' => $ex->getMessage(),
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); 
                exit(1);
            } break;
        }        
    });
    
    $_SERVER['_']['IS_CLI'] = empty($_SERVER['HTTP_HOST']);
    $_SERVER['_']['INTFC'] = $intfc = $_SERVER['FW__INTFC']
        ?? ($_SERVER['_']['IS_CLI'] 
            ? 'cli'
            : $_SERVER['HTTP_X_REQUEST_INTERFACE'] ?? 'web'
        )
    ;
    $_SERVER['_']['MPLEX_DIR'] = \strtr(__DIR__,'\\','/');
    $_SERVER['_']['SITE_DIR'] = \strtr($_SERVER['FW__SITE_DIR'] ?? (empty($_SERVER['HTTP_HOST'])
        ? realpath($_SERVER['FW__SITE_DIR'] ?? \getcwd())
        : realpath(\dirname($_SERVER['SCRIPT_FILENAME']))
    ),'\\','/');
    $_SERVER['_']['SPLEX_DIR'] = (\basname($_SERVER['_']['SITE_DIR']) == '--epx') 
        ? $_SERVER['_']['SITE_DIR']
        : "{$_SERVER['_']['SITE_DIR']}/--epx"
    ;
    if(\is_file($f = "{$_SERVER['_']['SITE_DIR']}/.local-start.php")){
        return include $f;
    }
    
    global $_;
    (isset($_) && \is_array($_)) OR $_ = [];
    function o(){ static $I; return $I ?? ($I = \epx::_()); }
    $_['ALT'][\epx::class] = fn($n) => \class_alias(\epx\std\origin::class, $n);
    $_SERVER['_']['OB_OUT'] = \ob_get_level();
    $_SERVER['_']['IS_CLI'] OR \ob_start();
    $_SERVER['_']['OB_TOP'] = \ob_get_level();
    $_SERVER['_']['PHP_TSP_DEFAULTS'] = [
        'handler' => 'spl_autoload',
        'extensions' => \spl_autoload_extensions(),
        'path' =>  \get_include_path(),
    ];
    $_SERVER['_']['PVND_DIR'] = "{$_SERVER['_']['MPLEX_DIR']}/.local/vnd";
    \spl_autoload_extensions("-#{$intfc}.php,/-#{$intfc}.php,-#.php,/-#.php");
    \spl_autoload_register();
    \set_include_path($_SERVER['_']['PVND_DIR'].PATH_SEPARATOR.get_include_path());
    \spl_autoload_register(function($n){
        global $_;
        if(\is_callable($alt = $_['ALT'][$n] ?? null)){
            ($alt)($n);
        } else if(\preg_match(
            "#^epx(__(?<w_repo>.*?)__(?<w_owner>[^/]+))?#",
            $p = \strtr($n,'\\','/'),
            $m
        )){
            if(!\is_file($f_path = "{$_SERVER['_']['PVND_DIR']}/{$p}/-#.php")){
                $w_owner = (\strtr($m['w_owner'] ?? '','_','-') ?: ($_['ghalt_owner'] ?? 'klude-org'));
                $w_repo = "epx-".(\strtr($m['w_repo'] ?? '','_','-') ?: ($_['ghalt_repo'] ?? 'pax-alpha'));
                $w_ref = $_['ghalt_ref'] ?? 'main';
                $w_url = "https://raw.githubusercontent.com/{$w_owner}/{$w_repo}/{$w_ref}/--epx/lib/vnd";
                $api_token = \is_file($f = "{$_SERVER['_']['MPLEX_DIR']}/.keys-$.php") 
                    ? (($x = include $f)["{$w_owner}/{$w_repo}"] ?? $x[$w_owner] ?? null)
                    : null
                ;
                $args = $api_token
                    ? [
                        false,
                        \stream_context_create([
                            "http" => [
                                "method" => "GET",
                                "header" => "Authorization: Bearer {$api_token}\r\n"
                            ]
                        ])
                    ] 
                    : []
                ;                
                \set_error_handler(fn() => true);
                $contents = \file_get_contents("{$w_url}/{$p}/".($j = \urlencode('-#.php')), ...$args)
                    ?: \file_get_contents("{$w_url}/{$p}{$j}", ...$args)
                ;
                \restore_error_handler();
                //$x = $http_response_header;
                if(!$contents){
                    throw new \Exception("Failed: Unable to download type '{$n}'");
                }
                \is_dir($d = \dirname($f_path)) OR @mkdir($d,0777,true);
                \file_put_contents($f_path, $contents);
            }
            include $f_path;
        }
    },true,false);
    if(\basename($_SERVER['_']['SITE_DIR']) == '--epx'){
        \epx\std\module::_('plex_admin_v1.app:github/klude-org/epx-pax-alpha/main')->include();
        $_SERVER['_']['ENV_SOURCE'] = 3;
    } else if(\is_file($f = "{$_SERVER['_']['SPLEX_DIR']}/.local-env-{$intfc}.php")){ 
        include $f;
        $_SERVER['_']['ENV_SOURCE'] = 1;
    } else {
        \is_file($f = "{$_SERVER['_']['SPLEX_DIR']}/.config.php") AND include $f;
        \is_file($f = "{$_SERVER['_']['SPLEX_DIR']}/.config-{$intfc}.php") AND include $f;
        $_SERVER['_']['ENV_SOURCE'] = 2;
    }
    \set_include_path($_SERVER['_']['TSP_PATH'] ?? ($_SERVER['_']['TSP_PATH'] = \implode(
        PATH_SEPARATOR, 
        \array_keys(\array_filter($_SERVER['_']['TSP_LIST'] ?? $_SERVER['_']['TSP_LIST'] = (\iterator_to_array((function(){
            foreach(['app' => true, ...($GLOBALS['_']['MODULES'] ?? [])] as $path => $v){
                if(($path[0]??'')=='/' || ($path[1]??'')==':'){
                    if(\is_dir($path)){
                        yield \strtr($path, '\\','/') => $v;
                    }
                } else if($d = \glob($r[] = "{$_SERVER['_']['SPLEX_DIR']}/*/{$path}",GLOB_ONLYDIR)[0] ?? null){
                    yield \strtr($d, '\\','/') => $v; 
                } else if($d = \glob($r[] = "{$_SERVER['_']['MPLEX_DIR']}/*/{$path}",GLOB_ONLYDIR)[0] ?? null){
                    yield \strtr($d, '\\','/') => $v;
                }
            }
            yield $_SERVER['_']['PVND_DIR'] => true;
            foreach(\explode(PATH_SEPARATOR, $_SERVER['_']['PHP_TSP_DEFAULTS']['path']) as $dir){
                if(\is_dir($dir)){
                    yield \str_replace('\\','/', $dir) => 1;
                }
            }
        })())))))
    ));
    if(!($GLOBALS['AUTOLAUNCH'] ?? true) || ($GLOBALS['IS_STANDALONE'] ?? null)){
        return;
    } else if($f = \stream_resolve_include_path('.start.php')){
        return include $f;
    }
    
})()) ? $c() : $c; }


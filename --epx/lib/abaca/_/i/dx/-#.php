<?php namespace _\i;

class dx {
 
    use \_\i\singleton__t;
    
    public function __construct(){
        \set_error_handler(function($severity, $message, $file, $line){
            if(($x = $_SERVER['FW__ON_ERROR'] ?? 'throw') == 'throw'){
                throw new \ErrorException(
                    $message, 
                    0,
                    $severity, 
                    $file, 
                    $line
                );
            } else if($x == 'fault'){
                try{
                    throw new \ErrorException(
                        $message, 
                        0,
                        $severity, 
                        $file, 
                        $line
                    );
                } catch(\Throwable $ex) {
                    $this->on_fault($ex);
                }
            }
        });
        \set_exception_handler(function($ex){
            $this->on_fault($ex);
        });
        \register_shutdown_function(function() { 
            try {
                if(\defined('_\SIG_END')){
                    throw new \Exception("Invalid SIG_END setting or Duplicate call to Root Finalizer");
                } else {
                    \define('_\SIG_END', \microtime(true));
                };
                
                if($error = \error_get_last()){ 
                    \error_clear_last();
                    try {
                        throw new \ErrorException(
                            $error['message'], 
                            0,
                            $error["type"], 
                            $error["file"], 
                            $error["line"]
                        );
                    } catch(\Throwable $ex) {
                        $this->on_fault($ex);
                    }
                }
                
                $exit = null;
                // response is used only if loaded
                if (\is_null($response = $GLOBALS['--RESPONSE'] ?? null)) {
                    $exit = null;
                } else if($response instanceof \SplFileInfo){
                    $exit = (object)[];
                    if(\is_file($file = $response)){
                        $mime_type = match($ext = \strtolower(\pathinfo($file, PATHINFO_EXTENSION))){
                            'html' => null,
                            'css'  => 'text/css',
                            'js'   => 'application/javascript',
                            'json' => 'application/json',
                            'jpg'  => 'image/jpeg',
                            'png'  => 'image/png',
                            'gif'  => 'image/gif',
                            'html' => 'text/html',
                            'txt'  => 'text/plain',
                            default => \mime_content_type((string) $file) ?: 'application/octet-stream',
                        };
                        if(empty($mime_type)) {
                            $exit->code = 404;
                            $exit->content = '404: Not Found: Unknown Mime Type';
                        } else {
                            // Set appropriate headers
                            $exit->headers[] = 'Content-Type: ' . $mime_type;
                            $exit->headers[] = 'Cache-Control: public, max-age=86400'; // Cache for 1 day
                            $exit->headers[] = 'Expires: ' . \gmdate('D, d M Y H:i:s', \time() + 86400) . ' GMT'; // 1 day in the future
                            $exit->headers[] = 'Last-Modified: ' . \gmdate('D, d M Y H:i:s', \filemtime($file)) . ' GMT';
                            // Check for If-Modified-Since header
                            if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
                                \strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= \filemtime($file)) {
                                $exit->code = 304; // Not Modified
                                $exit->content = null;
                            } else {
                                // Output the file content
                                $exit->content = new \SplFileInfo((string) $file);
                            }
                        }
                    } else {
                        $exit->code = 404;
                        $exit->content = '404: Not Found';
                    }
                } else if(\is_scalar($response)){
                    $exit = (object)[
                        'content' => $response,
                    ];
                } else if($response instanceof \Throwable) {
                    $this->on_fault($response, "Fault [E0.4]");
                } else if(\is_array($response)) {
                    $exit = (object)[
                        'headers' => [
                            'Content-Type: application/json'
                        ],
                        'content' => $response,
                    ];
                } else if(\is_object($response)) {
                    $exit = $response;
                } else {
                    $exit = null;
                }
                
            } catch (\Throwable $ex) {
                $this->on_fault($response, "Fault [E0.5]");
            }
            
            \define('_\SIG_EXIT', \microtime(true));
            
            if($exit){
                try {
                    if(\_\IS_CLI){
                        if(\is_null($content = $exit->content ?? null)){ 
                            return; 
                        } else if($content instanceof \SplFileInfo){
                            echo $content;
                        } else if(\is_scalar($content)) {
                            echo $content;
                        } else {
                            echo \json_encode($content ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                        }
                        if(\is_numeric($code = $exit->code ?? null) &&  $code >= 400){
                            \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 1);
                        }
                    } else {
                        while(\ob_get_level() > 0){ @\ob_end_clean(); }
                        if(\is_numeric($code = $exit->code ?? null)){
                            \http_response_code($code ?: 200);
                        } 
                        if(\is_array($exit->headers ?? null)){
                            foreach($exit->headers ?? [] as $k => $v){
                                if(\is_string($v)){
                                    if(\is_numeric($k)){
                                        \header($v);    
                                    } else {
                                        \header("{$k}: {$v}");
                                    }
                                }
                            }
                        }
                        if(\is_null($content = $exit->content ?? null)){ 
                            return; 
                        } else if($content instanceof \SplFileInfo){
                            \readfile($content);
                        } else if(\is_scalar($content)) {
                            echo $content;
                        } else {
                            \header('Content-Type: application/json');
                            echo \json_encode($content ?? [],JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                        }
                        if(\is_numeric($code) && $code >= 400){
                            \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 1);
                        }
                    }
                } catch (\Throwable $ex) {
                    ($this->fn->report_fault)($ex);
                }
            }
            
            if(i()->request->IS_HTML){
                $data = $this->data();
                $data = \json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                echo PHP_EOL."<script> const DX_DETAILS = {$data}; console.log(DX_DETAILS);</script>"; 
            }
        });         
        
    }
    
    
    public function trap(){
        $data = $this->data();
        \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', -1);
        \header('Content-Type: application/json');
        echo \json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); 
        exit();
    }

    
    public static function filter__path($path){
        return \str_replace('\\','/', $path);
    }
    
    public static function filter__types($types){
        $types_out = [];
        foreach ($types as $type) {
            $reflect = new \ReflectionClass($type);
            // Only user defined classes, exclude internal or classes added by PHP extensions.
            if(!$reflect->isInternal() ){
                $types_out[$type] = true;
            }
        }
        return \array_keys($types_out);
    }
    
    public static function filter__output($var){
        //* visual filter
        static $filter = null;
        $filter OR ($filter = function($v) use(&$filter) {
            if(!$v){
                return $v;
            } else if(\is_string($v)){
                return \htmlspecialchars(\str_replace("\n","<br>",$v));
            } else if(\is_scalar($v)){
                return $v;
            } else if(\is_array($v)){
                return array_map($filter, (array) $v);
            } else if($v instanceof \Throwable){
                $resp = (object)[];
                $resp->caption = "Encountered {$resp->class}".\get_class($v);
                $resp->message = $v->getMessage();
                $resp->trace = \explode("\n", $v->getTraceAsString());
                $resp->details = $params;
                return $resp;
            } else if(\is_object($v)){
                if(\method_exists($v, 'i__4dump')){
                    return $v->dump__i();
                } else if($v instanceof \stdClass){
                    return \array_map($filter, (array) $v);
                } else {
                    //! avoid recursion
                    return 'OBJECT: '.\get_class($v);
                }
            } else {
                return '??';
            }
        });
        if(\is_scalar($var)){
            return $var;
        } else {
            return \array_map($filter, $var);
        }
    }      
    
    public static function data(){
        i()->env->_;
        i()->request->_;
        i()->server->_;
        return [
            'nav' => (array) i()->nav,
            'env' => $_ENV,
            'server' => (function(){
                $list = [];
                foreach($_SERVER['_'] ?? [] as $k => $v){
                    if($k === 'TSP_PATH'){
                        $list[$k] = \explode(PATH_SEPARATOR, $v);
                    } else {
                        $list[$k] = $v;
                    }
                }
                return $list;
            })(),
            'request' => (array) i()->request,
            'headers' => (function(){
                $list = [];
                foreach(i()->request->headers as $k => $v){
                    if($k === 'Cookie'){
                        //$list[$k] = \explode(' ', $v);
                    } else {
                        $list[$k] = $v;
                    }
                }
                return $list;
            })(),
            'constants' => \get_defined_constants(true)['user'],
            'at' =>  \number_format(((\microtime(true) - \_\MSTART)), 6).'s',
            'tsp' => \explode(PATH_SEPARATOR, \get_include_path()),
            'constants' => \get_defined_constants(true)['user'],
            '_' => \json_decode(\json_encode($GLOBALS['_'] ?? [])),
            'debug' => \json_decode(\json_encode($GLOBALS['DEBUG'] ?? [])),
            '_env' => $_ENV,
            '_server' => (function(){
                $list = [];
                foreach($_SERVER as $k => $v){
                    if($k === '_'){
                        
                    } else if($k === 'HTTP_COOKIE'){
                        //$list[$k] = \explode(' ', $v);
                    } else if($k === 'PATH'){
                        $list[$k] = \explode(PATH_SEPARATOR, $v);
                    } else {
                        $list[$k] = $v;
                    }
                }
                return $list;
            })(),
            '_get' => $_GET,
            '_post' => $_POST,
            '_files' => $_FILES,
            '_cookie' => $_COOKIE,
            'included' => \array_map("_\i\dx::filter__path", \get_included_files()),
            'classes' => static::filter__types(\get_declared_classes()),
            'interfaces' => static::filter__types(\get_declared_interfaces()),
            'traits' => static::filter__types(\get_declared_interfaces()),
            'profile' => [
                'time' => [
                    'elapsed' => number_format(((microtime(true) - \_\MSTART)),6).'s',
                ],
                'mem' => [
                    'limit' => \ini_get('memory_limit'),
                    'start' => (\defined('_\DX_STATS')) ? \number_format(\_\DX_STATS['MUSAGE'] / (1024 * 1024), 2).'MB' : null,
                    'usage' => \number_format(\memory_get_usage(true) / (1024 * 1024), 2).'MB',
                    'mpeak' => \number_format(\memory_get_peak_usage(true) / (1024 * 1024), 2).'MB',
                ],
                'rusage' => [
                    'start' => (\defined('_\DX_STATS'))? \_\DX_STATS['RUSAGE'] : null,
                    'usage' => \getrusage(),
                ],
                'trace' => \array_filter(
                    \preg_split(
                        "/<br>|\n/",
                        \implode('<br>',$GLOBALS['_']['FW_OUTPUT']['TRACE'] ?? [])
                    )
                ),
            ]
        ];
    }
    
    
    public function on_fault($ex = null){
        if($ex instanceof \_\i\fault\exception){
            echo (string) $ex;
        } else {
            switch(\_\INTFC){
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
        }    
    }
    
    public function report(string|array|callable|\Throwable $k = null,...$v){
        static $dx; $dx ?? $dx = (function(){ 
            $dx = (object)[];
            $dx->FAULTS = [];
            $dx->options= [];
            return $dx;
        })();
        if(\is_null($k)){
            return (array) $dx;
        } else if($k instanceof \Throwable){
            $dx->FAULTS[\date('Y-md-Hi-s').'/'.\uniqid()] = $ex = $k;
            switch(\_\INTFC){
                case 'cli':{
                    echo "\033[91m\n"
                        .$ex::class.": {$ex->getMessage()}\n"
                        ."File: {$ex->getFile()}\n"
                        ."Line: {$ex->getLine()}\n"
                        ."\033[31m{$ex}\033[0m\n"
                    ;
                } break;
                case 'web':{
                    \http_response_code(500);
                    while(\ob_get_level() > 0){ @\ob_end_clean(); }
                    \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', -1);
                    exit(<<<HTML
                        <pre style="overflow:auto; color:red;border:1px solid red;padding:5px;"><br>{$ex}</pre>
                    HTML);
                } break;
                default:{
                    \http_response_code(500);
                    while(\ob_get_level() > 0){ @\ob_end_clean(); }
                    \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', -1);
                    \header('Content-Type: application/json');
                    exit(\json_encode([
                        'status' => "error",
                        'message' => $ex->getMessage(),
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                } break;
            }
        } else if(\is_string($k)) {
            if($v){
                if(\is_null($v[0])){
                    unset($dx->options[$k]);
                } else {
                    $dx->options[$k] = $v[0];
                }
            } else {
                return $dx->$k ?? $dx->options[$k] ?? null;
            }  
        } else if(\is_callable($value[0])) {
            try{
                \error_clear_last();
                $dx->options['error_mask'] = true;
                return ($value[0])();
            } finally { 
                $dx->options['error_mask'] = false;
                \error_clear_last();
            }
        } else if(\is_array($k)) {
            $dx->options = \array_replace($dx->options, $k);
        }
    }
    
}
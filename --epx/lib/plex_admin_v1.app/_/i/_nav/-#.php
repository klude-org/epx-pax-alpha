<?php namespace _\i; 

final class _nav extends \_\i\feature\solo {

    const SPFX = '/$~';
    
    protected function i__construct(){ }
    
    public function route_abort($code, $message){
        if($this->IS_CLI){
            return function() use($message){
                echo "\033[91m{$message}\033[0m\n";
            };
        } else {
            return function() use($code, $message){
                \http_response_code($code);
                echo $message;
            };
        }
    }
        
    public function route(){
        $this->IS_CLI = $_SERVER['_']['IS_CLI'];
        $this->INTFC = $_SERVER['_']['INTFC'];
        $this->HANDLER = $_SERVER['_']['INTFC'];
        $this->RURP = (function(){
            if($this->IS_CLI){
                if(!\str_starts_with(($s = $_SERVER['argv'][1] ?? ''),'-')){
                    $parsed = \parse_url('/'.\ltrim($s,'/'));
                    !empty($parsed['query']) AND \parse_str($parsed['query'], $_GET);
                    return $parsed['path'];
                } else {
                    return '/';
                }
                $this->RSSN ='';
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
                
                if(\str_starts_with($rurp, static::SPFX)){
                    $token = \trim(\strtok($rurp,'/'), static::SPFX);
                    $rurp = '/'.\strtok('');
                    $this->HANDLER = 'frame';
                } else {
                    $token = '';
                }
                $this->RSSN = $token;
                return $rurp;
            }
        })();
        i()->session?->authenticate();
        $this->SESSION_ID = \session_id();
        $this->ASSET = $_GET['--asset'] ?? null;
        if(!\preg_match(
            "#^/"
                ."(?:"
                    ."(?<NPATH>.*)"
                .")?"
            . "$#",
            $this->RURP,
            $m
        )){
            return $this->route_abort(404, "404: Not Found: Invalid request path format");
        } else {
            foreach(\array_filter($m, fn($k) => !is_numeric($k), \ARRAY_FILTER_USE_KEY) as $k => $v){
                $this->$k = $v;
            }
        }
        $this->NPATH = \trim($this->NPATH, '/');
        $this->IS_ASSET = \preg_match('#(-pub|-asset-app|-asset-pvt|-asset)[/\.\-]#', $this->NPATH); 
        $this->CTLR_FILE = (function(){
            $resolve_extensions = $this->IS_ASSET 
                ? null 
                : ["-@{$this->HANDLER}.php", "-@.php", "-@.html"]
            ;        
            $resolve_cfile__fn = function($cpath, $sfx = null){
                try{
                    $r = [];
                    $suffixes = \is_array($sfx) ? $sfx : [$sfx];
                    $path = \strtr($cpath,'\\', '/',);
                    foreach($suffixes as $suffix){
                        if($f = (($suffix)
                            ? \stream_resolve_include_path($r[] = "{$path}/{$suffix}") 
                                ?: (\stream_resolve_include_path($r[] = "{$path}{$suffix}")
                            )
                            : \stream_resolve_include_path($r[] = "{$path}")
                        )){
                            return new \SplFileInfo($f);
                        }
                    }
                } finally {
                    $this->SEARCHED = \array_merge($this->SEARCHED ?? [], $r);
                }
            };
            return ($this->IS_ASSET ?: '')
                ? ($resolve_cfile__fn)(
                    \trim("__/".$this->NPATH,'/'),
                )
                : ($resolve_cfile__fn)(
                    \trim("__/".$this->NPATH,'/'),
                    $resolve_extensions,
                )
            ;
        })();
        
        if(!($file = $this->CTLR_FILE)){
            return $this->route_abort(404, '404: Not Found');
        }
        
        if($this->ASSET){
            $INIT__EN = false;
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
                return $this->route_abort(404, '404: Not Found: Unknown Mime Type');
            } else {
                if($this->IS_CLI){
                    return function() use($file){
                        echo "\n{$file}";    
                    };
                } else {
                    // Set appropriate headers
                    $exit = (object)[];
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
                        $exit->content = $file;
                    }
                    return function() use($exit){
                        while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                        if(\is_numeric($code = $exit->code ?? null)){
                            \http_response_code($code ?: 200);
                        } 
                        foreach($exit->headers ?? [] as $k => $v){
                            if(\is_string($v)){
                                if(\is_numeric($k)){
                                    \header($v);
                                } else {
                                    \header("{$k}: {$v}");
                                }
                            }
                        }
                        if(\is_null($content = $exit->content ?? null)){ 
                            return; 
                        } else if($content instanceof \SplFileInfo){
                            \readfile($content);
                        }
                    };
                }
            }
        }
        
        try {
            $INIT__EN = true;
            $__CONTEXT__ = i();
            $__NAV__ = $this;
            $abort__fn = fn(...$args) => $this->route_abort(...$args);
            return (function() use($abort__fn, $__NAV__){
                if($__NAV__->control ?? null){
                    if(\is_object($o = (include $__NAV__->CTLR_FILE))){
                        if(\method_exists($o, $c = 'ctl__'.$__NAV__->control)){
                            $o->$c();
                        } else {
                            ($abort__fn)(404, '404: Not Found: Missing control');
                        }
                    }
                } else {
                    if(\is_callable($o = (include $__NAV__->CTLR_FILE))){
                        ($o)();
                    }
                }
            })->bindTo($__CONTEXT__, $__CONTEXT__::class);
        } finally {
            
            \define('_\SIG_START', \microtime(true)); //always master_start!
            //echo \json_encode($req, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;
            if($INIT__EN ?? false){

                i()->request;
                i()->server;
                i()->env;
                
                $include__fn = function($f){ include $f; };
                $tsp_list = \explode(PATH_SEPARATOR, \get_include_path());
                foreach(\array_reverse($tsp_list) as $d){
                    if(\is_file($f = "{$d}/.module.php")){
                        $include__fn($f);
                    }
                }
                
                foreach($tsp_list as $d){
                    if(\is_file($f = "{$d}/.functions-{$this->INTFC}.php")){
                        $include__fn($f);
                    }
                    if(\is_file($f = "{$d}/.functions.php")){
                        $include__fn($f);
                    }
                }
                
                ($f = \stream_resolve_include_path('.dx.php')) AND include $f;
            }
        }
    }      
}
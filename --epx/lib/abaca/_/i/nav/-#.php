<?php namespace _\i;

class nav extends \stdClass {
    
    use \_\i\singleton__t;
    
    const SPFX = '/$~';
    
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
    
    public function route_abort_404 () {
        \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 0);
        $debug = ((($_REQUEST['--trap'] ?? '') == 'not-found')
            ? \json_encode(
                [
                    'error' => "404 Not Found: {$this->RURP}",
                    'info' => $_SERVER['.'], 
                ],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )
            : ''
        );
        if($_SERVER['_']['IS_CLI']){
            echo "\e[31m404 Not Found: \e[91m{$this->RURP}\e[0m\n";
            echo $debug;
        } else {
            \http_response_code(404);
            while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
            if($f = \stream_resolve_include_path('_/views/html_error_404-v.php')){
                include $f;
            } else if($debug){
                \header('Content-Type: application/json');
                echo $debug;
            } else {
                echo "404 Not Found: {$this->RURP}";
            }
        }
        exit(404);
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
                    ."(?:"
                        ."(?<FACET>"
                            ."(?<PORTAL>(?:__|--)[^/\.]*)"
                            ."(?:\.(?<ROLE>[^/]*))?"
                        .")/?"
                    .")?"
                    ."(?<NPATH>"
                        ."((?<ENTITY>[a-zA-Z_0-9]*)(?:/(?<ISEG>@(?<ID>[^~/]*))?)?(?<SPATH>[^~]*))?"
                    .")"
                    ."(?:\~(?<CONTROL>[a-zA-Z_][\w]*))?"
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
        $this->SPATH = \trim($this->SPATH, '/');
        $this->PANEL = \trim(\str_replace('-','_', $this->PORTAL ?? null ?: '__'),'/');
        $this->ENTITY = \trim($this->ENTITY, '/');
        $this->ELIST = \iterator_to_array((function(){
            if($this->ENTITY){
                if(\class_exists($class = \str_replace('/','\\', $epath = "_/{$this->ENTITY}"))){
                    yield "{$epath}/{$this->PANEL}";
                    foreach(\class_parents($class) as $v){
                        yield  \str_replace('\\','/', "{$v}/{$this->PANEL}");
                    }
                }
                yield "{$this->PANEL}/{$this->ENTITY}";
            } else {
                yield $this->PANEL;
            }
        })());        
        $this->CPATH = ($this->ISEG 
            ? \implode('/',\array_map(fn($k) => \trim($k,'/'), \array_filter([
                '@',
                $this->SPATH, 
                ($_GET['--asset'] ?? null),
            ])))
            : \implode('/',\array_map(fn($k) => \trim($k,'/'), \array_filter([
                $this->SPATH,
                ($_GET['--asset'] ?? null)
            ])))
        );
        $this->DPATH = \implode('/',\array_map(fn($k) => \trim($k,'/'), \array_filter([
            $this->NPATH,
            ($_GET['--asset'] ?? null)
        ])));
        $this->IS_ASSET = (\preg_match('#(-pub|-asset-app|-asset-pvt|-asset)[/\.\-]#', $this->CPATH, $m)) 
            ? match($m[1]){
                default => null,
                '-pub' => 'public',
                '-asset' => 'public',
                '-asset-pvt' => (($_SERVER['_']['IS_CLI'] || (
                    !empty($this->ISEG)  
                    && !empty($_SERVER['HTTP_REFERER'])
                    && \str_contains($_SERVER['HTTP_REFERER'], "{$this->ENTITY}/{$this->ISEG}")
                )) ? 'private' : 'private-not-allowed'),
                '-asset-app' => 'app',
            }
            : false
        ;
        $this->FPATH = \implode('/',\array_map(fn($k) => \trim($k,'/'), \array_filter([
            $this->PANEL,
            $this->NPATH,
            $this->ASSET,
        ])));
        $this->CTLR_FILE = (function(){
            $resolve_extensions = $this->IS_ASSET 
                ? null 
                : ["-@{$this->HANDLER}.php", "-@.php", "-@.html"]
            ;        
            $resolve_dfile__fn = function($dpath){
                if(\is_file($f = $this->SEARCHED[] = \_\DATA_DIR."/_/com/{$dpath}")){
                    return new \SplFileInfo($f);
                }
            };
            $resolve_cfile__fn = function($elist, $cpath, $sfx = null){
                try{
                    $r = [];
                    $suffixes = \is_array($sfx) ? $sfx : [$sfx];
                    foreach($elist as $j){
                        $path = \str_replace('\\', '/', \trim("{$j}/{$cpath}",'/'));
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
                    }
                } finally {
                    $this->SEARCHED = \array_merge($this->SEARCHED ?? [], $r);
                }
            };
            switch($this->IS_ASSET ?: ''){
                case 'private-not-allowed':{
                    return '';
                } break;
                case 'private':
                case 'app':{
                    if($_SERVER['_']['IS_CLI'] || ($_SESSION['--AUTH']['en'] ?? false)){
                        return ($resolve_dfile__fn)($this->DPATH) 
                        ?: ($resolve_cfile__fn)(
                            $this->ELIST,
                            $this->CPATH,
                        );
                    } else {
                        return '';
                    }
                } break;
                case 'public':{
                    return ($resolve_dfile__fn)($this->DPATH) 
                    ?: ($resolve_cfile__fn)(
                        $this->ELIST,
                        $this->CPATH,
                    );
                } break; 
                case '':{
                    return ($resolve_cfile__fn)(
                        $this->ELIST,
                        $this->CPATH,
                        $resolve_extensions,
                    );
                } break;
                default: {
                    throw new \Exception('Unanticipated Error');
                }
            }            
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
            $__CONTEXT__ = ($this->{$this->ENTITY}->panel) ?? $this;
            $__REQ__ = $this;
            $abort__fn = fn(...$args) => $this->route_abort(...$args);
            return (function() use($abort__fn, $__REQ__){
                if($__REQ__->control ?? null){
                    if(\is_object($o = (include $__REQ__->ctlr_file))){
                        if(\method_exists($o, $c = 'ctl__'.$__REQ__->control)){
                            $o->$c();
                        } else {
                            ($abort__fn)(404, '404: Not Found: Missing control');
                        }
                    }
                } else {
                    if(\is_callable($o = (include $__REQ__->ctlr_file))){
                        ($o)();
                    }
                }
            })->bindTo($__CONTEXT__, $__CONTEXT__::class);
        } finally {
            
            \define('_\SIG_START', \microtime(true)); //always master_start!
            //echo \json_encode($req, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;
            if($INIT__EN ?? false){

                i()->request->_;
                i()->server->_;
                i()->env->_;
                
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
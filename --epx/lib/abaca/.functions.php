<?php
namespace {

    \define('_\REGEX_CLASS_FQN', '/^(([a-zA-Z_\\x80-\\xff][\\\\a-zA-Z0-9_\\x80-\\xff]*)\\\\)?([a-zA-Z_\\x80-\\xff][a-zA-Z0-9_\\x80-\\xff]*)$/');
    \define('_\REGEX_CLASS_QN', '/^[a-zA-Z_\\x80-\\xff][a-zA-Z0-9_\\x80-\\xff]*$/');
    //\define('NL', \_\IS_HTML ? '<br>' : "\n");
    
}
# ######################################################################################################################
#region Core
namespace { if(!\function_exists(o::class)){ function o(){ 
    static $I; return $I ?? $I = (\class_exists(\_::class) ? \_::_() : new \stdClass); 
}}}
#endregion
# ######################################################################################################################
#region Path
namespace _ { if(!\function_exists(p::class)){ function p(string $expr, int $levels = 0){
    return \strtr(($levels ? \dirname($expr , $levels) : $expr), '\\','/');
}}}
namespace _ { if(!\function_exists(p__is_rooted::class)){ function p__is_rooted($expr){
    return ($expr[0]??'')=='/' || ($expr[1]??'')==':';
}}}
namespace _ { if(!\function_exists(slashes::class)){ function slashes(string $p, string ...$px) {
    return (\func_num_args() == 1)
        ? \strtr($p,'\\','/')
        : \strtr(\implode('/',\func_get_args()),'\\','/')
    ;
}}}
namespace _ { if(!\function_exists(backslashes::class)){ function backslashes(string $p, string ...$px) {
    return (\func_num_args() == 1)
        ? \strtr($p,'/','\\')
        : \strtr(\implode('\\',\func_get_args()),'/','\\')
    ;
}}}
namespace _ { if(!\function_exists(typename::class)){ function typename(string|object $p, string ...$px) {
    return (\func_num_args() == 1)
        ? (\is_object($p) 
            ? \get_class($p) 
            : \strtr($p,'/','\\')
        )
        : (\is_object($p) 
            ? \strtr(\implode('\\',[\get_class($p), ...$px]),'/','\\') 
            : \strtr(\implode('\\',\func_get_args()),'/','\\')
        )
    ;
}}}
namespace _ { if(!\function_exists(typepath::class)){ function typepath(string|object $p, string ...$px) {
    return (\func_num_args() == 1)
        ? (\is_object($p) 
            ? \get_class($p) 
            : \strtr($p,'\\','/')
        )
        : (\is_object($p) 
            ? \strtr(\implode('/',[\get_class($p), ...$px]),'\\','/') 
            : \strtr(\implode('/',\func_get_args()),'\\','/')
        )
    ;
}}}
#endregion
# ######################################################################################################################
#region Node
namespace _ { if(!\function_exists(node::class)){ function node(string|array $path) {
    $_I = [];
    if(!\array_key_exists($c = \is_array($path) ? \_\typename(...$path) : \strtr($path,'/','\\'), $_I)){
        if(\class_exists($c)){
            if(\method_exists($c,'_')){
                $_I[$c] = $c::_();
            } else {
                $_I[$c] = new $c();
            }
        } else {
            $_I[$c] = null;
        }
    }
    return $_I[$c];
}}}
#endregion
# ######################################################################################################################
#region Misc
namespace _ { if(!\function_exists(get_caller::class)){ function get_caller($offset = 0){
    $backtrace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 2 + $offset);
    // stupid stuff ... name of this function but file is of the previous function //BP
    // Index 0: get_caller - but file point to before this !!!!!!
    // Index 1: my_function
    // Index 2: caller of my_function (this is what we want)
    return $backtrace[1 + $offset] ?? null;
}}}
#endregion
# ######################################################################################################################
#region Misc
namespace _ { if(!\function_exists(path_here::class)){ function path_here(string $path) {
    if($file = \_\get_caller(-1)['file'] ?? null){
        return \dirname($file)."/{$path}";
    }
}}}
#endregion
# ######################################################################################################################
#region Env
namespace _ { if(!\function_exists(e::class)){ function e(string $n){
    static $E = [];
    if(!\array_key_exists($n, $E)){
        $k = "FW_{$n}";
        $E[$n] = 
            $_ENV[$n]
            ?? (\defined($k) ? \constant($k) : null)
            ?? ((($r = \getenv($k)) !== false) ? $r : null)
            ?? $_SERVER[$k]
            ?? $_SERVER["REDIRECT_{$k}"]
            ?? $_SERVER["REDIRECT_REDIRECT_{$k}"]
            ?? null
        ;
    }
    return $E[$n];
}}}
#endregion
# ######################################################################################################################
#region File
namespace _ { if(!\function_exists(fob::class)){ function fob(string $path){
    static $I; $I OR $I = (\class_exists(\_\i\file::class)) ? \_\i\file::class : \SplFileInfo::class;
    return new $I($path);
}}}
namespace _ { if(!\function_exists(t::class)){ function t($expr = null){
    static $fn;
    static $remap = [];
    static $alt = [];
    static $ensure__fn; $ensure__fn OR $ensure__fn = function(string $new, string $extends, array $options = []){
        if($extends && \class_exists($extends)){
            $def = '';
            if(\preg_match("/((.*)\\\)?(\w+)$/", $new, $mx)){
                //* yeah evil-eval: but this is for dynamic use so please excuse me!!!
                $extends = \ltrim($extends,'\\');
                if($mx[2]){
                    eval("namespace {$mx[2]}; final class {$mx[3]} extends \\{$extends} { {$def} }");
                } else {
                    eval("final class {$mx[3]} extends \\{$extends} { {$def} }");
                }
                return true;
            } else {
                //* hey you can't be here!!! not possible!!! 
                throw new \Exception('Invalid Expression');
            }
        } else {
            return false;
        }
    };
    !\is_bool($expr) && \is_null($fn) AND \_\t(true);
    if(\is_string($expr)){
        $p = \str_replace('\\','/', $n);
        $resolve = ((($p[0]??'')=='/' || ($p[1]??'')==':'))
            ? 'realpath'
            : 'stream_resolve_include_path'
        ;
        if($remap){
            foreach($remap as $k => $v){ 
                if(\str_starts_with($p, $k)){
                    $p = $v.(\substr($p,\strlen($k)));
                    break;
                }
            }
        }
        if(!$suffix){
            if($f = $resolve($GLOBALS['_TRACE']['File Resolve'] = $p)){
                return \_\fob($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f));    
            }
        } else {
            foreach(\is_array($suffix) ? $suffix : explode('|', $suffix) as $k => $t){
                $x = ($m = !\is_numeric($k)) ? $k : $t;
                if($f = $resolve($GLOBALS['_TRACE']['File Resolve'] = $p.$x)){
                    if($t){
                        if($a){
                            return [ \_\fob($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f)), $t, $p];
                        } else {
                            return \_\fob($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f));
                        }
                    } else {
                        return \_\fob($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f));
                    }
                }
            }
        }
    } else if(\is_array($expr)){
        foreach($expr as $k => $v){
            $p = \str_replace('\\','/', $k);
            if(\is_string($v)){
                $remap[\str_replace('\\','/', $k)] = \str_replace('\\','/', $v);
                \krsort($remap);
            } else if($v instanceof \closure){
                $alt[$p] = $v;
            } else if(\is_array($v)) {
                if(isset($v['extends'])){
                    $alt[$p] = function() use($k, $v, $ensure__fn){
                        ($ensure__fn)($k, $v['extends'], $v);
                    };
                }
            }
        }
    } else if(\is_bool($expr)){
        if($expr == true && !$fn){ //is false or null or empty
            \spl_autoload_register($fn = function($n) use(&$alt,&$remap, $ensure__fn){
                $p = \str_replace('\\','/', $n);
                if($c = $alt[$p] ?? false){
                    ($c)();
                    return;
                } else if($f = \_\f($p,'#.php')) {
                    include (string) $f;
                    return;
                } else if(
                    \str_starts_with(($panel = \strtok($n,'\\')),'__')
                    && ($table = \strtok('\\'))
                    && ($extn = \strtok(''))
                    && \class_exists($component = "{$panel}\\{$table}")
                ){
                    $x = $component;
                    do{
                        if(($ensure__fn)($n, "{$x}\\{$extn}")){
                            return;
                        }
                    } while($x = \get_parent_class($x));
                }
            },true,false);
        } else if($fn){
            \spl_autoload_unregister($fn);
            $fn = false;
        }
    } else {
        throw new \Exception('Invalid Argument');
    }
}}}
namespace _ { if(!\function_exists(f::class)){ function f(string|array $n, array|string $suffix = ''){
    static $remap = [];
    if(!$n){
    
    } else if(\is_string($n)){
        $p = \str_replace('\\','/', $n);
        $resolve = ((($p[0]??'')=='/' || ($p[1]??'')==':'))
            ? 'realpath'
            : 'stream_resolve_include_path'
        ;
        if(!$suffix){
            if($f = $resolve($GLOBALS['_TRACE']['File Resolve'] = $p)){
                return \_\fob($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f));    
            }
        } else {
            foreach(\is_array($suffix) ? $suffix : explode('|', $suffix) as $k => $t){
                $x = ($a = !\is_numeric($k)) ? $k : $t;
                if(
                    ($f = $resolve($GLOBALS['_TRACE']['File Resolve'] = $p.$x))
                    || ($f = $resolve($GLOBALS['_TRACE']['File Resolve'] = $p.'/'.$x))
                ){
                    if($t){
                        if($a){
                            return [ \_\fob($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f)), $t, $p];
                        } else {
                            return \_\fob($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f));
                        }
                    } else {
                        return \_\fob($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f));
                    }
                }
            }
        }
    } else if(\is_array($n) && \count($p) == 2) {
        if(\is_class($class = \str_replace('/','\\', $n[0]))){
            $path = $n[1];
            do{
                $j = \str_replace('\\','/',$class).($path ? '/' : '');
                if($f = \_\f("{$j}{$path}", $suffix)){
                    return $f;
                }
            } while($class = \get_parent_class($class));
        } else if($v = $remap[$p] ?? null){
            if($f = \_\f($v.(\substr($p,\strlen($k))), $suffix)){
                return $f;    
            }
        } else if($f = \_\f(\implode('/',$n), $suffix)){
            return $f;
        }
    } else if(\is_object($n)) {
        if(isset($n->remap)){
            foreach($n->remap as $k => $v){
                $p = \str_replace('\\','/', $k);
                if($v instanceof \closure){
                    $alt[$p] = $v;
                } else if(\is_string($v)){
                    $remap[\str_replace('\\','/', $k)] = \str_replace('\\','/', $v);
                    \krsort($remap);
                }
            }
        }        
    } else if($n instanceof \SplFileInfo){
        return $n;
    }
}}}
namespace _ { if(!\function_exists(g::class)){ function g(string $p, int $glob_flags = 0, $expr ='{*,.*}', \closure $mapper = null){ 
    if(func_num_args() <= 2){
        /* using gxp would not work on files */
        $p = \_\p($p);
        $list = [];
        foreach(\explode(PATH_SEPARATOR, \get_include_path()) as $d){
            foreach(\glob("{$d}/{$p}", $glob_flags) as $f){
                $list[] = \_\fob($f);
            }
        }
        return $list;
    } else {
        static $level = 0;
        try {
            $level ++;
            $x = [];
            if($level === 1){
                $glob_flags |= (\strpos($expr,'{') !== false) ? GLOB_BRACE : 0;
            }
            $files = \glob("{$p}/{$expr}", GLOB_MARK | $glob_flags);
            foreach($files as $file){
                if($file[-1] !== DIRECTORY_SEPARATOR){
                    $x[($mapper) ? ($mapper)(basename($file),'key') : basename($file)] = ($mapper) ? ($mapper)($file,'val') : $file;
                }
            }
            $dirs = \glob("{$p}/{*,.*}", GLOB_ONLYDIR | GLOB_BRACE );
            foreach($dirs as $dir){
                $basename = basename($dir);
                if($basename !== '.' && $basename !== '..'){
                    if($r = g($dir, $glob_flags, $expr, $mapper)){
                        $x[($mapper) ? ($mapper)($basename,'dir') : $basename] = $r;
                    }
                }
            }
            return $x;
        } finally {
            $level --;
        }
    }
}}}
#endregion
# ######################################################################################################################
#region Url
namespace _ { if(!\function_exists(u::class)){ function u($path = null, $portal = null){
    if(\func_num_args() > 1){
        return \_\SITE_URL.(
            ($role = $_REQUEST->_['role'] ?? '') 
                ? ($portal ? "/{$portal}.{$role}" : "") //only portals have roles
                : ($portal ? "/{$portal}" : "") //only portals have roles
        ).($path ? "/{$path}" : "");
    } else {
        return \_\BASE_URL.($path ? "/{$path}" : "");
    }
}}}
#endregion
# ######################################################################################################################
#region Dx
namespace _ { if(!\function_exists(on_default::class)){ function on_default($on_default = null){
    if($on_default instanceof \Throwable){
        throw $on_default;
    } else if($on_default instanceof \closure){
        return ($on_default)();
    } else {
        return $on_default;
    }
}}}
#endregion
# ######################################################################################################################
#region ARRAY
namespace { if(!\function_exists(array_patch_recursive::class)){ function array_patch_recursive($array,...$patches){
    static $patcher;
    if(!$patcher){
        $patcher = function(&$array, $patch) use(&$patcher){
            foreach($patch as $k => $v){
                if(isset($array[$k])){
                    if(\is_array($v) && \is_array($array[$k]) && $k[0] != '.'){
                        ($patcher)($array[$k], $v);
                    } else {
                        $array[$k] = $patch[$k];
                    }
                } else {
                    $array[$k] = $patch[$k];
                }
            }
        };
    }
    foreach($patches as $patch){
        ($patcher)($array, $patch);
    }
    return $array;
}}}
namespace { if(!\function_exists(array_purge_recursive::class)){ function array_purge_recursive(&$array, $eq = null){
    foreach($array as $k => &$v){
        if(\is_array($v)){
            \array_purge_recursive($v,$eq);
        } else if($v === $eq){
            unset($array[$k]);
        }
    }
}}}
namespace { if(!\function_exists(array_purge::class)){ function array_purge(&$array, $eq = null){
    foreach($array as $k => &$v){
        if($v === $eq){
            unset($array[$k]);
        }
    }
}}}

#endregion
# ######################################################################################################################
#region DOB
namespace _ { if(!\function_exists(dob::class)){ function dob(string|array $path, array $options = []){
    $path = \is_array($path) ? \_\slashes(...$path) : \_\slashes($path);
    if(array_key_exists('value', $options)){
        $value = $options['value'];
        if(!\str_ends_with($path,'-$.php')){
            $path = "{$path}-$.php";
        }
        if(($path[0]??'')=='/' || ($path[1]??'')==':'){
            $file = $path;
        } else {
            $file = \_\DATA_DIR."/{$path}";
        }
        if(\is_null($value)){
            if(\is_file($file)){
                unlink($file);
            }
        } else {
            \is_dir($d = \dirname($file)) OR \mkdir($d,0777,true);
            if($options['patch'] ?? null){
                $o = \is_file($file) ? dob($file) : [];
                $write = $o ? \array_patch_recursive($o, (array) $value) : $value;
                if(isset($options['purge'])){
                    \array_purge_recursive($write, $options['purge']);
                }
                \file_put_contents($file, "<?php return ".\var_export($write, true).';');
            } else {
                $write = (array) $value;
                if(isset($options['purge'])){
                    \array_purge_recursive($write, $options['purge']);
                }
                \file_put_contents($file, "<?php return ".\var_export($write, true).';');
            }
        }
    } else { 
        if(
            (\str_ends_with($path,'-$.php') 
                ? (
                    \is_file($file = \_\DATA_DIR."/{$path}")
                    || ($file = \stream_resolve_include_path($path))
                ) 
                : (
                    \is_file($file = \_\DATA_DIR."/{$path}-$.php")
                    || ($file = \stream_resolve_include_path("{$path}-$.php"))
                    || ($file = \stream_resolve_include_path("{$path}-$.php"))
                )
            )
        ){
            return (function($__FILE__){ return include $__FILE__; })($file);
        }
    }
}}}
namespace _\dob { if(!\function_exists(json::class)){ function json(string|array $path, $value, array $options = []){
    $path = \is_array($path) ? \_\slashes(...$path) : \_\slashes($path);
    if(array_key_exists('value', $options)){
        $value = $options['value'];
        if(($path[0]??'')=='/' || ($path[1]??'')==':'){
            $file = "{$path}-$.json";
        } else {
            $file = \_\DATA_DIR."/{$path}-$.json";
        }
        if(\is_null($value)){
            if(\is_file($file)){
                unlink($file);
            }
        } else {
            \is_dir($d = \dirname($file)) OR \mkdir($d,0777,true);
            if($options['patch'] ?? null){
                $o = \is_file($file) ? dob($file) : [];
                $write = $o ? \array_patch_recursive($o, (array) $value) : $value;
                if(isset($options['purge'])){
                    \array_purge_recursive($write, $options['purge']);
                }
                \file_put_contents($file, \json_encode($value,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
            } else {
                $write = (array) $value;
                if(isset($options['purge'])){
                    \array_purge_recursive($write, $options['purge']);
                }
                \file_put_contents($file, \json_encode($value,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
            }
        }
    } else {
        if(
            (\str_ends_with($path,'-$.json') 
                ? (
                    \is_file($file = \_\DATA_DIR."/{$path}")
                    || ($file = \stream_resolve_include_path($path))
                ) 
                : (
                    \is_file($file = \_\DATA_DIR."/{$path}-$.json")
                    || ($file = \stream_resolve_include_path("{$path}-$.json"))
                    || ($file = \stream_resolve_include_path("{$path}-$.json"))
                )
            )
        ){
            return \json_decode(\file_get_contents($file), $options['assoc'] ?? true);
        }
    }
}}}
# ######################################################################################################################
#region CANVAS
namespace _ { if(!\function_exists(clear::class)){ function clear(){
    while(\ob_get_level() > \_\OB_OUT){ 
        @\ob_end_clean(); 
    }
    \ob_start();
}}}
namespace _ { if(!\function_exists(clean::class)){ function clean(callable $to = null, bool $restart = true, bool $till = \_\OB_OUT){
    ($till <= \_\OB_TOP) OR $till = \_\OB_TOP;
    if($to){
        $i = $till + 1;
        while(\ob_get_level() > $i){ 
            @\ob_end_clean(); 
        }
        $d = @\ob_get_clean();
        $restart AND \ob_start();
        if(\is_callable($to)){
            return ($to)($d);
        } else {
            return $d;
        }
    } else {
        while(\ob_get_level() > $till){ 
            @\ob_end_clean(); 
        }
        $restart AND \ob_start();
    }
}}}
namespace _ { if(!\function_exists(capture::class)){ function capture(callable|null $to = null){
    $d = \ob_get_contents(); 
    \ob_end_clean();  
    \ob_start();
    if(\is_callable($to)){
        return ($to)($d);
    } else {
        return $d;
    }
}}}
namespace _ { if(!\function_exists(render::class)){ function render(mixed $expr, bool|array $params = [], bool $texate = false){
    if(\is_bool($params)){
        $texate = $params;
        $params = [];
    }
    if(!$expr && $expr != 0){
        if($texate){
            return '';
        } else {
            echo '';
            return;
        }
    } else if(\is_scalar($expr)){
        if($texate){
            return $expr;
        } else {
            echo $expr;
            return;
        }
    }
    
    try{ 
        $texate AND \ob_start();
        if(\is_array($expr) && ($params[0] ?? null) === true){
            foreach($expr as $v){
                if(\is_scalar($v)){
                    echo $v;
                }
            }
        } else if($expr instanceof \closure) {
            \is_array($params) ? ($expr)(...$params) : ($expr)();
        } else if($expr instanceof \SplFileInfo) {
            include $expr;
        } else if($expr instanceof \_\i\prt__i){
            \is_array($params) ? $expr->prt(...$params) : ($expr)();
        } else {
            echo '<pre>'.\json_encode($expr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).'<pre>';
        }
    } finally { 
        if($texate){
            $d = \ob_get_contents(); \ob_end_clean();  
        }  
    }
    if($texate){
        return $d; //* if returned in finally exceptions get lost
    }
}}}
namespace _ { if(!\function_exists(texate::class)){ function texate(mixed $expr, array $params = []){
    return \_\render($expr, $params, true);
}}}
namespace _ { if(!\function_exists(prt::class)){ function prt($o){
    \_\IS_HTML AND print("<pre>");
    $json = \is_scalar($o) 
        ? $o 
        : \json_encode(
            $o,
                JSON_PRETTY_PRINT 
                | JSON_UNESCAPED_SLASHES 
                | JSON_INVALID_UTF8_SUBSTITUTE
                //| JSON_HEX_TAG ^ JSON_HEX_AMP ^ JSON_HEX_APOS ^ JSON_HEX_QUOT
                | JSON_UNESCAPED_UNICODE 
            ,
        )
    ;
    echo \_\IS_HTML 
        ? htmlspecialchars($json, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
        : $json
    ;
    if (\json_last_error() !== JSON_ERROR_NONE) {
        echo \json_encode(["PRT-ERROR" => \json_last_error().": ".\json_last_error_msg()]);
    }
    \_\IS_HTML AND print("</pre>");
}}}
namespace _ { if(!\function_exists(view::class)){ function view($expr = null){
    static $I; 
    return \func_num_args()
        ? \_\ui\view::_($expr)
        : ($I ?? ($I = \_\ui::_()))
    ;
}}}
#endregion
# ######################################################################################################################
#region Misc
namespace _ { if(!\function_exists(once::class)){ function once($key){
    static $keys = [];
    if($keys[$key] ?? false){
        return false;
    } else {
        $keys[$key] = true;
        return true;
    }
}}}
namespace _ { if(!\function_exists(call_once::class)){ function call_once($key,\closure $fn){
    if(\_\once($key)){
        ($fn)();
    }
}}}
namespace _ { if(!\function_exists(owner_of::class)){ function owner_of(object $resource, object|bool $owner = null){
    static $c = []; 
    if(\is_null($owner)){
        return $c[\spl_object_id($resource)] ?? null;
    } else if(\is_bool($owner) && $owner == false){
        unset($c[\spl_object_id($resource)]);
    } else {
        return $c[\spl_object_id($resource)] = $owner;
    }
}}}
namespace _ { if(!\function_exists(shared_ref::class)){ function &shared_ref($name = null){
    static $_CACHE = [];
    if($name){
        if(!isset($_CACHE[$name])){
            $_CACHE[$name] = [];
        }
        return $_CACHE[$name];
    } else {
        return $_CACHE;
    }
}}}
namespace _ { if(!\function_exists(on_default::class)){ function on_default($on_default = null){
    if($on_default instanceof \Throwable){
        throw $on_default;
    } else if($on_default instanceof \closure){
        return ($on_default)();
    } else {
        return $on_default;
    }
}}}
namespace _ { if(!\function_exists(is_empty::class)){ function is_empty($obj, array $exclude = []){
    if(\is_object($obj)){
        if(!$exclude){
            foreach( $obj as $x ) return false;
        } else {
            foreach ($obj as $key => &$value) {
                if(!\in_array($key, $exclude)){
                    return false;
                }
            }
        }
    } else {
        return empty($obj);
    }
    return true;
}}}
#endregion
# ######################################################################################################################
#region Misc
namespace _ { if(!\function_exists(php_file::class)){ function php_file(string $file, bool|callable $on_default = false):callable {
    if($__FILE__ = \_\f($path,'.php')){
        return (function (array $__PARAM__) use($__FILE__){
            $__PARAM__ AND \extract($__PARAM__, EXTR_OVERWRITE | EXTR_PREFIX_ALL, 'p__');
            return include $__FILE__;
        }); //->bindTo(\_::_(),\_::class) if needed do it outside
    }
    return \_\on_default($on_default ?? new \Exception("Unable to resolve: $file"));
}}}
namespace _ { if(!\function_exists(php_func::class)){ function php_func(string $func, bool|callable $on_default = false):callable {
    if(\function_exists($func)){
        return $func;
    }
    if($__FILE__ = \_\f($func,'-fdef.php')){
        include $__FILE__;
        if(!\function_exists($func)){
            return $func;
        }
    }
    return \_\on_default($on_default ?? new \Exception("Unable to resolve: $file"));
}}}
namespace _ { if(!\function_exists(fn_func::class)){ function fn_func(string $func, bool|callable $on_default = false):callable {
    static $fnf = [];
    if($__FILE__ = $fnf[$func] ?? ($fnf[$func] = \_\f($func,'-fn_func.php'))){
        return include $__FILE__; //->bindTo(\_::_(),\_::class) if needed do it outside
    }
    return \_\on_default($on_default ?? new \Exception("Unable to resolve: $file"));
}}}
#endregion
# ######################################################################################################################
#region Session
namespace _ { if(!\function_exists(session::class)){ function session(){
    return $_SESSION;
}}}
namespace _ { if(!\function_exists(session_var::class)){ function session_var($key,...$args){
    if(!$args){
        return $_SESSION['--var'][$key] ?? '';
    } else {
        $_SESSION['--var'][$key] = $args[0];
    }
}}}
namespace _ { if(!\function_exists(flash::class)){ function flash($key,...$args){
    if(!$args){
        return $GLOBALS['_']['FLASH'][$key];
    } else {
        $_SESSION['--flash'][$key] = $args[0];
    }
}}}
namespace _ { if(!\function_exists(runspan::class)){ function runspan(int $decimal = 6, $unit = 's'){
    return \number_format(((\microtime(true) - \_\MSTART)), $decimal).$unit;
}}}
#endregion
# ######################################################################################################################
#region Response
namespace _ { if(!\function_exists(abort::class)){ function abort(int $httpcode_or_level = 1, string $message = null){
    if($httpcode_or_level < 100){
        \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', $httpcode_or_level);
        exit();
    } else {
        \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 0);
        \http_response_code($code);
        echo $message;
        exit();
    }
}}}
namespace _ { if(!\function_exists(redirect::class)){ function redirect(bool|string|null $url = null, bool|string|array|null $query = null){
    if(!$url){
        $goto = $_SERVER['REQUEST_URI'];
    } else if($url === true){
        $goto = \strtok($_SERVER['REQUEST_URI'],'?');
    } else if($url === '.'){
        $goto = \_\CTLR_URL ?? null ?: \_\BASE_URL;
    } else if($url[0] == '?') {
        //* note: URP - pure path
        $goto = \_\CTLR_URL.'/'.$url; 
    } else if($url[0] == '.') {
        $prefix = \_\CTLR_URL ?? null ?: \_\BASE_URL;
        if(($url[1] ?? '') == '/'){
            $goto = \rtrim($prefix.'/'.\substr($url,1), '/.');
        } else {
            $goto = \rtrim($prefix.'/'.$url,'.');
        }
    } else if($url[0] === '/') {
        $goto = $url;
    } else if(preg_match('/^http[s]?:/',$url)){
        $goto = $url;
    } else {
        $goto = \_\BASE_URL.'/'.$url;
    }
    
    if($goto){
        if($query == true){
            $goto .= "?0=".\_\MSTART;
        } else if(\is_array($query)){
            $goto .= "?".\http_build_query($query);
        }
    }
    
    // if($goto){
    //     $GLOBALS['--RESPONSE'] = (object)[
    //         //* note: by default the redirect is 302 i.e. temporary
    //         'type' => 'redirect',
    //         'headers' => ["Location: ". $goto],
    //     ];
    // }

    if($goto){
        \header("Location: ". $goto);
        exit();
    }
}}}
#endregion
# ######################################################################################################################
#region DOWNLOAD
namespace _ { if(!\function_exists(download::class)){ function download($file, array $options = []){
    $headers = null;
    $download_name = false;
    \extract($options);
    if(!file_exists($file)){
        \http_response_code(404); exit('{ "status":"error", "info":"Not Found" }');
    } else {
        if(!$headers){
            if($download_name === false){
                $download_name = \basename($file);
            } else if($download_name === true){
                $fname = pathinfo($file, PATHINFO_FILENAME);
                $download_name = \str_replace('/','-','download-'.date('Y-md-Hi-s')."-{$fname}");
            } else if(\is_string($download_name)){
                
            }
            $headers = [
                "Content-Type: application/octet-stream",
                "Content-Transfer-Encoding: Binary", 
                "Content-disposition: attachment; filename=\"".$download_name."\"",
                "Content-length:".(string)(filesize($file)),
            ];
        }
        try {
            foreach($headers as $h){
                header($h);
            }
            readfile($file);
        } finally {
            exit();
        }
    }
}}}
#endregion
# ######################################################################################################################

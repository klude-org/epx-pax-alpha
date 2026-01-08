<?php 

final class _ extends \stdClass {
    
    use \_\i\singleton__t;
    
    private function __construct(){ 
        $this->fn = (object)[];
        function o(){ static $I; return $I ?? $I = \_::_();  }
        function i(){ static $I; return $I ?? $I = \_\i::_();  }        
        \spl_autoload_register($fn = function($n){
            static $x = [];
            if(empty($x[$n])){
                $x[$n] = true; //! we'll avoid re-searching
                if(\str_starts_with($n, \_::class)){
                    if(($epath = ($_ENV['COM'][\substr($n,2)] ?? null ?: [])[0] ?? null ?: '') 
                        && \class_exists($extends = \str_replace('/','\\', $epath))
                    ){
                        $def = '';
                        if(\preg_match("/((.*)\\\)?(\w+)$/", $n, $mx)){
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
                    }
                }
            }
        },true,false);
    }
    
    public function __get($n){
        //static $N =[];  return $N[$k = \strtolower($n)] ?? ($N[$k] = (static::class.'\\'.$k)::_());
        return $this->$n = \class_exists($c = static::class.'\\'.$n)
            ? $c::_()
            : null
        ;
    }
    
    public function __call($m,$args){
        return ($this->fn->$m)(...$args);
    }
    
    public static function fob_(string $f){
        static $C; 
        $C OR $C = \class_exists(\_\i\file::class) 
            ? \_\i\file::class 
            : \SplFileInfo::class
        ;
        if($f){
            return new $C($f);    
        }
    }
    
    public static function f(string $path, array | string $sfx = null){
        $path = \str_replace('\\','/', $path);
        if(!$sfx){
            return static::fob_(\stream_resolve_include_path($path));
        } else if(\is_array($sfx)){
            foreach($sfx as $suffix){
                if($f = (($suffix)
                    ? \stream_resolve_include_path($r[] = "{$path}/{$suffix}") 
                        ?: (\stream_resolve_include_path($r[] = "{$path}{$suffix}")
                    )
                    : \stream_resolve_include_path($r[] = "{$path}")
                )){
                    return static::fob_($f);
                }
            }
        } else if($f = \stream_resolve_include_path($r[] = "{$path}/{$sfx}") 
            ?: (\stream_resolve_include_path($r[] = "{$path}{$sfx}"))
        ){
            return static::fob_($f);
        }
    }
    
    public static function g(string $p, int $flags = 0, array &$list = []){
        /* using gxp would not work on files */
        foreach(\explode(PATH_SEPARATOR, \get_include_path()) as $d){
            foreach(\glob("{$d}/{$p}", $flags) as $f){
                $list[] = static::fob_(\str_replace('\\','/', $f));
            }
        }
        return $list;
    }
    
    public static function db($table, $id = null, $value = null){
        static $PDO;
        switch(\func_num_args()){
            case 0:{
                return \_\db::_();
            } break;
            case 1:{
                return \_\db::_()[$table];
            } break;
            case 2:{
                //pdo read value
                return \_\db::_()[$table][$id];
            } break;
            case 3:{
                //pdo write value
                return \_\db::_()[$table][$id] = $value;
            } break;
        }
    }
    
    public static function vars(string|array $n = null, mixed $args = null){
        static $V = [];
        if(($count = \func_num_args()) > 1){
            if(\is_scalar($n)){
                $V[$n] = $args;
            } else {
                throw new \Exception('Invalid Key Type');
            }
        } else if($count) {
            if(\is_scalar($n)){
                if($v = $V[$n] ?? null){
                    if(\is_callable($v)){
                        return ($v)();
                    } else {
                        return $v;
                    }
                }
            } else if(\is_array($n) || \is_object($n)){
                foreach($n as $k => $v){
                    static::vars($k, $v);
                }
            } else {
                throw new \Exception("Invalid var parameter \$n");
            }
            
        } else {
            return $V;
        }
    }
    
    public static function clear(){
        while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
    }
    
    public static function render(mixed $expr, bool|array $params = [], bool $texate = false){
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
    }

    public static function texate(mixed $expr, array $params = []){
        return static::render($expr, $params, true);
    }    
    
    public static function phpx(string|array $file, bool|callable $on_default = false):callable {
        if($__FILE__ = \is_string($file) ? static::f($file) : static::f(...$file)){
            return (function (array $__PARAM__) use($__FILE__){
                $__PARAM__ AND \extract($__PARAM__, EXTR_OVERWRITE | EXTR_PREFIX_ALL, 'p__');
                return include $__FILE__;
            })->bindTo(static::_(), static::class);
        } else {
            if(\is_callable($on_default) ){
                return $on_default;
            } else if($on_default == true){
                return function(){ };
            } else {
                if(\is_array($file)){
                    throw new \Exception("View not found: '{$file[0]}'");
                } else {
                    throw new \Exception("View not found: '{$file}'");
                }
            }
        }
    }
    
    public static function view(string $file, bool|callable $on_default = false):callable {
        if($__FILE__ = static::f($file,'-v.php')){
            return (function ($__INSET__ = null, array $__PARAM__ = null) use($__FILE__){
                if(\is_callable($__INSET__)){ 
                    $__INSET__ = static::texate($__INSET__);
                } else if($__INSET__ instanceof \SplFileInfo) {
                    $__INSET__ = static::texate(function() use($__INSET__){ include $__INSET__; });
                } else if(\is_array($__INSET__)) {
                    $__PARAM__ = $__INSET__;
                    $__INSET__ = $__PARAM__[0] ?? '';
                } else if(\is_scalar($__INSET__)) {
                    $__INSET__ = $__INSET__;
                }
                $__PARAM__ AND \extract($__PARAM__, EXTR_OVERWRITE | EXTR_PREFIX_ALL, 'p__');
                return include $__FILE__;
            })->bindTo(static::_(), static::class);
        } else {
            if(\is_callable($on_default) ){
                return $on_default;
            } else if($on_default == true){
                return function(){ };
            } else {
                throw new \Exception("View not found: '{$file}'");
            }
        }
    }
    
    public static function on_action(string $action, callable $f){
        if($action = $_REQUEST['--action'] ?? null){
            if(\is_string($r = ($f)())){
                static::clear();
                \header('Location: '.$r);
                exit;
            } else if(\is_array($r)){
                static::clear();
                \header('Content-Type: application/json');
                echo \json_encode($r, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); 
                exit;
            } else if($r instanceof \SplFileInfo) {
                static::respond__download($r);
                exit;
            } else if($r !== false){
                static::clear();
                \header('Location: '.\strtok($_SERVER['REQUEST_URI'],'?'));
                exit;
            }
        }
    }
    
    public static function on_view(callable $f){
        if(empty($_REQUEST['--action'] ?? null)){
            ($f)();
        }
    }
    
}
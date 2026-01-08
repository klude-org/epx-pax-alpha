<?php namespace epx\std;

abstract class module extends \stdClass {
    
    public readonly string $DIR;
    public readonly string $REM;
    protected static $I = [];
    private static $depstack = [];
    private $running_dependencies = false;
    
    public static function _(string $p){ 
        return static::$I[$p] ?? static::$I[$p] = static::i__build($p);
    }
    
    private static function i__build($p){
        $r_module = \strtok($p,':');
        if($r_x = (\strtok('') ?: static::$depstack[0] ?? '')){
            $r_domain = \strtok($r_x, '/');
            $r_source = \strtok('');
        } else {
            $r_domain =  'lib';
            $r_source = null;
        }
        if(\class_exists($c = "epx\\std\\{$r_domain}\\module")){
            return new $c($p, $r_module, $r_source);
        } else {
            throw new \Exception("Domain '{$r_domain}' is not supported");
        }
    }
    
    protected function __construct($dir, $remote){
        $this->DIR = $dir;
        $this->REM = $remote ?? '';
    }
    
    public function __toString(){
        return $this->DIR;
    }
    
    public function dir(){
        return $this->DIR;
    }
    
    public function run_dependencies(){
        if($this->running_dependencies){
           return $this; 
        }
        try {
            \array_push(static::$depstack,$this->REM);
            $this->running_dependencies = true;
            if(\is_file($f = "{$this->DIR}/.module.php")){
                include $f;
            }
        } finally {
            \array_pop(static::$depstack);
            $this->running_dependencies = false;
        }
    }
    
    public function include($prefix = false){
        if(!\is_dir($this->DIR)){
            $this->update();
        }
        if($prefix){
            if($prefix == -1){
                $GLOBALS['_']['MODULES'] = [$this->DIR => \is_dir($this->DIR)] + ($GLOBALS['_']['MODULES'] ?? []);
            } else {
                $GLOBALS['_']['MODULES'][$this->DIR] = \is_dir($this->DIR);
            }
            $this->run_dependencies();
        } else {
            $this->run_dependencies();
            $GLOBALS['_']['MODULES'][$this->DIR] = \is_dir($this->DIR);
        }
        return $this;
    }
    
    public function install($dependencies = true){ 
        if(!\is_dir($this->DIR)){
            $this->update();
        }
        if($dependencies){
            $this->run_dependencies();
        }
        return $this;
    }

    public function update(){
        //place holder
    }
    
}
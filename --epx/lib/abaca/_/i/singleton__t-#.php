<?php namespace _\i;

trait singleton__t {
    public final static function _() { 
        static $I = []; return $I[static::class] ?? (function(&$i){
            $i = new static();
            $i->i__initialize();
            return $i;
        })($I[static::class]);
    }
    private final function __construct(){ 
        $GLOBALS['_TRACE'][] = "Singleton: ".static::class; 
        $this->i__construct();
    }
    protected function i__construct(){ }
    protected function i__initialize(){ }
}
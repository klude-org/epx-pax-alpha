<?php namespace _\i\feature;

class solo extends \stdClass {
    
    use \_\i\feature\__t;

    public final static function _() { 
        static $I = []; return $I[static::class] ?? (function(&$i){
            $i = new static();
            $i->i__initialize();
            return $i;
        })($I[static::class]);
    }
    
}
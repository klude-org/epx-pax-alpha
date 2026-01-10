<?php namespace _\i\feature;

class node extends \stdClass {
    
    use \_\i\feature\__t;
    
    public final static function _($parent = null) { 
        $GLOBALS['_TRACE'][] = "Node: ".static::class; 
        $i = new static($parent);
        $i->i__initialize();
        return $i;
    }
    
}
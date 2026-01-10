<?php namespace _\i\feature;

trait __t {
    
    public readonly ?object $PAR;

    private final function __construct($parent = null){ 
        $this->PAR = $parent;
        $this->i__construct();
    }
    protected function i__construct(){ }
    protected function i__initialize(){ }

    public function __get($n){
        if(\ctype_lower($n[0] ?? '')) {
            if(\class_exists($c = static::class.'\\_'.$n)){
                return $this->$n = $c::_($this);
            }
            return $this->$n = null;
        } 
    }
    
}
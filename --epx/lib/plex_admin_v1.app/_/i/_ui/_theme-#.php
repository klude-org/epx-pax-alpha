<?php namespace _\i\_ui;

class _theme extends \_\i\feature\solo { 
    
    public $CLASS = 'w__theme_alpha';
    public $PATH = 'w__theme_alpha';
    
    public function select($name = 'w__theme_alpha'){
        if(\class_exists($name)){
           $this->CLASS = \strtr($name,'/','\\');
           $this->PATH = \strtr($name,'\\','/'); 
        }
        return $this;
    }
    
    public function file(string $name, string ...$sfx){
        return \_\f("{$this->PATH}/{$name}", ...$sfx);
    }
    
}

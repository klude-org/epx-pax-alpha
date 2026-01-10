<?php namespace _\i;

class _ui extends \_\i\feature\solo {
    
    public function load(string $name){
        $view = \_\i\_ui\view::_()->select($name);
        return $view;
    }
    
}


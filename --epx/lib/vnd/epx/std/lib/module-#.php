<?php namespace epx\std\lib;

final class module extends \epx\std\module {
    
    public function __construct(string $r_key, string $r_module){
        if( 
            ($d = \glob($r[] = "{$_SERVER['_']['SPLEX_DIR']}/*/{$r_module}", GLOB_ONLYDIR)[0] ?? null)
            || ($d = \glob($r[] = "{$_SERVER['_']['MPLEX_DIR']}/*/{$r_module}", GLOB_ONLYDIR)[0] ?? null)
        ){
            $r_dir = \strtr($d, '\\','/');
        } else {
            $r_dir = "{$_SERVER['_']['SPLEX_DIR']}/lib/{$r_module}";
        }
        parent::__construct($r_dir, null);
    }

}
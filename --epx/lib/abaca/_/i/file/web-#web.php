<?php namespace _\i\file;

class php {
    
    public readonly \_\i\file $file;

    private object $context;

    public static function _(\_\i\file $file){ return new static($file); }

    protected function __construct($file){
        $this->file = $file;
    }

    public function url(){
        $p = \str_replace('\\','/', $this->file->getRealPath());
        if(\str_starts_with($p, \_\ROOT_DIR)){
            return \_\i\url::_(o()->srv->root_url.'/'.\substr($p, \strlen(\_\ROOT_DIR) + 1));
        }
    }
    

}
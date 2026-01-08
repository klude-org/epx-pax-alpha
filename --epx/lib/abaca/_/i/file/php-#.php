<?php namespace _\i\file;

class php {
    
    public readonly \_\i\file $file;

    private object $context;

    public static function _(\_\i\file $file){ return new static($file); }

    protected function __construct($file){
        $this->file = $file;
    }

    public function context(object $context){
        if(\func_num_args()){
            $this->context = $context;
            return $this;
        } else {
            return $this->context;
        }
    }
    

}
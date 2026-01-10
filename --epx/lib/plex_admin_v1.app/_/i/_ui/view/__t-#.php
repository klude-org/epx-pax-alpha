<?php namespace _\i\_ui\view;

trait __t  {
    
    private $__INSET__;
    
    private $FILE;
    private $NAME;
    
    public readonly object $ui;
    
    protected function i__construct(){
        $this->ui = i()->ui;
    }
    
    public function inset($inset = null){
        if(func_num_args()){
            if(\is_scalar($inset)){
                $this->__INSET__ = $__INSET__;
            } else if(\is_callable($inset)){
                try{
                    \ob_start();
                    $inset($this);
                } finally {
                    $this->__INSET__ = \ob_get_contents(); \ob_end_clean();  
                }
            }
            return $this;
        } else {
            return $this->__INSET__;
        }
    }
    
    public function prt($inset = null){
        $this->inset($inset);
        $this->i__prt();
    }
    
    public function txt($inset = null){
        $this->inset($inset);
        try{
            \ob_start();
            $this->i__prt();
        } finally {
            $d = \ob_get_contents(); \ob_end_clean();  
        }
        return $d;
    }
    
    public function select($name = null){
        if(!\func_num_args()){
            $name = $this->NAME;
        }
        if($name){
            $this->NAME = $name;
            if(
                ($f = i()->ui->theme->file($name,'-v.php'))
                || ($f = \_\f(\_\i\_ui::class."/{$name}",'-v.php'))
                || ($f = \_\f($name,'-v.php'))
            ){
                $this->FILE = $f;
            } else {
                $this->FILE = false;
            }
        }
        return $this;
    }
    
    public function i__prt(){ 
        if(\is_null($this->FILE)){
            $this->select();
        }
        if($this->FILE && \is_file($this->FILE)){
            include $this->FILE;
        } else {
            echo "Invalid View '{$this->NAME}'";
        }
    }
    
    public function load(string $name){
        $view = \_\i\_ui\view::_($this)->select($name);
        return $view;
    }    
    
}
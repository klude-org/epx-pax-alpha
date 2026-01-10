<?php namespace _\i\_ui;

trait vars__t { 
    
    private $_ = [];

    public function offsetSet($n, $v):void { 
        $this->set($n, $v);
    }
    public function offsetExists($n):bool { 
        return \array_key_exists($n, $this->_);
    }
    public function offsetUnset($n):void { 
        \unset($this->_[$n]);
    }
    public function offsetGet($n):mixed {  
        if(\array_key_exists($n,$this->_)){
            $v = $this->_[$n];
            if(\is_callable($v)){
                return $v($this->PAR);
            } else if(\is_scalar($v)){
                return $v;    
            } else {
                //do nothing!
            }
        }
        return null;
    }

    public function set($n, $v = null){
        if(\is_callable($v)){
            try{
                \ob_start();
                $v($this->PAR);
            } finally {
                $this->_[$n] = \ob_get_contents(); \ob_end_clean();  
            }
        } else if(\is_scalar($v)){
            $this->_[$n] = $v;
        } else {
            throw new \Exception('Invalid inset type');
        }
        return $this;
    }
    
    public function set_latent($n, $inset = null){
        if(\is_callable($v) || \is_scalar($v)){
            $this->_[$n] = $inset;    
        } else {
            throw new \Exception('Invalid inset type');
        }
        return $this;
    }
    
    public function set_view($n, $view_name, $inset = null){
        $this->_[$n] = i()->ui->view($view_name)->txt($inset);
        return $this;
    }
    
}


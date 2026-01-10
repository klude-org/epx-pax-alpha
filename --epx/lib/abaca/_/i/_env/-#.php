<?php namespace _\i;

class _env extends \_\i\feature\solo implements \ArrayAccess, \JsonSerializable {
    
    public array $_;
    
    protected function i__construct(){
        if($_ENV['SG_TOTING__EN'] ?? true){
            $this->_ = $_ENV;
            $_ENV = $this;
        } else {
            $this->_ =& $_REQUEST;
        }
    }
    
    public function offsetSet($n, $v):void { 
        //throw new \Exception('Set-Accessor is not supported for class '.static::class);
        $this->_[$n] = $v;
    }
    public function offsetExists($n):bool { 
        return \array_key_exists($n, $this->_) ? true : !\is_null($this[$n]);
    }
    public function offsetUnset($n):void { 
        //throw new \Exception('Unset-Accessor is not supported for class '.static::class);
        unset($this->_[$n]);
    }
    public function offsetGet($n):mixed { 
        if(!\array_key_exists($n, $this->_)){
            $k = "FW_{$n}";
            $this->_[$n] = 
                $this->_['ENV'][$n]
                ?? (\defined($k) ? \constant($k) : null)
                ?? ((($r = \getenv($k)) !== false) ? $r : null)
                ?? $_SERVER[$k]
                ?? $_SERVER["REDIRECT_{$k}"]
                ?? $_SERVER["REDIRECT_REDIRECT_{$k}"]
                ?? null
            ;
        }
        return $this->_[$n] ?? null;
    }
    public function jsonSerialize():mixed {
        return $this->_;
    }    
    
  
    
    
}
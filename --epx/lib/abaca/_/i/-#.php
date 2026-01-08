<?php namespace _;

class i extends \stdClass implements \ArrayAccess, \JsonSerializable {
    
    use \_\i\singleton__t;
    
    
    public function __get($n){
        //static $N =[];  return $N[$k = \strtolower($n)] ?? ($N[$k] = (static::class.'\\'.$k)::_());
        return $this->$n = \class_exists($c = static::class.'\\'.$n)
            ? $c::_()
            : null
        ;
    }
    
    public function offsetSet($n, $v):void { 
        //throw new \Exception('Set-Accessor is not supported for class '.static::class);
        static::$_[$n] = $v;
    }
    public function offsetExists($n):bool { 
        return \array_key_exists($n, static::$_) ? true : !\is_null($this[$n]);
    }
    public function offsetUnset($n):void { 
        //throw new \Exception('Unset-Accessor is not supported for class '.static::class);
        unset(static::$_[$n]);
    }
    public function offsetGet($n):mixed { 
        if(!\array_key_exists($n, static::$_)){
            $k = "FW_{$n}";
            static::$_[$n] = 
                static::$_['ENV'][$n]
                ?? (\defined($k) ? \constant($k) : null)
                ?? ((($r = \getenv($k)) !== false) ? $r : null)
                ?? $_SERVER[$k]
                ?? $_SERVER["REDIRECT_{$k}"]
                ?? $_SERVER["REDIRECT_REDIRECT_{$k}"]
                ?? null
            ;
        }
        return static::$_[$n] ?? null;
    }
    public function jsonSerialize():mixed {
        return static::$_;
    }    
    
  
    
    
}
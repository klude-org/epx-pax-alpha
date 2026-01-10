<?php namespace _\i\_request;

final class _headers extends \_\i\feature\solo implements \ArrayAccess, \JsonSerializable {
    
    private readonly array $_;
    
    protected function i__construct(){
        $_ = \iterator_to_array((function(){
            foreach(\getallheaders() as $k => $v){
                yield $k => $v;
            }
        })());
        $_['Accept'] = \explode(',', $_['Accept'] ?? '');
        $this->_ = $_;
    }
    
    public function __get($n){
        //static $N =[];  return $N[$k = \strtolower($n)] ?? ($N[$k] = (static::class.'\\'.$k)::_());
        return $this->$n = \class_exists($c = static::class.'\\'.$n)
            ? $c::_()
            : null
        ;
    }
    
    public function offsetSet($n, $v):void { 
        throw new \Exception('Set-Accessor is not supported for class '.static::class);
    }
    public function offsetExists($n):bool { 
        return \array_key_exists($n, $this->_) ? true : !\is_null($this[$n]);
    }
    public function offsetUnset($n):void { 
        throw new \Exception('Unset-Accessor is not supported for class '.static::class);
    }
    public function offsetGet($n):mixed { 
        return $this->_[$n] ?? null;
    }
    public function jsonSerialize():mixed {
        return $this->_;
    }    
    
    
}
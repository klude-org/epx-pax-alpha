<?php namespace _\i;

final class server extends \stdClass implements \ArrayAccess, \JsonSerializable {
    
    use \_\i\singleton__t;

    private $_;
    
    protected function __construct(){ }

    public function offsetSet($n, $v):void { 
        throw new \Exception('Set-Accessor is not supported for class '.static::class);
    }
    public function offsetExists($n):bool { 
        return isset($this->_[$n]);
    }
    public function offsetUnset($n):void { 
        throw new \Exception('Unset-Accessor is not supported for class '.static::class);
    }
    public function offsetGet($n):mixed { 
        return $this->_[$n] ?? null;
    }
    public function jsonSerialize():mixed {
        return (array) $this;
    }
    
    public function __get($n){
        if(\ctype_upper($n[0])) {
            return $this->$n = $this->_['_'][$n] ?? null;
        } else if($n !== '_'){
            if(\class_exists($c = static::class.'\\'.$n)){
                return $this->$n = $c::_();
            }
            return $this->$n = null;
        } 
        $nav = i()->nav;
        $_SERVER['_']['BASE_URL'] = $base_url = $_SERVER['_']['SITE_URL'].($nav->RSSN ? $nav::SPFX.$nav->RSSN : '');
        $_SERVER['_']['FRAME_URL'] = $frame_url = $_SERVER['_']['SITE_URL'].$nav::SPFX.$nav->SESSION_ID;
        $_SERVER['_']['PANEL_URL'] = $panel_url = rtrim($base_url."/"
            .(
                ($nav->PORTAL ?? null ?: '')
                .'.'.($nav->ROLE ?? null ?: '')
            )
            , 
            '/.'
        );
        $_SERVER['_']['CTLR_URL'] = \rtrim($panel_url."/{$nav->NPATH}",'/');
        $this->_ = $_SERVER;
        $_SERVER = $this;
        return $this->_;
    }
    
}
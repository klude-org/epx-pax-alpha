<?php namespace _\i;

final class _session extends \_\i\feature\solo implements \ArrayAccess, \JsonSerializable {

    private $_;
    
    public function i__construct(){
        $rssn = i()->nav->RSSN;
        if(\session_status() == PHP_SESSION_NONE) {
            \session_name($_SERVER['_']['KEY']); 
            if($rssn){
                \session_id($rssn);
            }
            \session_start();
        }
        \define('_\SESSION_ID', \session_id());
        if($rssn && empty($_SESSION['--CSRF'])){
            while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
            \http_response_code(403);
            echo '403: Unauthorized';
            exit(403);
        }
        
        isset($_SESSION['--CSRF']) OR ($b = $_SESSION['--CSRF'] = \md5(uniqid('csrf-')));
        \define('_\CSRF', ($c = $_SESSION['--CSRF']));
        if(
            \in_array($_SERVER['REQUEST_METHOD'], ['POST','PUT','PATCH','DELETE'])
            && ($a = $_REQUEST['--csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null) != ($_SESSION['--CSRF'] ?? null)
        ){
            while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
            \http_response_code(406);
            echo '406: Not Acceptable';
            exit(406);
        }
        \define('_\FLASH', $_SESSION['--FLASH'] ?? []);
        $_SESSION['--FLASH'] = [];
        $this->_ =& $_SESSION;
    }
    
    public function offsetSet($n, $v):void { 
        if(!\is_null($n)){
            $this->_[$n] = $v;    
        }
    }
    public function offsetExists($n):bool { 
        return isset($this->_[$n]);
    }
    public function offsetUnset($n):void { 
        unset($this->_[$n]);
    }
    public function &offsetGet($n):mixed { 
        return $this->_[$n] ?? null;
    }
    
    public function jsonSerialize():mixed {
        return (array) $this;
    }
    
    public function authenticate(){
        include '.auth-web.php';
    }
}
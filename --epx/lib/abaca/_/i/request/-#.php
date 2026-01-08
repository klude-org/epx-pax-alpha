<?php namespace _\i;

final class request extends \stdClass implements \ArrayAccess, \JsonSerializable {
    
    use \_\i\singleton__t;
    
    public readonly array $_;
    
    public function __construct(){
        
        $this->IS_CLI = $_SERVER['_']['IS_CLI'];            
        if(!$this->IS_CLI){
            $this->URL_PARTS = \parse_url($this->URL = (($_SERVER["REQUEST_SCHEME"] 
                ?? ((\strtolower(($_SERVER['HTTPS'] ?? 'off') ?: 'off') === 'off') ? 'http' : 'https'))
            ).'://'.$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI']);
            $this->METHOD = $method = $_SERVER['REQUEST_METHOD'] ?? '';
            # ----------------------------------------------------------------------
            $this->IS_GET = $is_get = !\in_array($method, ['POST','PUT','PATCH','DELETE']);
            $this->ACTION = $action = $_REQUEST['--action'] ?? null;
            $this->IS_ACTION = $is_action = ($action || !$is_get) ? true : false;
            $this->IS_VIEW = !$is_action;
            $this->REFERER = ($j = $_SERVER['HTTP_REFERER'] ?? null) ? \parse_url($j) : [];
            $this->IS_TOP = (($dest = $_SERVER['HTTP_SEC_FETCH_DEST'] ?? ($j ? 'document' : null)) === 'document');
            $this->IS_FRAME = ($dest == 'iframe');
            $this->IS_MINE = !$j || \str_starts_with($j, $this->URL);
            $this->IS_HTML = (\str_contains(($_SERVER['HTTP_ACCEPT'] ?? ''),'text/html'));
            $this->IS_WEB = ($_SERVER['_']['INTFC'] === 'web');
            $this->IS_HTTP = ($_SERVER['_']['INTFC'] !== 'cli');
            $this->IS_API = (!$this->IS_CLI && !$this->IS_WEB);
            $this->IS_HTML = (\strpos(($_SERVER['HTTP_ACCEPT'] ?? ''),'text/html') !== false);
            $this->IS_XHR = ('xmlhttprequest' == \strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '' ));
            $this->AGENT = $agent = (function() {
                if(!\is_null($agent = \getallheaders()['Epx-Agent'] ?? null)){
                    return $agent;
                } else if('xmlhttprequest' == \strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '' )) {
                    return 'xhr';
                } else {
                    return 'page';
                }
            })();
        }        
        
        $this->_ = ($this->IS_CLI
            ? function(){
                $parsed = [];
                $key = null;
                $args = \array_slice($argv = $_SERVER['argv'] ?? [], 1);
                foreach ($args as $arg) {
                    if ($key !== null) {
                        $parsed[$key] = $arg;
                        $key = null;
                    } else if(\str_starts_with($arg, '-')){
                        if(\str_ends_with($arg, ':')){
                            $key = \substr($arg,0,-1);
                        } else if(\str_contains($arg,':')) {
                            [$k, $v] = \explode(':', $arg);
                            $parsed[$k] = $v;
                        } else {
                            $parsed[$arg] = true;
                        }
                    } else {
                        $parsed[] = $arg;
                    }
                }
                if ($key !== null) {
                    $parsed[$key] = true;
                }
                $parsed[0] ??= '/';
                return $parsed;
            }
            : function(){
                global $_;
                $json = [];
                $files = [];
                switch($content_type = \strtok($_SERVER["CONTENT_TYPE"] ?? '',';')){
                    case "application/json": {
                        $json = (function(){
                            $input = \file_get_contents('php://input');
                            $ox = [];
                            foreach(\json_decode($input, true) as $k => $v){
                                $oy =& $ox;
                                foreach(explode('[',\str_replace("]","", $k)) as $kk){
                                    ($oy[$kk] = []);
                                    $oy = &$oy[$kk];
                                }
                                $oy = $v;
                            }
                            return $ox;
                        })();
                    } break;
                    case "multipart/form-data": {
                        $files = (function(){
                            $o = [];
                            foreach($_FILES as $field => $array){
                                foreach($array as $attrib => $inner){
                                    if(\is_array($inner)){
                                        foreach(($r__fn = function($array, $pfx = '', $ifx = '[', $sfx = ']') use(&$r__fn){
                                            foreach($array as $k  => $v){
                                                if(\is_array($v)){
                                                    yield from ($r__fn)($v,"{$pfx}{$ifx}{$k}{$sfx}",$ifx,$sfx);
                                                } else {
                                                    yield "{$pfx}{$ifx}{$k}{$sfx}" => $v;
                                                }
                                            }
                                        })($inner,$field) as $k => $v){
                                            $o[$k][$attrib] = $v;
                                        }
                                    } else {
                                        $o[$field][$attrib] = $inner;
                                    }
                                }
                            }
                            $ox = [];
                            foreach($o as $k => $v){
                                if(!($v['name'] ?? null)){ continue; }
                                $oy =& $ox;
                                foreach(explode('[',\str_replace("]","", $k)) as $kk){
                                    isset($oy[$kk]) OR $oy[$kk] = [];
                                    $oy = &$oy[$kk];
                                }
                                $oy =  new class($v) extends \SplFileInfo implements \JsonSerializable {
                                    public readonly array $info;
                                    public function __construct($v){
                                        $this->info = $v; 
                                        parent::__construct($v['tmp_name']);
                                    }
                                    public function info($n){
                                        if($n == 'extension'){
                                            return \pathinfo($this->details['name'] ?? '', PATHINFO_EXTENSION);
                                        } else {
                                            return $this->details[$n] ?? null;
                                        }
                                    }
                                    public function jsonSerialize(): mixed {
                                        return "--file::".$this->getRealPath();
                                    }
                                    public function f(){
                                        return \_\i\file::_((string) $this, $this->info);
                                    }
                                    public function move_to($path){
                                        \is_dir($d = \dirname($path)) OR \mkdir($d,0777,true);
                                        if(\move_uploaded_file($this, $path)){
                                            return new \SplFileInfo($path);
                                        } else {
                                            return false;
                                        }
                                    }
                                };
                            }
                            return $ox;
                        })();
                    } break;
                    case "application/x-www-form-urlencoded": 
                    default:{
                        //* do nothing
                    } break;
                }
                
                //! warning: array_merge_recursive messes up if $_FILES and $_POST have same key
                return \array_replace_recursive(
                    $_POST, 
                    $_FILES, //* $_FILES is higher priority over $_POST
                    $json,
                    $_GET,
                );
            }
        )();
        $_REQUEST = $this;
        return $this->_;        
    }
    
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
        if(\ctype_lower($n[0])) {
            if(\class_exists($c = static::class.'\\'.$n)){
                return $this->$n = $c::_();
            }
            return $this->$n = null;
        } 
    }

    
}
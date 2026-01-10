<?php namespace _;

class i extends \_\i\feature\solo {
    
        
    public static function on_action(string $action, callable $f){
        if($action = $_REQUEST['--action'] ?? null){
            if(\is_string($r = ($f)())){
                \_::clear();
                \header('Location: '.$r);
                exit;
            } else if(\is_array($r)){
                \_::clear();
                \header('Content-Type: application/json');
                echo \json_encode($r, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); 
                exit;
            } else if($r instanceof \SplFileInfo) {
                static::download($r);
                exit;
            } else if($r !== false){
                \_::clear();
                \header('Location: '.\strtok($_SERVER['REQUEST_URI'],'?'));
                exit;
            }
        }
    }
    
    public static function on_view(callable|string $f, callable $s){
        if(empty($_REQUEST['--action'] ?? null)){
            if(\is_callable($f)){
                ($f)($this->ui);
            } else {
                $this->ui->load($f)->prt($s);
            }
        }
    } 
}
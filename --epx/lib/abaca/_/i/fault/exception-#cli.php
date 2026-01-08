<?php namespace _\i\fault;

class exception extends \Exception implements \JsonSerializable {
    
    protected $details = [];
    
    public static function _(string $message = "", int $code = 0, ?\Throwable $previous = null, array $details = []) { 
        return new static($message, $code, $previous,$details); 
    }
   
    public function __construct($message, $code = 0, ?\Throwable $previous = null, array $details = []) {
        $this->details = $details;
        parent::__construct($message, $code, $previous);
    }
    
    public function getDetails(){
        return $this->details;
    }
    
    public function jsonSerialize():mixed{
        return \iterator_to_array((function(){
            foreach($this->fault_stack() as $ex){
                yield [
                    'class' => $ex::class,
                    'message' => $ex->getMessage(),
                    'code' => $ex->getCode(),
                    'file' => $ex->getFile(),
                    'line' => $ex->getLine(),
                    'details' => \method_exists($ex,'getDetails') ? $ex->getDetails() : null,
                    'trace'    => array_map(
                        function($v){ 
                            return \str_replace('\\','/', $v); 
                        }, 
                        explode("\n", $ex->getTraceAsString())
                    ),
                ];
            }
        })());
    }
    
    public function fault_stack(){
        return \array_reverse(\iterator_to_array((function(){
            $ex = $this;
            for($i = 0, $ex = $this; $ex && $i < 5; $i++, $ex = $ex->getPrevious()){
                yield $ex;
            }
        })()));
    }
    
    public function as_text(){
        $text = "\033[91m";
        foreach($this->fault_stack() as $ex){
            $text .="\033[91m\n"
                .$ex::class.": {$ex->getMessage()}\n"
                ."File: {$ex->getFile()}\n"
                ."Line: {$ex->getLine()}\n"
                ."\033[31m{$ex->getTraceAsString()}\033[0m\n"
            ;
        }
        return $text."\033[0m\n";
    }    
    
    public function __toString() {
        return $this->as_text();
    }
}
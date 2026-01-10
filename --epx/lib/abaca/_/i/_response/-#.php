<?php namespace _\i;

final class _response extends \_\i\feature\solo {
    
    protected function i__construct(){ }
    
    public static function respond__asset($file){
        if(!\is_file($file)){
            static::clear();
            \http_response_code(404);
            echo '404: Not Found: Asset file not found';
            exit(404);
        }
        $mime_type = match($ext = \strtolower(\pathinfo($file, PATHINFO_EXTENSION))){
            'html' => null,
            'css'  => 'text/css',
            'js'   => 'application/javascript',
            'json' => 'application/json',
            'jpg'  => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'html' => 'text/html',
            'txt'  => 'text/plain',
            default => \mime_content_type((string) $file) ?: 'application/octet-stream',
        };
        if(empty($mime_type)) {
            static::clear();
            \http_response_code(404);
            echo '404: Not Found: Unknown Mime Type';
            exit(404);
        } else {
            static::clear();
            $exit = (object)[];
            $exit->headers[] = 'Content-Type: ' . $mime_type;
            $exit->headers[] = 'Cache-Control: public, max-age=86400'; // Cache for 1 day
            $exit->headers[] = 'Expires: ' . \gmdate('D, d M Y H:i:s', \time() + 86400) . ' GMT'; // 1 day in the future
            $exit->headers[] = 'Last-Modified: ' . \gmdate('D, d M Y H:i:s', \filemtime($file)) . ' GMT';
            // Check for If-Modified-Since header
            if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
                \strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= \filemtime($file)) {
                $exit->code = 304; // Not Modified
                $exit->content = null;
            } else {
                // Output the file content
                $exit->content = new \SplFileInfo($file);
            }
            while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
            if(\is_numeric($code = $exit->code ?? null)){
                \http_response_code($code ?: 200);
            } 
            foreach($exit->headers ?? [] as $k => $v){
                if(\is_string($v)){
                    if(\is_numeric($k)){
                        \header($v);
                    } else {
                        \header("{$k}: {$v}");
                    }
                }
            }
            if(\is_null($content = $exit->content ?? null)){ 
                return; 
            } else if($content instanceof \SplFileInfo){
                \readfile($content);
            }
        }
    }
    
    public static function respond__download($file, array $options = []){
        $headers = null;
        $download_name = false;
        \extract($options);
        if(!file_exists($file)){
            \http_response_code(404); 
            \header('Content-Type: application/json');
            echo '{ "status":"error", "info":"Not Found" }';
            exit(1);
        } else {
            if(!$headers){
                if($download_name === false){
                    $download_name = \basename($file);
                } else if($download_name === true){
                    $fname = pathinfo($file, PATHINFO_FILENAME);
                    $download_name = \str_replace('/','-','download-'.date('Y-md-Hi-s')."-{$fname}");
                } else if(\is_string($download_name)){
                    
                }
                $headers = [
                    "Content-Type: application/octet-stream",
                    "Content-Transfer-Encoding: Binary", 
                    "Content-disposition: attachment; filename=\"".$download_name."\"",
                    "Content-length:".(string)(filesize($file)),
                ];
            }
            try {
                foreach($headers as $h){
                    header($h);
                }
                readfile($file);
            } finally {
                exit();
            }
        }
    }     
    
    public static function respond__json($data, int $options = 0){
        static::clear();
        \header('Content-Type: application/json');
        echo \json_encode($data, $options | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); 
        exit();
    }    
    
}
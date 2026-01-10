<?php 
namespace _ { if(!\function_exists(p::class)){ function p(string $expr, int $levels = 0){
    return \strtr(($levels ? \dirname($expr , $levels) : $expr), '\\','/');
}}}
namespace _ { if(!\function_exists(p__is_rooted::class)){ function p__is_rooted($expr){
    return ($expr[0]??'')=='/' || ($expr[1]??'')==':';
}}}
namespace _ { if(!\function_exists(slashes::class)){ function slashes(string $p, string ...$px) {
    return (\func_num_args() == 1)
        ? \strtr($p,'\\','/')
        : \strtr(\implode('/',\func_get_args()),'\\','/')
    ;
}}}
namespace _ { if(!\function_exists(backslashes::class)){ function backslashes(string $p, string ...$px) {
    return (\func_num_args() == 1)
        ? \strtr($p,'/','\\')
        : \strtr(\implode('\\',\func_get_args()),'/','\\')
    ;
}}}
namespace _ { if(!\function_exists(typename::class)){ function typename(string|object $p, string ...$px) {
    return (\func_num_args() == 1)
        ? (\is_object($p) 
            ? \get_class($p) 
            : \strtr($p,'/','\\')
        )
        : (\is_object($p) 
            ? \strtr(\implode('\\',[\get_class($p), ...$px]),'/','\\') 
            : \strtr(\implode('\\',\func_get_args()),'/','\\')
        )
    ;
}}}
namespace _ { if(!\function_exists(fob_::class)){ function fob_(string $f){
    static $C; 
    $C OR $C = \class_exists(\_\i\file::class) 
        ? \_\i\file::class 
        : \SplFileInfo::class
    ;
    return new $C($f);
}}}
namespace _ { if(!\function_exists(f::class)){ function f(string $n, string ...$sfx){
    $path = \strtr($n,'\\', '/',);
    if($sfx){
        if(($path[0]??'')=='/' || ($path[1]??'')==':'){
            foreach($sfx as $suffix){
                if(
                    \file_exists($f = "{$path}/{$suffix}")
                    || \file_exists($f = "{$path}{$suffix}")
                ){
                    return \_\fob_($f);
                }
            }
        } else {
            foreach($sfx as $suffix){
                if($f = \stream_resolve_include_path("{$path}/{$suffix}") 
                    ?: (\stream_resolve_include_path("{$path}{$suffix}"))
                ){
                    return \_\fob_($f);
                }
            }
        }
    } else {
        if(($path[0]??'')=='/' || ($path[1]??'')==':'){
            if(\is_file($f = "{$path}/{$suffix}")){
                return $f; 
            } else if(\is_file($f = "{$path}{$suffix}")){
                return $f; 
            }
        } else {
            if($f = \stream_resolve_include_path("{$path}/{$suffix}") 
                ?: (\stream_resolve_include_path("{$path}{$suffix}"))
            ){
                return \_\fob_($f);
            }
        }
    }
}}}
namespace _ { if(!\function_exists(g::class)){ function g(string $p, int $flags = 0, array &$list = []){
    /* using gxp would not work on files */
    foreach(\explode(PATH_SEPARATOR, \get_include_path()) as $d){
        foreach(\glob("{$d}/{$p}", $flags) as $f){
            $list[] = \_\fob_(\strtr($f,'\\','/'));
        }
    }
    return $list;
}}}

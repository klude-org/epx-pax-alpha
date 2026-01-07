::<?php echo "\r   \r"; if(0): ?>
@echo off
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 goto:launch_cmd
goto :no_cmd
:launch_cmd
echo [92mEPX CMD Version 1.00 (C) Klude Pty Ltd. All Rights Reserved[0m
rem [92mLaunching cmd[0m
cmd /k
goto :exit_ok
:no_cmd
if "%FW__DEBUG%"=="" goto :php_alt
if not exist C:/xampp/current/php__xdbg/php.exe goto :php_alt
C:/xampp/current/php__xdbg/php.exe "%~f0" %*
goto :exit_php
:php_alt
php "%~f0" %*
:exit_php
@exit /b 0
<?php endif;
if(empty($_SERVER['DOCUMENT_ROOT'])){
    for (
        $i=0, $dx=\getcwd(); 
        $dx && $i < 20 ; 
        $i++, $dx = (\strchr($dx, DIRECTORY_SEPARATOR) != DIRECTORY_SEPARATOR) ? \dirname($dx) : null
    ){ 
        if(\is_file($f = "{$dx}/--epx/.local-http-root.php")){
            include $f;
            break;
        }
    }
}
include 'index.php';
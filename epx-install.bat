@echo off
if not exist %~dp0\--epx mkdir %~dp0\--epx
if exist %~dp0\--epx\index.php goto :step_1
curl --fail --globoff -o %~dp0\--epx\index.php https://raw.githubusercontent.com/klude-org/epx-pax-alpha/main/--epx/index.php
if %errorlevel%==0 goto :step_1
echo [91m!!! EPX START DOWNLOAD ERRO 2R[0m
goto :exit_error

:step_1
if exist %~dp0\--epx\.htaccess goto :step_2
curl --fail --globoff -o %~dp0\--epx\.htaccess https://raw.githubusercontent.com/klude-org/epx-pax-alpha/main/--epx/.htaccess
if %errorlevel%==0 goto :start_downloaded
echo [91m!!! EPX START DOWNLOAD ERROR 2[0m
goto :exit_error

:exit_error
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
exit /b 1

:step_2
:exit_ok
exit /b 0

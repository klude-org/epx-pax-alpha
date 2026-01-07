@echo off

if not exist %~dp0\--epx mkdir %~dp0\--epx

set step=1
curl --fail --globoff -o %~dp0\--epx\index.bat https://raw.githubusercontent.com/klude-org/epx-pax-alpha/main/--epx/setup/sub/index.bat
if errorlevel 1 goto :exit_error
set step=2
curl --fail --globoff -o %~dp0\--epx\index.php https://raw.githubusercontent.com/klude-org/epx-pax-alpha/main/--epx/setup/sub/index.php
if errorlevel 1 goto :exit_error
set step=3
curl --fail --globoff -o %~dp0\--epx\.htaccess https://raw.githubusercontent.com/klude-org/epx-pax-alpha/main/--epx/setup/sub/.htaccess
if errorlevel 1 goto :exit_error

:exit_ok
exit /b 0

:exit_error
echo [91m!!! Download error at step %step%2[0m
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
exit /b 1


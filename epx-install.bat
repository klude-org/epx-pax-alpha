@echo off
if not exist %~dp0\--epx mkdir %~dp0\--epx
if exist %~dp0\--epx\index.php goto :start_ready
curl --fail --globoff -o %~dp0\--epx\index.php https://raw.githubusercontent.com/klude-org/epx-pax-alpha/main/--epx/index.php
if %errorlevel%==0 goto :start_downloaded
echo [91m!!! EPX START DOWNLOAD ERROR[0m
goto :exit_error

:start_downloaded
if exist %~dp0\--epx\index.php goto :start_ready
echo [91m!!! EPX START NOT FOUND[0m
goto :exit_error

:exit_error
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
exit /b 1

goto :exit_ok
exit /b 0

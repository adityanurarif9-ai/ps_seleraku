@echo off
setlocal
cd /d "%~dp0"
set "DEMO_PHP=C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe"
if not exist "%DEMO_PHP%" set "DEMO_PHP=php"
echo Pastikan MySQL Laragon aktif. Buka http://127.0.0.1:8000 setelah server berjalan.
pushd "%~dp0public"
"%DEMO_PHP%" -d extension=zip -S 127.0.0.1:8000 "%~dp0vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php"
popd
pause
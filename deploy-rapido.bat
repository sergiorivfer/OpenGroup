@echo off
echo ========================================
echo SUBIR CAMBIOS RAPIDOS
echo ========================================
echo.

:: Montar Z: si no existe
if not exist Z:\ (
 echo [*] Conectando al servidor...
 net use Z: \\opengroupsa.com@SSL@2078\DavWWWRoot * /user:ftp2@opengroupsa.com
)

echo [*] Creando carpetas en el servidor...
if not exist "Z:\blog" mkdir "Z:\blog"
if not exist "Z:\blog\data" mkdir "Z:\blog\data"
if not exist "Z:\blog\data\posts" mkdir "Z:\blog\data\posts"
if not exist "Z:\admin" mkdir "Z:\admin"

echo [*] Subiendo archivos...

:: === HTML raiz ===
copy /Y "C:\xampp\htdocs\open-2026\*.html" "Z:\"

:: === Blog ===
copy /Y "C:\xampp\htdocs\open-2026\blog\index.php" "Z:\blog\"
copy /Y "C:\xampp\htdocs\open-2026\blog\data\init.php" "Z:\blog\data\"
copy /Y "C:\xampp\htdocs\open-2026\blog\data\posts.json" "Z:\blog\data\"
copy /Y "C:\xampp\htdocs\open-2026\blog\data\posts\*.json" "Z:\blog\data\posts\"
if exist "C:\xampp\htdocs\open-2026\blog\.htaccess" copy /Y "C:\xampp\htdocs\open-2026\blog\.htaccess" "Z:\blog\.htaccess" 2>nul
if exist "C:\xampp\htdocs\open-2026\blog\data\.htaccess" copy /Y "C:\xampp\htdocs\open-2026\blog\data\.htaccess" "Z:\blog\data\.htaccess" 2>nul

:: === Admin ===
copy /Y "C:\xampp\htdocs\open-2026\admin\*.php" "Z:\admin\"
if exist "C:\xampp\htdocs\open-2026\admin\.htaccess" copy /Y "C:\xampp\htdocs\open-2026\admin\.htaccess" "Z:\admin\.htaccess" 2>nul

:: === Assets (imagenes nosotros) ===
copy /Y "C:\xampp\htdocs\open-2026\assets\img\nosotros\*.webp" "Z:\assets\img\nosotros\"

echo.
echo [OK] Archivos subidos
pause

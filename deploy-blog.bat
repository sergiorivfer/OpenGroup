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

echo [*] Subiendo archivos...

:: === HTML raiz ===
copy /Y "C:\xampp\htdocs\open-2026\*.html" "Z:\"
copy /Y "C:\xampp\htdocs\open-2026\menu.html" "Z:\"

:: === Blog (router + datos + htaccess) ===
copy /Y "C:\xampp\htdocs\open-2026\blog\index.php" "Z:\blog\"
copy /Y "C:\xampp\htdocs\open-2026\blog\.htaccess" "Z:\blog\"
xcopy /Y /E /I "C:\xampp\htdocs\open-2026\blog\data" "Z:\blog\data\"

:: === Admin (panel + auth) ===
copy /Y "C:\xampp\htdocs\open-2026\admin\*.php" "Z:\admin\"
copy /Y "C:\xampp\htdocs\open-2026\admin\.htaccess" "Z:\admin\"

echo.
echo [OK] Archivos subidos
pause

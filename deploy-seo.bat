@echo off
echo ========================================
echo  DEPLOY - SEO Fixes + PHP cleanup
echo  Open Group - Canonicals + limpieza
echo ========================================
echo.

if not exist Z:\ (
    net use Z: \\opengroupsa.com@SSL@2078\DavWWWRoot * /user:ftp2@opengroupsa.com
)

echo [*] Subiendo canonicals corregidos...
copy /Y "C:\xampp\htdocs\open-2026\centro-servicios.html" "Z:\centro-servicios.html"
copy /Y "C:\xampp\htdocs\open-2026\ciberseguridad.html" "Z:\ciberseguridad.html"
copy /Y "C:\xampp\htdocs\open-2026\comunicaciones.html" "Z:\comunicaciones.html"
copy /Y "C:\xampp\htdocs\open-2026\multicloud.html" "Z:\multicloud.html"
copy /Y "C:\xampp\htdocs\open-2026\trabajos-inteligentes.html" "Z:\trabajos-inteligentes.html"
copy /Y "C:\xampp\htdocs\open-2026\nosotros.html" "Z:\nosotros.html"

echo.
echo [*] Eliminando PHP basura del servidor...
del /Q "Z:\mail-contacto.php" 2>nul
del /Q "Z:\mail-modal.php" 2>nul
del /Q "Z:\mail-trabaja.php" 2>nul
del /Q "Z:\test-smtp.php" 2>nul

echo.
echo [OK] Deploy completado
echo.
echo  Archivos subidos:
echo   - 6 HTMLs (canonicals corregidos)
echo   - 4 PHP eliminados del servidor
echo   - mail-home.php y smtp-config.php conservados
echo.
pause

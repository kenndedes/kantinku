@echo off
echo ========================================
echo  E-Canteen - Git Setup Helper
echo ========================================
echo.

REM Check if Git is installed
git --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Git tidak terinstall!
    echo Download di: https://git-scm.com/download/win
    pause
    exit /b 1
)

echo [OK] Git terdeteksi
echo.

REM Initialize Git if not exists
if not exist .git (
    echo [SETUP] Initialize Git repository...
    git init
    echo [OK] Git repository initialized
) else (
    echo [OK] Git repository sudah ada
)

echo.
echo ========================================
echo  Instructions:
echo ========================================
echo.
echo 1. Buat GitHub repository:
echo    https://github.com/new
echo.
echo 2. Copy URL repository Anda
echo    Contoh: https://github.com/username/kantinku.git
echo.
echo 3. Run command ini (ganti USERNAME):
echo.
echo    git add .
echo    git commit -m "Initial commit - E-Canteen dengan Xoftware QRIS"
echo    git remote add origin https://github.com/USERNAME/kantinku.git
echo    git branch -M main
echo    git push -u origin main
echo.
echo 4. Lanjut deploy ke Railway:
echo    https://railway.app/new
echo.
echo ========================================
pause

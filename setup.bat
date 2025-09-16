@echo off
echo ====================================
echo Student Helper AI - Setup Script
echo ====================================
echo.

echo 1. Checking Python installation...
python --version
if %errorlevel% neq 0 (
    echo ERROR: Python is not installed or not in PATH
    echo Please install Python 3.9+ from https://python.org
    pause
    exit /b 1
)

echo.
echo 2. Creating virtual environment...
if not exist ".venv" (
    python -m venv .venv
    echo Virtual environment created successfully.
) else (
    echo Virtual environment already exists.
)

echo.
echo 3. Activating virtual environment...
call .venv\Scripts\activate.bat

echo.
echo 4. Installing dependencies...
pip install -r backend\requirements.txt

echo.
echo 5. Setup completed successfully!
echo.
echo ====================================
echo Next Steps:
echo ====================================
echo 1. Edit .env file to add your API keys (optional)
echo 2. Run: start_server.bat
echo 3. Open frontend\index.html in your browser
echo.
echo Note: The app works with mock responses even without API keys!
echo ====================================
pause
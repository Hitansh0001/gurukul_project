@echo off
echo ====================================
echo Student Helper AI - Starting Server
echo ====================================
echo.

echo Starting FastAPI server on http://localhost:8000
echo.
echo ====================================
echo Server Commands:
echo ====================================
echo - Health Check: http://localhost:8000/health
echo - API Docs: http://localhost:8000/docs
echo - Admin Panel: http://localhost:8000/redoc
echo.
echo Press Ctrl+C to stop the server
echo ====================================
echo.

cd /d "%~dp0"
python server.py

pause
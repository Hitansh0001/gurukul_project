# 🚀 Quick Start Guide

## Step 1: Setup Environment

```powershell
# Create virtual environment
python -m venv .venv

# Activate virtual environment (Windows PowerShell)
.\.venv\Scripts\Activate.ps1

# Run setup script
python setup.py
```

## Step 2: Get Your API Keys

### 🤖 Gemini API Key (Required)
1. Go to: https://makersuite.google.com/app/apikey
2. Sign in with Google account
3. Click "Create API Key"
4. Copy your API key

### 📺 YouTube API Key (Optional)
1. Go to: https://console.cloud.google.com/apis/api/youtube.googleapis.com
2. Enable YouTube Data API v3
3. Create credentials → API Key
4. Copy your API key

## Step 3: Input API Keys (Choose ONE method)

### Method 1: Environment File (.env) - RECOMMENDED
```powershell
# Edit the .env file that was created
notepad .env
```

**In the .env file, replace the placeholder:**
```
GEMINI_API_KEY=your_actual_gemini_api_key_here
YOUTUBE_API_KEY=your_actual_youtube_api_key_here
```

### Method 2: Config Files (Alternative)
```powershell
# Create API key files in config folder
echo "your_actual_gemini_api_key_here" > config/gemini_api_key.txt
echo "your_actual_youtube_api_key_here" > config/youtube_api_key.txt
```

## Step 4: Run the Application

```powershell
# Start the backend server
uvicorn backend.main:app --host 0.0.0.0 --port 8000 --reload
```

## Step 5: Open Frontend

1. Open `frontend/index.html` in your web browser
2. Test by typing a message and clicking "Process Text"

## 🔧 Troubleshooting

**If Python is not found:**
- Install Python from https://python.org
- Or install from Microsoft Store

**If setup fails:**
- Make sure you're in the virtual environment
- Check that `backend/requirements.txt` exists

**If API doesn't work:**
- Check your API keys are correct in `.env` file
- Ensure no extra spaces around the API keys
- Restart the server after changing API keys

## 🎯 Success Indicators

✅ Setup script completes without errors
✅ `.env` file exists with your API keys
✅ Server starts on http://localhost:8000
✅ Frontend shows your AI responses (not mock responses)

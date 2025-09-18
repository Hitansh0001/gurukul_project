# 🔑 API Keys Setup Guide

## 📋 **Required API Keys**

To enable YouTube video recommendations and real AI responses, you need to obtain the following API keys:

### 1. **Gemini AI API Key** 🤖

**Get your key from:** https://aistudio.google.com/app/apikey

**Steps:**
1. Go to [Google AI Studio](https://aistudio.google.com/app/apikey)
2. Sign in with your Google account
3. Click "Create API Key"
4. Copy the generated API key
5. Add it to your `.env` file: `GEMINI_API_KEY=your_actual_key_here`

### 2. **YouTube Data API Key** 📺

**Get your key from:** https://console.cloud.google.com/apis/credentials

**Steps:**
1. Go to [Google Cloud Console](https://console.cloud.google.com/apis/credentials)
2. Create a new project or select an existing one
3. Enable the "YouTube Data API v3":
   - Go to "APIs & Services" → "Library"
   - Search for "YouTube Data API v3"
   - Click "Enable"
4. Create credentials:
   - Go to "APIs & Services" → "Credentials"
   - Click "Create Credentials" → "API Key"
   - Copy the generated API key
5. Add it to your `.env` file: `YOUTUBE_API_KEY=your_actual_key_here`

## 🛠️ **Setup Instructions**

### Step 1: Edit your .env file
Open the `.env` file in your project root and replace the placeholder values:

```env
# Replace these with your actual API keys
GEMINI_API_KEY=AIzaSyBxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
YOUTUBE_API_KEY=AIzaSyBxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

# Optional: Keep these default values
GEMINI_MODEL=gemini-pro
MAX_TOKENS=1000
AI_TEMPERATURE=0.7
PORT=8000
HOST=0.0.0.0
```

### Step 2: Install required dependencies
Make sure you have the required libraries installed:

```bash
# Activate your virtual environment first
.venv\Scripts\Activate.ps1

# Install all dependencies
pip install -r backend/requirements.txt
```

### Step 3: Restart your server
After adding the API keys, restart your backend server:

```bash
# Stop the current server (Ctrl+C)
# Then restart
start_server.bat
```

## ✅ **Verification**

### Test if APIs are working:

1. **Check API Status:**
   - Open the AI Chat page
   - Look for the green status indicators in the API Status section

2. **Test AI Responses:**
   - Ask a question in the AI chat
   - You should get more detailed, contextual responses

3. **Test YouTube Recommendations:**
   - Ask about a specific topic (e.g., "calculus derivatives")
   - You should see related YouTube videos below the AI response

## 🚨 **Troubleshooting**

### If APIs are not working:

1. **Check API Keys:**
   - Ensure there are no extra spaces or quotes around the keys
   - Verify the keys are correctly copied from the respective consoles

2. **Check API Quotas:**
   - Gemini AI: Has generous free tier limits
   - YouTube API: 10,000 quota units per day (free tier)

3. **Check Console Errors:**
   - Open browser developer tools (F12)
   - Look for any error messages in the console

4. **Verify Server Logs:**
   - Check the terminal where your server is running
   - Look for initialization messages:
     - ✅ Gemini AI initialized with model: gemini-pro
     - ✅ YouTube API initialized

### Common Issues:

- **"Mock responses"**: API keys not found or invalid
- **"Failed to initialize"**: Library not installed or API key invalid
- **Rate limiting**: Too many requests, wait a few minutes

## 🔒 **Security Notes**

- **Never commit API keys to version control**
- **Keep your .env file private**
- **Consider using environment variables in production**
- **Monitor your API usage to avoid unexpected charges**

## 💡 **Free Tier Limits**

- **Gemini AI**: 60 requests per minute, generous monthly quota
- **YouTube Data API**: 10,000 quota units per day
  - Each search costs ~100 units
  - Each video details request costs ~1 unit

With proper setup, you'll get:
- ✨ Real AI responses instead of mock responses
- 📺 Actual YouTube video recommendations with thumbnails
- 🎯 Context-aware educational content

Happy learning! 🚀
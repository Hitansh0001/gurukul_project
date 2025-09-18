# Student Helper AI - Setup & Running Guide

## 🚀 Quick Start

### 1. **Setup (First Time Only)**
```bash
# Run the setup script
setup.bat
```

### 2. **Start the Backend Server**
```bash
# Start the FastAPI backend
start_server.bat
```

### 3. **Open the Frontend**
- Open `frontend/index.html` in your web browser
- Or navigate to any page directly:
  - `frontend/dashboard.html`
  - `frontend/ai-chat.html`

## 🔧 Manual Setup (Alternative)

### Prerequisites
- Python 3.9+
- Web browser

### Backend Setup
```bash
# 1. Create virtual environment
python -m venv .venv

# 2. Activate virtual environment
.venv\Scripts\activate.bat     # Windows
# or
source .venv/bin/activate      # Linux/Mac

# 3. Install dependencies
pip install -r backend/requirements.txt

# 4. Start server
python -m uvicorn backend.main:app --host 0.0.0.0 --port 8000 --reload
```

## 🔑 API Keys (Optional)

The application works with **mock responses** by default. To enable real AI responses:

1. **Edit `.env` file:**
   ```env
   # Get from: https://makersuite.google.com/app/apikey
   GEMINI_API_KEY=your_actual_gemini_key_here
   
   # Get from: https://console.developers.google.com/
   YOUTUBE_API_KEY=your_actual_youtube_key_here
   ```

<<<<<<< HEAD
2. **Restart the server** after adding keys

## 🌐 Endpoints

Once the server is running:

- **Health Check:** http://localhost:8000/health
- **API Documentation:** http://localhost:8000/docs
- **AI Chat:** http://localhost:8000/api/process-text
- **YouTube Search:** http://localhost:8000/api/youtube-recommendations

## 🎯 Features

### ✅ **Working Features:**
- 📱 **Responsive Web Interface**
- 🤖 **AI Chat with Backend Integration**
- 📊 **API Status Monitoring**
- 📝 **Notes Management** (Frontend)
- ✅ **Task Management** (Frontend)
- 📅 **Schedule View** (Frontend)
- 🎨 **Modern Glass-morphism Design**

### 🔄 **AI Integration:**
- **Real AI Responses** (when API keys provided)
- **Smart Fallback** (mock responses when offline)
- **Context-Aware** responses
- **Error Handling** with graceful degradation

## 🛠️ Troubleshooting

### Backend Issues:
```bash
# Check if server is running
curl http://localhost:8000/health

# Check logs in terminal
# Look for any error messages
```

### Frontend Issues:
- **Open browser console** (F12)
- **Check for CORS errors**
- **Verify API endpoint** in browser network tab

### Common Solutions:
1. **Port 8000 in use:** Change port in start_server.bat
2. **CORS errors:** Check backend CORS configuration
3. **API not responding:** Restart the server
4. **Dependencies missing:** Re-run pip install

## 📁 Project Structure

```
alex-/
├── backend/                 # FastAPI backend
│   ├── main.py             # Main server file
│   ├── services/           # AI & YouTube services
│   └── requirements.txt    # Python dependencies
├── frontend/               # Static web interface
│   ├── index.html         # Landing page
│   ├── dashboard.html     # Main dashboard
│   ├── ai-chat.html       # AI chat interface
│   ├── style.css          # Styling
│   └── *.js               # JavaScript functionality
├── .env                   # Environment variables
├── setup.bat             # Automated setup
└── start_server.bat      # Server launcher
```

## 💡 Tips

1. **Test API Status** using the dashboard's API monitoring section
2. **Use Fallback Mode** when developing without API keys
3. **Check Browser Console** for any JavaScript errors
4. **Monitor Server Logs** for backend issues
5. **Use API Documentation** at `/docs` for testing endpoints

## 🆘 Support

If you encounter issues:
1. Check the console logs (F12 in browser)
2. Verify the backend server is running
3. Test API endpoints manually using `/docs`
4. Check the `.env` file configuration
=======
## Notes
- CORS is enabled for development; adjust origins for production.
- The YouTube API requires an API key. Without it, mock results are returned.
- The AI service uses OpenAI Chat Completions if OPENAI_API_KEY is set, otherwise mock.
>>>>>>> gurukul/main

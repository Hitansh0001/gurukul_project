# AI Integration Template

A starter template that provides:
- Frontend (HTML/CSS/JS) for user text input, AI text output, and YouTube recommendations with thumbnails and clickable links
- Backend (Python FastAPI) with endpoints for AI text processing and YouTube search
- Mock fallbacks when API keys are not configured
- Clean separation so the frontend can integrate with any backend language by matching the API contract

## Features
- POST /api/process-text — returns AI-generated text
- POST /api/youtube-recommendations — returns a list of YouTube videos with thumbnails and links
- POST /api/combined-response — returns both text and related videos
- GET /health — health check

## Project Structure
- backend/ — FastAPI app, services
- frontend/ — static HTML, CSS, JS
- docs/ — documentation
- examples/ — sample integration snippets for other backends

## Getting Started (Python backend)
1) Create and activate a virtual environment
   - Windows (PowerShell):
     python -m venv .venv
     .venv\Scripts\Activate.ps1

2) Install dependencies
   pip install -r backend/requirements.txt

3) Configure environment variables
   - Copy .env.example to .env and fill in values as needed

4) Run the backend server
   uvicorn backend.main:app --host 0.0.0.0 --port 8000 --reload

5) Open the frontend
   - Open frontend/index.html in your browser
   - Set API Endpoint (gear icon) if different from http://localhost:8000

## API Contract (so any backend language can be used)
- POST /api/process-text
  Request: { "text": string, "context"?: string }
  Response: { "response": string, "timestamp": string }

- POST /api/youtube-recommendations
  Request: { "query": string, "max_results"?: number }
  Response: Array<{ "title": string, "video_id": string, "thumbnail_url": string, "channel_name": string, "url": string }>

- POST /api/combined-response
  Request: { "text": string, "context"?: string }
  Response: {
    "text_response": { "response": string, "timestamp": string },
    "youtube_recommendations": Array<...same as above>
  }

If you prefer another backend language (Node.js, Go, Java, C#, etc.), implement the same endpoints and response shapes above.

## Security and Keys
- Do NOT hardcode secrets.
- Use environment variables (see .env.example). The app will work in mock mode without keys.

## Notes
- CORS is enabled for development; adjust origins for production.
- The YouTube API requires an API key. Without it, mock results are returned.
- The AI service uses OpenAI Chat Completions if OPENAI_API_KEY is set, otherwise mock.

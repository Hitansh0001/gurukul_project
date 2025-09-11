# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Essential Commands

### Development Setup
```bash
# Quick setup (Windows PowerShell)
python -m venv .venv
.\.venv\Scripts\Activate.ps1
python setup.py

# Manual setup alternative
pip install -r backend/requirements.txt
cp .env.example .env  # Then edit .env with API keys and AI provider

# Configure Gemini API key (choose one method)
# Method 1: Environment variable (recommended)
echo 'GEMINI_API_KEY=your_gemini_api_key_here' >> .env

# Method 2: Config file (development only)
echo 'your_gemini_api_key_here' > config/gemini_api_key.txt
echo 'your_youtube_api_key_here' > config/youtube_api_key.txt
```

### Running the Application
```bash
# Start backend server (development mode with auto-reload)
uvicorn backend.main:app --host 0.0.0.0 --port 8000 --reload

# Start backend server (production mode)
uvicorn backend.main:app --host 0.0.0.0 --port 8000

# Health check
curl http://localhost:8000/health
```

### Development Testing
```bash
# Test API endpoints manually
curl -X POST http://localhost:8000/api/process-text -H "Content-Type: application/json" -d '{"text":"hello world"}'
curl -X POST http://localhost:8000/api/youtube-recommendations -H "Content-Type: application/json" -d '{"query":"python tutorial","max_results":3}'

# Check service configuration
curl http://localhost:8000/api/service-info
```

## Architecture Overview

This is an **AI Integration Template** that demonstrates a clean separation between frontend and backend with a standardized API contract. The architecture is designed to be **backend-agnostic** - the frontend can work with any backend language that implements the same API endpoints.

### Core Components

**Backend Service Layer:**
- `backend/main.py` - FastAPI application with CORS configuration and API endpoints
- `backend/services/gemini_service.py` - Gemini AI text processing service with Google AI integration and mock fallbacks
- `backend/services/youtube_service.py` - YouTube video recommendation service with API integration and mock fallbacks
- `backend/requirements.txt` - Python dependencies including FastAPI, Google Generative AI, Google API client

**Frontend:**
- `frontend/index.html` - Single-page application with responsive UI
- `frontend/script.js` - JavaScript client that communicates with backend via REST API
- `frontend/styles.css` - Styled UI components
- Frontend is completely decoupled from backend implementation

**API Contract:**
The system defines a strict API contract that any backend must implement:
- `POST /api/process-text` - AI text processing
- `POST /api/youtube-recommendations` - YouTube video search  
- `POST /api/combined-response` - Both text processing and video recommendations
- `GET /health` - Health check endpoint

### Key Architectural Patterns

**Graceful Degradation:**
- Both AI and YouTube services have mock modes when API keys are not configured
- Application works fully without any external API dependencies
- Error handling with fallbacks at every API integration point

**Service Abstraction:**
- Gemini AI service provides Google's latest AI capabilities
- YouTube service encapsulates Google API complexity
- Services handle async operations and timeouts

**Cross-Platform Backend Support:**
- `examples/` contains Java Spring Boot and Node.js implementations
- Any backend language can implement the API contract
- Frontend remains unchanged regardless of backend choice

## Environment Configuration

The application uses environment variables for configuration:

**API Keys:**
- `GEMINI_API_KEY` - For Google Gemini AI (required for AI features)
- `YOUTUBE_API_KEY` - For video recommendations (falls back to mock results)

**AI Model Configuration:**
- `GEMINI_MODEL` - Gemini model selection (default: gemini-pro)
- `MAX_TOKENS` - AI response length limit (default: 1000)
- `AI_TEMPERATURE` - AI creativity setting (default: 0.7)

**Server Configuration:**
- `HOST`, `PORT` - Server binding configuration
- `ALLOWED_ORIGINS` - CORS configuration

**Alternative Configuration (Development Only):**
API keys can also be stored in the `config/` folder:
- `config/gemini_api_key.txt` - Gemini API key
- `config/youtube_api_key.txt` - YouTube API key

## Implementation Notes

**When extending the AI service:**
- New AI features should extend the `GeminiService` class
- Always provide mock responses for development without API keys
- Handle rate limiting and API errors gracefully
- Support both environment variable and config file API key loading

**When adding new API endpoints:**
- Follow the existing pattern in `main.py` with Pydantic models for request/response
- Update the root endpoint documentation
- Ensure CORS is configured appropriately
- Add corresponding frontend functionality in `script.js`

**When working with the frontend:**
- API endpoint configuration is stored in localStorage and configurable via UI
- All API calls include proper error handling and loading states
- UI components are responsive and accessible

**Multi-language backend development:**
- Reference `examples/JavaBackendController.java` for Spring Boot implementation
- Reference `examples/nodejs-backend.js` for Express.js implementation
- Maintain exact API contract compliance for frontend compatibility

## Development Workflow

1. **Environment Setup:** Always use virtual environment and run `setup.py` for automated dependency installation
2. **Gemini API Key:** Get your free key from https://makersuite.google.com/app/apikey
3. **Development Server:** Use `--reload` flag for live code changes during development
4. **Frontend Testing:** Open `frontend/index.html` directly in browser - no build process required
5. **API Testing:** Backend includes built-in API documentation at root endpoint and health checks

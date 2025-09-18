#!/usr/bin/env python3
import sys
import os
from pathlib import Path
from contextlib import asynccontextmanager
from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import List, Optional
from dotenv import load_dotenv
import uvicorn

# Setup
project_root = Path(__file__).parent
sys.path.insert(0, str(project_root))
load_dotenv()

from backend.services.simple_gemini_service import GeminiService
from backend.services.simple_youtube_service import YouTubeService

# Initialize services
ai_service = GeminiService()
youtube_service = YouTubeService()

@asynccontextmanager
async def lifespan(app: FastAPI):
    print("🌟 Server starting up...")
    print(f"AI Service: {ai_service.get_service_info()['service']}")
    print(f"YouTube Service: {'✅ Configured' if youtube_service.is_configured() else '⚠️ Mock Mode'}")
    print("🚀 Server ready!")
    yield
    print("👋 Server shutting down...")

# Create FastAPI app
app = FastAPI(
    title="AI Integration Template API",
    description="A template for AI integration with text processing and YouTube recommendations",
    version="1.0.0",
    lifespan=lifespan
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Pydantic models
class TextRequest(BaseModel):
    text: str
    context: Optional[str] = None

class TextResponse(BaseModel):
    response: str
    timestamp: str

class YouTubeRecommendation(BaseModel):
    title: str
    video_id: str
    thumbnail_url: str
    channel_name: str
    view_count: Optional[int] = None
    duration: Optional[str] = None
    url: str

class YouTubeRequest(BaseModel):
    query: str
    max_results: Optional[int] = 10

# API Endpoints
@app.get("/")
async def root():
    """Root endpoint with API information"""
    return {
        "message": "AI Integration Template API",
        "version": "1.0.0",
        "endpoints": {
            "text_processing": "/api/process-text",
            "youtube_recommendations": "/api/youtube-recommendations",
            "health": "/health"
        }
    }

@app.get("/health")
async def health_check():
    """Health check endpoint"""
    return {"status": "healthy", "service": "AI Integration Template API"}

@app.get("/api/service-info")
async def get_service_info():
    """Get information about configured AI services"""
    return {
        "ai_service": ai_service.get_service_info(),
        "youtube_service": {"configured": bool(os.getenv("YOUTUBE_API_KEY"))}
    }

@app.post("/api/process-text", response_model=TextResponse)
async def process_text(request: TextRequest):
    """Process text input and return AI-generated response"""
    try:
        response = await ai_service.process_text(request.text, request.context)
        return response
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Text processing failed: {str(e)}")

@app.post("/api/youtube-recommendations", response_model=List[YouTubeRecommendation])
async def get_youtube_recommendations(request: YouTubeRequest):
    """Get YouTube video recommendations based on query"""
    try:
        recommendations = await youtube_service.get_recommendations(
            request.query, 
            max_results=request.max_results or 10
        )
        return recommendations
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"YouTube recommendations failed: {str(e)}")

@app.post("/api/combined-response")
async def get_combined_response(request: TextRequest):
    """Get both AI text response and related YouTube recommendations"""
    try:
        # Process text with AI
        text_response = await ai_service.process_text(request.text, request.context)
        
        # Get YouTube recommendations based on the input
        youtube_recommendations = await youtube_service.get_recommendations(
            request.text, 
            max_results=5
        )
        
        return {
            "text_response": text_response,
            "youtube_recommendations": youtube_recommendations
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Combined response failed: {str(e)}")



if __name__ == "__main__":
    import os
    port = int(os.getenv("PORT", 8000))
    uvicorn.run(app, host="0.0.0.0", port=port, log_level="info")
import os
import openai
from datetime import datetime
from typing import Optional
import asyncio
import httpx

class AIService:
    """
    AI Service for processing text input and generating responses.
    This is designed to be easily replaceable with different AI providers.
    """
    
    def __init__(self):
        self.api_key = os.getenv("OPENAI_API_KEY")
        self.model = os.getenv("AI_MODEL", "gpt-3.5-turbo")
        self.max_tokens = int(os.getenv("MAX_TOKENS", "1000"))
        self.temperature = float(os.getenv("AI_TEMPERATURE", "0.7"))
        
        # Initialize OpenAI client if API key is available
        if self.api_key:
            openai.api_key = self.api_key
    
    async def process_text(self, text: str, context: Optional[str] = None) -> dict:
        """
        Process text input and return AI-generated response
        
        Args:
            text (str): User input text
            context (str, optional): Additional context for the AI
            
        Returns:
            dict: Contains response text and timestamp
        """
        
        # If no API key is provided, return a mock response
        if not self.api_key:
            return await self._mock_response(text, context)
        
        try:
            # Prepare messages for the AI
            messages = []
            
            if context:
                messages.append({
                    "role": "system",
                    "content": context
                })
            
            messages.append({
                "role": "user", 
                "content": text
            })
            
            # Make async API call to OpenAI
            response = await self._make_openai_request(messages)
            
            return {
                "response": response,
                "timestamp": datetime.now().isoformat()
            }
            
        except Exception as e:
            # Fallback to mock response if API fails
            print(f"AI API Error: {str(e)}")
            return await self._mock_response(text, context, error=True)
    
    async def _make_openai_request(self, messages: list) -> str:
        """
        Make asynchronous request to OpenAI API
        """
        async with httpx.AsyncClient() as client:
            response = await client.post(
                "https://api.openai.com/v1/chat/completions",
                headers={
                    "Authorization": f"Bearer {self.api_key}",
                    "Content-Type": "application/json"
                },
                json={
                    "model": self.model,
                    "messages": messages,
                    "max_tokens": self.max_tokens,
                    "temperature": self.temperature
                },
                timeout=30.0
            )
            
            if response.status_code == 200:
                data = response.json()
                return data["choices"][0]["message"]["content"].strip()
            else:
                raise Exception(f"OpenAI API error: {response.status_code} - {response.text}")
    
    async def _mock_response(self, text: str, context: Optional[str] = None, error: bool = False) -> dict:
        """
        Generate a mock response for testing or when API is not available
        """
        # Simulate API delay
        await asyncio.sleep(0.5)
        
        if error:
            mock_text = f"I apologize, but I'm currently experiencing technical difficulties. However, I can see you asked about: '{text[:100]}...' Please try again later or check your API configuration."
        else:
            mock_text = f"This is a mock AI response to your input: '{text[:100]}...'. " + \
                       f"To enable real AI responses, please set your OPENAI_API_KEY environment variable. " + \
                       f"Context provided: {context if context else 'None'}"
        
        return {
            "response": mock_text,
            "timestamp": datetime.now().isoformat()
        }
    
    def get_service_info(self) -> dict:
        """
        Get information about the AI service configuration
        """
        return {
            "model": self.model,
            "max_tokens": self.max_tokens,
            "temperature": self.temperature,
            "api_configured": bool(self.api_key),
            "service": "OpenAI GPT"
        }

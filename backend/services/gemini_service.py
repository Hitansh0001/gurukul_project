import os
import google.generativeai as genai
from datetime import datetime
from typing import Optional
import asyncio
from pathlib import Path

class GeminiService:
    """
    Gemini AI Service for processing text input and generating responses.
    Uses Google's Gemini API for AI text generation.
    """
    
    def __init__(self):
        self.api_key = self._get_api_key()
        self.model_name = os.getenv("GEMINI_MODEL", "gemini-pro")
        self.max_tokens = int(os.getenv("MAX_TOKENS", "1000"))
        self.temperature = float(os.getenv("AI_TEMPERATURE", "0.7"))
        
        # Configure Gemini API if key is available
        if self.api_key:
            genai.configure(api_key=self.api_key)
            self.model = genai.GenerativeModel(self.model_name)
    
    def _get_api_key(self) -> Optional[str]:
        """
        Get Gemini API key from environment variable or config file
        """
        # First, try environment variable
        api_key = os.getenv("GEMINI_API_KEY")
        if api_key:
            return api_key
        
        # Then try config file
        config_file = Path(__file__).parent.parent.parent / "config" / "gemini_api_key.txt"
        if config_file.exists():
            try:
                return config_file.read_text().strip()
            except Exception as e:
                print(f"Error reading Gemini API key from config file: {e}")
        
        return None
    
    async def process_text(self, text: str, context: Optional[str] = None) -> dict:
        """
        Process text input and return Gemini AI-generated response
        
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
            # Prepare the prompt
            prompt = text
            if context:
                prompt = f"Context: {context}\n\nUser: {text}"
            
            # Configure generation parameters
            generation_config = genai.types.GenerationConfig(
                max_output_tokens=self.max_tokens,
                temperature=self.temperature,
            )
            
            # Generate response using Gemini
            response = await self._make_gemini_request(prompt, generation_config)
            
            return {
                "response": response,
                "timestamp": datetime.now().isoformat()
            }
            
        except Exception as e:
            # Fallback to mock response if API fails
            print(f"Gemini API Error: {str(e)}")
            return await self._mock_response(text, context, error=True)
    
    async def _make_gemini_request(self, prompt: str, generation_config) -> str:
        """
        Make asynchronous request to Gemini API
        """
        try:
            # Run the synchronous Gemini API call in a thread pool
            loop = asyncio.get_event_loop()
            response = await loop.run_in_executor(
                None, 
                lambda: self.model.generate_content(
                    prompt, 
                    generation_config=generation_config
                )
            )
            
            if response.text:
                return response.text.strip()
            else:
                raise Exception("No response text generated")
                
        except Exception as e:
            raise Exception(f"Gemini API request failed: {str(e)}")
    
    async def _mock_response(self, text: str, context: Optional[str] = None, error: bool = False) -> dict:
        """
        Generate a mock response for testing or when API is not available
        """
        # Simulate API delay
        await asyncio.sleep(0.5)
        
        if error:
            mock_text = f"I apologize, but I'm currently experiencing technical difficulties with the Gemini AI service. However, I can see you asked about: '{text[:100]}...' Please try again later or check your Gemini API configuration."
        else:
            mock_text = f"This is a mock Gemini AI response to your input: '{text[:100]}...'. " + \
                       f"To enable real Gemini AI responses, please set your GEMINI_API_KEY environment variable or add it to the config/gemini_api_key.txt file. " + \
                       f"Context provided: {context if context else 'None'}"
        
        return {
            "response": mock_text,
            "timestamp": datetime.now().isoformat()
        }
    
    def get_service_info(self) -> dict:
        """
        Get information about the Gemini AI service configuration
        """
        return {
            "provider": "Google Gemini",
            "model": self.model_name,
            "max_tokens": self.max_tokens,
            "temperature": self.temperature,
            "api_configured": bool(self.api_key),
            "service": "Google Gemini AI"
        }
    
    def is_configured(self) -> bool:
        """
        Check if the Gemini service is properly configured
        """
        return bool(self.api_key)

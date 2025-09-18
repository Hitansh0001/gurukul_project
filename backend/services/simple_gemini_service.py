import os
import os
from datetime import datetime
from typing import Optional
import asyncio
from pathlib import Path
from dotenv import load_dotenv

load_dotenv()

try:
    import google.generativeai as genai
    GENAI_AVAILABLE = True
except ImportError:
    GENAI_AVAILABLE = False

class GeminiService:
    def __init__(self):
        self.api_key = self._get_api_key()
        self.model_name = os.getenv("GEMINI_MODEL", "gemini-1.5-flash")
        self.max_tokens = int(os.getenv("MAX_TOKENS", "1000"))
        self.temperature = float(os.getenv("AI_TEMPERATURE", "0.7"))
        self.model = None
        
        if self.api_key and GENAI_AVAILABLE:
            try:
                genai.configure(api_key=self.api_key)
                self.model = genai.GenerativeModel(self.model_name)
            except Exception as e:
                print(f"Failed to initialize Gemini: {e}")
                self.model = None
    
    def _get_api_key(self) -> Optional[str]:
        api_key = os.getenv("GEMINI_API_KEY")
        if api_key:
            return api_key
        
        config_file = Path(__file__).parent.parent.parent / "config" / "gemini_api_key.txt"
        if config_file.exists():
            try:
                return config_file.read_text().strip()
            except Exception:
                pass
        return None
    
    async def process_text(self, text: str, context: Optional[str] = None) -> dict:
        if self.model and self.api_key:
            try:
                return await self._real_gemini_response(text, context)
            except Exception:
                return await self._mock_response(text, context, error=True)
        else:
            return await self._mock_response(text, context)
    
    async def _mock_response(self, text: str, context: Optional[str] = None, error: bool = False) -> dict:
        await asyncio.sleep(0.5)
        
        if error:
            mock_text = "I'm currently experiencing technical difficulties. Please try again later."
        else:
            mock_text = f"Thanks for your question: '{text}'. I'm here to help with your studies!"
            if context:
                mock_text += f" (Context: {context})"
        
        return {
            "response": mock_text,
            "timestamp": datetime.now().isoformat()
        }
    
    async def _real_gemini_response(self, text: str, context: Optional[str] = None) -> dict:
        """
        Get response from real Gemini API
        """
        if not GENAI_AVAILABLE or not self.model:
            raise Exception("Gemini API not available")
            
        try:
            # Prepare the prompt with context
            prompt = f"You are a helpful AI tutor for students. Please provide educational assistance.\n\n"
            if context:
                prompt += f"Context: {context}\n\n"
            prompt += f"Student question: {text}\n\nPlease provide a helpful, educational response:"
            
            # Generate response using Gemini
            import google.generativeai as genai  # type: ignore
            response = await asyncio.to_thread(
                self.model.generate_content,
                prompt,
                generation_config=genai.types.GenerationConfig(  # type: ignore
                    max_output_tokens=self.max_tokens,
                    temperature=self.temperature,
                )
            )
            
            return {
                "response": response.text,
                "timestamp": datetime.now().isoformat()
            }
            
        except Exception as e:
            print(f"Gemini API error: {e}")
            raise e
    
    def get_service_info(self) -> dict:
        """
        Get information about the AI service configuration
        """
        if self.model and self.api_key:
            return {
                "provider": "Google Gemini AI",
                "model": self.model_name,
                "max_tokens": self.max_tokens,
                "temperature": self.temperature,
                "api_configured": True,
                "service": "Real Gemini AI Service",
                "note": "Using real Gemini API"
            }
        else:
            return {
                "provider": "Mock AI Service",
                "model": "educational-assistant",
                "max_tokens": self.max_tokens,
                "temperature": self.temperature,
                "api_configured": bool(self.api_key),
                "service": "Student Helper AI Mock Service",
                "note": "Using mock responses - check API key configuration"
            }
    
    def is_configured(self) -> bool:
        """
        Check if the service is properly configured (always true for mock)
        """
        return True
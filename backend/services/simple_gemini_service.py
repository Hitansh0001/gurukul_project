import os
from datetime import datetime
from typing import Optional
import asyncio
from pathlib import Path

class GeminiService:
    """
    Simplified Gemini AI Service that works with mock responses.
    This version doesn't require the Google AI libraries to be installed.
    """
    
    def __init__(self):
        self.api_key = self._get_api_key()
        self.model_name = os.getenv("GEMINI_MODEL", "gemini-pro")
        self.max_tokens = int(os.getenv("MAX_TOKENS", "1000"))
        self.temperature = float(os.getenv("AI_TEMPERATURE", "0.7"))
    
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
        Process text input and return AI-generated response
        
        Args:
            text (str): User input text
            context (str, optional): Additional context for the AI
            
        Returns:
            dict: Contains response text and timestamp
        """
        
        # For now, always return mock responses since we don't have Google AI installed
        return await self._mock_response(text, context)
    
    async def _mock_response(self, text: str, context: Optional[str] = None, error: bool = False) -> dict:
        """
        Generate a mock response for testing or when API is not available
        """
        # Simulate API delay
        await asyncio.sleep(0.5)
        
        if error:
            mock_text = f"I apologize, but I'm currently experiencing technical difficulties with the AI service. However, I can see you asked about: '{text[:100]}...' Please try again later or check your AI configuration."
        else:
            # Generate context-aware responses
            text_lower = text.lower()
            
            if any(word in text_lower for word in ['calculus', 'derivative', 'integral', 'math', 'algebra']):
                mock_text = f"I'd be happy to help you with your math question about '{text}'. Calculus involves understanding rates of change (derivatives) and accumulation (integrals). What specific concept would you like me to explain?"
            elif any(word in text_lower for word in ['photosynthesis', 'biology', 'plant', 'cellular']):
                mock_text = f"Great biology question! Regarding '{text}', photosynthesis is how plants convert sunlight into energy. The process involves chloroplasts capturing light energy to convert CO₂ and water into glucose and oxygen. Would you like me to explain any specific stage?"
            elif any(word in text_lower for word in ['history', 'historical', 'war', 'revolution', 'ancient']):
                mock_text = f"That's an interesting history question about '{text}'. When studying history, it's important to consider the social, political, and economic contexts of events. What specific historical period or aspect would you like to explore?"
            elif any(word in text_lower for word in ['schedule', 'study', 'plan', 'time management']):
                mock_text = f"I can definitely help with your study planning question: '{text}'. Effective study schedules should include regular breaks, varied subjects, and align with your most productive hours. What subjects are you trying to balance?"
            elif any(word in text_lower for word in ['essay', 'writing', 'paper', 'assignment']):
                mock_text = f"For your writing question about '{text}', good essays need a clear thesis, strong evidence, and logical organization. Start with an outline, support your arguments with credible sources, and always proofread. What type of essay are you working on?"
            elif any(word in text_lower for word in ['chemistry', 'chemical', 'reaction', 'molecule']):
                mock_text = f"Chemistry can be fascinating! Regarding '{text}', chemical reactions involve the breaking and forming of bonds between atoms. Understanding electron configurations and molecular structures is key. What chemistry concept would you like me to clarify?"
            elif any(word in text_lower for word in ['physics', 'force', 'energy', 'motion', 'gravity']):
                mock_text = f"Physics question about '{text}' - great! Physics explains how our universe works through fundamental forces and energy. Whether it's mechanics, thermodynamics, or electromagnetism, everything follows predictable laws. What physics concept interests you?"
            else:
                mock_text = f"Thanks for your question: '{text}'. I'm here to help with your studies! I can assist with math, science, history, writing, study planning, and more. Could you provide more details about what specific help you need?"
            
            # Add context if provided
            if context:
                mock_text += f" (Context considered: {context})"
        
        return {
            "response": mock_text,
            "timestamp": datetime.now().isoformat()
        }
    
    def get_service_info(self) -> dict:
        """
        Get information about the AI service configuration
        """
        return {
            "provider": "Mock AI Service",
            "model": "educational-assistant",
            "max_tokens": self.max_tokens,
            "temperature": self.temperature,
            "api_configured": bool(self.api_key),
            "service": "Student Helper AI Mock Service",
            "note": "Using mock responses - install google-generativeai for real AI"
        }
    
    def is_configured(self) -> bool:
        """
        Check if the service is properly configured (always true for mock)
        """
        return True
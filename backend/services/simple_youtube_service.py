import os
import asyncio
from typing import List, Dict, Any
from datetime import datetime

class YouTubeService:
    """
    Simplified YouTube Service that provides mock video recommendations.
    This version doesn't require the YouTube API to be installed.
    """
    
    def __init__(self):
        self.api_key = os.getenv("YOUTUBE_API_KEY")
    
    async def get_recommendations(self, query: str, max_results: int = 10) -> List[Dict[str, Any]]:
        """
        Get YouTube video recommendations based on query with mock data
        
        Args:
            query (str): Search query
            max_results (int): Maximum number of results to return
            
        Returns:
            List[Dict]: List of video recommendations with thumbnails and links
        """
        
        # For now, return mock data since we don't have YouTube API
        return await self._mock_recommendations(query, max_results)
    
    async def _mock_recommendations(self, query: str, max_results: int) -> List[Dict[str, Any]]:
        """
        Generate mock YouTube recommendations based on the query
        """
        # Simulate API delay
        await asyncio.sleep(0.3)
        
        # Generate contextual mock videos based on query
        query_lower = query.lower()
        
        mock_videos = []
        
        if any(word in query_lower for word in ['calculus', 'derivative', 'integral', 'math']):
            mock_videos = [
                {
                    "title": "Calculus Fundamentals - Derivatives Explained",
                    "video_id": "calc_001",
                    "thumbnail_url": "https://img.youtube.com/vi/calc_001/maxresdefault.jpg",
                    "channel_name": "Khan Academy",
                    "view_count": 1500000,
                    "duration": "12:34",
                    "url": "https://youtube.com/watch?v=calc_001"
                },
                {
                    "title": "Integration Techniques - Step by Step",
                    "video_id": "calc_002", 
                    "thumbnail_url": "https://img.youtube.com/vi/calc_002/maxresdefault.jpg",
                    "channel_name": "Professor Leonard",
                    "view_count": 850000,
                    "duration": "18:45",
                    "url": "https://youtube.com/watch?v=calc_002"
                },
                {
                    "title": "Calculus Applications in Real Life",
                    "video_id": "calc_003",
                    "thumbnail_url": "https://img.youtube.com/vi/calc_003/maxresdefault.jpg", 
                    "channel_name": "3Blue1Brown",
                    "view_count": 2100000,
                    "duration": "15:22",
                    "url": "https://youtube.com/watch?v=calc_003"
                }
            ]
        elif any(word in query_lower for word in ['photosynthesis', 'biology', 'plant']):
            mock_videos = [
                {
                    "title": "Photosynthesis: Light and Dark Reactions",
                    "video_id": "bio_001",
                    "thumbnail_url": "https://img.youtube.com/vi/bio_001/maxresdefault.jpg",
                    "channel_name": "Crash Course Biology",
                    "view_count": 980000,
                    "duration": "11:28",
                    "url": "https://youtube.com/watch?v=bio_001"
                },
                {
                    "title": "How Plants Make Food - Photosynthesis Process",
                    "video_id": "bio_002",
                    "thumbnail_url": "https://img.youtube.com/vi/bio_002/maxresdefault.jpg",
                    "channel_name": "Amoeba Sisters",
                    "view_count": 1200000,
                    "duration": "8:15",
                    "url": "https://youtube.com/watch?v=bio_002"
                }
            ]
        elif any(word in query_lower for word in ['history', 'historical', 'war']):
            mock_videos = [
                {
                    "title": "World War II in 15 Minutes",
                    "video_id": "hist_001",
                    "thumbnail_url": "https://img.youtube.com/vi/hist_001/maxresdefault.jpg",
                    "channel_name": "Crash Course World History",
                    "view_count": 3200000,
                    "duration": "14:52",
                    "url": "https://youtube.com/watch?v=hist_001"
                },
                {
                    "title": "Ancient Civilizations Documentary",
                    "video_id": "hist_002",
                    "thumbnail_url": "https://img.youtube.com/vi/hist_002/maxresdefault.jpg",
                    "channel_name": "History Channel",
                    "view_count": 1800000,
                    "duration": "45:30",
                    "url": "https://youtube.com/watch?v=hist_002"
                }
            ]
        elif any(word in query_lower for word in ['study', 'schedule', 'productivity']):
            mock_videos = [
                {
                    "title": "How to Create the Perfect Study Schedule",
                    "video_id": "study_001",
                    "thumbnail_url": "https://img.youtube.com/vi/study_001/maxresdefault.jpg",
                    "channel_name": "Thomas Frank",
                    "view_count": 750000,
                    "duration": "10:45",
                    "url": "https://youtube.com/watch?v=study_001"
                },
                {
                    "title": "Study Tips That Actually Work",
                    "video_id": "study_002", 
                    "thumbnail_url": "https://img.youtube.com/vi/study_002/maxresdefault.jpg",
                    "channel_name": "Ali Abdaal",
                    "view_count": 2500000,
                    "duration": "16:30",
                    "url": "https://youtube.com/watch?v=study_002"
                }
            ]
        else:
            # Default educational videos
            mock_videos = [
                {
                    "title": f"Educational Content About: {query}",
                    "video_id": "edu_001",
                    "thumbnail_url": "https://img.youtube.com/vi/edu_001/maxresdefault.jpg",
                    "channel_name": "Educational Channel",
                    "view_count": 500000,
                    "duration": "12:00",
                    "url": "https://youtube.com/watch?v=edu_001"
                },
                {
                    "title": f"Learn More About {query} - Complete Guide",
                    "video_id": "edu_002",
                    "thumbnail_url": "https://img.youtube.com/vi/edu_002/maxresdefault.jpg", 
                    "channel_name": "Learning Hub",
                    "view_count": 320000,
                    "duration": "20:15",
                    "url": "https://youtube.com/watch?v=edu_002"
                }
            ]
        
        # Return limited results based on max_results
        return mock_videos[:max_results]
    
    def is_configured(self) -> bool:
        """
        Check if the YouTube service is configured
        """
        return True  # Always true for mock service
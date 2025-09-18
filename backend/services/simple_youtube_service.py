import os
import os
import asyncio
from typing import List, Dict, Any
from dotenv import load_dotenv

load_dotenv()

try:
    from googleapiclient.discovery import build
    YOUTUBE_API_AVAILABLE = True
except ImportError:
    YOUTUBE_API_AVAILABLE = False

class YouTubeService:
    def __init__(self):
        self.api_key = os.getenv("YOUTUBE_API_KEY")
        self.youtube_service = None
        
        if self.api_key and YOUTUBE_API_AVAILABLE:
            try:
                self.youtube_service = build('youtube', 'v3', developerKey=self.api_key)
            except Exception as e:
                print(f"Failed to initialize YouTube API: {e}")
                self.youtube_service = None
    
    async def get_recommendations(self, query: str, max_results: int = 10) -> List[Dict[str, Any]]:
        if self.youtube_service and self.api_key:
            try:
                return await self._real_youtube_search(query, max_results)
            except Exception:
                return await self._mock_recommendations(query, max_results)
        else:
            return await self._mock_recommendations(query, max_results)
    
    async def _mock_recommendations(self, query: str, max_results: int) -> List[Dict[str, Any]]:
        await asyncio.sleep(0.3)
        
        mock_videos = [
            {
                "title": f"Educational Content About: {query}",
                "video_id": "edu_001",
                "thumbnail_url": "https://via.placeholder.com/320x180/607D8B/FFFFFF?text=🎓+Education",
                "channel_name": "Educational Channel",
                "view_count": 500000,
                "duration": "12:00",
                "url": "https://youtube.com/watch?v=edu_001"
            },
            {
                "title": f"Learn More About {query} - Complete Guide",
                "video_id": "edu_002",
                "thumbnail_url": "https://via.placeholder.com/320x180/009688/FFFFFF?text=📚+Learning+Guide",
                "channel_name": "Learning Hub",
                "view_count": 320000,
                "duration": "20:15",
                "url": "https://youtube.com/watch?v=edu_002"
            }
        ]
        
        return mock_videos[:max_results]
    
    async def _real_youtube_search(self, query: str, max_results: int) -> List[Dict[str, Any]]:
        """
        Search YouTube using real API
        """
        try:
            # Perform search request
            search_request = self.youtube_service.search().list(
                q=query,
                part='id,snippet',
                maxResults=min(max_results, 50),
                type='video',
                order='relevance'
            )
            search_response = await asyncio.to_thread(search_request.execute)
            
            videos = []
            for item in search_response['items']:
                video_id = item['id']['videoId']
                snippet = item['snippet']
                
                # Get video details for view count and duration
                video_request = self.youtube_service.videos().list(
                    part='statistics,contentDetails',
                    id=video_id
                )
                video_details = await asyncio.to_thread(video_request.execute)
                
                stats = video_details['items'][0]['statistics'] if video_details['items'] else {}
                content_details = video_details['items'][0]['contentDetails'] if video_details['items'] else {}
                
                video_info = {
                    "title": snippet['title'],
                    "video_id": video_id,
                    "thumbnail_url": snippet['thumbnails'].get('high', snippet['thumbnails'].get('default', {})).get('url', ''),
                    "channel_name": snippet['channelTitle'],
                    "view_count": int(stats.get('viewCount', 0)),
                    "duration": self._format_duration(content_details.get('duration', '')),
                    "url": f"https://youtube.com/watch?v={video_id}"
                }
                videos.append(video_info)
            
            return videos
            
        except Exception as e:
            print(f"YouTube API error: {e}")
            raise e
    
    def _format_duration(self, duration: str) -> str:
        """
        Convert ISO 8601 duration to readable format (PT4M13S -> 4:13)
        """
        if not duration:
            return "Unknown"
        
        import re
        match = re.match(r'PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?', duration)
        if not match:
            return "Unknown"
        
        hours, minutes, seconds = match.groups()
        hours = int(hours) if hours else 0
        minutes = int(minutes) if minutes else 0
        seconds = int(seconds) if seconds else 0
        
        if hours > 0:
            return f"{hours}:{minutes:02d}:{seconds:02d}"
        else:
            return f"{minutes}:{seconds:02d}"
    
    def is_configured(self) -> bool:
        """
        Check if the YouTube service is configured with real API
        """
        return bool(self.youtube_service and self.api_key)
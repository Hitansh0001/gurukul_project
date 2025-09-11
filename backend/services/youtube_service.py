import os
import asyncio
from datetime import datetime
from typing import List
import httpx

class YouTubeService:
    """
    Service to fetch YouTube recommendations
    Note: Uses YouTube Data API v3. For demo without API key, it returns mock results.
    """
    def __init__(self):
        self.api_key = os.getenv("YOUTUBE_API_KEY")
        self.base_url = "https://www.googleapis.com/youtube/v3/search"

    async def get_recommendations(self, query: str, max_results: int = 10) -> List[dict]:
        if not self.api_key:
            return await self._mock_results(query, max_results)

        params = {
            "part": "snippet",
            "q": query,
            "type": "video",
            "maxResults": max_results,
            "key": self.api_key,
            "safeSearch": "moderate",
        }

        async with httpx.AsyncClient() as client:
            resp = await client.get(self.base_url, params=params, timeout=20.0)
            if resp.status_code != 200:
                # fallback to mock if API fails
                return await self._mock_results(query, max_results)
            data = resp.json()

        results = []
        for item in data.get("items", []):
            vid = item["id"]["videoId"]
            snippet = item["snippet"]
            thumb = snippet.get("thumbnails", {}).get("medium", {}).get("url") or \
                    snippet.get("thumbnails", {}).get("default", {}).get("url")
            results.append({
                "title": snippet.get("title"),
                "video_id": vid,
                "thumbnail_url": thumb,
                "channel_name": snippet.get("channelTitle"),
                "url": f"https://www.youtube.com/watch?v={vid}",
            })
        return results

    async def _mock_results(self, query: str, max_results: int) -> List[dict]:
        await asyncio.sleep(0.3)
        results = []
        for i in range(max_results):
            vid = f"mock-video-{i+1}"
            results.append({
                "title": f"Mock result {i+1} for '{query}'",
                "video_id": vid,
                "thumbnail_url": "https://img.youtube.com/vi/dQw4w9WgXcQ/mqdefault.jpg",
                "channel_name": "Mock Channel",
                "url": f"https://www.youtube.com/watch?v={vid}",
            })
        return results


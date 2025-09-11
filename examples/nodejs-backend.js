// Node.js backend example implementing the AI Integration Template API contract
const express = require('express');
const cors = require('cors');
const { Configuration, OpenAIApi } = require('openai');
const axios = require('axios');

const app = express();
app.use(cors());
app.use(express.json());

// Configuration
const PORT = process.env.PORT || 8000;
const OPENAI_API_KEY = process.env.OPENAI_API_KEY;
const YOUTUBE_API_KEY = process.env.YOUTUBE_API_KEY;

// Initialize OpenAI
let openai = null;
if (OPENAI_API_KEY) {
    const configuration = new Configuration({ apiKey: OPENAI_API_KEY });
    openai = new OpenAIApi(configuration);
}

// Health check endpoint
app.get('/health', (req, res) => {
    res.json({ status: 'healthy', service: 'AI Integration Template API' });
});

// Root endpoint
app.get('/', (req, res) => {
    res.json({
        message: "AI Integration Template API",
        version: "1.0.0",
        endpoints: {
            text_processing: "/api/process-text",
            youtube_recommendations: "/api/youtube-recommendations",
            health: "/health"
        }
    });
});

// Process text endpoint
app.post('/api/process-text', async (req, res) => {
    try {
        const { text, context } = req.body;
        
        if (!text) {
            return res.status(400).json({ error: 'Text is required' });
        }

        let responseText;
        
        if (openai) {
            // Real AI response
            const messages = [];
            if (context) {
                messages.push({ role: 'system', content: context });
            }
            messages.push({ role: 'user', content: text });

            const completion = await openai.createChatCompletion({
                model: process.env.AI_MODEL || 'gpt-3.5-turbo',
                messages: messages,
                max_tokens: parseInt(process.env.MAX_TOKENS || '1000'),
                temperature: parseFloat(process.env.AI_TEMPERATURE || '0.7'),
            });

            responseText = completion.data.choices[0].message.content.trim();
        } else {
            // Mock response
            responseText = `This is a mock AI response to your input: '${text.substring(0, 100)}...'. ` +
                          `To enable real AI responses, please set your OPENAI_API_KEY environment variable. ` +
                          `Context provided: ${context || 'None'}`;
        }

        res.json({
            response: responseText,
            timestamp: new Date().toISOString()
        });
    } catch (error) {
        console.error('Text processing error:', error);
        res.status(500).json({ error: 'Text processing failed' });
    }
});

// YouTube recommendations endpoint
app.post('/api/youtube-recommendations', async (req, res) => {
    try {
        const { query, max_results = 10 } = req.body;
        
        if (!query) {
            return res.status(400).json({ error: 'Query is required' });
        }

        let recommendations;
        
        if (YOUTUBE_API_KEY) {
            // Real YouTube API call
            const response = await axios.get('https://www.googleapis.com/youtube/v3/search', {
                params: {
                    part: 'snippet',
                    q: query,
                    type: 'video',
                    maxResults: max_results,
                    key: YOUTUBE_API_KEY,
                    safeSearch: 'moderate'
                }
            });

            recommendations = response.data.items.map(item => ({
                title: item.snippet.title,
                video_id: item.id.videoId,
                thumbnail_url: item.snippet.thumbnails?.medium?.url || 
                              item.snippet.thumbnails?.default?.url,
                channel_name: item.snippet.channelTitle,
                url: `https://www.youtube.com/watch?v=${item.id.videoId}`
            }));
        } else {
            // Mock recommendations
            recommendations = Array.from({ length: max_results }, (_, i) => ({
                title: `Mock result ${i + 1} for '${query}'`,
                video_id: `mock-video-${i + 1}`,
                thumbnail_url: 'https://img.youtube.com/vi/dQw4w9WgXcQ/mqdefault.jpg',
                channel_name: 'Mock Channel',
                url: `https://www.youtube.com/watch?v=mock-video-${i + 1}`
            }));
        }

        res.json(recommendations);
    } catch (error) {
        console.error('YouTube recommendations error:', error);
        res.status(500).json({ error: 'YouTube recommendations failed' });
    }
});

// Combined response endpoint
app.post('/api/combined-response', async (req, res) => {
    try {
        const { text, context } = req.body;
        
        if (!text) {
            return res.status(400).json({ error: 'Text is required' });
        }

        // Get text response
        const textResponse = await fetch('http://localhost:' + PORT + '/api/process-text', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ text, context })
        }).then(r => r.json());

        // Get YouTube recommendations
        const youtubeRecommendations = await fetch('http://localhost:' + PORT + '/api/youtube-recommendations', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ query: text, max_results: 5 })
        }).then(r => r.json());

        res.json({
            text_response: textResponse,
            youtube_recommendations: youtubeRecommendations
        });
    } catch (error) {
        console.error('Combined response error:', error);
        res.status(500).json({ error: 'Combined response failed' });
    }
});

app.listen(PORT, () => {
    console.log(`🚀 AI Integration Template API running on port ${PORT}`);
    console.log(`🔑 OpenAI configured: ${!!OPENAI_API_KEY}`);
    console.log(`📺 YouTube API configured: ${!!YOUTUBE_API_KEY}`);
});

/*
To use this Node.js backend:

1. Install dependencies:
   npm install express cors openai axios

2. Set environment variables:
   OPENAI_API_KEY=your_key_here
   YOUTUBE_API_KEY=your_key_here
   PORT=8000

3. Run:
   node nodejs-backend.js

4. The frontend will work with this backend using the same API endpoints!
*/

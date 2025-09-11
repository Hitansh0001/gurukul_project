// Java Spring Boot backend example implementing the AI Integration Template API contract
// File: src/main/java/com/example/aiintegration/controller/AIIntegrationController.java

package com.example.aiintegration.controller;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.client.RestTemplate;
import com.fasterxml.jackson.databind.ObjectMapper;
import com.fasterxml.jackson.databind.JsonNode;

import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.*;

@RestController
@CrossOrigin(origins = "*") // Configure appropriately for production
public class AIIntegrationController {

    @Value("${openai.api.key:#{null}}")
    private String openaiApiKey;

    @Value("${youtube.api.key:#{null}}")
    private String youtubeApiKey;

    @Value("${ai.model:gpt-3.5-turbo}")
    private String aiModel;

    @Value("${ai.max.tokens:1000}")
    private int maxTokens;

    @Value("${ai.temperature:0.7}")
    private double temperature;

    private final RestTemplate restTemplate = new RestTemplate();
    private final ObjectMapper objectMapper = new ObjectMapper();

    // Health check endpoint
    @GetMapping("/health")
    public ResponseEntity<Map<String, String>> health() {
        Map<String, String> response = new HashMap<>();
        response.put("status", "healthy");
        response.put("service", "AI Integration Template API");
        return ResponseEntity.ok(response);
    }

    // Root endpoint
    @GetMapping("/")
    public ResponseEntity<Map<String, Object>> root() {
        Map<String, Object> response = new HashMap<>();
        response.put("message", "AI Integration Template API");
        response.put("version", "1.0.0");
        
        Map<String, String> endpoints = new HashMap<>();
        endpoints.put("text_processing", "/api/process-text");
        endpoints.put("youtube_recommendations", "/api/youtube-recommendations");
        endpoints.put("health", "/health");
        response.put("endpoints", endpoints);
        
        return ResponseEntity.ok(response);
    }

    // Process text endpoint
    @PostMapping("/api/process-text")
    public ResponseEntity<?> processText(@RequestBody TextRequest request) {
        try {
            if (request.getText() == null || request.getText().trim().isEmpty()) {
                return ResponseEntity.badRequest().body(Map.of("error", "Text is required"));
            }

            String responseText;
            
            if (openaiApiKey != null && !openaiApiKey.trim().isEmpty()) {
                // Real AI response using OpenAI API
                responseText = callOpenAI(request.getText(), request.getContext());
            } else {
                // Mock response
                responseText = String.format(
                    "This is a mock AI response to your input: '%s...'. " +
                    "To enable real AI responses, please set your OPENAI_API_KEY environment variable. " +
                    "Context provided: %s", 
                    request.getText().length() > 100 ? request.getText().substring(0, 100) : request.getText(),
                    request.getContext() != null ? request.getContext() : "None"
                );
            }

            Map<String, String> response = new HashMap<>();
            response.put("response", responseText);
            response.put("timestamp", LocalDateTime.now().format(DateTimeFormatter.ISO_LOCAL_DATE_TIME));
            
            return ResponseEntity.ok(response);
        } catch (Exception e) {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body(Map.of("error", "Text processing failed: " + e.getMessage()));
        }
    }

    // YouTube recommendations endpoint
    @PostMapping("/api/youtube-recommendations")
    public ResponseEntity<?> getYouTubeRecommendations(@RequestBody YouTubeRequest request) {
        try {
            if (request.getQuery() == null || request.getQuery().trim().isEmpty()) {
                return ResponseEntity.badRequest().body(Map.of("error", "Query is required"));
            }

            List<Map<String, String>> recommendations;
            int maxResults = request.getMaxResults() != null ? request.getMaxResults() : 10;
            
            if (youtubeApiKey != null && !youtubeApiKey.trim().isEmpty()) {
                // Real YouTube API call
                recommendations = callYouTubeAPI(request.getQuery(), maxResults);
            } else {
                // Mock recommendations
                recommendations = generateMockRecommendations(request.getQuery(), maxResults);
            }

            return ResponseEntity.ok(recommendations);
        } catch (Exception e) {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body(Map.of("error", "YouTube recommendations failed: " + e.getMessage()));
        }
    }

    // Combined response endpoint
    @PostMapping("/api/combined-response")
    public ResponseEntity<?> getCombinedResponse(@RequestBody TextRequest request) {
        try {
            if (request.getText() == null || request.getText().trim().isEmpty()) {
                return ResponseEntity.badRequest().body(Map.of("error", "Text is required"));
            }

            // Get text response
            ResponseEntity<?> textResponseEntity = processText(request);
            if (!textResponseEntity.getStatusCode().is2xxSuccessful()) {
                return textResponseEntity;
            }

            // Get YouTube recommendations
            YouTubeRequest youtubeRequest = new YouTubeRequest();
            youtubeRequest.setQuery(request.getText());
            youtubeRequest.setMaxResults(5);
            
            ResponseEntity<?> youtubeResponseEntity = getYouTubeRecommendations(youtubeRequest);
            if (!youtubeResponseEntity.getStatusCode().is2xxSuccessful()) {
                return youtubeResponseEntity;
            }

            Map<String, Object> combinedResponse = new HashMap<>();
            combinedResponse.put("text_response", textResponseEntity.getBody());
            combinedResponse.put("youtube_recommendations", youtubeResponseEntity.getBody());
            
            return ResponseEntity.ok(combinedResponse);
        } catch (Exception e) {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body(Map.of("error", "Combined response failed: " + e.getMessage()));
        }
    }

    // Helper method to call OpenAI API (simplified)
    private String callOpenAI(String text, String context) throws Exception {
        // Note: In a real implementation, you would use a proper OpenAI Java client
        // This is a simplified example
        return "AI response to: " + text.substring(0, Math.min(text.length(), 50)) + "...";
    }

    // Helper method to call YouTube API
    private List<Map<String, String>> callYouTubeAPI(String query, int maxResults) throws Exception {
        // Note: In a real implementation, you would use the YouTube Data API
        // This is a simplified example that returns mock data
        return generateMockRecommendations(query, maxResults);
    }

    // Helper method to generate mock recommendations
    private List<Map<String, String>> generateMockRecommendations(String query, int maxResults) {
        List<Map<String, String>> recommendations = new ArrayList<>();
        
        for (int i = 1; i <= maxResults; i++) {
            Map<String, String> video = new HashMap<>();
            video.put("title", String.format("Mock result %d for '%s'", i, query));
            video.put("video_id", "mock-video-" + i);
            video.put("thumbnail_url", "https://img.youtube.com/vi/dQw4w9WgXcQ/mqdefault.jpg");
            video.put("channel_name", "Mock Channel");
            video.put("url", "https://www.youtube.com/watch?v=mock-video-" + i);
            recommendations.add(video);
        }
        
        return recommendations;
    }

    // Request DTOs
    public static class TextRequest {
        private String text;
        private String context;

        // Getters and setters
        public String getText() { return text; }
        public void setText(String text) { this.text = text; }
        public String getContext() { return context; }
        public void setContext(String context) { this.context = context; }
    }

    public static class YouTubeRequest {
        private String query;
        private Integer maxResults;

        // Getters and setters
        public String getQuery() { return query; }
        public void setQuery(String query) { this.query = query; }
        public Integer getMaxResults() { return maxResults; }
        public void setMaxResults(Integer maxResults) { this.maxResults = maxResults; }
    }
}

/*
To use this Java Spring Boot backend:

1. Add dependencies to pom.xml:
   <dependency>
       <groupId>org.springframework.boot</groupId>
       <artifactId>spring-boot-starter-web</artifactId>
   </dependency>
   <dependency>
       <groupId>com.fasterxml.jackson.core</groupId>
       <artifactId>jackson-databind</artifactId>
   </dependency>

2. Set environment variables or application.properties:
   openai.api.key=your_openai_key
   youtube.api.key=your_youtube_key
   server.port=8000

3. Run the Spring Boot application

4. The frontend will work with this backend using the same API endpoints!
*/

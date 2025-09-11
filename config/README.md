# Configuration Folder

This folder is designed to store sensitive configuration files and API keys.

## Security Notice

⚠️ **IMPORTANT**: This folder is for storing sensitive API keys and configuration files. 
- Never commit actual API keys to version control
- Use `.env` files for development
- Use secure environment variables or secret management for production

## Supported API Keys

### Gemini AI API
- **File**: `gemini_api_key.txt` (optional, for development only)
- **Environment Variable**: `AIzaSyCbE_9eqICvhwLX5rpYZ3iR2QLWrDWucs8`
- **Get your key**: https://makersuite.google.com/app/apikey

### YouTube Data API
- **File**: `youtube_api_key.txt` (optional, for development only)
- **Environment Variable**: `AIzaSyBM0SEXYcqW4yDQxFvRShvK0stkhOGl8Bs`
- **Get your key**: https://console.cloud.google.com/apis/api/youtube.googleapis.com

## Usage

1. **For Development**: Store keys in individual `.txt` files here (they are gitignored)
2. **For Production**: Use environment variables or proper secret management
3. **For Docker**: Mount this folder as a volume with restricted permissions

## File Structure

```
config/
├── README.md (this file)
├── gemini_api_key.txt (your Gemini API key - create manually)
├── youtube_api_key.txt (your YouTube API key - create manually)
└── .gitignore (ensures keys are never committed)
```

## Example Usage

```bash
# Create API key files (development only)
echo "your_gemini_api_key_here" > config/gemini_api_key.txt
echo "your_youtube_api_key_here" > config/youtube_api_key.txt

# Set as environment variables (recommended)
export GEMINI_API_KEY="your_gemini_api_key_here"
export YOUTUBE_API_KEY="your_youtube_api_key_here"
```

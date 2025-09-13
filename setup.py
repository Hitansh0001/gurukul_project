#!/usr/bin/env python3
"""
Setup script for AI Integration Template
Installs dependencies and sets up the development environment
"""

import subprocess
import sys
import os
from pathlib import Path

def run_command(command, shell=True):
    """Run a command and return its output"""
    try:
        result = subprocess.run(command, shell=shell, check=True, capture_output=True, text=True)
        return result.stdout.strip()
    except subprocess.CalledProcessError as e:
        print(f"Error running command: {command}")
        print(f"Error: {e.stderr}")
        return None

def setup_environment():
    """Set up the Python environment"""
    print("🚀 Setting up AI Integration Template...")
    
    # Check if we're in a virtual environment
    in_venv = hasattr(sys, 'real_prefix') or (hasattr(sys, 'base_prefix') and sys.base_prefix != sys.prefix)
    
    if not in_venv:
        print("⚠️  Warning: You're not in a virtual environment!")
        print("💡 Consider creating one with: python -m venv .venv")
        print("   Then activate it and run this setup again.")
        response = input("Continue anyway? (y/N): ")
        if response.lower() != 'y':
            print("Setup cancelled.")
            return False
    
    # Install requirements
    print("📦 Installing Python dependencies...")
    requirements_path = Path(__file__).parent / "backend" / "requirements.txt"
    
    if requirements_path.exists():
        result = run_command(f"pip install -r {requirements_path}")
        if result is None:
            print("❌ Failed to install dependencies")
            return False
        print("✅ Dependencies installed successfully")
    else:
        print("❌ requirements.txt not found")
        return False
    
    # Create .env file if it doesn't exist
    env_path = Path(__file__).parent / ".env"
    env_example_path = Path(__file__).parent / ".env.example"
    
    if not env_path.exists() and env_example_path.exists():
        print("📝 Creating .env file from .env.example...")
        with open(env_example_path, 'r') as src, open(env_path, 'w') as dst:
            dst.write(src.read())
        print("✅ .env file created. Please configure your API keys.")
    
    # Create config folder if it doesn't exist
    config_path = Path(__file__).parent / "config"
    if not config_path.exists():
        config_path.mkdir()
        print("📁 Created config folder for API keys")
    
    print("\n🎉 Setup complete!")
    print("\nNext steps:")
    print("1. Configure Gemini API key (choose one method):")
    print("   • Environment variables (.env file): GEMINI_API_KEY=your_key")
    print("   • Config file: config/gemini_api_key.txt")
    print("2. Optional: Configure YouTube API key for video recommendations")
    print("3. Run: uvicorn backend.main:app --host 0.0.0.0 --port 8000 --reload")
    print("4. Open frontend/index.html in your browser")
    print("\n💡 Check README.md and config/README.md for detailed instructions")
    print("🔑 Get Gemini API key: https://makersuite.google.com/app/apikey")
    
    return True

if __name__ == "__main__":
    setup_environment()
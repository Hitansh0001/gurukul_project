// Basic functionality for Student Helper AI
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the AI chat page and load the specialized script
    if (window.location.pathname.includes('ai-chat.html')) {
        const script = document.createElement('script');
        script.src = 'ai-chat.js';
        document.head.appendChild(script);
        return;
    }

    // Basic chat functionality for other pages (if chat elements exist)
    const sendBtn = document.getElementById('send-btn');
    const userInput = document.getElementById('user-input');
    const chatBox = document.getElementById('chat-box');

    if (sendBtn && userInput && chatBox) {
        sendBtn.addEventListener('click', function() {
            const message = userInput.value.trim();
            if (message !== "") {
                // Add user message
                const userMessage = document.createElement('p');
                userMessage.innerHTML = '<strong>You:</strong> ' + message;
                chatBox.appendChild(userMessage);

                // Add AI reply
                const aiReply = document.createElement('p');
                aiReply.innerHTML = '<strong>AI:</strong> This is a sample AI reply to "' + message + '"';
                chatBox.appendChild(aiReply);

                // Clear input and scroll
                userInput.value = "";
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        });

        // Enter key support
        userInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendBtn.click();
            }
        });
    }

    // Navigation active state
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('nav a');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPage || (currentPage === '' && href === 'dashboard.html')) {
            link.classList.add('active');
        }
    });

    // Subject button click handlers
    const subjectButtons = document.querySelectorAll('.subject-btn');
    subjectButtons.forEach(button => {
        button.addEventListener('click', function() {
            alert('Redirecting to ' + this.textContent + ' details...');
        });
    });

    // Quick button handlers
    const quickButtons = document.querySelectorAll('.quick-btn');
    quickButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href && !href.startsWith('#')) {
                // Allow normal navigation
                return true;
            }
            e.preventDefault();
            alert('This feature will be implemented soon!');
        });
    });

    // Add some interactive effects
    const cards = document.querySelectorAll('.note-item, .stat-card, .activity-item');
    cards.forEach(card => {
        card.addEventListener('click', function() {
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });

    // Initialize API status checking if on dashboard
    if (window.location.pathname.includes('dashboard.html') || window.location.pathname === '' || window.location.pathname === '/') {
        initializeAPIStatus();
    }
});

// API Status Management
let autoCheckInterval = null;
let isAutoChecking = false;

function initializeAPIStatus() {
    // Load saved endpoint from localStorage
    const savedEndpoint = localStorage.getItem('api_endpoint');
    if (savedEndpoint) {
        const endpointInput = document.getElementById('api-endpoint-input');
        const endpointDisplay = document.getElementById('api-endpoint-display');
        if (endpointInput) endpointInput.value = savedEndpoint;
        if (endpointDisplay) endpointDisplay.textContent = savedEndpoint;
    }
    
    // Perform initial API check
    setTimeout(() => {
        testBackendAPI();
    }, 1000);
}

function updateAPIStatus(service, status, responseTime = null, details = null) {
    const statusElement = document.getElementById(service + '-status');
    if (!statusElement) return;
    
    const dot = statusElement.querySelector('.status-dot');
    const text = statusElement.querySelector('.status-text');
    
    // Remove all status classes
    dot.classList.remove('connected', 'error', 'testing');
    
    switch (status) {
        case 'connected':
            dot.classList.add('connected');
            text.textContent = responseTime ? `Connected (${responseTime}ms)` : 'Connected';
            break;
        case 'error':
            dot.classList.add('error');
            text.textContent = details || 'Connection failed';
            break;
        case 'testing':
            dot.classList.add('testing');
            text.textContent = 'Testing...';
            break;
        default:
            text.textContent = 'Unknown';
    }
    
    // Update last check time
    const lastCheckElement = document.getElementById('last-check-time');
    if (lastCheckElement && status !== 'testing') {
        lastCheckElement.textContent = new Date().toLocaleTimeString();
    }
    
    // Update specific service details
    if (service === 'ai' && responseTime) {
        const responseTimeElement = document.getElementById('ai-response-time');
        if (responseTimeElement) {
            responseTimeElement.textContent = responseTime + 'ms';
        }
    }
}

function getAPIEndpoint() {
    const endpointInput = document.getElementById('api-endpoint-input');
    return endpointInput ? endpointInput.value.trim() : 'http://localhost:8000';
}

window.testBackendAPI = async function() {
    const endpoint = getAPIEndpoint();
    updateAPIStatus('backend', 'testing');
    
    const startTime = Date.now();
    
    try {
        const response = await fetch(endpoint + '/health', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        const responseTime = Date.now() - startTime;
        
        if (response.ok) {
            updateAPIStatus('backend', 'connected', responseTime);
            showNotification('Backend API is connected!', 'success');
        } else {
            updateAPIStatus('backend', 'error', null, `HTTP ${response.status}`);
            showNotification('Backend API returned an error', 'error');
        }
    } catch (error) {
        const responseTime = Date.now() - startTime;
        updateAPIStatus('backend', 'error', null, 'Connection failed');
        showNotification('Failed to connect to backend API', 'error');
        console.error('Backend API test failed:', error);
    }
};

window.testAIService = async function() {
    const endpoint = getAPIEndpoint();
    updateAPIStatus('ai', 'testing');
    
    const startTime = Date.now();
    
    try {
        const response = await fetch(endpoint + '/api/process-text', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                text: 'Test message for API connectivity',
                context: 'API testing'
            })
        });
        
        const responseTime = Date.now() - startTime;
        
        if (response.ok) {
            const data = await response.json();
            updateAPIStatus('ai', 'connected', responseTime);
            showNotification('AI Service is working!', 'success');
        } else {
            updateAPIStatus('ai', 'error', null, `HTTP ${response.status}`);
            showNotification('AI Service returned an error', 'error');
        }
    } catch (error) {
        updateAPIStatus('ai', 'error', null, 'Service unavailable');
        showNotification('AI Service is not available', 'error');
        console.error('AI service test failed:', error);
    }
};

window.testYouTubeService = async function() {
    const endpoint = getAPIEndpoint();
    updateAPIStatus('youtube', 'testing');
    
    const startTime = Date.now();
    
    try {
        const response = await fetch(endpoint + '/api/youtube-recommendations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                query: 'test query',
                max_results: 1
            })
        });
        
        const responseTime = Date.now() - startTime;
        
        if (response.ok) {
            const data = await response.json();
            updateAPIStatus('youtube', 'connected', responseTime);
            showNotification('YouTube API is working!', 'success');
            
            // Update quota information if available
            const quotaElement = document.getElementById('youtube-quota');
            if (quotaElement) {
                quotaElement.textContent = 'Available';
            }
        } else {
            updateAPIStatus('youtube', 'error', null, `HTTP ${response.status}`);
            showNotification('YouTube API returned an error', 'error');
        }
    } catch (error) {
        updateAPIStatus('youtube', 'error', null, 'Service unavailable');
        showNotification('YouTube API is not available', 'error');
        console.error('YouTube API test failed:', error);
    }
};

window.testAllAPIs = async function() {
    showNotification('Testing all APIs...', 'info');
    
    // Test APIs sequentially to avoid overwhelming the server
    await testBackendAPI();
    await new Promise(resolve => setTimeout(resolve, 500)); // Wait 500ms
    
    await testAIService();
    await new Promise(resolve => setTimeout(resolve, 500)); // Wait 500ms
    
    await testYouTubeService();
    
    showNotification('API testing completed!', 'success');
};

window.updateAPIEndpoint = function() {
    const endpointInput = document.getElementById('api-endpoint-input');
    const endpointDisplay = document.getElementById('api-endpoint-display');
    
    if (!endpointInput) return;
    
    const newEndpoint = endpointInput.value.trim();
    
    if (!newEndpoint) {
        showNotification('Please enter a valid endpoint', 'error');
        return;
    }
    
    // Validate URL format
    try {
        new URL(newEndpoint);
    } catch (error) {
        showNotification('Please enter a valid URL', 'error');
        return;
    }
    
    // Save to localStorage
    localStorage.setItem('api_endpoint', newEndpoint);
    
    // Update display
    if (endpointDisplay) {
        endpointDisplay.textContent = newEndpoint;
    }
    
    showNotification('API endpoint updated!', 'success');
    
    // Test the new endpoint
    setTimeout(() => {
        testBackendAPI();
    }, 500);
};

window.toggleAutoCheck = function() {
    const button = document.getElementById('auto-check-btn');
    if (!button) return;
    
    if (isAutoChecking) {
        // Stop auto-checking
        if (autoCheckInterval) {
            clearInterval(autoCheckInterval);
            autoCheckInterval = null;
        }
        isAutoChecking = false;
        button.textContent = '🔄 Enable Auto-Check';
        button.classList.remove('active');
        showNotification('Auto-check disabled', 'info');
    } else {
        // Start auto-checking
        isAutoChecking = true;
        button.textContent = '⏹️ Disable Auto-Check';
        button.classList.add('active');
        
        // Check every 30 seconds
        autoCheckInterval = setInterval(() => {
            testBackendAPI();
        }, 30000);
        
        showNotification('Auto-check enabled (every 30 seconds)', 'success');
    }
};

// Global utility functions
window.showNotification = function(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = 'notification ' + type;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#007bff'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000;
        animation: slideInRight 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
};

// Add notification animations
const notificationStyles = document.createElement('style');
notificationStyles.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(notificationStyles);

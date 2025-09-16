// AI Chat functionality with backend integration
document.addEventListener('DOMContentLoaded', function() {
    const chatBox = document.getElementById('chat-box');
    const userInput = document.getElementById('user-input');
    const sendBtn = document.getElementById('send-btn');
    const typingIndicator = document.getElementById('typing-indicator');
    const soundNotifications = document.getElementById('sound-notifications');

    // Backend configuration
    const API_BASE_URL = localStorage.getItem('api_endpoint') || 'http://localhost:8000';
    let isProcessing = false;

    // Enhanced AI responses with backend integration
    const fallbackResponses = {
        'calculus': [
            "I'd be happy to help with calculus! What specific topic are you working on? Integration, derivatives, limits, or something else?",
            "Calculus can be challenging, but it's very logical once you understand the fundamentals. What problem are you stuck on?",
            "For calculus, I recommend breaking down complex problems into smaller steps. What would you like to work on?"
        ],
        'photosynthesis': [
            "Photosynthesis is the process by which plants convert sunlight, carbon dioxide, and water into glucose and oxygen.",
            "There are two main stages of photosynthesis: the light-dependent reactions and the light-independent reactions or Calvin cycle.",
            "Photosynthesis occurs in chloroplasts and uses chlorophyll to capture light energy. Would you like me to explain any specific part in more detail?"
        ],
        'history': [
            "History essays require strong thesis statements, evidence from primary sources, and clear analysis. What historical period are you writing about?",
            "For history essays, start with a compelling introduction, organize your body paragraphs chronologically or thematically, and conclude with the significance of your topic.",
            "When writing history essays, always cite your sources and analyze rather than just summarize events. What's your essay topic?"
        ],
        'schedule': [
            "A good study schedule should balance different subjects, include regular breaks, and account for your most productive hours. What subjects do you need to schedule?",
            "I recommend using the Pomodoro Technique: 25 minutes of focused study followed by a 5-minute break. Would you like help creating a weekly schedule?",
            "Effective scheduling involves prioritizing tasks by deadline and difficulty. What's your biggest scheduling challenge?"
        ],
        'default': [
            "I'm here to help with your studies! You can ask me about any subject, homework help, study tips, or academic planning.",
            "That's an interesting question! Could you provide more details so I can give you a more specific answer?",
            "I'd be happy to assist you with that. Can you tell me more about what you're working on?",
            "Let me help you with that! What specific aspect would you like me to focus on?",
            "Great question! I can help you understand this topic better. What would you like to know?"
        ]
    };

    // Add event listeners
    if (sendBtn) {
        sendBtn.addEventListener('click', sendMessage);
    }
    
    if (userInput) {
        userInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }

    function sendMessage() {
        if (!userInput || !chatBox || isProcessing) return;
        
        const message = userInput.value.trim();
        if (message === '') return;

        // Prevent multiple simultaneous requests
        isProcessing = true;
        sendBtn.disabled = true;
        sendBtn.textContent = '⏳';

        // Add user message to chat
        addMessage(message, 'user');
        userInput.value = '';

        // Show typing indicator if enabled
        if (typingIndicator && typingIndicator.checked) {
            showTypingIndicator();
        }

        // Try to get AI response from backend first
        getAIResponseFromBackend(message)
            .then(response => {
                hideTypingIndicator();
                addMessage(response, 'ai');
                
                // Play notification sound if enabled
                if (soundNotifications && soundNotifications.checked) {
                    playNotificationSound();
                }
            })
            .catch(error => {
                console.error('Backend AI request failed:', error);
                hideTypingIndicator();
                
                // Fallback to local response
                const fallbackResponse = generateFallbackResponse(message);
                addMessage(fallbackResponse, 'ai');
                
                // Show warning about backend connection
                setTimeout(() => {
                    addMessage('⚠️ Note: I\'m currently using offline responses. For better AI assistance, please ensure the backend server is running.', 'ai');
                }, 1000);
            })
            .finally(() => {
                isProcessing = false;
                sendBtn.disabled = false;
                sendBtn.textContent = '📤';
            });
    }

    async function getAIResponseFromBackend(message) {
        try {
            const response = await fetch(`${API_BASE_URL}/api/process-text`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    text: message,
                    context: 'Student Helper AI Chat'
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            return data.response || 'I received your message but couldn\'t generate a proper response. Please try again.';
            
        } catch (error) {
            console.error('Backend request error:', error);
            throw error;
        }
    }

    function addMessage(content, sender) {
        if (!chatBox) return;
        
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message ' + sender + '-message';
        
        const avatar = document.createElement('div');
        avatar.className = 'message-avatar';
        avatar.textContent = sender === 'user' ? '👤' : '🤖';
        
        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';
        messageContent.innerHTML = sender === 'user' ? 
            '<strong>You:</strong> ' + content : 
            '<strong>AI:</strong> ' + content;
        
        const timestamp = document.createElement('div');
        timestamp.className = 'message-time';
        timestamp.textContent = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        
        messageDiv.appendChild(avatar);
        messageDiv.appendChild(messageContent);
        messageContent.appendChild(timestamp);
        
        chatBox.appendChild(messageDiv);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function generateFallbackResponse(userMessage) {
        const message = userMessage.toLowerCase();
        
        // Check for keywords to determine response category
        if (message.includes('calculus') || message.includes('derivative') || message.includes('integral') || message.includes('math')) {
            return getRandomResponse('calculus');
        } else if (message.includes('photosynthesis') || message.includes('plant') || message.includes('biology')) {
            return getRandomResponse('photosynthesis');
        } else if (message.includes('history') || message.includes('essay') || message.includes('historical')) {
            return getRandomResponse('history');
        } else if (message.includes('schedule') || message.includes('study plan') || message.includes('time management')) {
            return getRandomResponse('schedule');
        } else {
            return getRandomResponse('default');
        }
    }

    function getRandomResponse(category) {
        const responses = fallbackResponses[category] || fallbackResponses['default'];
        return responses[Math.floor(Math.random() * responses.length)];
    }

    function showTypingIndicator() {
        if (!chatBox) return;
        
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message ai-message typing-indicator';
        typingDiv.id = 'typing-indicator-msg';
        typingDiv.innerHTML = '<div class="message-avatar">🤖</div><div class="message-content"><strong>AI:</strong> <span class="typing-dots"><span>.</span><span>.</span><span>.</span></span></div>';
        
        chatBox.appendChild(typingDiv);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function hideTypingIndicator() {
        const typingIndicator = document.getElementById('typing-indicator-msg');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    function playNotificationSound() {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
            
            gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.2);
        } catch (error) {
            console.log('Audio notification not supported');
        }
    }

    // Global functions
    window.askQuickQuestion = function(question) {
        if (userInput) {
            userInput.value = question;
            sendMessage();
        }
    };

    window.clearChat = function() {
        if (confirm('Are you sure you want to clear the chat history?')) {
            if (chatBox) {
                chatBox.innerHTML = '<div class="message ai-message"><div class="message-avatar">🤖</div><div class="message-content"><strong>AI:</strong> Hello! How can I assist you with your studies today?</div><div class="message-time">Just now</div></div>';
            }
        }
    };

    window.showTab = function(tabName) {
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(tab => tab.classList.remove('active'));
        
        const tabButtons = document.querySelectorAll('.tab-btn');
        tabButtons.forEach(btn => btn.classList.remove('active'));
        
        const selectedTab = document.getElementById(tabName + '-tab');
        if (selectedTab) {
            selectedTab.classList.add('active');
        }
        
        if (event && event.target) {
            event.target.classList.add('active');
        }
    };

    window.filterTasks = function(filter) {
        const tasks = document.querySelectorAll('.task-item');
        const filterButtons = document.querySelectorAll('.filter-btn');
        
        filterButtons.forEach(btn => btn.classList.remove('active'));
        
        if (event && event.target) {
            event.target.classList.add('active');
        }
        
        tasks.forEach(task => {
            const status = task.dataset.status;
            if (filter === 'all' || status === filter) {
                task.style.display = 'flex';
            } else {
                task.style.display = 'none';
            }
        });
    };

    // Modal functions
    window.createNewNote = function() {
        const modal = document.getElementById('note-modal');
        if (modal) {
            modal.classList.remove('hidden');
            const noteTitle = document.getElementById('note-title');
            if (noteTitle) noteTitle.focus();
        }
    };

    window.closeNoteModal = function() {
        const modal = document.getElementById('note-modal');
        if (modal) {
            modal.classList.add('hidden');
            const noteTitle = document.getElementById('note-title');
            const noteContent = document.getElementById('note-content');
            if (noteTitle) noteTitle.value = '';
            if (noteContent) noteContent.value = '';
        }
    };

    window.saveNote = function() {
        const noteTitle = document.getElementById('note-title');
        const noteContent = document.getElementById('note-content');
        
        if (!noteTitle || !noteContent) return;
        
        const title = noteTitle.value;
        const content = noteContent.value;
        
        if (title.trim() === '' || content.trim() === '') {
            alert('Please fill in both title and content.');
            return;
        }
        
        alert('Note saved successfully!');
        window.closeNoteModal();
    };

    window.createNewTask = function() {
        const modal = document.getElementById('task-modal');
        if (modal) {
            modal.classList.remove('hidden');
            const taskTitle = document.getElementById('task-title-input');
            if (taskTitle) taskTitle.focus();
        }
    };

    window.closeTaskModal = function() {
        const modal = document.getElementById('task-modal');
        if (modal) {
            modal.classList.add('hidden');
            const taskTitle = document.getElementById('task-title-input');
            const taskDate = document.getElementById('task-due-date');
            const taskDesc = document.getElementById('task-description');
            if (taskTitle) taskTitle.value = '';
            if (taskDate) taskDate.value = '';
            if (taskDesc) taskDesc.value = '';
        }
    };

    window.saveTask = function() {
        const taskTitle = document.getElementById('task-title-input');
        
        if (!taskTitle) return;
        
        const title = taskTitle.value;
        
        if (title.trim() === '') {
            alert('Please enter a task title.');
            return;
        }
        
        alert('Task created successfully!');
        window.closeTaskModal();
    };

    // Placeholder functions
    window.editNote = function(id) {
        alert('Edit note functionality would be implemented here.');
    };

    window.deleteNote = function(id) {
        if (confirm('Are you sure you want to delete this note?')) {
            alert('Note deleted successfully!');
        }
    };

    window.toggleTask = function(id) {
        alert('Task status toggled!');
    };

    window.editTask = function(id) {
        alert('Edit task functionality would be implemented here.');
    };

    window.deleteTask = function(id) {
        if (confirm('Are you sure you want to delete this task?')) {
            alert('Task deleted successfully!');
        }
    };

    window.addEvent = function() {
        alert('Add event functionality would be implemented here.');
    };

    // API Status testing for chat page
    window.testChatAPI = async function() {
        const statusElement = document.getElementById('chat-backend-status');
        if (!statusElement) return;
        
        const dot = statusElement.querySelector('.status-dot');
        const text = statusElement.querySelector('.status-text');
        
        // Set testing state
        dot.classList.remove('connected', 'error');
        dot.classList.add('testing');
        text.textContent = 'Testing...';
        
        try {
            const response = await fetch(`${API_BASE_URL}/health`);
            
            if (response.ok) {
                dot.classList.remove('testing');
                dot.classList.add('connected');
                text.textContent = 'Connected';
                
                // Test AI endpoint as well
                try {
                    const aiTest = await fetch(`${API_BASE_URL}/api/process-text`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ text: 'test', context: 'connection test' })
                    });
                    
                    if (aiTest.ok) {
                        text.textContent = 'AI Ready';
                    } else {
                        text.textContent = 'Backend OK, AI Issues';
                    }
                } catch (aiError) {
                    text.textContent = 'Backend OK, AI Issues';
                }
            } else {
                dot.classList.remove('testing');
                dot.classList.add('error');
                text.textContent = 'Error';
            }
        } catch (error) {
            dot.classList.remove('testing');
            dot.classList.add('error');
            text.textContent = 'Failed';
        }
    };

    // Auto-test API on page load
    if (window.location.pathname.includes('ai-chat.html')) {
        setTimeout(() => {
            if (window.testChatAPI) {
                window.testChatAPI();
            }
        }, 1000);
    }

    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.classList.add('hidden');
        }
    });
});

// Add CSS for typing indicator animation
const style = document.createElement('style');
style.textContent = '.typing-dots { display: inline-block; } .typing-dots span { opacity: 0; animation: typing 1.4s infinite; } .typing-dots span:nth-child(1) { animation-delay: 0s; } .typing-dots span:nth-child(2) { animation-delay: 0.2s; } .typing-dots span:nth-child(3) { animation-delay: 0.4s; } @keyframes typing { 0%, 60%, 100% { opacity: 0; } 30% { opacity: 1; } }';
document.head.appendChild(style);
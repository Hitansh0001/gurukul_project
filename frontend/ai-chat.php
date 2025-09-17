<?php include(__DIR__ . "/../includes/nav.php"); ?>

<main class="ai-chat-main">
    <section class="chat-container">
        <div class="chat-header">
            <h2>AI Assistant</h2>
            <p>Ask me anything about your studies!</p>
        </div>

        <div id="chat-box" class="chat-messages">
            <div class="message ai-message">
                <div class="message-avatar">🤖</div>
                <div class="message-content">
                    <strong>AI:</strong> Hello! How can I assist you with your studies today? 
                    I can help you with homework, explain concepts, or answer any academic questions you have.
                </div>
                <div class="message-time">Just now</div>
            </div>
        </div>

        <div class="chat-input-container">
            <div class="input-wrapper">
                <input type="text" id="user-input" placeholder="Type your message here..." aria-label="Chat message input">
                <button id="send-btn" aria-label="Send message">
                    <span>📤</span>
                </button>
            </div>
        </div>
    </section>

    <aside class="chat-sidebar">
        <div class="api-status-widget">
            <h3>🔗 API Status</h3>
            <div class="status-item">
                <span>Backend:</span>
                <div class="status-indicator" id="chat-backend-status">
                    <span class="status-dot"></span>
                    <span class="status-text">Checking...</span>
                </div>
            </div>
            <button onclick="testChatAPI()" class="quick-test-btn">Quick Test</button>
        </div>

        <div class="quick-questions">
            <h3>Quick Questions</h3>
            <button class="quick-question" onclick="askQuickQuestion('Help me with calculus')">📐 Help with Calculus</button>
            <button class="quick-question" onclick="askQuickQuestion('Explain photosynthesis')">🌱 Explain Photosynthesis</button>
            <button class="quick-question" onclick="askQuickQuestion('History essay tips')">📚 History Essay Tips</button>
            <button class="quick-question" onclick="askQuickQuestion('Study schedule advice')">⏰ Study Schedule</button>
        </div>

        <div class="chat-settings">
            <h3>Settings</h3>
            <label>
                <input type="checkbox" id="typing-indicator" checked>
                Show typing indicator
            </label>
            <label>
                <input type="checkbox" id="sound-notifications">
                Sound notifications
            </label>
            <button onclick="clearChat()" class="clear-btn">Clear Chat History</button>
        </div>
    </aside>
</main>

<script src="ai-chat.js"></script>

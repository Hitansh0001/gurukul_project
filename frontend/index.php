<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Helper AI</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Student Helper AI</h1>
        <nav>
            <a href="dashboard.html">Dashboard</a>
            <a href="notes.html">Notes</a>
            <a href="tasks.html">Tasks</a>
            <a href="schedule.html">Schedule</a>
            <a href="ai-chat.html">AI Chat</a>
        </nav>
    </header>

    <main class="home-main">
        <section class="notes">
            <h2>Recent Notes</h2>
            <div class="note-item">Calculus II <span>Last edited: Yesterday</span></div>
            <div class="note-item">Modern European History <span>Last edited: Yesterday</span></div>
            <a href="notes.html" class="view-all">View All Notes →</a>
        </section>

        <section class="assignments">
            <h2>Upcoming Assignments</h2>
            <label><input type="checkbox" aria-label="Finish Physics Lab Report"> Finish Physics Lab Report (Due: Today) <span>7%</span></label><br>
            <label><input type="checkbox" aria-label="Read Chapter 5 History"> Read Chapter 5 History</label><br>
            <label><input type="checkbox" aria-label="Complete Math Problem Set"> Complete Math Problem Set</label>
            <a href="tasks.html" class="view-all">View All Tasks →</a>
        </section>

        <section class="schedule">
            <h2>Today's Schedule</h2>
            <div class="schedule-buttons">
                <button class="subject-btn history">History - 9:00 AM</button>
                <button class="subject-btn science">Physics Lab - 11:00 AM</button>
                <button class="subject-btn programming">Programming - 2:00 PM</button>
            </div>
            <a href="schedule.html" class="view-all">View Full Schedule →</a>
        </section>

        <section class="quick-ai">
            <h2>Quick AI Help</h2>
            <p>Need instant help with your studies?</p>
            <a href="ai-chat.html" class="ai-btn">🤖 Start AI Chat</a>
        </section>
    </main>

    <script>
        // Auto-redirect to dashboard after 5 seconds, but allow navigation clicks
        let autoRedirectTimer;
        
        function startAutoRedirect() {
            autoRedirectTimer = setTimeout(() => {
                window.location.href = 'dashboard.html';
            }, 5000);
        }
        
        function stopAutoRedirect() {
            if (autoRedirectTimer) {
                clearTimeout(autoRedirectTimer);
            }
        }
        
        // Start auto-redirect timer
        startAutoRedirect();
        
        // Handle clicks - allow navigation but stop auto-redirect for other clicks
        document.addEventListener('click', function(e) {
            const clickedElement = e.target;
            
            // Check if clicked element is a navigation link
            if (clickedElement.tagName === 'A' && clickedElement.href) {
                // Allow navigation - stop auto-redirect
                stopAutoRedirect();
                return; // Let the link work normally
            }
            
            // Check if clicked element is inside a navigation link
            const parentLink = clickedElement.closest('a');
            if (parentLink && parentLink.href) {
                stopAutoRedirect();
                return; // Let the link work normally
            }
            
            // For other clicks, go to dashboard
            stopAutoRedirect();
            window.location.href = 'dashboard.html';
        });
        
        // Stop auto-redirect on mouse movement to give user control
        let mouseMoveTimer;
        document.addEventListener('mousemove', function() {
            if (mouseMoveTimer) {
                clearTimeout(mouseMoveTimer);
            }
            mouseMoveTimer = setTimeout(() => {
                stopAutoRedirect();
            }, 1000);
        });
    </script>
</body>
</html>

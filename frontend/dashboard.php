<?php include(__DIR__ . "/../includes/nav.php");
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
$showWelcome = false;
if (!isset($_SESSION['welcome_shown'])) {
    $showWelcome = true;
    $_SESSION['welcome_shown'] = true;
}
?>

<div class="dashboard-main">
    <section class="welcome-section" id="welcome-section">
        <h2>Welcome back, <span class="welcome-msg"><?= htmlspecialchars($_SESSION['username']); ?></span>!</h2>
    </section>
    <section class="welcome-section">
     <p>Here's your academic overview for today</p>
     </section>
    <section class="stats-grid">
        <div class="stat-card">
            <h3>Pending Tasks</h3>
            <div class="stat-number">3</div>
            <p>assignments due this week</p>
        </div>
        <div class="stat-card">
            <h3>Notes Created</h3>
            <div class="stat-number">12</div>
            <p>this month</p>
        </div>
        <div class="stat-card">
            <h3>Study Hours</h3>
            <div class="stat-number">24</div>
            <p>this week</p>
        </div>
        <div class="stat-card">
            <h3>Upcoming Events</h3>
            <div class="stat-number">2</div>
            <p>classes today</p>
        </div>
    </section>

    <section class="quick-access">
        <h2>Quick Access</h2>
        <div class="quick-buttons">
            <a href="notes.php" class="quick-btn">📝 Create Note</a>
            <a href="tasks.php" class="quick-btn">✅ Add Task</a>
            <a href="schedule.php" class="quick-btn">📅 View Schedule</a>
            <a href="ai-chat.php" class="quick-btn">🤖 Ask AI</a>
        </div>
    </section>

    <section class="recent-activity">
        <h2>Recent Activity</h2>
        <div class="activity-list">
            <div class="activity-item">
                <span class="activity-time">2 hours ago</span>
                <span class="activity-text">Completed "Physics Lab Report"</span>
            </div>
            <div class="activity-item">
                <span class="activity-time">Yesterday</span>
                <span class="activity-text">Created note "Calculus II - Integration"</span>
            </div>
            <div class="activity-item">
                <span class="activity-time">2 days ago</span>
                <span class="activity-text">Added task "Read Chapter 5 History"</span>
            </div>
        </div>
    </section>
    <script>
        const welcomeSection = document.getElementById("welcome-section");

        welcomeSection.style.opacity = 0;
        welcomeSection.style.transition = "opacity 1s ease-in-out";

        setTimeout(() => {
            welcomeSection.style.opacity = 1;

            setTimeout(() => {
                welcomeSection.style.opacity = 0;

                setTimeout(() => {
                    welcomeSection.remove();
                }, 1000);
            }, 3000);
        }, 500);
    </script>
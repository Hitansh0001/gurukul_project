<?php include(__DIR__ . "/../includes/nav.php"); ?>
<style>
/* Hide form by default */
.form-popup {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5); /* dark overlay */
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

/* The actual popup box */
.form-container {
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  width: 400px;
  max-width: 90%;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.form-container h3 {
  margin-bottom: 15px;
}

.form-container label {
  display: block;
  margin: 10px 0 5px;
}

.form-container input,
.form-container textarea {
  width: 100%;
  padding: 8px;
  margin-bottom: 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.form-actions button {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.form-actions button:first-child {
  background: #4caf50;
  color: #fff;
}

.form-actions button:last-child {
  background: #f44336;
  color: #fff;
}
</style>

<main class="schedule-main">
    <section class="page-header">
      <h2>My Schedule</h2>
      <button class="add-btn" onclick="openEventForm()">+ Add Event</button>
    </section>
  
    <!-- ✅ Event Form moved inside main -->
    <div id="event-form" class="form-popup">
      <div class="form-container">
        <h3>Add New Event</h3>
        <label>Title</label>
        <input type="text" id="event-title" placeholder="Event Title">
  
        <label>Date</label>
        <input type="date" id="event-date">
  
        <label>Time</label>
        <input type="time" id="event-time">
  
        <label>Details</label>
        <textarea id="event-details" placeholder="Details..."></textarea>
  
        <div class="form-actions">
          <button onclick="saveEvent()">Save</button>
          <button onclick="closeEventForm()">Cancel</button>
        </div>
      </div>
    </div>
  
    <section class="schedule-view">
      <div class="schedule-tabs">
        <button class="tab-btn active" onclick="showTab('today')">Today</button>
        <button class="tab-btn" onclick="showTab('week')">This Week</button>
        <button class="tab-btn" onclick="showTab('month')">This Month</button>
      </div>
  
      <div id="today-tab" class="tab-content active">
        <h3>Today's Schedule</h3>
        <div class="schedule-grid" id="today-events">
          <div class="time-slot">
            <span class="time">9:00 AM</span>
            <div class="event history">
              <h4>Modern European History</h4>
              <p>Room 204, Prof. Johnson</p>
            </div>
          </div>
        </div>
      </div>
  
      <div id="week-tab" class="tab-content">
        <h3>This Week</h3>
        <div class="weekly-view" id="week-events"></div>
      </div>
  
      <div id="month-tab" class="tab-content">
        <h3>This Month</h3>
        <div class="calendar-grid">
          <div class="calendar-header">
            <div>Sun</div><div>Mon</div><div>Tue</div>
            <div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
          </div>
          <div class="calendar-body">
            <div class="calendar-day">1</div>
            <div class="calendar-day">2</div>
            <div class="calendar-day active">3</div>
            <div class="calendar-day">4</div>
            <div class="calendar-day">5</div>
            <div class="calendar-day">6</div>
            <div class="calendar-day">7</div>
          </div>
        </div>
      </div>
    </section>
  </main>
<script>
    function openEventForm() {
  document.getElementById("event-form").style.display = "flex";
}

function closeEventForm() {
  document.getElementById("event-form").style.display = "none";
}

function saveEvent() {
  const title = document.getElementById("event-title").value;
  const date = document.getElementById("event-date").value;
  const time = document.getElementById("event-time").value;
  const details = document.getElementById("event-details").value;

  if (title && date && time) {
    const newEvent = document.createElement("div");
    newEvent.classList.add("time-slot");
    newEvent.innerHTML = `
      <span class="time">${time}</span>
      <div class="event custom">
        <h4>${title}</h4>
        <p>${details}</p>
      </div>
    `;

    // For now, append to "today" tab
    document.getElementById("today-events").appendChild(newEvent);

    closeEventForm();
  } else {
    alert("Please fill Title, Date and Time!");
  }
}

</script>

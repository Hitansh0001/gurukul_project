<?php
include(__DIR__ . "/../includes/nav.php");
$user_id = $_SESSION['user_id'];

$successMsg = '';
$errorMsg = '';
$result = $conn->query("SELECT * FROM notes WHERE user_id=$user_id ORDER BY updated_at DESC");
$notes = [];
while ($row = $result->fetch_assoc()) {
    $notes[] = $row;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // print_r($_POST);die;
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (empty($title)) {
        $errorMsg = "Title cannot be empty";
    } else {
        $stmt = $conn->prepare("INSERT INTO notes (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $title, $content);
        if ($stmt->execute()) {
            $successMsg = "Note added successfully!";
            $result = $conn->query("SELECT * FROM notes WHERE user_id=$user_id ORDER BY updated_at DESC");
            $notes = [];
            while ($row = $result->fetch_assoc()) {
                $notes[] = $row;
            }
        } else {
            $errorMsg = "Error saving note: " . $stmt->error;
        }
        $stmt->close();
    }
}


?>
<?php if ($successMsg): ?>
    <div style="color: green; margin-bottom: 10px;"><?= $successMsg ?></div>
<?php endif; ?>
<?php if ($errorMsg): ?>
    <div style="color: red; margin-bottom: 10px;"><?= $errorMsg ?></div>
<?php endif; ?>
<main class="notes-main">
    <section class="page-header">
        <h2>My Notes</h2>
        <button class="add-btn" onclick="createNewNote()">+ New Note</button>
    </section>

    <section class="notes-grid">
        <?php foreach ($notes as $note): ?>
            <div class="note-card">
                <h3><?= htmlspecialchars($note['title']) ?></h3>
                <p><?= nl2br(htmlspecialchars($note['content'])) ?></p>
                <div class="note-meta">
                    <span>Last edited: <?= $note['updated_at'] ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

</main>

<!-- Note Editor Modal -->
<div id="note-modal" class="modal hidden">
    <div class="modal-content">
        <span class="close" onclick="closeNoteModal()">&times;</span>
        <h3 id="modal-title">New Note</h3>

        <!-- Form for creating note -->
        <form id="note-form" action="notes.php" method="POST">
            <input type="text" id="note-title" name="title" placeholder="Note title..." required>
            <textarea id="note-content" name="content" placeholder="Write your note here..." required></textarea>
            <div class="modal-actions">
                <button type="submit">Save</button>
                <button type="button" onclick="closeNoteModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="script.js"></script>
<script>
    function createNewNote() {
        document.getElementById("note-modal").classList.remove("hidden");
        document.getElementById("modal-title").innerText = "New Note";
        document.getElementById("note-form").reset();
    }

    function closeNoteModal() {
        document.getElementById("note-modal").classList.add("hidden");
    }
</script>
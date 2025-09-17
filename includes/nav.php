<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../includes/auth.php";

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gurukul</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 14px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        .logout-section {
            margin-left: auto;
        }

        .head {
            display: flex;
            gap: 15px;
        }

        .welcome-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: #fff;
            padding: 12px 20px;
            border-radius: 6px;
            opacity: 0;
            transition: opacity 0.5s ease;
            font-family: sans-serif;
            z-index: 9999;
        }
    </style>
</head>

<body>
    <header>
        <div class="head">
            <h1>Gurukul</h1>
            <div class="logout-section">
                <?php if (isset($_SESSION['username'])): ?>
                    <a href="../logout.php" class="logout-btn">Logout</a>
                <?php endif; ?>
            </div>
        </div>

        <nav>
            <a href="dashboard.php" class="<?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
            <a href="notes.php" class="<?= $currentPage == 'notes.php' ? 'active' : '' ?>">Notes</a>
            <a href="tasks.php" class="<?= $currentPage == 'tasks.php' ? 'active' : '' ?>">Tasks</a>
            <a href="schedule.php" class="<?= $currentPage == 'schedule.php' ? 'active' : '' ?>">Schedule</a>
            <a href="ai-chat.php" class="<?= $currentPage == 'ai-chat.php' ? 'active' : '' ?>">AI Chat</a>
        </nav>

    </header>

    </script>
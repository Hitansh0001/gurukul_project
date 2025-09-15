<?php


require_once "config/database.php";
require_once "includes/auth.php";
require_once "includes/functions.php";
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Assign User - Task Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="kanban.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <?php if ($currentPage == 'chat_group.php') { ?>
        <link rel="stylesheet" href="chat.css">
    <?php } ?>
</head>

<body>


    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 60px;
            /* Collapsed width */
            background-color: #0066cc;
            padding-top: 45px;
            transition: width 0.3s ease;
            overflow-x: hidden;
            z-index: 1000;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 45px;
            transition: width 0.3s ease;

        }

        .sidebar .nav-link span {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.09s ease 0.09s, visibility 0s linear 0.09s;
            white-space: nowrap;
        }

        .sidebar:hover .nav-link span {
            opacity: 1;
            visibility: visible;
            transition-delay: 0s;
        }

        .sidebar:hover .nav-link {
            width: 202px;
            /* expanded state */
        }

        .sidebar:hover {
            width: 220px;
            /* Expanded width on hover */
        }

        #main-content {
            margin-left: 60px;
            transition: margin-left 0.3s ease;
        }

        /* This works because both .sidebar and #main-content are siblings */
        .sidebar:hover~#main-content {
            margin-left: 225px;
        }

        .sidebar .nav-link i {
            width: 20px;
            min-width: 20px;
            text-align: center;
            margin-right: 8px;
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .sidebar .nav-link {
            color: #fff;

        }

        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background-color: #0066cc;
            /* hover/active highlight */
        }

        .top-navbar {
            z-index: 1060;
        }



        @media (min-width: 768px) {
            .sidebar.show+#main-content {
                max-width: calc(100% - 250px);
                margin-left: 250px;
            }
        }
    </style>

    <!-- Top Navbar -->
    <nav class="navbar  fixed-top top-navbar">
        <div class="container-fluid">
            <!-- <button class="btn btn-outline-light me-2" id="toggleSidebar">
                <i class="fas fa-bars"></i>
            </button> -->
            <a class="navbar-brand" href="dashboard.php">Task Management</a>

            <div class="d-flex align-items-center ms-auto nnn">
                <span class="text-white me-3">
                    <i class="bi bi-person-circle"></i>
                    <?php echo getCurrentUsername(); ?> (<?php echo getCurrentUserRole(); ?>)
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar bg-dark" id="sidebarMenu">
        <ul class="nav flex-column px-2 py-4">
            <li class="nav-item">
                <a class="nav-link <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i> <span> Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentPage == 'kanban.php' ? 'active' : '' ?>" href="kanban.php">
                    <i class="fas fa-tasks me-2"></i> <span> My Tasks</span>
                </a>
            </li>
            <?php if (isManager()): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'manage_users.php' ? 'active' : '' ?>" href="manage_users.php">
                        <i class="fas fa-users-cog me-2"></i> <span> Manage Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'manager_dashboard.php' ? 'active' : '' ?>" href="manager_dashboard.php">
                        <i class="fas fa-users me-2"></i> <span> Team Overview</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if (isSuper()): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'assign_user.php' ? 'active' : '' ?>" href="assign_user.php">
                        <i class="fas fa-user-plus me-2"></i> <span> Assign Users</span>
                    </a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link <?= $currentPage == 'chat_group.php' ? 'active' : '' ?>" href="chat_group.php">
                    <i class="fas fa-comments me-2"></i> <span> Chat Group</span>
                </a>
            </li>
        </ul>
    </div>
    <div id="main-content">
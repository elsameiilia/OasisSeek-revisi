<?php
if (!session_id()) session_start();
include_once __DIR__ . "/../database/database.php";
include_once __DIR__ . "/../middleware/middleware.php";
isLoggedIn();
isAdmin();

// Fetch the total number of destinations
$query_destinations = "SELECT COUNT(*) as total_destinations FROM destinations";
$stmt_destinations = mysqli_prepare($dbs, $query_destinations);
if (!$stmt_destinations) {
    die("Prepare failed: " . mysqli_error($dbs));
}
mysqli_stmt_execute($stmt_destinations);
mysqli_stmt_bind_result($stmt_destinations, $total_destinations);
mysqli_stmt_fetch($stmt_destinations);
mysqli_stmt_close($stmt_destinations);

// Fetch the total number of events
$query_events = "SELECT COUNT(*) as total_events FROM events";
$stmt_events = mysqli_prepare($dbs, $query_events);
if (!$stmt_events) {
    die("Prepare failed: " . mysqli_error($dbs));
}
mysqli_stmt_execute($stmt_events);
mysqli_stmt_bind_result($stmt_events, $total_events);
mysqli_stmt_fetch($stmt_events);
mysqli_stmt_close($stmt_events);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/../template/meta.php"; ?>
    <title>Document</title>
    <link rel="stylesheet" href="../images/assets/styles.css">
    <style>
        .stats-grid {
            align-self: start;
            display: flex;
            margin-top: 31px;
            align-items: center;
            gap: 36px;
            font-family: Poppins, sans-serif;
            white-space: nowrap;
            justify-content: start;
            flex-wrap: wrap;
        }

        @media (max-width: 991px) {
            .stats-grid {
                white-space: initial;
            }

        }

        .stats-card {
            border-radius: 0;
            align-self: stretch;
            display: flex;
            min-width: 240px;
            flex-direction: column;
            width: 327px;
            margin: auto 0;
            text-decoration: none;
        }

        @media (max-width: 991px) {
            .stats-card {
                white-space: initial;
            }
        }

        .card-content {
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            display: flex;
            width: 100%;
            flex-direction: column;
            align-items: start;
            justify-content: center;
            padding: 31px;
        }

        @media (max-width: 991px) {
            .card-content {
                white-space: initial;
                padding: 0 20px;
            }
        }

        .stats-wrapper {
            display: flex;
            align-items: end;
            gap: 23px;
            justify-content: start;
        }

        @media (max-width: 991px) {
            .stats-wrapper {
                white-space: initial;
            }
        }

        .stats-icon {
            aspect-ratio: 1;
            object-fit: contain;
            object-position: center;
            width: 73px;
        }

        .stats-info {
            display: flex;
            flex-direction: column;
            justify-content: start;
            width: 101px;
        }

        @media (max-width: 991px) {
            .stats-info {
                white-space: initial;
            }
        }

        .stats-number {
            color: black;
            font-size: 30px;
            font-family: 'Sora', sans-serif;
            font-weight: 600;
        }

        .stats-label {
            color: #747171;
            font-size: 20px;
            margin-top: 17px;
        }

        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }
    </style>
</head>

<body>
<?php include_once __DIR__ . "/../template/navbarAdm.php"; ?>

<!-- ======= MAIN DASHBOARD ========  -->
<div class="main-dashboard">
    <div class="dashboard">

        <!-- ===== Header =======  -->
        <header class="dashboard-header">
            <h1 class="page-title-dashboard">Manage Posts</h1>
            <div class="user-profile-dashboard">
                <img class="profile-icon-dashboard" src="../images/assets/profile-admin.png" alt="User profile"/>
                <div class="profile-text-dashboard">Admin</div>
            </div>
        </header>

        <!-- ===== Konten Stats ======= -->
        <div class="dashboard-content">
            <section class="stats-grid" aria-label="Statistics Overview">
                <a href="/admin/manageDstn.php" class="stats-card">
                    <div class="card-content">
                        <div class="stats-wrapper"><img loading="lazy" src="../images/assets/places-POST.png"
                                                        class="stats-icon" alt="Places icon"/>
                            <div class="stats-info">
                                <div class="stats-number"><?= htmlspecialchars($total_destinations); ?></div>
                                <div class="stats-label">Places</div>
                            </div>
                        </div>
                    </div>
                </a> <a href="/admin/manageEvnt.php" class="stats-card">
                    <div class="card-content">
                        <div class="stats-wrapper"><img loading="lazy" src="../images/assets/events-POST.png"
                                                        class="stats-icon" alt="Events icon"/>
                            <div class="stats-info">
                                <div class="stats-number"><?= htmlspecialchars($total_events); ?></div>
                                <div class="stats-label">Events</div>
                            </div>
                        </div>
                    </div>
                </a>
            </section>
        </div>
    </div>
</div>
</body>

</html>
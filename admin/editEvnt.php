<?php
if (!session_id()) session_start();

include_once __DIR__ . "/../database/database.php";
include_once __DIR__ . "/../middleware/middleware.php";
isLoggedIn();
isAdmin();

$event_id = $_GET['id'] ?? 0;

if (!isset($event_id) or $event_id <= 0) {
    header('Location: /admin/manageEvnt.php');
    exit();
}

// Fetch event details
$query = "SELECT event_id, name, title, description, location, date, time FROM events WHERE event_id = ?";
$stmt = mysqli_prepare($dbs, $query);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($dbs));
}
mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $event_id, $name, $title, $description, $location, $date, $time);
mysqli_stmt_fetch($stmt);

$event = [
    'event_id' => $event_id,
    'name' => $name,
    'title' => $title,
    'description' => $description,
    'location' => $location,
    'date' => $date,
    'time' => $time
];

mysqli_stmt_close($stmt);

if (!$event['event_id']) {
    header('Location: /admin/manageEvnt.php');
    exit();
}

if (isset($_POST["update"])) {
    $name = $_POST["name"] ?? "";
    $title = $_POST["title"] ?? "";
    $description = $_POST["description"] ?? "";
    $location = $_POST["location"] ?? "";
    $date = $_POST["date"] ?? "";
    $time = $_POST["time"] ?? "";

    try {
        $query = "UPDATE events SET name = ?, title = ?, description = ?, location = ?, date = ?, time = ? WHERE event_id = ?";
        $stmt = mysqli_prepare($dbs, $query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($dbs));
        }
        mysqli_stmt_bind_param($stmt, "ssssssi", $name, $title, $description, $location, $date, $time, $event_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Success message or redirect
        echo "<script>
        alert('Event berhasil di update');
        location.replace('/admin/manageEvnt.php');
        </script>";
        exit();
    } catch (Exception $e) {
        // Handle the error
        echo "<script>
        alert('Event gagal di update');
        location.replace('/admin/manageEvnt.php');
        </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/../template/meta.php"; ?>
    <title>Edit Event</title>
    <link rel="stylesheet" href="../images/assets/styles.css">
    <style>
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-top: 6px;
            margin-bottom: 30px;
        }

        .form-label {
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 600;
            margin-top: 50px;
        }


        .form-input {
            border-radius: 5px;
            background-color: rgba(249, 250, 251, 1);
            padding: 11px 18px;
            border: 1px solid rgba(115, 76, 16, 1);
            font-size: 12px;
            font-weight: 300;
            width: 100%;
            margin-top: 5px;
        }


        .datetime-container {
            display: flex;
            gap: 30px;
            margin-top: 23px;
        }

        .date-input,
        .time-input {
            flex: 1;
        }


        .form-textarea {
            border-radius: 5px;
            background-color: rgba(249, 250, 251, 1);
            width: 100%;
            padding: 10px 18px 40px;
            border: 1px solid rgba(115, 76, 16, 1);
            font-size: 12px;
            font-weight: 300;
            resize: vertical;
            margin-bottom: 10px;
            margin-top: 5px;
        }

        .upload-container {
            border-radius: 5px;
            background-color: rgba(249, 250, 251, 1);
            display: flex;
            margin-top: 11px;
            flex-direction: column;
            align-items: center;
            color: rgba(115, 76, 16, 1);
            letter-spacing: 0.3px;
            justify-content: center;
            padding: 26px 80px;
            font: 400 12px/2 Poppins, sans-serif;
            border: 1px dashed rgba(115, 76, 16, 1);
        }

        #thumbnail-preview,
        #gallery-preview {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: flex-start;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .file-preview {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #000;
        }

        .preview-icon {
            width: 20px;
            height: 20px;
        }

        .visually-hidden {
            display: none;
        }

        .upload-icon {
            width: 32px;
            height: 32px;
        }


        /* button save & cancel */
        .action-buttons {
            align-self: end;
            display: flex;
            margin-top: 34px;
            align-items: center;
            gap: 14px;
            font-size: 20px;
        }

        .btn-cancel {
            border-radius: 24px;
            color: var(--btn-daftar-masuk, #734c10);
            padding: 15px 25px;
            border: 1px solid rgba(115, 76, 16, 1);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background-color: #ebdac8;
            border-color: #734c10;
            color: #734c10;
        }

        .btn-save {
            border-radius: 24px;
            background-color: rgba(115, 76, 16, 1);
            color: var(--Foundation-Yellow-Light, #f8f3ed);
            padding: 15px 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-save:hover {
            background-color: #ebdac8;
            border: 1px solid #734c10;
            color: #734c10;
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

        @media (max-width: 991px) {
            .dashboard-container {
                padding: 0 20px;
            }

            .sidebar-container {
                margin-top: 40px;
            }

            .main-content {
                padding: 0 20px 100px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .datetime-container {
                flex-direction: column;
                gap: 20px;
            }
            
            .action-buttons {
                margin-right: 3px;
            }

            .btn-cancel,
            .btn-save {
                padding: 0 20px;
            }
        }
    </style>
</head>

<body>
<?php include_once __DIR__ . "/../template/navbarAdm.php"; ?>

<!-- ======= MAIN DASHBOARD ======== -->
<div class="main-dashboard">
    <div class="dashboard">
        <!-- ===== Header ======= -->
        <header class="dashboard-header">
            <h1 class="page-title-dashboard">Edit Event</h1>
            <div class="user-profile-dashboard">
                <img class="profile-icon-dashboard" src="/images/assets/profile-admin.png" alt="User profile"/>
                <div class="profile-text-dashboard">Admin</div>
            </div>
        </header>

        <!-- ===== Konten Posts ======= -->
        <div class="dashboard-content">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-grid">
                    <div>
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-input"
                               value="<?= htmlspecialchars($event["name"]) ?>" required>
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" id="title" class="form-input"
                               value="<?= htmlspecialchars($event["title"]) ?>" required>
                        <label for="location" class="form-label">Location</label>
                        <input type="text" name="location" id="location" class="form-input"
                               value="<?= htmlspecialchars($event["location"]) ?>" required>
                        <div class="datetime-container">
                            <div class="date-input">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" name="date" id="date" class="form-input"
                                       value="<?= htmlspecialchars($event["date"]) ?>" required>
                            </div>
                            <div class="time-input">
                                <label for="time" class="form-label">Time</label>
                                <input type="time" name="time" id="time" class="form-input"
                                       value="<?= htmlspecialchars($event["time"]) ?>" required>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-textarea" rows="10"
                                  required><?= htmlspecialchars($event["description"]) ?></textarea>
                    </div>
                </div>
                <!-- button save and cancel -->
                <div class="action-buttons">
                    <button type="button" class="btn-cancel"
                            onclick="window.location.href='/admin/manageEvnt.php';">Cancel
                    </button>
                    <button type="submit" name="update" class="btn-save">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>

</html>
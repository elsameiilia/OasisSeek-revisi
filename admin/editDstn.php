<?php
if (!session_id()) session_start();
include_once __DIR__ . "/../database/database.php";
include_once __DIR__ . "/../middleware/middleware.php";
isAdmin();
isLoggedIn();

$des_id = $_GET['id'] ?? 0;

if (!isset($des_id) or $des_id <= 0) {
    header('Location: /admin/manageDstn.php');
    exit();
}

// Fetch destination details
$query = "SELECT des_id, name, title, description FROM destinations WHERE des_id = ?";
$stmt = mysqli_prepare($dbs, $query);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($dbs));
}
mysqli_stmt_bind_param($stmt, "i", $des_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $des_id, $name, $title, $description);
mysqli_stmt_fetch($stmt);

$destination = [
    'des_id' => $des_id,
    'name' => $name,
    'title' => $title,
    'description' => $description
];

mysqli_stmt_close($stmt);

if (!$destination['des_id']) {
    header('Location: /admin/manageDstn.php');
    exit();
}

if (isset($_POST["update"])) {
    $name = $_POST["name"] ?? "";
    $title = $_POST["title"] ?? "";
    $description = $_POST["description"] ?? "";

    try {
        $query = "UPDATE destinations SET name = ?, title = ?, description = ? WHERE des_id = ?";
        $stmt = mysqli_prepare($dbs, $query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($dbs));
        }
        mysqli_stmt_bind_param($stmt, "sssi", $name, $title, $description, $des_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Success message or redirect
        echo "<script>
        alert('Places berhasil di update');
        location.replace('/admin/manageDstn.php');
        </script>";
        exit();
    } catch (Exception $e) {
        // Handle the error
        echo "<script>
        alert('Places gagal di update');
        location.replace('/admin/manageDstn.php');
        </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/../template/meta.php"; ?>
    <title>Edit Destination</title>
    <link rel="stylesheet" type="text/css" href="../images/assets/styles.css"/>
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

<div class="container-dashboard">
    <?php include_once __DIR__ . "/../template/navbarAdm.php"; ?>
    <!-- Edit -->
    <div class="main-dashboard">
        <div class="dashboard">
            <!-- ===== Header ======= -->
            <header class="dashboard-header">
                <h1 class="page-title-dashboard">Edit Destination</h1>
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
                            <input type="text" name="name" id="name" class="form-input" value="<?= htmlspecialchars($destination["name"]) ?>" required>
                        </div>
                        <div>
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-input" value="<?= htmlspecialchars($destination["title"]) ?>" required>
                        </div>
                    </div>
                    <div>
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-textarea" rows="10" required><?= htmlspecialchars($destination["description"]) ?></textarea>
                    </div>
                    <!-- button save and cancel -->
                    <div class="action-buttons">
                        <button type="button" class="btn-cancel"
                                onclick="window.location.href='/admin/manageDstn.php';">Cancel
                        </button>
                        <button type="submit" name="update" class="btn-save">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


</body>

</html>
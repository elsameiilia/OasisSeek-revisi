<?php

if (!session_id()) session_start();
include_once __DIR__ . "/../database/database.php";
include_once __DIR__ . "/../middleware/middleware.php";
isAdmin();
isLoggedIn();

// Fetch destinations
$query = "SELECT des_id, name, banner FROM destinations ORDER BY des_id DESC";
$stmt = mysqli_prepare($dbs, $query);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $des_id, $name, $banner);

$destinations = [];
while (mysqli_stmt_fetch($stmt)) {
    $destinations[] = [
        'des_id' => $des_id,
        'name' => $name,
        'banner' => $banner
    ];
}
mysqli_stmt_close($stmt);

if (isset($_POST['delete'])) {
    // Fetch destination details for deletion
    $stmt = mysqli_prepare($dbs, 'SELECT des_id, banner FROM destinations WHERE des_id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $_POST['des_id']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $des_id, $banner);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($des_id) {
        $pathdir = __DIR__ . '/../images/destinations/';

        if (file_exists($pathdir . $banner)) {
            unlink($pathdir . $banner);
        }

        // Delete associated images from img_destinations
        $stmt = mysqli_prepare($dbs, "SELECT photo FROM img_destinations WHERE des_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $des_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $photo);

        while (mysqli_stmt_fetch($stmt)) {
            if (file_exists($pathdir . $photo)) {
                unlink($pathdir . $photo);
            }
        }
        mysqli_stmt_close($stmt);

        $stmt = mysqli_prepare($dbs, "DELETE FROM img_destinations WHERE des_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $des_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $stmt = mysqli_prepare($dbs, "DELETE FROM destinations WHERE des_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $des_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        echo "<script>
            alert('Delete successfully');
            location.replace('/admin/manageDstn.php');
        </script>";
    } else {
        echo "<script>
            alert('ERROR: Destination not found');
            location.replace('/admin/manageDstn.php');
        </script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/../template/meta.php"; ?>
    <link rel="stylesheet" type="text/css" href="../images/assets/styles.css"/>
    <title>Document</title>
    <style>
        .places-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: flex-start;
            margin: 20px auto;
        }

        .place-card {
            display: flex;
            flex-direction: column;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
            background: #fff;
            max-width: 200px;
            width: 100%;
            overflow: hidden;
        }

        .image-container {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        .place-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .action-buttons {
            position: absolute;
            bottom: 10px;
            right: 10px;
            display: flex;
            gap: 8px;
        }

        .action-icon {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            cursor: pointer;
            border: none;
        }

        .card-content {
            padding: 12px;
            height: 83px;
            text-align: left;
            background-color: #734c10;
            color: #eef1f6;
            font-family: "Sora", sans-serif;
            font-size: 14px;
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
        }

        .place-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .place-date {
            font-weight: 300;
            font-size: 12px;
            opacity: 0.8;
        }

        .add-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #734c10;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            padding: 10px 16px;
            margin: 20px;
            border-radius: 8px;
            cursor: pointer;
            z-index: 100;
            border: none;
        }

        .add-button:hover {
            background: #5e3a0e;
            transition: 0.3s;
        }

        @media (max-width: 768px) {
            .places-grid {
                gap: 12px;
            }

            .place-card {
                max-width: 150px;
            }

            .card-content {
                font-size: 12px;
            }

            .add-button {
                padding: 8px 12px;
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
<div class="container-dashboard">
    <?php include_once __DIR__ . "/../template/navbarAdm.php"; ?>
    <div class="main-dashboard">
        <div class="dashboard">
            <header class="dashboard-header">
                <h1 class="page-title-dashboard">Places</h1>
                <div class="user-profile-dashboard">
                    <img class="profile-icon-dashboard" src="../images/assets/profile-admin.png" alt="User profile"/>
                    <div class="profile-text-dashboard">Admin</div>
                </div>
            </header>
            <div class="dashboard-content">
                <div class="places-grid">
                    <?php foreach ($destinations as $data): ?>
                        <article class="place-card">
                            <div class="image-container">
                                <img src="/images/destinations/<?= $data["banner"] ?>" alt="Destination banner"
                                     class="place-image"/>
                                <div class="action-buttons">
                                    <button class="action-icon" aria-label="Edit place"
                                            onclick="window.location.href='/admin/editDstn.php?id=<?= htmlspecialchars($data["des_id"]); ?>';">
                                        ✏️
                                    </button>
                                    <form action="" method="post">
                                        <input type="hidden" name="des_id"
                                               value="<?= htmlspecialchars($data["des_id"]); ?>">
                                        <button class="action-icon" type="submit" name="delete"
                                                aria-label="Delete place">❌
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="content-wrapper">
                                    <h2 class="place-title"><?= htmlspecialchars($data["name"]); ?></h2>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <button class="add-button" onclick="window.location.href='/admin/createDstn.php';">Add Post</button>
            </div>
        </div>
    </div>
</div>
</body>

</html>
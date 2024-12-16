<?php
if (!session_id()) session_start();
include_once __DIR__ . "/../database/database.php";
include_once __DIR__ . "/../middleware/middleware.php";
isLoggedIn();
isAdmin();

if (isset($_POST["create"])) {
    $name = $_POST["name"];
    $title = $_POST["title"];
    $description = $_POST["description"];
    $location = $_POST["location"];
    $date = $_POST["date"];
    $time = $_POST["time"];
    $banner = "";

    $pathdir = __DIR__ . '/../images/events/';

    mysqli_begin_transaction($dbs);

    try {
        if (isset($_FILES["banner"]) && $_FILES["banner"]["error"] === UPLOAD_ERR_OK) {
            $photo_tmp_path = $_FILES['banner']['tmp_name'];
            $photo_extension = pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION);
            $photo_filename = uniqid() . '.' . $photo_extension;
            $photo_path = $pathdir . $photo_filename;

            if (move_uploaded_file($photo_tmp_path, $photo_path)) {
                $banner = $photo_filename;
            } else {
                echo '<script>alert("Error uploading banner photo"); location.replace("/admin/createEvnt.php");</script>';
                exit();
            }
        }

        $query = "INSERT INTO events (name, title, description, location, date, time, banner) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($dbs, $query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($dbs));
        }
        mysqli_stmt_bind_param($stmt, "sssssss", $name, $title, $description, $location, $date, $time, $banner);
        mysqli_stmt_execute($stmt);

        mysqli_commit($dbs);

        echo "<script>
            alert('Event added successfully');
            location.replace('/admin/manageEvnt.php');
        </script>";

    } catch (Exception $err) {
        mysqli_rollback($dbs);
        echo "<script>alert('Error: " . $err->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/../template/meta.php"; ?>
    <title>Create Event - OasisSeek</title>
    <link rel="stylesheet" type="text/css" href="../images/assets/styles.css"/>
    <style>
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-top: 6px;
            margin-bottom: 15px;
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
            color: rgba(0, 0, 0, 0.5);
            letter-spacing: 0.3px;
            padding: 9px 18px;
            font: 400 12px/2 'Poppins', sans-serif;
            border: 1px solid rgba(115, 76, 16, 1);
            width: 100%;
            height: 30px;
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
            color: rgba(0, 0, 0, 0.5);
            letter-spacing: 0.3px;
            padding: 9px 17px;
            min-height: 100px;
            font: 400 12px/2 Poppins, sans-serif;
            border: 1px solid rgba(115, 76, 16, 1);
            width: 100%;
            margin-bottom: 0px;
        }


        .upload-container {
            border-radius: 5px;
            background-color: rgba(249, 250, 251, 1);
            display: flex;
            justify-content: center;
            width: 100%;
            margin-top: 11px;
            flex-direction: column;
            align-items: center;
            color: rgba(115, 76, 16, 1);
            letter-spacing: 0.3px;
            padding: 26px 80px;
            font: 400 12px/2 Poppins, sans-serif;
            border: 1px dashed rgba(115, 76, 16, 1);
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


        .submit-button {
            border-radius: 5px;
            background-color: rgba(115, 76, 16, 1);
            align-self: end;
            display: flex;
            margin-top: 40px;
            min-height: 42px;
            align-items: center;
            gap: 5px;
            color: var(--white, #fff);
            text-align: center;
            justify-content: center;
            padding: 11px 20px;
            font: 13px 'Poppins', sans-serif;
            border: none;
            cursor: pointer;
            border-style: none;
        }

        .submit-icon {
            aspect-ratio: 1;
            object-fit: contain;
            width: 14px;
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
            <h1 class="page-title-dashboard">Add Event</h1>
            <div class="user-profile-dashboard">
                <img class="profile-icon-dashboard" src="/images/assets/profile-admin.png" alt="User profile"/>
                <div class="profile-text-dashboard">Admin</div>
            </div>
        </header>

        <!-- ===== Konten Posts =======  -->
        <div class="dashboard-content">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-grid">
                    <div>
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-input" required>
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" id="title" class="form-input" required>
                        <label for="location" class="form-label">Location</label>
                        <input type="text" name="location" id="location" class="form-input" required>
                        <div class="datetime-container">
                            <div class="date-input">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" name="date" id="date" class="form-input" required>
                            </div>
                            <div class="time-input">
                                <label for="time" class="form-label">Time</label>
                                <input type="time" name="time" id="time" class="form-input" required>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-textarea" rows="10" required></textarea>
                    </div>
                </div>
                <label for="banner" class="form-label">Banner:</label>
                <div class="upload-container" role="button" tabindex="0"
                         onclick="document.getElementById('thumbnail-upload').click()">
                        <div id="thumbnail-preview"><img src="/images/assets/upload.png" alt="" class="upload-icon"/>
                            <span>Click to upload photo</span>
                        </div>
                        <input type="file" name="banner" id="thumbnail-upload" class="visually-hidden" accept="image/*" required onchange="updateThumbnailPreview(event)"/>
                    </div> 
                <button type="submit" name="create" class="submit-button"><img src="/images/assets/add-post.png" alt="" class="submit-icon"/> Add Post</button>
            </form>
        </div>
    </div>
</div>

                    <script>
                        // preview thumbnile
                        function updateThumbnailPreview(event) {
                            const previewContainer = document.getElementById('thumbnail-preview');
                            const files = event.target.files;

                            // hapus previous content
                            previewContainer.innerHTML = '';

                            if (files && files[0]) {
                                const file = files[0];

                                // preview icon
                                const icon = document.createElement('img');
                                icon.src = '../assets/attach-icon.png'; // Replace with the attach icon URL
                                icon.alt = 'Attach Icon';
                                icon.className = 'preview-icon';

                                // tambah nama file
                                const fileName = document.createElement('span');
                                fileName.textContent = file.name;

                                // tambah ke container
                                previewContainer.appendChild(icon);
                                previewContainer.appendChild(fileName);
                            }
                        }
                    </script>


</body>

</html>
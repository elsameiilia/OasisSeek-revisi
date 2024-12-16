<?php
if (!session_id()) session_start();
include_once __DIR__ . "/../database/database.php";
include_once __DIR__ . "/../middleware/middleware.php";
isAdmin();
isLoggedIn();

if (isset($_POST["create"])) {
    $name = $_POST["name"] ?? "";
    $title = $_POST["title"] ?? "";
    $description = $_POST["description"] ?? "";
    $gallery = $_FILES["gallery"] ?? [];
    $banner = "";

    // Use an absolute path for the upload directory
    $pathdir = __DIR__ . "/../images/destinations/";

    mysqli_begin_transaction($dbs);

    try {
        if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
            $photo_tmp_path = $_FILES['banner']['tmp_name'];
            $photo_extension = pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION);
            $photo_filename = uniqid() . '.' . $photo_extension;
            $photo_path = $pathdir . $photo_filename;

            if (move_uploaded_file($photo_tmp_path, $photo_path)) {
                $banner = $photo_filename;
            } else {
                throw new Exception('Error uploading banner photo');
            }
        }

        $query = "INSERT INTO destinations (name, title, description, banner) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($dbs, $query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($dbs));
        }
        mysqli_stmt_bind_param($stmt, "ssss", $name, $title, $description, $banner);
        mysqli_stmt_execute($stmt);
        $des_id = mysqli_insert_id($dbs);

        if (!empty($gallery["tmp_name"])) {
            foreach ($gallery["tmp_name"] as $index => $tmp_name) {
                $extension = pathinfo($gallery["name"][$index], PATHINFO_EXTENSION);
                $img_name = uniqid() . "." . $extension;

                if (move_uploaded_file($tmp_name, $pathdir . $img_name)) {
                    $query = "INSERT INTO img_destinations (des_id, photo) VALUES (?, ?)";
                    $stmt = mysqli_prepare($dbs, $query);
                    if (!$stmt) {
                        throw new Exception("Prepare failed: " . mysqli_error($dbs));
                    }
                    mysqli_stmt_bind_param($stmt, "is", $des_id, $img_name);
                    mysqli_stmt_execute($stmt);
                } else {
                    throw new Exception('Error uploading gallery photo');
                }
            }
        }

        mysqli_commit($dbs);
        echo "<script>
            alert('Destination added successfully');
            location.replace('/admin/manageDstn.php');
          </script>";

    } catch (Exception $e) {
        mysqli_rollback($dbs);
        echo "<script>
            alert('Error: " . $e->getMessage() . "');
            location.replace('/admin/createDstn.php');
          </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/../template/meta.php"; ?>
    <title>Create Destination</title>
    <link rel="stylesheet" href="../images/assets/styles.css">
    <style>
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-top: 6px;
            margin-bottom: 10px;
        }

        .form-group {
            display: flex;
            gap: 25px;
            font-size: 20px;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .form-label {
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 600;
            margin-top: 30px;
        }

        .form-input {
            border-radius: 5px;
            background-color: rgba(249, 250, 251, 1);
            color: rgba(0, 0, 0, 0.5);
            letter-spacing: 0.3px;
            padding: 9px 18px;
            font: 400 12px/2 Poppins, sans-serif;
            border: 1px solid rgba(115, 76, 16, 1);
            width: 100%;
            height: 30px;
            margin-bottom: 5px;
        }

        .form-textarea {
            border-radius: 5px;
            background-color: rgba(249, 250, 251, 1);
            color: rgba(0, 0, 0, 0.5);
            letter-spacing: 0.3px;
            padding: 9px 17px;
            min-height: 100px;
            font: 400 12px/2 'Poppins', sans-serif;
            border: 1px solid rgba(115, 76, 16, 1);
            width: 100%;
            margin-bottom: 10px;
        }

        .upload-container {
            border-radius: 5px;
            background-color: rgba(249, 250, 251, 1);
            display: flex;
            margin-top: 0px;
            flex-direction: column;
            align-items: center;
            color: rgba(115, 76, 16, 1);
            letter-spacing: 0.3px;
            justify-content: center;
            padding: 26px 80px;
            font: 400 12px/2 'Poppins', sans-serif;
            border: 1px dashed rgba(115, 76, 16, 1);
            margin-bottom: 10px;
        }

        .upload-icon {
            aspect-ratio: 1;
            object-fit: contain;
            width: 24px;
            margin-bottom: 8px;
        }

        *

        /
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


        .submit-button {
            border-radius: 5px;
            background-color: rgba(115, 76, 16, 1);
            align-self: end;
            display: flex;
            margin-top: 20px;
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
                max-width: 100%;
                padding: 0 20px 100px;
            }

            .header {
                max-width: 100%;
            }

            .form-input,
            .form-textarea {
                max-width: 100%;
                padding-right: 20px;
            }

            .upload-container {
                max-width: 100%;
                padding: 20px;
            }
        }
    </style>

</head>

<body>


<div class="container-dashboard"> 
    <?php include_once __DIR__ . "/../template/navbarAdm.php"; ?> 
    <div class="main-dashboard">
        <div class="dashboard"> 
            <!-- ===== Header ======= -->
            <header class="dashboard-header">
                <h1 class="page-title-dashboard">Add Place</h1>
                <div class="user-profile-dashboard"><img class="profile-icon-dashboard" src="/images/assets/profile-admin.png"
                                                         alt="User profile"/>
                    <div class="profile-text-dashboard">Admin</div>
                </div>
            </header> 
            <!-- ===== Konten Posts ======= -->
            <div class="dashboard-content">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div>
                            <label for="name" class="form-label">Name</label> <input type="text" name="name" id="name" class="form-input" placeholder="Enter place name" required>
                        </div>
                        <div>
                            <label for="title" class="form-label">Title</label> 
                            <input type="text" name="title"  id="title" class="form-input" placeholder="Enter post title" required>
                        </div>
                    </div> 
                    <div>
                        <label for="description" class="form-label">Description</label> <textarea name="description" id="description" class="form-textarea" placeholder="Describe places in paragraphs" required></textarea>
                    </div>
                    <!-- Thumbnail --> 
                    <label for="thumbnail-upload" class="form-label">Thumbnail</label>
                    <div class="upload-container" role="button" tabindex="0"
                         onclick="document.getElementById('thumbnail-upload').click()">
                        <div id="thumbnail-preview"><img src="/images/assets/upload.png" alt="" class="upload-icon"/>
                            <span>Click to upload photo</span>
                        </div>
                        <input type="file" name="banner" id="thumbnail-upload" class="visually-hidden" accept="image/*" required onchange="updateThumbnailPreview(event)"/>
                    </div> 
                    <!-- Gallery --> 
                    <label for="gallery-upload" class="form-label">Gallery</label>
                    <div class="upload-container" role="button" tabindex="0" onclick="document.getElementById('gallery-upload').click()">
                        <div id="gallery-preview"><img src="/images/assets/upload.png" alt="" class="upload-icon"/>
                            <span>Click to upload photo (max 3 photos)</span>
                        </div>
                        <input type="file" name="gallery[]" id="gallery-upload" class="visually-hidden" accept="image/*" multiple onchange="updateGalleryPreview(event)"/>
                    </div>
                    <button type="submit" name="create" class="submit-button"><img src="/images/assets/add-post.png" alt="" class="submit-icon"/> Add Post</button>
                </form>
            </div>
        </div>
    </div>
</div> <!-- ===== FUNGSI JS UNTUK PREVIEW UP FOTO===== -->
<script> // preview thumbnile function updateThumbnailPreview(event) { const previewContainer = document.getElementById('thumbnail-preview'); const files = event.target.files; // hapus previous content previewContainer.innerHTML = ''; if (files && files[0]) { const file = files[0]; // preview icon const icon = document.createElement('img'); icon.src = '../assets/attach-icon.png'; // Replace with the attach icon URL icon.alt = 'Attach Icon'; icon.className = 'preview-icon'; // tambah nama file const fileName = document.createElement('span'); fileName.textContent = file.name; // tambah ke container previewContainer.appendChild(icon); previewContainer.appendChild(fileName); } } // gallery upload preview function updateGalleryPreview(event) { const previewContainer = document.getElementById('gallery-preview'); const files = event.target.files; // hapus previous content previewContainer.innerHTML = ''; if (files) { Array.from(files).slice(0, 3).forEach((file) => { // wrapper untuk file preview const previewItem = document.createElement('div'); previewItem.className = 'file-preview'; // tambah preview icon const icon = document.createElement('img'); icon.src = '../assets/attach-icon.png'; // Replace with the attach icon URL icon.alt = 'Attach Icon'; icon.className = 'preview-icon'; // tambah nama file const fileName = document.createElement('span'); fileName.textContent = file.name; // tambah icon dan nama file ke preview previewItem.appendChild(icon); previewItem.appendChild(fileName); // tambah preview item ke container previewContainer.appendChild(previewItem); }); // gallery dapat up 3 file if (files.length > 3) { const warning = document.createElement('span'); warning.textContent = 'Only 3 photos can be uploaded.'; warning.style.color = 'red'; warning.style.fontSize = '12px'; previewContainer.appendChild(warning); } } } </script>
</body>

</html>
<?php
if (!session_id()) session_start();

require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/../middleware/middleware.php";

// Middleware to check if user is logged in
isLoggedIn();
isUser();

$user = $_SESSION["user"] ?? [];
$username = $user['username'] ?? '';

// Fetch user bookmarks
$query_bookmarks = "SELECT destinations.banner, destinations.name, destinations.description 
                    FROM bookmark 
                    JOIN destinations ON bookmark.des_id = destinations.des_id 
                    WHERE bookmark.username = ?";
$stmt_bookmarks = mysqli_prepare($dbs, $query_bookmarks);
if (!$stmt_bookmarks) {
    die("Prepare failed: " . mysqli_error($dbs));
}
mysqli_stmt_bind_param($stmt_bookmarks, "s", $username);
mysqli_stmt_execute($stmt_bookmarks);
mysqli_stmt_bind_result($stmt_bookmarks, $banner, $name, $description);

$bookmarks = [];
while (mysqli_stmt_fetch($stmt_bookmarks)) {
    $bookmarks[] = [
        'banner' => $banner,
        'name' => $name,
        'description' => $description
    ];
}
mysqli_stmt_close($stmt_bookmarks);

// Function to generate unique filename
function generateUniqueFilename($path, $extension)
{
    $filename = uniqid() . "." . $extension;
    while (file_exists($path . $filename)) {
        $filename = uniqid() . "." . $extension;
    }
    return $filename;
}

// Handle profile update and photo upload
if (isset($_POST["Save"])) {
    $new_username = $_POST['username'];
    $new_name = $_POST['name'];
    $new_email = $_POST['email'];
    $new_photo = $user['photo'];

    // Handle photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        if (!empty($user["photo"]) && file_exists(__DIR__ . "/.." . $user["photo"])) {
            unlink(__DIR__ . "/.." . $user["photo"]);
        }

        $photo_tmp_path = $_FILES['photo']['tmp_name'];
        $photo_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo_dir = __DIR__ . "/../images/profiles/";
        $photo_filename = generateUniqueFilename($photo_dir, $photo_extension);
        $photo_dest_path = $photo_dir . $photo_filename;

        // Move uploaded file to the destination directory
        if (move_uploaded_file($photo_tmp_path, $photo_dest_path)) {
            $new_photo = "/images/profiles/" . $photo_filename;
        } else {
            echo "<script>alert('Error uploading photo.')</script>";
        }
    }

    $update_query = "UPDATE users SET username = ?, name = ?, email = ?, photo = ? WHERE username = ?";
    $stmt_update = mysqli_prepare($dbs, $update_query);
    if (!$stmt_update) {
        die("Prepare failed: " . mysqli_error($dbs));
    }
    mysqli_stmt_bind_param($stmt_update, "sssss", $new_username, $new_name, $new_email, $new_photo, $username);
    mysqli_stmt_execute($stmt_update);
    mysqli_stmt_close($stmt_update);

    // Update session data
    $user["username"] = $new_username;
    $user["name"] = $new_name;
    $user["email"] = $new_email;
    $user["photo"] = $new_photo;
    $_SESSION["user"] = $user;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>Profile - OasisSeek</title>
    <link rel="stylesheet" type="text/css" href="../images/assets/styles.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .dashboard-profile {
        background: #fff;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        }
        
        .main-content-profile {
        flex: 1;
        padding: 85px 140px;
        margin-top: 20px;
        margin-bottom: 20px;
        }
        
        .profile-section {
        background: #bd874f;
        border-radius: 20px;
        color: #fff;
        padding: 60px 55px;
        }
        
        .profilesection-title {
        font: 28px 'Poppins', sans-serif;
        font-weight: bolder;
        margin: 0;
        }
        
        .profilesection-subtitle {
        font: 16px 'Poppins', sans-serif;
        font-weight: lighter;
        margin: 2px 0 42px;
        }
        
        .profile-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 60px;
        }
        
        .profile-form {
        display: flex;
        flex-direction: column;
        gap: 42px;
        }
        
        .form-group-profile {
        display: flex;
        align-items: center;
        gap: 25px;
        }
        
        .form-label-profile {
        color: #fff;
        font: 500 16px 'Poppins', sans-serif;
        text-align: right;
        width: 89px;
        }
        
        .form-input-profile {
        background: #fff;
        border: none;
        border-radius: 10px;
        color: #000;
        flex: 1;
        font: 500 14px 'Poppins', sans-serif;
        padding: 7px;
        }
        
        .form-actions-profile {
        display: flex;
        gap: 17px;
        margin-top: 20px;
        }
        
        .btn-primary-profile {
        background: #734c10;
        border: none;
        border-radius: 24px;
        color: #f8f3ed;
        cursor: pointer;
        font: 550 14px 'Poppins', sans-serif;
        padding: 8px 20px;
        }

        .btn-primary-profile:hover {
        background: #bd874f; 
        border: 2px solid #f8f3ed; 
        color: #f8f3ed; 
        }
        
        .profile-picture-section {
        background: #fff;
        border-radius: 10px;
        padding: 50px 45px;
        text-align: center;
        margin: 0px 0px;
        }
        
        .current-profile-picture {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        margin-bottom: 20px;
        object-fit: cover;
        }
        
        .upload-btn-profile {
        border: 1px solid #000;
        background: none;
        cursor: pointer;
        font: 300 12px 'Poppins', sans-serif;
        padding: 13px 15px;
        margin-bottom: 12px;
        }
        
        .upload-info-profile {
        color: #000;
        font: 300 14px 'Poppins', sans-serif;
        margin: 10px 0;
        }
        
        .bookmarks-section {
            display: flex;
            flex-direction: column;
            margin-top: 100px;
        }

        .bookmark-card {
            background: #fff;
            border-radius: 24px;
            display: flex;
            gap: 20px;
            padding: 19px 21px;
            margin: 15px 20px;
            align-items: center;
            margin-top: 0;
        }

        .bookmark-image {
            width: 100px;
            height: 100px;
            border-radius: 24px;
            object-fit: cover;
        }

        .bookmark-content {
            flex: 1;
        }

        .bookmark-title {
            font: 400 24px 'Sora', sans-serif;
            letter-spacing: 3.2px;
            margin: 0;
            color: black;
        }

        .bookmark-description {
            font: 300 12px 'Sora', sans-serif;
            margin-bottom: 0;
            color: black;
            /* Membatasi teks ke 2 baris */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .bookmark-action {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            transition: transform 0.3s ease; 
        }

        .bookmark-action:hover {
            transform: scale(1.1);
        }

        .action-icon {
            width: 50px;
            height: 50px;
            transition: opacity 0.3s ease; 
        }

        .bookmark-action:hover .action-icon {
            opacity: 0.7; 
        }

        
        @media (max-width: 991px) {
        .main-content {
            padding: 40px 20px;
        }
        
        .profile-section {
            padding: 40px 20px;
        }
        
        .profile-grid {
            grid-template-columns: 1fr;
        }
        
        .form-group {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        
        .form-label {
            text-align: left;
            width: auto;
        }
        
        .profile-picture-section {
            padding: 30px 20px;
        }
        
        .bookmark-card {
        flex-direction: column; 
        align-items: center; 
        text-align: center;
        }

        .bookmark-image {
            width: 100px;
            height: 100px;
        }

        .bookmark-content {
            text-align: center; 
        }
        }
    </style>
</head>

<body>
    <div class="dashboard">
        <!-- ======== HEADER ======== -->
        <div class="landing-container">
            <?php include_once __DIR__ . "/../template/navbar.php"; ?>
        </div>

        <!-- ======== FORM UBAH PROFILE ======== -->
        <main class="main-content-profile">
            <section class="profile-section" aria-labelledby="profile-title">
                <h1 id="profile-title" class="profilesection-title">My Profile</h1>
                <p class="profilesection-subtitle">Manage your account profile</p>

                <div class="profile-grid">
                    <form class="profile-form" aria-label="Profile information form" method="POST"
                        enctype="multipart/form-data">
                        <div class="form-group-profile">
                            <label for="username" class="form-label-profile">Username</label>
                            <input type="text" id="username" name="username" class="form-input-profile"
                                value="<?= htmlspecialchars($user['username']); ?>" readonly />
                        </div>
                        <div class="form-group-profile">
                            <label for="name" class="form-label-profile">Name</label>
                            <input type="text" id="name" name="name" class="form-input-profile"
                                value="<?= htmlspecialchars($user['name']); ?>" required />
                        </div>
                        <div class="form-group-profile">
                            <label for="email" class="form-label-profile">Email</label>
                            <input type="email" id="email" name="email" class="form-input-profile"
                                value="<?= htmlspecialchars($user['email']); ?>" required />
                        </div>
                        <div class="form-actions-profile">
                            <input type="submit" name="Save" class="btn-primary-profile" value="Save" />
                            <a class="btn-primary-profile" href="/logout.php">Log Out</a>
                        </div>

                        <!-- ======== UPLOAD FOTO PROFILE ======== -->
                        <div class="profile-picture-section">
                            <img src="<?= htmlspecialchars($user['photo'] ?? "../assets/update-profile.png"); ?>"
                                alt="User profile picture" class="current-profile-picture">
                            <div class="picture-upload">
                                <input type="file" id="file-input" name="photo" class="file-input" accept=".jpeg, .png"
                                    hidden />
                                <button type="button" class="upload-btn-profile"
                                    onclick="document.getElementById('file-input').click()">Select picture</button>
                                <p class="upload-info-profile">Max file size: 5 MB</p>
                                <p class="upload-info-profile">Format: .JPEG, .PNG</p>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- ======== BOOKMARKS ======== -->
                <section class="bookmarks-section" aria-labelledby="bookmarks-title">
                    <h2 id="bookmarks-title" class="profilesection-title">Bookmarks</h2>
                    <p class="profilesection-subtitle">Mark your journey through Egypt's treasures!</p>

                    <div class="bookmarks-list">
                        <?php foreach ($bookmarks as $bookmark): ?>
                            <article class="bookmark-card">
                                <img src="/images/destinations/<?= htmlspecialchars($bookmark['banner']); ?>"
                                    alt="<?= htmlspecialchars($bookmark['name']); ?>" class="bookmark-image" />
                                <div class="bookmark-content">
                                    <h3 class="bookmark-title"><?= htmlspecialchars($bookmark['name']); ?></h3>
                                    <p class="bookmark-description"><?= htmlspecialchars($bookmark['description']); ?></p>
                                </div>
                                <button type="button" class="bookmark-action" aria-label="Remove bookmark">
                                    <img src="../images/assets/bookmark-dashboard.png"
                                        alt="" class="action-icon" />
                                </button>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

            </section>
        </main>

        <!-- =========== FOOTER =========== -->
        <?php include_once __DIR__ . "/../template/footer.php"; ?>
    </div>

    <!-- =============== KODE JAVASCRIPT ================= -->
    <script>
        // UPLOAD FOTO
        const fileInput = document.getElementById('file-input');
        const currentProfilePicture = document.querySelector('.current-profile-picture');

        fileInput.addEventListener('change', (event) => {
            const file = event.target.files[0];

            if (file && file.size <= 5 * 1024 * 1024) { // Maksimum ukuran 5 MB
                const reader = new FileReader();

                reader.onload = (e) => {
                    currentProfilePicture.src = e.target.result;
                };

                reader.readAsDataURL(file);
            } else {
                alert('Please select an image smaller than 5 MB.');
            }
        });
    </script>

</body>

</html>
<?php
if (!session_id()) session_start();
include_once __DIR__ . "/database/database.php";

$username = $_SESSION['user']['username'] ?? '';

// Check if des_id or id is set
$des_id = isset($_GET['des_id']) ? $_GET['des_id'] : (isset($_GET['id']) ? $_GET['id'] : null);

if ($des_id === null) {
    header('Location: /destinations.php');
    exit();
}

// Handle bookmark actions
if (isset($_POST['bookmark_action'])) {
    $action = $_POST['bookmark_action'];

    if ($action == 'add') {
        $bookmark_query = "INSERT INTO bookmark (des_id, username) VALUES (?, ?)";
        $stmt = mysqli_prepare($dbs, $bookmark_query);
        if (!$stmt) {
            die("Prepare failed: " . mysqli_error($dbs));
        }
        mysqli_stmt_bind_param($stmt, 'is', $des_id, $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } elseif ($action == 'remove') {
        $bookmark_query = "DELETE FROM bookmark WHERE des_id = ? AND username = ?";
        $stmt = mysqli_prepare($dbs, $bookmark_query);
        if (!$stmt) {
            die("Prepare failed: " . mysqli_error($dbs));
        }
        mysqli_stmt_bind_param($stmt, 'is', $des_id, $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Fetch destination details
$query = "SELECT des_id, name, title, description, banner FROM destinations WHERE des_id = ?";
$stmt = mysqli_prepare($dbs, $query);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($dbs));
}
mysqli_stmt_bind_param($stmt, "i", $des_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $des_id, $name, $title, $description, $banner);
mysqli_stmt_fetch($stmt);
$destination = [
    'des_id' => $des_id,
    'name' => $name,
    'title' => $title,
    'description' => $description,
    'banner' => $banner
];
mysqli_stmt_close($stmt);

if (!$destination['des_id']) {
    header("Location: /destinations.php");
    exit();
}

// Fetch images related to the destination
$query_images = "SELECT photo FROM img_destinations WHERE des_id = ?";
$stmt_images = mysqli_prepare($dbs, $query_images);
if (!$stmt_images) {
    die("Prepare failed: " . mysqli_error($dbs));
}
mysqli_stmt_bind_param($stmt_images, "i", $des_id);
mysqli_stmt_execute($stmt_images);
mysqli_stmt_bind_result($stmt_images, $photo);
$images = [];
while (mysqli_stmt_fetch($stmt_images)) {
    $images[] = ['photo' => $photo];
}
mysqli_stmt_close($stmt_images);

// Check if the destination is bookmarked
$bookmark_check_query = "SELECT 1 FROM bookmark WHERE des_id = ? AND username = ?";
$stmt_check = mysqli_prepare($dbs, $bookmark_check_query);
if (!$stmt_check) {
    die("Prepare failed: " . mysqli_error($dbs));
}
mysqli_stmt_bind_param($stmt_check, 'is', $des_id, $username);
mysqli_stmt_execute($stmt_check);
mysqli_stmt_bind_result($stmt_check, $is_bookmarked);
$is_bookmarked = mysqli_stmt_fetch($stmt_check);
mysqli_stmt_close($stmt_check);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Places Each - OasisSeek</title>
    <link rel="stylesheet" type="text/css" href="/images/assets/styles.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="placeseach-wrapper">
    <!-- ======== HEADER ======== -->
    <div class="landing-container">

        <?php include_once __DIR__ . "/template/navbar.php"; ?>

        <!-- ======== HERO SECTION ======== -->
        <section class="hero-section-placeseach">
            <img src="/images/destinations/<?= htmlspecialchars($destination['banner']); ?>"
                 alt="Scenic view of <?= htmlspecialchars($destination['name']); ?>" class="hero-image-placeseach"/>
            <div class="hero-content-placeseach">
                <h1 class="hero-title-placeseach"><?= htmlspecialchars($destination['name']); ?></h1>

                <!-- ====== share & bookmarks ===== -->
                <div class="social-icons-placeseach">
                    <img src="/images/assets/share-icon.png"
                         alt="Share on social media"
                         class="social-icons-placeseach"/>
                    <form method="POST" action="">
                        <input type="hidden" name="bookmark_action" value="<?= $is_bookmarked ? 'remove' : 'add'; ?>">
                        <button type="submit">
                            <img src="/images/assets/bookmark-icon-hover.png"
                                 alt="<?= $is_bookmarked ? 'Remove from favorites' : 'Save to favorites'; ?>"
                                 class="bookmark-icon-eventeach"/>
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- ====== navigasi back to placelist ===== -->
        <nav class="breadcrumb-placeseach" aria-label="Breadcrumb navigation">
            <div class="breadcrumb-list-placeseach">
                <a href="destinations.php" class="back-nav-placeseach">Places</a>
                <span>/</span>
                <span><?= htmlspecialchars($destination['name']); ?></span>
            </div>
        </nav>

        <!-- ======== KONTEN ARTIKEL ======== -->
        <main class="main-content-placeseach">
            <article class="content-description-placeseach">
                <h2 class="content-subtitle-placeseach"><?= htmlspecialchars($destination['title']); ?></h2>
                <p class="content-text-placeseach"><?= nl2br(htmlspecialchars($destination['description'])); ?></p>
            </article>

            <!-- ======== GALLERY ======== -->
            <section class="gallery-placeseach" aria-label="Photo gallery">
                <?php foreach ($images as $image): ?>
                    <img src="/images/destinations/<?= htmlspecialchars($image['photo']); ?>"
                         alt="Image of <?= htmlspecialchars($destination['name']); ?>" class="gallery-main-placeseach"/>
                <?php endforeach; ?>
            </section>
        </main>

        <?php include_once __DIR__ . "/template/footer.php"; ?>

    </div>

</body>
</html>

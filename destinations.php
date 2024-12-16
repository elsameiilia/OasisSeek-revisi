<?php
// php -S localhost:3000
if (!session_id()) session_start();
include_once __DIR__ . "/database/database.php";

// Setting up pagination variables
$limit = 10;
$page = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1; // Ensure page number is greater than 0
$off = ($page * $limit) - $limit;

// Get total number of records
$total_query = "SELECT COUNT(*) as total FROM destinations";
$stmt = mysqli_prepare($dbs, $total_query);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($dbs));
}
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $total);
mysqli_stmt_fetch($stmt);
$total_pages = ceil($total / $limit); // Calculate total pages
mysqli_stmt_close($stmt);

// Fetch records for the current page
$query = "SELECT des_id, name, title, description, banner FROM destinations ORDER BY des_id DESC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($dbs, $query);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($dbs));
}
mysqli_stmt_bind_param($stmt, "ii", $limit, $off);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $des_id, $name, $title, $description, $banner);

$destinations = [];
while (mysqli_stmt_fetch($stmt)) {
    $destinations[] = [
        'des_id' => $des_id,
        'name' => $name,
        'title' => $title,
        'description' => $description,
        'banner' => $banner,
    ];
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Place List - OasisSeek</title>
    <link rel="stylesheet" type="text/css" href="/images/assets/styles.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
<div class="placeslist-container">
    <!-- ======== HEADER ======== -->
    <div class="landing-container">

        <?php include_once __DIR__ . "/template/navbar.php"; ?>

        <!-- ======== PLACES LIST ======== -->
        <main class="main-content-placelist">
            <h1 class="section-title-placelist">Places</h1>
            <p class="section-description-placelist">Make Egypt part of your destination itinerary, adventuring through
                the deserts and one of the richest farm lands in the world.</p>

            <div class="placelist-grid">
                <div class="placelist-grid">
                    <?php foreach ($destinations as $destination): ?>
                        <article class="placelist-card">
                            <img src="/images/destinations/<?= $destination['banner'] ?>"
                                 alt="<?= $destination['title'] ?>"
                                 class="placelist-image"/>
                            <div class="placelist-content">
                                <h2 class="placelist-title"><?= $destination['name'] ?></h2>
                                <p class="placelist-description"><?= $destination['description'] ?></p>
                                <a href="/destinations-detail.php?id=<?= $destination['des_id'] ?>"
                                   class="read-more-btn">Read more >></a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination links -->
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1; ?>">&laquo; Previous</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?= $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?= $i; ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1; ?>">Next &raquo;</a>
                    <?php endif; ?>
                </div>
        </main>

        <!-- =========== FOOTER =========== -->
        <?php include_once __DIR__ . "/template/footer.php"; ?>
    </div>
</body>
</html>
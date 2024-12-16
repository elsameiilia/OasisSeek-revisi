<?php 
    // Mulai sesi jika belum dimulai
    if (!session_id()) {
        session_start();
    }

    // Hapus semua variabel sesi
    $_SESSION = array();

    // Hancurkan sesi
    session_destroy();

    // Arahkan pengguna ke halaman login
    header('Location: index.php');
    exit();

?>
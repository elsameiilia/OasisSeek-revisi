<?php
if (!session_id()) session_start();

use \model\User;

require_once __DIR__ . "/database/database.php";
require_once __DIR__ . "/model/User.php";
require_once __DIR__ . "/middleware/middleware.php";

// Middleware untuk memeriksa jika pengguna belum login
isNotLoggedIn();

if (isset($_POST['login'])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST["password"];

    if ($email && $password) {
        $query = "SELECT email, password, username, name, phone, photo, role FROM users WHERE email = ?";

        // Menyiapkan statement menggunakan mysqli_
        $statement = mysqli_prepare($dbs, $query);
        if ($statement === false) {
            die("Failed to prepare statement: " . mysqli_error($dbs));
        }

        // Mengikat parameter
        mysqli_stmt_bind_param($statement, "s", $email);

        // Menjalankan statement
        mysqli_stmt_execute($statement);

        // Mengikat hasil query
        mysqli_stmt_bind_result($statement, $db_email, $db_password, $db_username, $db_name, $db_phone, $db_photo, $db_role);
        mysqli_stmt_fetch($statement);

        if (!$db_email) {
            echo "<script>alert('Email atau Password salah')</script>";
        } else if (password_verify($password, $db_password)) {
            $user = new User();
            $user->email = $db_email;
            $user->username = $db_username;
            $user->password = $db_password; // Hindari menyimpan password dalam sesi
            $user->name = $db_name;
            $user->photo = $db_photo;
            $user->role = $db_role;
            $user->phone = $db_phone;

            $_SESSION['user'] = (array)$user;
            $_SESSION['loggedin'] = true;
            $_SESSION['role'] = $db_role;

            if ($user->role == 'admin') {
                header('Location: /admin/dashboard.php');
                exit();
            }

            header('Location: /');
            exit(); // Pastikan untuk keluar setelah mengirim header
        } else {
            echo "<script>alert('Email atau Password salah')</script>";
        }

        mysqli_stmt_close($statement);
    } else {
        echo "<script>alert('Email atau Password tidak valid')</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Oasis Seek</title>
    <link rel="stylesheet" type="text/css" href="/images/assets/styles.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .login-container {
            background-color: #fff;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .login-wrapper {
            display: flex;
            flex-direction: column;
            position: relative;
            min-height: 1024px;
            width: 100%;
            justify-content: center;
            padding: 85px 48px;
        }

        .background-image {
            position: absolute;
            inset: 0;
            height: 100%;
            width: 100%;
            object-fit: cover;
            object-position: center;
        }

        .content-container {
            position: relative;
            border-radius: 20px;
            background: var(--foundation-yellow-light-active, #ebdac8);
            padding: 59px 64px;
        }

        .content-grid {
            gap: 20px;
            display: flex;
        }

        .image-column {
            display: flex;
            flex-direction: column;
            line-height: normal;
            width: 59%;
        }

        .hero-image {
            aspect-ratio: 0.85;
            object-fit: cover;
            object-position: center;
            width: 88%;
            border-radius: 20px;
            flex-grow: 1;

        }

        .form-column {
            display: flex;
            flex-direction: column;
            line-height: normal;
            width: 41%;
            margin-left: 20px;
        }

        .login-form {
            position: relative;
            display: flex;
            width: 100%;
            flex-direction: column;
            align-self: stretch;
            font-family: Poppins, sans-serif;
            margin: auto 0;
        }

        .brand-title {
            align-self: center;
            font: 400 48px Underdog, sans-serif;
            flex-grow: 1;
            font-family: 'Underdog', sans-serif;
            background: linear-gradient(#ff9900, #734c10);
            -webkit-background-clip: text;
            color: transparent;
            text-decoration: none;
        }

        .login-header {
            align-self: center;
            display: flex;
            margin-top: 17px;
            width: 209px;
            max-width: 100%;
            align-items: center;
            gap: 5px -1px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .login-title {
            color: #000;
            font-size: 30px;
            font-weight: 600;
            align-self: stretch;
            margin: auto 0;
        }

        .signup-prompt {
            align-self: stretch;
            display: flex;
            align-items: start;
            gap: 2px;
            font-size: 14px;
            color: black;
            font-weight: 500;
            justify-content: center;
            flex-grow: 1;
            width: 186px;
            margin: auto 0;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            margin-top: 14px;
        }

        .form-label {
            color: #444b59;
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 1.8px;
            margin-bottom: 8px;
        }

        .form-input {
            border-radius: 12px;
            background-color: rgba(255, 255, 255, 0);
            width: 100%;
            font-size: 14px;
            color: #734c10;
            font-weight: 400;
            letter-spacing: 1.4px;
            padding: 11px 18px;
            border: 3px solid #734c10;
            outline: none;
        }

        .form-input::placeholder {
            color: rgba(115, 76, 16, 0.7);
            font-weight: 300;
        }

        .form-input:focus {
            border-color: #b57a3d;
        }

        .password-input-wrapper {
            display: flex;
            align-items: center;
            gap: 20px;
            border-radius: 12px;
            background-color: rgba(255, 255, 255, 0);
            width: 100%;
            padding: 9px 15px;
            border: 3px solid #734c10;
        }

        .password-input-wrapper input {
            background-color: transparent;
            border: none;
            width: 100%;
            font-size: 14px;
            color: #734c10;
            font-weight: 400;
            letter-spacing: 1.4px;
            outline: none;
        }

        .password-input-wrapper input::placeholder {
            color: rgba(115, 76, 16, 0.7);
        }

        .password-input-wrapper input:focus {
            background-color: transparent;
        }

        .toggle-password {
            aspect-ratio: 1;
            object-fit: contain;
            object-position: center;
            width: 24px;
            cursor: pointer;
            fill: #734c10;
            transition: transform 0.3s ease;
        }

        .toggle-password:hover {
            transform: scale(1.1);
        }

        .submit-button {
            align-self: stretch;
            border-radius: 12px;
            background-color: #734c10;
            margin-top: 28px;
            gap: 10px;
            overflow: hidden;
            font-size: 15px;
            color: var(--Foundation-Yellow-Light, #f8f3ed);
            font-weight: 400;
            white-space: nowrap;
            padding: 8px 186px;
            border: none;
            cursor: pointer;
        }

        .submit-button:hover {
            background-color: #ebdac8;
            color: #734c10;
            border: 2px solid #734c10;
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
            .login-wrapper {
                max-width: 100%;
                padding: 0 20px;
            }

            .content-container {
                max-width: 100%;
                padding: 0 20px;
            }

            .content-grid {
                flex-direction: column;
                align-items: stretch;
                gap: 0;
            }

            .image-column {
                width: 100%;
            }

            .hero-image {
                max-width: 100%;
                margin-top: 40px;
            }

            .form-column {
                width: 100%;
                margin-left: 0;
            }

            .login-form {
                max-width: 100%;
                margin-top: 40px;
            }

            .brand-title {
                font-size: 40px;
            }

            .form-input {
                max-width: 100%;
                padding-right: 20px;
            }

            .password-input-wrapper {
                max-width: 100%;
            }

            .submit-button {
                white-space: initial;
                padding: 8px 20px;
            }
        }
    </style>
</head>

<body>
<!-- ======== CONTAINER LOGIN ======== -->
<div class="login-container">
    <div class="login-wrapper">
        <img class="background-image" src="/images/assets/background.png" alt="" loading="lazy"/>
        <div class="content-container">
            <div class="content-grid">
                <div class="image-column">
                    <img class="hero-image" src="/images/assets/hero-log-sign.png" alt="login illustration"
                         loading="lazy"/>
                </div>
                <div class="form-column">
                    <form class="login-form" method="POST" action="">
                        <a href="landing-page.html" class="brand-title">OasisSeek</a>
                        <div class="login-header">
                            <h2 class="login-title">Login</h2>
                            <div class="signup-prompt">
                                <span>Don't have account?</span>
                                <a href="register.php" class="login">Sign-up</a>
                            </div>
                        </div>

                        <!-- ======== FORM INPUT DATA LOGIN ======== -->
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" id="email" class="form-input" placeholder="Enter email"
                                   required/>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <div class="password-input-wrapper">
                                <input type="password" name="password" id="password" placeholder="Enter your password"
                                       required/>
                                <svg class="toggle-password" onclick="togglePassword('password', this)"
                                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="24"
                                     height="24">
                                    <path d="M12 4.5C7.5 4.5 3.7 7.4 2 12c1.7 4.6 5.5 7.5 10 7.5s8.3-2.9 10-7.5c-1.7-4.6-5.5-7.5-10-7.5zM12 17.5c-3.1 0-5.6-2.5-5.6-5.5S8.9 6.5 12 6.5s5.5 2.5 5.5 5.5-2.4 5.5-5.5 5.5zm0-9c-2.1 0-3.8 1.6-3.8 3.5S9.9 15.5 12 15.5 15.5 14 15.5 12 14 8.5 12 8.5z"
                                          fill="#734c10"/>
                                </svg>
                            </div>
                        </div>
                        <button type="submit" name="login" class="submit-button">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, icon) {
        const input = document.getElementById(inputId);

        if (input.type === "password") {
            input.type = "text"; // Ubah ke teks
            icon.innerHTML =
                '<path d="M12 4.5C7.5 4.5 3.7 7.4 2 12c1.7 4.6 5.5 7.5 10 7.5s8.3-2.9 10-7.5c-1.7-4.6-5.5-7.5-10-7.5zm0 13C8.9 17.5 6 15 6 12s2.9-5.5 6-5.5 6 2.5 6 5.5-2.9 5.5-6 5.5zm-2.6-5.8c.1-.3.3-.6.6-.9.4-.3.8-.6 1.3-.7s1.1 0 1.6.3c.5.3.8.7 1 .9.2.3.3.7.3 1-.1.4-.3.7-.6 1-.4.3-.9.5-1.4.5-.6 0-1.1-.1-1.5-.5-.4-.3-.7-.7-.8-1.1z" />';
        } else {
            input.type = "password"; // Kembali ke password
            icon.innerHTML =
                '<path d="M12 4.5C7.5 4.5 3.7 7.4 2 12c1.7 4.6 5.5 7.5 10 7.5s8.3-2.9 10-7.5c-1.7-4.6-5.5-7.5-10-7.5zM12 17.5c-3.1 0-5.6-2.5-5.6-5.5S8.9 6.5 12 6.5s5.5 2.5 5.5 5.5-2.4 5.5-5.5 5.5zm0-9c-2.1 0-3.8 1.6-3.8 3.5S9.9 15.5 12 15.5 15.5 14 15.5 12 14 8.5 12 8.5z" />';
        }
    }
</script>
</body>
</html>

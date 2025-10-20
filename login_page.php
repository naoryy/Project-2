<?php
include "service/database.php";
session_start();

$login_notif = "";

if (isset($_POST['login'])) {
    $username = $_POST['USERNAME'];
    $password = $_POST['PASSWORD'];
    $hash_password = hash('sha256', $password);

    // Prepare statement to prevent SQL injection
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $hash_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $_SESSION['username'] = $data['username'];
        $_SESSION['isLogin'] = true;
        header("Location: main_page.php");
        exit();
    } else {
        $login_notif = "INVALID USERNAME OR PASSWORD PLEASE TRY AGAIN";
    }
}
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="login_style.css">
</head>

<body>
    <section class="container">
        <div class="login-container">
            <div class="form-container">
                <h1 class="opacity">LOGIN</h1>
                <p class="opacity" class="login-notif"><?= $login_notif ?></p>
                <form action='login_page.php' method='POST'>
                    <input type="text" placeholder="USERNAME" name="USERNAME" />
                    <input type="password" placeholder="PASSWORD" name="PASSWORD" />
                    <div class=button-div>
                        <button class="opacity" name="login">LOGIN</button>
                        <p>Don't have an account? <a href="signup_page.php">SIGNUP</a></p>
                    </div>
                </form>
            </div>
        </div>
        <div class="theme-btn-container"></div>
    </section>
</body>

</html>
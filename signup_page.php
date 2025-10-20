<?php
include "service/database.php";

$signup_notif = "";
$show_login = false;

if (isset($_POST["SIGNUP"])) {
    $username = $_POST["USERNAME"];
    $password = $_POST["PASSWORD"];

    if (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||  // At least one uppercase
        !preg_match('/[0-9]/', $password) ||  // At least one number
        !preg_match('/[\W]/', $password)      // At least one special character
    ) {
        $signup_notif = "PASSWORD MUST BE AT LEAST 8 CHARACTERS LONG AND INCLUDE 1 CAPITAL LETTER, 1 NUMBER, AND 1 SPECIAL CHARACTER";
        $show_login = false;
    } else {
        $hash_password = hash('sha256', $password);

        // Prepare statement
        $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hash_password);

        try {
            if ($stmt->execute()) {
                $signup_notif = "CONGRATS, YOUR ACCOUNT HAS BEEN CREATED";
                header("Location: login_page.php");
                exit();
            } else {
                throw new Exception("Database error: " . $stmt->error);
            }
        } catch (Exception $e) {
            if ($stmt->errno == 1062) {
                $signup_notif = "USERNAME ALREADY TAKEN, TRY ANOTHER USERNAME";
            } else {
                $signup_notif = "ERROR, PLESE TRY AGAIN";
            }
        }
    }
}
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
    <link rel="stylesheet" href="signup_style.css">
</head>

<body>
    <section class="container">
        <div class="signup-container">
            <div class="form-container">
                <h1 class="opacity">SIGNUP</h1>

                <form action="signup_page.php" method="POST">
                    <input type="text" placeholder="USERNAME" name="USERNAME" />
                    <input type="password" placeholder="PASSWORD" name="PASSWORD" />
                    <i><?= $signup_notif ?></i>
                    <?php if (!$show_login): ?>
                        <div class="button-div">
                            <button class="opacity" name="SIGNUP">SIGNUP</button>
                            <p>Already have an account? <a href="login_page.php">LOGIN</a></p>
                        </div>
                    <?php endif; ?>
                </form>
                <?php if ($show_login): ?>
                    <button class="login-button" name="LOGIN" onclick="window.location.href='login_page.php'">LOGIN</button>
                <?php endif; ?>
            </div>
        </div>
        <div class="theme-btn-container"></div>
    </section>
</body>

</html>
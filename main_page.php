<?php
session_start();

if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include("service/database.php");

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

if (isset($_POST["submit"])) {
    $caption = $_POST["caption"];
    $filename = $_FILES["content"]["name"];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $tmp_name = $_FILES["content"]["tmp_name"];

    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'mov', 'wmv', 'mkv'];

    if (!in_array($ext, $allowed_extensions)) {
        $upload_error = "Invalid file type. Allowed: JPG, JPEG, PNG, GIF, MP4, AVI, MOV, WMV, MKV.";
    } else {
        $unique_filename = uniqid() . '.' . $ext;
        $target_path = "uploaded-content/" . $unique_filename;

        if (move_uploaded_file($tmp_name, $target_path)) {
            $username = $_SESSION['username'];
            $stmt = $db->prepare("INSERT INTO content (caption, filename, username) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $caption, $unique_filename, $username);

            if ($stmt->execute()) {
                $upload_success = "Upload berhasil!";
                header("Location: main_page.php");
                exit;
            } else {
                $upload_error = "Gagal menyimpan ke database.";
            }
            $stmt->close();
        } else {
            $upload_error = "Gagal mengupload file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportyFit - Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>

<body>

    <div class="sidebar">
        <div class="nav-icons">
            <div class="logo">
                <i class="fas fa-running"></i>
            </div>
            <button class="nav-item active">
                <i class="fas fa-home"></i>
            </button>
            <button class="nav-item">
                <i class="fas fa-map-marker-alt"></i>
            </button>
            <button class="nav-item">
                <i class="fas fa-calendar-alt"></i>
            </button>
            <button class="nav-item">
                <i class="fas fa-dumbbell"></i>
            </button>
            <button class="nav-item">
                <i class="fas fa-users"></i>
            </button>
            <button class="nav-item">
                <i class="fas fa-comment"></i>
            </button>
        </div>

        <div class="sidebar-bottom">
            <form action="main_page.php" method="POST">
                <button class="nav-item" name="logout" type="submit">
                    <i class="fas fa-right-from-bracket"></i>
                </button>
            </form>

            <div class="dark-mode-toggle">
                <label class="toggle-label">
                    <input type="checkbox" class="toggle-input">
                    <div class="toggle-slider">
                        <div class="toggle-circle"></div>
                    </div>
                </label>
                <i class="fas fa-moon toggle-icon"></i>
            </div>
        </div>
    </div>

    <div class="header">
        <div class="header-left">
            <h1>Home Page</h1>
        </div>
        <div class="header-right">
            <div class="profile-avatar">
                <span><?= strtoupper($_SESSION['username'][0]) ?></span>
            </div>
            <div class="profile-info">
                <p class="profile-name"><?= $_SESSION['username'] ?></p>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="content-container">

            <?php
            $sql = "SELECT * FROM content ORDER BY id DESC";
            $result = $db->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id = $row['id'];
                    $filename = htmlspecialchars($row['filename']);
                    $caption = htmlspecialchars($row['caption']);
                    $username = htmlspecialchars($row['username']);

                    $video_extensions = ['mp4', 'avi', 'mov', 'wmv', 'mkv'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    echo "<div class='post-container' data-id='$id' data-username='$username'>";

                    echo "<div class='post-content'>";
                    if (in_array($ext, $video_extensions)) {
                        echo "<video controls class='post-media'>
                            <source src='uploaded-content/$filename' type='video/$ext'>
                            Your browser does not support the video tag.
                          </video>";
                    } else {
                        echo "<img src='uploaded-content/$filename' alt='Post Image' class='post-media'>";
                    }
                    echo "</div>";

                    echo "<div class='post-sidebar'>";
                    echo "<div class='post-header'>";
                    echo "<div class='user-info'>";
                    echo "<span class='avatar'>" . strtoupper($username[0]) . "</span>";
                    echo "<span class='username'>$username</span>";
                    echo "</div>";

                    echo "<div class='more-options'>";
                    echo "<button class='more-btn' onclick='toggleMenu($id)'><i class='fa-solid fa-ellipsis-vertical'></i></button>";
                    echo "<div class='dropdown-menu' id='menu-$id' style='display:none;'>";
                    echo "<button onclick='deletePost($id)'>Delete Post</button>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";

                    echo "<div class='post-caption'><p>$caption</p></div>";

                    echo "<div class='comment-form'>";
                    echo "<input type='text' placeholder='Add a comment...' />";
                    echo "<button>Post</button>";
                    echo "</div>";

                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>No posts available.</p>";
            }
            ?>

        </div>
    </div>

    <button class="add-post-button" id="addPostBtn">+</button>

    <div class="post-overlay" id="postOverlay">
        <form id="postForm" enctype="multipart/form-data" action="main_page.php" method="POST">
            <h2>Create a Post</h2>
            <label for="content">UPLOAD CONTENT</label>
            <input type="file" name="content" id="content" accept="image/*,video/*" required />
            <textarea name="caption" placeholder="Write a caption..." required></textarea>
            <div class="form-actions">
                <button type="button" id="cancelPost">Cancel</button>
                <button type="submit" name="submit">Upload</button>
            </div>
        </form>
    </div>

    <div class="post-overlay" id="deleteOverlay" style="display: none;">
        <div class="delete-confirm-box">
            <h2>Delete Post</h2>
            <p>Are you sure you want to delete this post?</p>
            <div class="form-actions">
                <button type="button" id="confirmDelete" style="background: #f44336;">Yes, Delete</button>
                <button type="button" id="cancelDelete">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        window.currentUser = "<?php echo addslashes($_SESSION['username']); ?>";
    </script>
    <script src="script/main_page.js"></script>

</body>

</html>
<?php
include("service/database.php");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportyFit</title>
    <link href="style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>

    <?php include 'layout/sidebar navigation.html' ?>

    <?php include 'layout/header.html' ?>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="content-container">
            <div class="welcome-section">
                <h2>Welcome</h2>
            </div>
            <div class="posts-section">
                <h3>Recent Posts</h3>
                <div class="posts-container">
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

                            echo "</div>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>No posts available.</p>";
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>

    <script>
        window.currentUser = "<?php echo addslashes($_SESSION['username']); ?>";
    </script>
    <script src="script/main_page.js"></script>

</body>

</html>
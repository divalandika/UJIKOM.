<?php
session_start();
if ($_SESSION['status_login'] != true) {
    echo '<script>window.location="login.php"</script>';
}
include 'koneksi.php'; // Make sure to include this for database connectivity

?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WEB Galeri Foto</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
    <!-- header -->
    <header>
        <div class="container">
            <h1><a href="dashboard.php">WEB GALERI FOTO</a></h1>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="profil.php">Profil</a></li>
                <li><a href="data-image.php">Data Foto</a></li>
                <li><a href="Keluar.php">Keluar</a></li>
            </ul>
        </div>
    </header>

    <!-- content -->
    <div class="section">
        <div class="container">
            <h3>Dashboard</h3>
            <div class="box">
                <h4>Selamat Datang <?php echo $_SESSION['a_global']->admin_name ?> di Galeri Foto</h4>
            </div>
        </div>

        <!-- category -->
        <div class="section">
            <div class="container">
                <h3>Kategori</h3>
                <div class="box">
                    <?php
                    $kategori = mysqli_query($conn, "SELECT * FROM tb_category ORDER BY category_id DESC");
                    if (mysqli_num_rows($kategori) > 0) { // Correct function name
                        while ($k = mysqli_fetch_array($kategori)) {
                    ?>
                            <a href="galeri.php?kat=<?php echo $k['category_id'] ?>">
                                <div class="col-5">
                                    <img src="img/emotion.png" width="50px" style="margin-bottom:5px;" />
                                    <p><?php echo $k['category_name'] ?></p>
                                </div>
                            </a>
                    <?php }
                    } else { ?>
                        <p>Kategori tidak ada</p>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- new photos -->
        <div class="section">
            <div class="container">
                <h3>Foto Terbaru</h3>
                <div class="box">
                    <?php
                    $foto = mysqli_query($conn, "SELECT * FROM tb_image WHERE image_status = 1 ORDER BY image_id DESC LIMIT 8");
                    if (mysqli_num_rows($foto) > 0) { // Correct function name
                        while ($p = mysqli_fetch_array($foto)) {
                    ?>
                            <a href="detail-image.php?id=<?php echo $p['image_id'] ?>">
                                <div class="col-4">
                                    <img src="foto/<?php echo $p['image'] ?>" height="150px" />
                                    <p class="nama"><?php echo substr($p['image_name'], 0, 30) ?></p>
                                    <p class="admin">Nama User : <?php echo $p['admin_name'] ?></p>
                                    <p class="date"><?php echo $p['date_created'] ?></p>
                                </div>
                            </a>
                    <?php }
                    } else { ?>
                        <p>Foto tidak ada</p>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- comment form handling -->
        <?php
        if (isset($_POST['comment_text']) && isset($_POST['image_id'])) {
            $image_id = $_POST['image_id'];
            $comment_text = mysqli_real_escape_string($conn, $_POST['comment_text']);
            $user_name = 'User'; // Set user name here
        
            mysqli_query($conn, "INSERT INTO tb_comments (image_id, user_name, comment_text) VALUES ($image_id, '$user_name', '$comment_text')");
            header("Location: comments.php?image_id=$image_id"); // Redirect back to comments page
        }
        ?>

    </div>

    <!-- footer -->
    <footer>
        <div class="container">
            <small>Dival &copy;Galeri Foto.</small>
        </div>
    </footer>
</body>

</html>

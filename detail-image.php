<?php
error_reporting(0);
include 'koneksi.php';
$kontak = mysqli_query($conn, "SELECT admin_telp, admin_email, admin_address FROM tb_admin WHERE admin_id = 2");
$a = mysqli_fetch_object($kontak);

// Menangani Like
if (isset($_POST['like'])) {
    $image_id = $_POST['image_id'];
    $user_id = 1; // Gantilah dengan ID user yang sedang login jika ada sistem login.

    // Periksa apakah user sudah memberikan like
    $check_like = mysqli_query($conn, "SELECT * FROM tb_likes WHERE image_id = '$image_id' AND user_id = '$user_id'");
    if (mysqli_num_rows($check_like) == 0) {
        mysqli_query($conn, "INSERT INTO tb_likes (image_id, user_id) VALUES ('$image_id', '$user_id')");
    }
}

// Menangani Komentar
if (isset($_POST['comment'])) {
    $image_id = $_POST['image_id'];
    $user_id = 1; // Gantilah dengan ID user yang sedang login jika ada sistem login
    $comment = mysqli_real_escape_string($conn, $_POST['comment_text']);

    mysqli_query($conn, "INSERT INTO tb_comments (image_id, user_id, comment) VALUES ('$image_id', '$user_id', '$comment')");
}

$produk = mysqli_query($conn, "SELECT * FROM tb_image WHERE image_id = '" . $_GET['id'] . "' ");
$p = mysqli_fetch_object($produk);

// Menghitung jumlah like
$like_count = mysqli_query($conn, "SELECT COUNT(*) AS total_likes FROM tb_likes WHERE image_id = '" . $p->image_id . "'");
$like_count = mysqli_fetch_object($like_count)->total_likes;

// Mengambil komentar
$comments = mysqli_query($conn, "SELECT c.comment, u.user_name FROM tb_comments c JOIN tb_users u ON c.user_id = u.user_id WHERE c.image_id = '" . $p->image_id . "' ORDER BY c.created_at DESC");
?>
<!DOCTYPE html>
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
            <h1><a href="index.php">WEB GALERI FOTO</a></h1>
            <ul>
            </ul>
        </div>
    </header>

    <!-- search -->
    <div class="search">
        <div class="container">
            <form action="galeri.php">
                <input type="text" name="search" placeholder="Cari Foto" value="<?php echo $_GET['search'] ?>" />
                <input type="hidden" name="kat" value="<?php echo $_GET['kat'] ?>" />
                <input type="submit" name="cari" value="Cari Foto" />
            </form>
        </div>
    </div>

    <!-- product detail -->
    <div class="section">
        <div class="container">
            <h3>Detail Foto</h3>
            <div class="box">
                <div class="col-2">
                    <img src="foto/<?php echo $p->image ?>" width="100%" />
                </div>
                <div class="col-2">
                    <h3><?php echo $p->image_name ?><br />Kategori : <?php echo $p->category_name ?></h3>
                    <h4>Nama User : <?php echo $p->admin_name ?><br />
                        Upload Pada Tanggal : <?php echo $p->date_created ?></h4>
                    <p>Deskripsi :<br />
                        <?php echo $p->image_description ?>
                    </p>

                    <!-- Like Button -->
                    <form action="" method="POST">
                        <input type="hidden" name="image_id" value="<?php echo $p->image_id ?>">
                        <button type="submit" name="like">Like</button>
                        <p><?php echo $like_count ?> Likes</p>
                    </form>


                    <!-- HTML untuk menampilkan komentar -->
                    <h4>Komentar:</h4>
                    <div>
                        <?php while ($comment = mysqli_fetch_object($comments)) { ?>
                            <div>
                                <strong><?php echo $comment->user_name; ?>:</strong>
                                <p><?php echo $comment->comment; ?></p>
                            </div>
                        <?php } ?>
                    </div>

                  
                    <!-- Form Komentar -->
                    <form action="" method="POST">
                        <input type="hidden" name="image_id" value="<?php echo $p->image_id ?>">
                        <textarea name="comment_text" placeholder="Tulis komentar" required></textarea>
                        <button type="submit" name="comment">Kirim Komentar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- footer -->
    <footer>
        <div class="container">
            <small>Dival&copy;Galeri Foto.</small>
        </div>
    </footer>
</body>

</html>
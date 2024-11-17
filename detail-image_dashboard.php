<?php
session_start(); // Memulai sesi
error_reporting(E_ALL); // Aktifkan laporan semua jenis error
include 'koneksi.php';

if (!isset($_SESSION['a_global']->admin_id)) {
    die("Session untuk user tidak ditemukan. Pastikan user login.");
}

// Menangani Klik Tombol Like / Unlike
if (isset($_POST['suka'])) {
    $gam = $_POST['gam'];
    $admin_id = $_POST['admin_id'];

    // Cek apakah pengguna sudah menyukai gambar
    $check_like = mysqli_query($conn, "SELECT * FROM tb_like WHERE admin_name = '$admin_id' AND image_id = '$gam' AND suka = 1");

    if (!$check_like) {
        die("Gagal memeriksa like: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($check_like) > 0) {
        // Jika sudah suka, maka ubah status suka menjadi 0 (berarti "unlike")
        $unlike = mysqli_query($conn, "UPDATE tb_like SET suka = 0 WHERE admin_name = '$admin_id' AND image_id = '$gam'");
        if (!$unlike) {
            die("Gagal mengubah status unlike: " . mysqli_error($conn));
        }
    } else {
        // Jika belum suka, tambahkan suka atau ubah status suka menjadi 1
        $like_exists = mysqli_query($conn, "SELECT * FROM tb_like WHERE admin_name = '$admin_id' AND image_id = '$gam'");
        if (!$like_exists) {
            die("Gagal memeriksa data like: " . mysqli_error($conn));
        }

        if (mysqli_num_rows($like_exists) > 0) {
            // Jika sudah ada record, ubah status suka menjadi 1
            $like = mysqli_query($conn, "UPDATE tb_like SET suka = 1, tanggal_like = NOW() WHERE admin_name = '$admin_id' AND image_id = '$gam'");
            if (!$like) {
                die("Gagal mengubah status like: " . mysqli_error($conn));
            }
        } else {
            // Jika belum ada record, tambahkan data suka baru
            $like = mysqli_query($conn, "INSERT INTO tb_like (image_id, admin_name, suka, tanggal_like) VALUES ('$gam', '$admin_id', 1, NOW())");
            if (!$like) {
                die("Gagal menambahkan like baru: " . mysqli_error($conn));
            }
        }
    }

    // Refresh halaman untuk memperbarui jumlah suka dan teks tombol
    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $_GET['id']);
    exit();
}

// Ambil data kontak admin
$kontak = mysqli_query($conn, "SELECT admin_telp, admin_email, admin_address FROM tb_admin WHERE admin_id = 2");
if (!$kontak) {
    die("Gagal mengambil data kontak: " . mysqli_error($conn));
}
$a = mysqli_fetch_object($kontak);

// Ambil data gambar berdasarkan ID
if (!isset($_GET['id'])) {
    die("ID gambar tidak ditemukan di URL.");
}

$idl = $_GET['id'];
$produk = mysqli_query($conn, "SELECT * FROM tb_image WHERE image_id = '$idl' ");
if (!$produk) {
    die("Gagal mengambil data gambar: " . mysqli_error($conn));
}

$p = mysqli_fetch_object($produk);
if (!$p) {
    die("Data gambar tidak ditemukan.");
}

// Mendapatkan ID user yang login
$user_id = $_SESSION['a_global']->admin_name;
$liked = mysqli_query($conn, "SELECT * FROM tb_like WHERE image_id = '$idl' AND admin_name = '$user_id' AND suka = 1");
if (!$liked) {
    die("Gagal memeriksa like user: " . mysqli_error($conn));
}

$has_liked = mysqli_num_rows($liked) > 0; // True jika user sudah menyukai gambar ini

// Query untuk menghitung jumlah like
$like_count_result = mysqli_query($conn, "SELECT * FROM tb_like WHERE image_id = '$idl' AND suka = 1");
if (!$like_count_result) {
    die("Gagal mengambil jumlah like: " . mysqli_error($conn));
}
$like_count = mysqli_num_rows($like_count_result);

// Menangani pengiriman komentar
if (isset($_POST['submit_comment'])) {
    $image_id = $_POST['image_id'];
    $admin_id = $_POST['admin_id'];
    $admin_name = $_POST['admin_name'];
    $isi_komentar = $_POST['isi_komentar'];

    // Memasukkan komentar ke database
    $insert_comment = mysqli_query($conn, "INSERT INTO komentar_foto (image_id, admin_id, admin_name, isi_komentar) VALUES ('$image_id', '$admin_id', '$admin_name', '$isi_komentar')");

    if (!$insert_comment) {
        die("Gagal menambahkan komentar: " . mysqli_error($conn));
    }

    // Refresh halaman untuk menampilkan komentar terbaru
    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $image_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WEB Galeri Foto</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<style>
    .comments-section {
        margin-top: 20px;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #fff;
    }

    .comments-section h4 {
        margin-bottom: 15px;
        font-size: 20px;
        font-weight: bold;
    }

    .comment-list {
        margin-top: 10px;
    }

    .comment {
        padding: 10px;
        border-bottom: 1px solid #eaeaea;
        /* Garis pemisah antar komentar */
    }

    .comment:last-child {
        border-bottom: none;
        /* Menghilangkan garis bawah pada komentar terakhir */
    }

    .comment strong {
        color: #262626;
        /* Warna nama pengguna */
    }

    .comment-date {
        color: #999;
        /* Warna untuk tanggal */
        font-size: 0.9em;
    }

    textarea {
        width: 100%;
        padding: 10px;
        padding-right: 0px;
        border-radius: 5px;
        border: 1px solid #ddd;
        margin-bottom: 10px;
        resize: none;
        /* Menghilangkan ukuran ulang pada textarea */
    }

    button {
        background-color: #0095f6;
        /* Warna tombol kirim */
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: #007bbf;
        /* Warna tombol saat hover */
    }
</style>

<body>
    <!-- header -->
    <header>
        <div class="container">
            <h1><a href="dashboard.php">GALERI FOTO</a></h1>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="profil.php">Profil</a></li>
                <li><a href="data-image.php">Data Foto</a></li>
                <li><a href="Keluar.php">Keluar</a></li>
            </ul>
        </div>
    </header>

    <!-- detail produk -->
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
                </div>
            </div>

            <div class="col-2">
                <!-- Tombol Like / Unlike -->
                <form method="POST" action="">
                    <input type="hidden" name="gam" value="<?php echo $p->image_id ?>">
                    <input type="hidden" name="admin_id" value="<?php echo $user_id ?>" required>
                    <button name="suka" class="like">
                        <?php echo $has_liked ? "UNLIKE" : "LIKE"; ?> <?php echo $like_count; ?>
                    </button>
                </form>
            </div>

            <!-- comment -->
            <div class="comments-section">
                <h4>Komentar</h4> <!-- Judul komentar tetap di atas textarea -->
                <form method="POST" action="">
                    <input type="hidden" name="image_id" value="<?php echo $p->image_id; ?>">
                    <input type="hidden" name="admin_id" value="<?php echo $_SESSION['a_global']->admin_id; ?>">
                    <input type="hidden" name="admin_name" value="<?php echo $_SESSION['a_global']->admin_name; ?>">
                    <textarea name="isi_komentar" placeholder="Tulis komentar..." required></textarea>
                    <button type="submit" name="submit_comment">Kirim Komentar</button>
                </form>

                <div class="comment-list">
                    <?php
                    // Menampilkan komentar
                    $comments_query = mysqli_query($conn, "SELECT * FROM komentar_foto WHERE image_id = '" . $p->image_id . "' ORDER BY tanggal_komentar DESC");
                    while ($comment = mysqli_fetch_object($comments_query)) {
                        echo "<div class='comment'>";
                        echo "<strong>" . htmlspecialchars($comment->admin_name) . "</strong>: " . htmlspecialchars($comment->isi_komentar);
                        echo "<br><small class='comment-date'>Ditambahkan pada: " . $comment->tanggal_komentar . "</small>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>


        </div>
    </div>

    <!-- footer -->
    <footer>
        <div class="container">
            <small>Dival &copy;Galeri Foto.</small>
        </div>
    </footer>
</body>

</html>
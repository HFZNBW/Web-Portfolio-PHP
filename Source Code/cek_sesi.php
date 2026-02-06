<?php
session_start();
include 'koneksi.php';

$halaman_skrg = basename($_SERVER['PHP_SELF']);
$whitelist = ['cetak_pdf.php'];

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php?pesan=belum_login");
    exit();
}

$id_user = $_SESSION['id_user'];
$session_sekarang = session_id();

if (!in_array($halaman_skrg, $whitelist)) {
    // 1. Cek Sesi di DB
    $res = mysqli_query($conn, "SELECT last_session_id FROM users WHERE id_user = '$id_user'");
    $db = mysqli_fetch_assoc($res);

    if ($db['last_session_id'] != $session_sekarang) {
        session_unset();
        session_destroy();
        header("location:login.php?pesan=sesi_berakhir");
        exit();
    }
?>
    <script>
        // 2. Cek Duplikat Tab (BroadcastChannel)
        if (!sessionStorage.getItem('tab_unique_id')) {
            sessionStorage.setItem('tab_unique_id', 'tab_' + Date.now());
        }
        const myTabId = sessionStorage.getItem('tab_unique_id');
        const bc = new BroadcastChannel('rt_ngemplak_auth');

        bc.postMessage({
            type: 'NEW_TAB',
            user: '<?= $id_user ?>',
            tab: myTabId
        });

        bc.onmessage = (e) => {
            if (e.data.user === '<?= $id_user ?>' && e.data.tab !== myTabId) {
                bc.close();
                alert('Sesi dialihkan ke tab baru!');
                window.location.href = 'logout.php';
            }
        };
    </script>
<?php
}
?>
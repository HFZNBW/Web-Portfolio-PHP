<?php
include_once "koneksi.php";

/* ================= QUERY ================= */
$sql = "
    SELECT 
        p.id_peminjaman,
        p.nama,
        p.jumlah,
        p.tgl_pinjam,
        p.tgl_kembali,  -- ambil ini
        b.nama_barang
    FROM peminjaman p
    JOIN barang b ON p.id_barang = b.id_barang
    ORDER BY p.id_peminjaman DESC
";

$data_res = mysqli_query($conn, $sql);

if (!$data_res) {
    die("Database error: " . mysqli_error($conn));
}

/* ================= FORMAT TANGGAL ================= */
function tgl_indo($tanggal)
{
    if (!$tanggal || $tanggal == "0000-00-00") return "-";

    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    $pecah = explode('-', $tanggal);
    return $pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
}



/* ================= STATUS OTOMATIS ================= */
function badge_status($tgl_kembali)
{
    if (!empty($tgl_kembali) && $tgl_kembali != "0000-00-00") {
        return '<span class="badge bg-success">SELESAI</span>';
    }

    return '<span class="badge bg-warning text-dark">DIPINJAM</span>';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: white;
            padding-top: 70px;
            font-size: 14px;
        }

        /* ===== TOOLBAR ===== */
        .toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
            padding: 10px 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        /* ===== PRINT MODE ===== */
        @media print {
            .toolbar {
                display: none !important;
            }
        }

        /* ===== HEADER ===== */
        .kop {
            border-bottom: 3px solid black;
            margin-bottom: 20px;
            padding-bottom: 10px;
            text-align: center;
        }

        .ttd {
            width: 300px;
            float: right;
            text-align: center;
            margin-top: 50px;
        }

        .space {
            height: 80px;
        }
    </style>
</head>


<body>

    <!-- ================= TOOLBAR ================= -->
    <div class="toolbar">
        <button onclick="closeTab()" class="btn btn-secondary btn-sm">
            Kembali
        </button>

        <button onclick="window.print()" class="btn btn-primary btn-sm">
            Cetak
        </button>
    </div>


    <div class="container">

        <!-- ================= HEADER ================= -->
        <div class="kop">
            <h4 class="fw-bold mb-0">LAPORAN PEMINJAMAN INVENTARIS RT</h4>
            <div>RT 04 RW 01 DK. NGEMPLAK BARU</div>
            <small>Gentan, Kecamatan Baki, Kabupaten Sukoharjo</small>
        </div>


        <!-- ================= TABEL ================= -->
        <table class="table table-bordered border-dark align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th width="50">No</th>
                    <th>Nama Peminjam</th>
                    <th>Nama Barang</th>
                    <th width="90">Jumlah</th>
                    <th width="140">Tgl Pinjam</th>
                    <th width="140">Status</th>
                </tr>
            </thead>

            <tbody>

                <?php
                $no = 1;
                while ($r = mysqli_fetch_assoc($data_res)) :
                ?>

                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="text-start"><?= $r['nama'] ?></td>
                        <td class="text-start"><?= $r['nama_barang'] ?></td>
                        <td><?= $r['jumlah'] ?></td>
                        <td><?= tgl_indo($r['tgl_pinjam']) ?></td>
                        <td><?= badge_status($r['tgl_kembali']) ?></td>

                    </tr>

                <?php endwhile; ?>

            </tbody>
        </table>


        <!-- ================= TTD ================= -->
        <div class="ttd">
            <p>Sukoharjo, <?= tgl_indo(date('Y-m-d')) ?></p>
            <p>Ketua RT / Pengelola</p>
            <div class="space"></div>
            <p class="fw-bold">( __________________ )</p>
        </div>

    </div>



    <script>
        /* ===== TOMBOL KEMBALI (CLOSE TAB) ===== */
        function closeTab() {
            window.close();

            // fallback kalau browser blokir
            setTimeout(() => {
                history.back();
            }, 150);
        }
    </script>


</body>

</html>
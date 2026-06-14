<?php
session_start();
include 'koneksi.php';

// Proteksi halaman: Jika belum login, tendang balik ke login.php
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); exit;
}

$role = $_SESSION['role'];
$nama = $_SESSION['nama_lengkap'];

// Hitung statistik sederhana untuk kebutuhan grafik animasi pembantu
$res_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM ujian_tahfidz");
$data_total = mysqli_fetch_assoc($res_total);
$total_lulus = $data_total['total'];

// 1. QUERY UTK GRAFIK PEKANAN: Hitung jumlah kelulusan ujian berdasarkan nama hari (Senin-Sabtu)
$hari_grafik = ['Monday' => 0, 'Tuesday' => 0, 'Wednesday' => 0, 'Thursday' => 0, 'Friday' => 0, 'Saturday' => 0];
$sql_pekanan = "SELECT DAYNAME(tanggal_ujian) as nama_hari, COUNT(*) as jumlah FROM ujian_tahfidz GROUP BY DAYNAME(tanggal_ujian)";
$q_pekanan = mysqli_query($conn, $sql_pekanan);
while($row_p = mysqli_fetch_assoc($q_pekanan)) {
    if(array_key_exists($row_p['nama_hari'], $hari_grafik)) {
        $hari_grafik[$row_p['nama_hari']] = (int)$row_p['jumlah'];
    }
}
// Susun data array untuk dikirim ke JavaScript Chart.js
$data_chart_dinamis = [
    $hari_grafik['Monday'],
    $hari_grafik['Tuesday'],
    $hari_grafik['Wednesday'],
    $hari_grafik['Thursday'],
    $hari_grafik['Friday'],
    $hari_grafik['Saturday']
];

// 2. QUERY UTK TABEL PROGRES UTAMA: Ambil data semua santri beserta Juz tertinggi yang dicapai
$sql_all_santri = "SELECT s.id, s.nama_santri, IFNULL(MAX(CAST(u.juz_diuji AS UNSIGNED)), 0) as juz_tertinggi 
                   FROM santri s 
                   LEFT JOIN ujian_tahfidz u ON s.id = u.santri_id 
                   GROUP BY s.id 
                   ORDER BY juz_tertinggi DESC";
$q_all_santri = mysqli_query($conn, $sql_all_santri);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Monitoring Tahfizh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <link href="style_dashboard.css" rel="stylesheet">
</head>
<body class="fade-in-animation">

<audio id="audioSukses" src="https://assets.mixkit.co/active_storage/sfx/2018/2018-84.wav" preload="auto"></audio>

<div class="main-wrapper">

    <nav class="navbar navbar-expand-lg navbar-custom shadow-sm w-100">
        <div class="container-fluid px-4">
            <span class="navbar-brand mb-0 h1 fw-bold text-white">🕌 Monitoring Hafalan Santri</span>
            <div class="d-flex align-items-center">
                <span class="text-white me-3 small d-none d-md-inline">Halo, <strong><?php echo $nama; ?></strong></span>
                <a href="logout.php" class="btn btn-sm btn-danger px-3 rounded-3 shadow-sm">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="content-container container-fluid px-4 my-3">
        <div class="row main-row">
            
            <div class="col-lg-3 mb-3 mb-lg-0">
                <div class="card card-sidebar shadow-sm">
                    <div class="sidebar-header pb-2 mb-2 text-center">
                        <div class="avatar-circle mx-auto mb-2"><?php echo strtoupper(substr($nama, 0, 1)); ?></div>
                        <h6 class="fw-bold mb-0 text-white"><?php echo $nama; ?></h6>
                        <small class="text-warning"><?php echo $role; ?></small>
                    </div>
                    
                    <span class="sidebar-title-menu d-block mb-2">Menu Utama</span>
                    <div class="nav flex-column nav-pills custom-sidebar-nav" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active text-start border-0 bg-transparent mb-1" id="tab-home-btn" data-bs-toggle="pill" data-bs-target="#panel-home" type="button" role="tab">
                            <span>🏠 Home</span>
                        </button>
                        <button class="nav-link text-start border-0 bg-transparent mb-1" id="tab-setoran-btn" data-bs-toggle="pill" data-bs-target="#panel-setoran" type="button" role="tab">
                            <span>📖 Setoran Hafalan</span>
                        </button>
                        <button class="nav-link text-start border-0 bg-transparent mb-1" id="tab-ujian-btn" data-bs-toggle="pill" data-bs-target="#panel-ujian" type="button" role="tab">
                            <span>🎓 Ujian Tasmi'</span>
                        </button>
                    </div>

                    <?php if($role == 'ustadz'): ?>
                        <div class="border-top pt-2 mt-auto">
                            <span class="sidebar-title-menu d-block mb-2">Aksi Cepat</span>
                            <button type="button" class="btn btn-action-sidebar btn-sidebar-success mb-2 w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInputUjian">
                                <span>Input Kelulusan</span> <span>+</span>
                            </button>
                            <button type="button" class="btn btn-action-sidebar btn-sidebar-primary w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSetoran">
                                <span>Input Setoran Harian</span> <span>+</span>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-9 tab-content tab-content-wrapper" id="v-pills-tabContent">
                
                <div class="tab-pane fade show active" id="panel-home" role="tabpanel">
                    
                    <div class="row">
                        <div class="col-md-5 mb-3 mb-md-0">
                            <div class="card p-3 card-content-main border-0 shadow-sm h-100">
                                <h6 class="fw-bold text-primary mb-3">📊 Grafik Kelulusan Ujian Pekanan</h6>
                                <div style="height: 380px; position: relative;">
                                    <canvas id="grafikAnimasi"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div class="card p-3 card-content-main border-0 shadow-sm h-100">
                                <h6 class="fw-bold text-dark mb-3">📈 Progres Akademik Hafalan Santri</h6>
                                <div class="table-responsive" style="max-height: 380px !important;">
                                    <table class="table table-custom align-middle text-center small mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-start">Nama Santri</th>
                                                <th>Capaian</th>
                                                <th style="width: 45%;">Target</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            while($santri = mysqli_fetch_assoc($q_all_santri)) {
                                                $s_nama = strtoupper($santri['nama_santri']);
                                                $s_juz = (int)$santri['juz_tertinggi'];
                                                $persen = round(($s_juz / 30) * 100, 1);
                                                
                                                // Menentukan warna bar berdasarkan tingginya juz
                                                $warna_bar = 'bg-danger';
                                                if($persen > 30) $warna_bar = 'bg-warning';
                                                if($persen > 70) $warna_bar = 'bg-success';
                                                
                                                echo "<tr>
                                                        <td class='text-start fw-bold text-dark'>$s_nama</td>
                                                        <td><span class='badge bg-light text-dark border'>$s_juz Juz</span></td>
                                                        <td>
                                                            <div class='d-flex align-items-center gap-2'>
                                                                <div class='progress flex-grow-1' style='height: 8px; border-radius: 4px;'>
                                                                    <div class='progress-bar $warna_bar progress-bar-striped progress-bar-animated' role='progressbar' style='width: $persen%'></div>
                                                                </div>
                                                                <span class='fw-bold text-secondary' style='font-size: 10px;'>$persen%</span>
                                                            </div>
                                                        </td>
                                                      </tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="tab-pane fade" id="panel-setoran" role="tabpanel">
                    <div class="card p-4 card-content-main">
                        <h5 class="fw-bold text-primary mb-3">📖 Rekapitulasi Setoran Hafalan Harian Santri</h5>
                        <div class="table-responsive">
                            <table id="tabelSetoran" class="table table-custom align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Nama Santri</th>
                                        <th>Surah</th>
                                        <th>Ayat</th>
                                        <th>Evaluasi Ustadz</th>
                                        <?php if($role == 'ustadz'): ?><th>Aksi</th><?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql_setoran = ($role == 'santri') ? "SELECT sh.*, s.nama_santri FROM setoran_harian sh JOIN santri s ON sh.santri_id = s.id WHERE sh.santri_id = '{$_SESSION['santri_id']}' ORDER BY sh.tanggal DESC" : "SELECT sh.*, s.nama_santri FROM setoran_harian sh JOIN santri s ON sh.santri_id = s.id ORDER BY sh.tanggal DESC";
                                    $q_setoran = mysqli_query($conn, $sql_setoran);
                                    while($setoran = mysqli_fetch_assoc($q_setoran)) {
                                        echo "<tr>
                                                <td>".date('d/m/Y', strtotime($setoran['tanggal']))."</td>
                                                <td class='fw-bold text-dark'>{$setoran['nama_santri']}</td>
                                                <td><strong>{$setoran['surah_dihafal']}</strong></td>
                                                <td><span class='badge bg-light text-dark border'>Ayat {$setoran['ayat_dihafal']}</span></td>
                                                <td><small class='text-muted'>{$setoran['evaluasi_ustadz']}</small></td>";
                                                if($role == 'ustadz') {
                                                    echo "<td>
                                                            <div class='d-flex gap-1'>
                                                                <button class='btn btn-warning btn-sm py-1 px-2 btn-edit text-dark shadow-xs' data-id='{$setoran['id']}' data-tgl='{$setoran['tanggal']}' data-surah='{$setoran['surah_dihafal']}' data-ayat='{$setoran['ayat_dihafal']}' data-evaluasi='{$setoran['evaluasi_ustadz']}'>Edit</button>
                                                                <button class='btn btn-danger btn-sm py-1 px-2 btn-hapus-setoran shadow-xs' data-id='{$setoran['id']}'>Hapus</button>
                                                            </div>
                                                          </td>";
                                                }
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="panel-ujian" role="tabpanel">
                    <div class="card p-4 card-content-main">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <h5 class="fw-bold text-dark m-0">🎓 Riwayat Kelulusan Ujian Sertifikasi Komprehensif</h5>
                        </div>
                        <div class="table-responsive">
                            <table id="tabelUjian" class="table table-custom align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Nama Santri</th>
                                        <th>Juz</th>
                                        <th>Nilai</th>
                                        <th>Tanggal</th>
                                        <th>Berkas Lampiran</th>
                                        <th style="width: 180px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = ($role == 'santri') ? "SELECT u.id, s.nama_santri, u.juz_diuji, u.nilai, u.tanggal_ujian, u.file_lampiran FROM ujian_tahfidz u JOIN santri s ON u.santri_id = s.id WHERE u.santri_id = '{$_SESSION['santri_id']}'" : "SELECT u.id, s.nama_santri, u.juz_diuji, u.nilai, u.tanggal_ujian, u.file_lampiran FROM ujian_tahfidz u JOIN santri s ON u.santri_id = s.id";
                                    $query = mysqli_query($conn, $sql);
                                    while($row = mysqli_fetch_assoc($query)) {
                                        echo "<tr>
                                                <td class='fw-bold text-dark'>{$row['nama_santri']}</td>
                                                <td><span class='badge bg-light text-dark border'>Juz {$row['juz_diuji']}</span></td>
                                                <td><span class='badge bg-success shadow-xs'>{$row['nilai']}</span></td>
                                                <td>".date('d/m/Y', strtotime($row['tanggal_ujian']))."</td>
                                                <td>";
                                                if(!empty($row['file_lampiran'])) {
                                                    $arr_files = explode(",", $row['file_lampiran']);
                                                    foreach($arr_files as $idx => $fname) { echo "<a href='uploads/$fname' target='_blank' class='btn-file-link m-1'>File ".($idx+1)."</a> "; }
                                                } else { echo "<span class='text-muted small'>Tidak ada berkas</span>"; }
                                        echo "   </td>
                                                <td>
                                                    <div class='d-flex justify-content-center gap-1'>
                                                        <a href='cetak_sertifikat.php?id={$row['id']}' class='btn btn-sm btn-primary py-1 px-2 shadow-xs'>PDF</a>";
                                                        if($role == 'ustadz') {
                                                            echo "<button class='btn btn-warning btn-sm py-1 px-2 btn-edit-ujian text-dark shadow-xs' data-id='{$row['id']}' data-juz='{$row['juz_diuji']}' data-nilai='{$row['nilai']}'>Edit</button>";
                                                            echo "<button class='btn btn-danger btn-sm py-1 px-2 btn-hapus-ujian shadow-xs' data-id='{$row['id']}'>Hapus</button>";
                                                        }
                                        echo "       </div>
                                                </td>
                                              </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include 'modals.php'; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>

<script> 
    const dataPekananDinamis = <?php echo json_encode($data_chart_dinamis); ?>; 
</script>
<script src="script_dashboard.js"></script>
</body>
</html>
<?php
session_start();
include 'koneksi.php';

// Proteksi halaman: Jika belum login, tendang balik
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); exit;
}

// Ambil ID ujian dari parameter URL
if (!isset($_GET['id'])) {
    echo "<script>alert('ID Sertifikat tidak ditemukan!'); window.location.href='index.php';</script>";
    exit;
}

$id_ujian = $_GET['id'];

// Query join mengambil data kelulusan, data santri, dan string TTD Canvas
$query = "SELECT u.*, s.nama_santri, ser.nomor_sertifikat, ser.ttd_ustadz, ser.tanggal_terbit 
          FROM ujian_tahfidz u 
          JOIN santri s ON u.santri_id = s.id 
          LEFT JOIN sertifikat ser ON u.id = ser.ujian_id 
          WHERE u.id = '$id_ujian'";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "<script>alert('Data kelulusan tidak ditemukan!'); window.location.href='index.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Kelulusan - <?php echo $data['nama_santri']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        
        /* FIX TOTAL: Mengunci tinggi jendela browser 100% tanpa toleransi scroll */
        html, body {
            height: 100vh !important;
            overflow: hidden !important;
            background-color: #0f172a; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
        }

        .main-wrapper-cert {
            display: flex;
            flex-direction: column;
            height: 100vh;
            padding: 15px 0; /* Diperkecil agar menghemat ruang vertical */
            box-sizing: border-box;
            justify-content: center;
        }

        /* BINGKAI UTAMA: Menggunakan max-height berbasis view-height (vh) yang ketat agar pas satu monitor */
        .certificate-container {
            max-width: 1000px;
            width: 90%;
            height: calc(100vh - 90px) !important; 
            max-height: 520px; /* Diturunkan ke 520px agar bener-bener pas di laptop */
            margin: 0 auto;
            background: #fdfbf7; 
            padding: 20px 50px; /* Padat dan ringkas */
            border: 12px double #111e38; 
            border-radius: 4px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            position: relative;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Membagi porsi ruang secara seimbang */
        }

        /* Garis Border Emas Bagian Dalam */
        .certificate-container::before {
            content: "";
            position: absolute;
            top: 5px; left: 5px; right: 5px; bottom: 5px;
            border: 2px solid #c5a059;
            pointer-events: none;
        }

        .cert-header-title {
            font-family: 'Cinzel', serif;
            color: #111e38;
            font-weight: 800;
            letter-spacing: 2px;
            font-size: 2rem; /* Ukuran yang pas */
            text-transform: uppercase;
        }

        .cert-subtitle {
            font-size: 0.95rem;
            color: #c5a059;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .student-name {
            font-family: 'Cinzel', serif;
            color: #111e38;
            font-weight: 700;
            border-bottom: 2px solid #c5a059;
            display: inline-block;
            padding: 0 25px 2px 25px;
            margin: 8px 0;
            font-size: 2.1rem;
            font-style: italic;
        }

        .img-ttd {
            max-width: 130px;
            height: 40px;
            object-fit: contain;
            display: block;
            margin: 1px auto;
        }

        .cert-text-p {
            font-size: 0.9rem; /* Mengecil halus agar teks naik */
            color: #475569;
            max-width: 780px;
            margin: 0 auto;
            line-height: 1.5;
        }

        /* Aturan Cetak Printer Pas Kertas A4 Landscape */
        @media print {
            @page { size: A4 landscape; margin: 0; }
            html, body { background: none; padding: 0; height: auto !important; overflow: visible !important; }
            .no-print { display: none !important; }
            .main-wrapper-cert { padding: 0; }
            .certificate-container { 
                box-shadow: none; 
                width: 100% !important;
                max-width: 100% !important;
                height: 100vh !important;
                max-height: 100vh !important;
                border: 15px double #111e38 !important;
                padding: 45px 60px !important;
                position: absolute;
                top: 0; left: 0;
            }
        }
    </style>
</head>
<body>

<div class="main-wrapper-cert">
    
    <div class="mx-auto mb-2 no-print" style="max-width: 1000px; width: 90%;">
        <div class="d-flex justify-content-between align-items-center">
            <a href="index.php?tab=ujian" class="btn btn-sm btn-outline-light px-3 py-1.5 fw-semibold rounded-3 shadow-sm">
                ⬅️ Kembali ke Dashboard Utama
            </a>
            <button onclick="window.print();" class="btn btn-sm btn-warning px-3 py-1.5 fw-bold rounded-3 shadow-sm" style="background-color: #c5a059; border: none; color: #111e38;">
                📥 Cetak / Unduh sebagai PDF
            </button>
        </div>
    </div>

    <div class="certificate-container">

        <div class="text-center" style="flex: 1; display: flex; flex-direction: column; justify-content: space-around;">
            
            <div class="mt-1"><span style="font-size: 2rem;">同</span></div>
            <h1 class="cert-header-title mb-0">Sertifikat Kelulusan</h1>
            <h5 class="cert-subtitle mb-0">Tahfidz Al-Qur'an</h5>
            <p class="text-muted small mb-1" style="font-size: 11px;">Nomor Dokumen: <?php echo !empty($data['nomor_sertifikat']) ? $data['nomor_sertifikat'] : 'CERT/TAHF/'.date('Y').'/'.rand(100,999); ?></p>
            
            <p class="cert-text-p text-secondary">Dengan rahmat Allah SWT, Dewan Penguji [NAMA INSTANSI/LEMBAGA BARU] menyatakan bahwa:</p>
            
            <div>
                <h2 class="student-name"><?php echo $data['nama_santri']; ?></h2>
            </div>
            
            <p class="cert-text-p text-dark fw-medium">
                Telah berhasil menyelesaikan ujian kompetensi hafalan Al-Qur'an secara <strong>Bil Ghaib (Hafalan Luar Kepala)</strong> untuk pengujian kategori target capaian akademik:
            </p>
            
            <h4 class="fw-bold my-1" style="color: #111e38; letter-spacing: 1px; font-family: 'Cinzel', serif; font-size: 1.4rem;">
                ✨ JENJANG 10 JUZ KOMPREHENSIF ✨
            </h4>
            
            <p class="text-muted mb-2 small" style="font-size: 13px;">
                Dinyatakan Lulus dengan Predikat Nilai Kelulusan: <span class="badge bg-dark px-2.5 py-1 text-warning fw-bold" style="font-size: 11px; background-color: #111e38 !important;"><?php echo $data['nilai'] == 'A' ? 'A (MUMTAZ)' : 'B (JAYYID JIDDAN)'; ?></span>
            </p>
            
            <div class="container-fluid p-0">
                <div class="row justify-content-between px-3">
                    <div class="col-5 text-center">
                        <p class="text-muted mb-0" style="font-size: 11px;">Ditetapkan di: Sukabumi</p>
                        <p class="text-muted mb-2" style="font-size: 11px;">Tanggal: <?php echo date('d F Y', strtotime($data['tanggal_ujian'])); ?></p>
                        <div style="height: 42px; display: flex; align-items: center; justify-content: center;">
                            <?php if(!empty($data['ttd_ustadz'])): ?>
                                <img src="<?php echo $data['ttd_ustadz']; ?>" class="img-ttd" alt="Terdigitalisasi">
                            <?php else: ?>
                                <div style="width: 110px; border-bottom: 1.5px dashed #cbd5e1; height: 25px;"></div>
                            <?php endif; ?>
                        </div>
                        <p class="fw-bold text-dark m-0 small" style="text-decoration: underline; font-size: 12px; margin-top: 2px !important;">Ustadz Penguji Utama</p>
                        <small class="text-muted d-block" style="font-size: 9px;">Sistem Pesantren Digital</small>
                    </div>
                    
                    <div class="col-5 text-center d-flex flex-column justify-content-end align-items-center">
                        <p class="fw-bold text-dark mb-3 small" style="font-size: 12px; line-height: 1.3;">An. Pimpinan Lembaga,<br>Ketua Koordinasi Pendidikan</p>
                        <div style="height: 42px; display: flex; align-items: center; justify-content: center;">
                            <div style="width: 130px; border-bottom: 1.5px solid #111e38; height: 25px; position: relative;">
                                <span style="font-size: 8px; color: #bcccda; position: absolute; top: 6px; left: 35px; font-style: italic;">Stempel Asli</span>
                            </div>
                        </div>
                        <p class="fw-bold text-dark m-0 small" style="text-decoration: underline; font-size: 12px; margin-top: 2px !important;">[Nama Lembaga/Instansi Kamu]</p>
                        <small class="text-muted d-block" style="font-size: 9px;">Bagian Administrasi Akademik</small>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

</body>
</html>
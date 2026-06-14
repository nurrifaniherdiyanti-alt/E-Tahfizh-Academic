<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['username'])) { die("Akses ditolak."); }

// Tambah Data Ujian (Create)
if (isset($_POST['santri_id']) && !isset($_POST['ubah_ujian'])) {
    $santri_id = $_POST['santri_id']; $juz_diuji = $_POST['juz_diuji']; $nilai = $_POST['nilai'];
    $status = 'Lulus'; $tanggal = date('Y-m-d'); $ttd_base64 = $_POST['image_ttd'];
    $no_sertifikat = "CERT/" . date('Ymd') . "/" . rand(100, 999);

    $daftar_file = [];
    if (!empty($_FILES['dokumen_pendukung']['name'][0])) {
        $files = $_FILES['dokumen_pendukung'];
        foreach ($files['name'] as $key => $name) {
            $tmp_name = $files['tmp_name'][$key]; $ext = pathinfo($name, PATHINFO_EXTENSION);
            $nama_file_baru = "BERKAS_" . time() . "_" . rand(10, 99) . "." . $ext;
            if (move_uploaded_file($tmp_name, "uploads/" . $nama_file_baru)) { $daftar_file[] = $nama_file_baru; }
        }
    }
    $string_files = implode(",", $daftar_file);

    $query_ujian = "INSERT INTO ujian_tahfidz (santri_id, juz_diuji, nilai, status, tanggal_ujian, file_lampiran) VALUES ('$santri_id', '$juz_diuji', '$nilai', '$status', '$tanggal', '$string_files')";
    if (mysqli_query($conn, $query_ujian)) {
        $id_u = mysqli_insert_id($conn);
        mysqli_query($conn, "INSERT INTO sertifikat (ujian_id, nomor_sertifikat, ttd_ustadz, tanggal_terbit) VALUES ('$id_u', '$no_sertifikat', '$ttd_base64', '$tanggal')");
        header("Location: index.php?status=sukses_input&tab=ujian"); exit;
    }
}

// Edit Data Ujian (Update)
if (isset($_POST['ubah_ujian'])) {
    $id_ujian = $_POST['id_ujian']; $juz_diuji = $_POST['juz_diuji']; $nilai = $_POST['nilai'];

    if (!empty($_FILES['dokumen_pendukung']['name'][0])) {
        $files = $_FILES['dokumen_pendukung']; $daftar_file = [];
        foreach ($files['name'] as $key => $name) {
            $tmp_name = $files['tmp_name'][$key]; $ext = pathinfo($name, PATHINFO_EXTENSION);
            $nama_file_baru = "BERKAS_" . time() . "_" . rand(10, 99) . "." . $ext;
            if (move_uploaded_file($tmp_name, "uploads/" . $nama_file_baru)) { $daftar_file[] = $nama_file_baru; }
        }
        $string_files = implode(",", $daftar_file);
        $query = "UPDATE ujian_tahfidz SET juz_diuji = '$juz_diuji', nilai = '$nilai', file_lampiran = '$string_files' WHERE id = '$id_ujian'";
    } else {
        $query = "UPDATE ujian_tahfidz SET juz_diuji = '$juz_diuji', nilai = '$nilai' WHERE id = '$id_ujian'";
    }
    if (mysqli_query($conn, $query)) { header("Location: index.php?status=sukses_update&tab=ujian"); exit; }
}

// Hapus Data Ujian (Delete)
if (isset($_GET['hapus_ujian'])) {
    if (mysqli_query($conn, "DELETE FROM ujian_tahfidz WHERE id = '{$_GET['hapus_ujian']}'")) {
        header("Location: index.php?status=sukses_hapus&tab=ujian"); exit;
    }
}
?>
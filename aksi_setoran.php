<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['username'])) { die("Akses ditolak."); }

// 1. PROSES TAMBAH (CREATE)
if (isset($_POST['tambah_setoran'])) {
    $santri_id      = $_POST['santri_id'];
    $tanggal        = $_POST['tanggal'];
    $surah_dihafal  = $_POST['surah_dihafal'];
    $ayat_dihafal   = $_POST['ayat_dihafal'];
    $evaluasi       = $_POST['evaluasi_ustadz'];

    $query = "INSERT INTO setoran_harian (santri_id, tanggal, surah_dihafal, ayat_dihafal, evaluasi_ustadz) VALUES ('$santri_id', '$tanggal', '$surah_dihafal', '$ayat_dihafal', '$evaluasi')";
    if (mysqli_query($conn, $query)) {
        header("Location: index.php?status=sukses_input&tab=setoran"); exit;
    }
}

// 2. PROSES UBAH/EDIT (UPDATE)
if (isset($_POST['ubah_setoran'])) {
    $id             = $_POST['id_setoran'];
    $tanggal        = $_POST['tanggal'];
    $surah_dihafal  = $_POST['surah_dihafal'];
    $ayat_dihafal   = $_POST['ayat_dihafal'];
    $evaluasi       = $_POST['evaluasi_ustadz'];

    $query = "UPDATE setoran_harian SET tanggal = '$tanggal', surah_dihafal = '$surah_dihafal', ayat_dihafal = '$ayat_dihafal', evaluasi_ustadz = '$evaluasi' WHERE id = '$id'";
    if (mysqli_query($conn, $query)) {
        header("Location: index.php?status=sukses_update&tab=setoran"); exit;
    }
}

// 3. PROSES HAPUS (DELETE)
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = "DELETE FROM setoran_harian WHERE id = '$id'";
    if (mysqli_query($conn, $query)) {
        header("Location: index.php?status=sukses_hapus&tab=setoran"); exit;
    }
}
?>
<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Hasil Ujian & TTD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #canvas-ttd {
            border: 2px dashed #ccc;
            border-radius: 4px;
            cursor: crosshair;
            background-color: #fcfcfc;
        }
    </style>
</head>
<body class="bg-light py-5">
<div class="container" style="max-width: 600px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h3 class="card-title text-center mb-4 text-success fw-bold">Form Kelulusan Ujian</h3>
            
            <form action="simpan_ujian.php" method="POST" id="formUjian">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Santri</label>
                    <select name="santri_id" class="form-select" required>
                        <?php
                        $query = mysqli_query($conn, "SELECT * FROM santri");
                        while($row = mysqli_fetch_assoc($query)) {
                            echo "<option value='{$row['id']}'>{$row['nama_santri']} ({$row['nis']})</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Juz yang Diuji</label>
                        <select name="juz_diuji" class="form-select" required>
                            <option value="5">Juz 1 - 5</option>
                            <option value="10">Juz 6 - 10</option>
                            <option value="15">Juz 11 - 15</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Nilai Huruf</label>
                        <select name="nilai" class="form-select" required>
                            <option value="A">A (Mumtaz)</option>
                            <option value="B">B (Jayyid Jiddan)</option>
                            <option value="C">C (Jayyid)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold d-flex justify-content-between">
                        Tanda Tangan Ustadz Penguji
                        <button type="button" class="btn btn-sm btn-outline-danger py-0" id="clear-btn">Hapus</button>
                    </label>
                    <center>
                        <canvas id="canvas-ttd" width="450" height="150"></canvas>
                    </center>
                    <input type="hidden" name="image_ttd" id="image_ttd" required>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">Simpan & Terbitkan Sertifikat</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Logika Canvas Tanda Tangan
    const canvas = document.getElementById('canvas-ttd');
    const ctx = canvas.getContext('2d');
    let drawing = false;

    // Pengaturan garis pena
    ctx.lineWidth = 3;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#1c3d27'; // Warna hijau tua elegan

    // Event Mouse (Desktop)
    canvas.addEventListener('mousedown', () => drawing = true);
    canvas.addEventListener('mouseup', () => { drawing = false; ctx.beginPath(); updateHiddenInput(); });
    canvas.addEventListener('mousemove', draw);

    // Event Touch (HP / Tablet)
    canvas.addEventListener('touchstart', (e) => { drawing = true; e.preventDefault(); });
    canvas.addEventListener('touchend', () => { drawing = false; ctx.beginPath(); updateHiddenInput(); });
    canvas.addEventListener('touchmove', (e) => {
        let touch = e.touches[0];
        let mouseEvent = new MouseEvent("mousemove", {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        canvas.dispatchEvent(mouseEvent);
    });

    function draw(e) {
        if (!drawing) return;
        const rect = canvas.getBoundingClientRect();
        
        // Menghitung posisi koordinat pointer di dalam canvas
        let x = (e.clientX || e.touches[0].clientX) - rect.left;
        let y = (e.clientY || e.touches[0].clientY) - rect.top;

        ctx.lineTo(x, y);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(x, y);
    }

    // Tombol Clear Canvas
    document.getElementById('clear-btn').addEventListener('click', () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        document.getElementById('image_ttd').value = "";
    });

    // Masukkan hasil coretan ke input hidden base64 sebelum disubmit
    function updateHiddenInput() {
        document.getElementById('image_ttd').value = canvas.toDataURL();
    }

    // Validasi form jika TTD belum diisi
    document.getElementById('formUjian').addEventListener('submit', function(e) {
        if(document.getElementById('image_ttd').value === "") {
            alert("Harap bubuhkan tanda tangan terlebih dahulu!");
            e.preventDefault();
        }
    });
</script>
</body>
</html>
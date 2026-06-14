$(document).ready(function() {
    // =========================================================================
    // 1. INISIALISASI DATATABLES DENGAN FITUR PENCARIAN & EKSPOR
    // =========================================================================
    $('#tabelUjian').DataTable({
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excel', className: 'btn btn-sm btn-outline-success mb-2' },
            { extend: 'print', className: 'btn btn-sm btn-outline-dark mb-2' }
        ],
        language: { 
            search: "🔍 Cari Kelulusan Ujian:",
            paginate: { next: "Next", previous: "Prev" }
        }
    });

    $('#tabelSetoran').DataTable({
        language: { 
            search: "🔍 Cari Catatan Jurnal:",
            paginate: { next: "Next", previous: "Prev" }
        }
    });

    // =========================================================================
    // 2. LOGIKA MEMAKSA TAB MENETAP DI TEMPAT & PUTAR AUDIO YANG DI-FIX KETAT
    // =========================================================================
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');
    
    if (activeTab === 'setoran') {
        var triggerEl = document.querySelector('#tab-setoran-btn');
        if(triggerEl) bootstrap.Tab.getOrCreateInstance(triggerEl).show();
    } else if (activeTab === 'ujian') {
        var triggerEl = document.querySelector('#tab-ujian-btn');
        if(triggerEl) bootstrap.Tab.getOrCreateInstance(triggerEl).show();
    }

    // Eksekusi Pop-up SweetAlert2 Sukses + Bunyi Audio Sah
    if (urlParams.get('status') === 'sukses_input' || urlParams.get('status') === 'sukses_update' || urlParams.get('status') === 'sukses_hapus') {
        let pesan = "Operasi berhasil dilaksanakan!";
        if(urlParams.get('status') === 'sukses_input') pesan = "Data sukses ditambahkan ke database pondok!";
        if(urlParams.get('status') === 'sukses_update') pesan = "Perubahan data sukses disimpan!";
        if(urlParams.get('status') === 'sukses_hapus') pesan = "Data telah permanen terhapus!";

        Swal.fire({
            title: "Alhamdulillah!",
            text: pesan,
            icon: "success",
            confirmButtonColor: "#111e38", 
            confirmButtonText: "OK"
        }).then((result) => {
            // JALUR UTAMA AUDIO: Bunyi HANYA setelah klik sadar tombol OK oleh user (Lolos proteksi browser!)
            if (result.isConfirmed) {
                var audio = document.getElementById('audioSukses');
                if (audio) {
                    audio.muted = false;
                    audio.currentTime = 0;
                    audio.play().catch(function(error) {
                        console.log("Audio gagal berbunyi: ", error);
                    });
                }
            }
        });
    }
});

// =========================================================================
// 3. LOGIKA POP-UP KONFIRMASI HAPUS MODERN (SWEETALERT2)
// =========================================================================
$('body').on('click', '.btn-hapus-ujian', function() {
    var id_ujian = $(this).attr('data-id');
    Swal.fire({
        title: "Apakah Anda Yakin?",
        text: "Data kelulusan dan sertifikat santri ini akan dihapus permanen!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'simpan_ujian.php?hapus_ujian=' + id_ujian;
        }
    });
});

$('body').on('click', '.btn-hapus-setoran', function() {
    var id_setoran = $(this).attr('data-id');
    Swal.fire({
        title: "Hapus Catatan Jurnal?",
        text: "Evaluasi setoran harian santri ini tidak bisa dikembalikan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'aksi_setoran.php?hapus=' + id_setoran;
        }
    });
});

// =========================================================================
// 4. KONTROL MELEMPAR VALUE DATA KE MODAL EDIT FORM
// =========================================================================
$('body').on('click', '.btn-edit-ujian', function() {
    $('#edit_ujian_id').val($(this).attr('data-id'));
    $('#edit_ujian_juz').val($(this).attr('data-juz'));
    $('#edit_ujian_nilai').val($(this).attr('data-nilai'));
    var modalUjianElement = document.getElementById('modalEditUjian');
    bootstrap.Modal.getOrCreateInstance(modalUjianElement).show();
});

$('body').on('click', '.btn-edit', function() {
    $('#edit_id').val($(this).attr('data-id'));
    $('#edit_tgl').val($(this).attr('data-tgl'));
    $('#edit_surah').val($(this).attr('data-surah'));
    $('#edit_ayat').val($(this).attr('data-ayat'));
    $('#edit_evaluasi').val($(this).attr('data-evaluasi'));
    var modalElement = document.getElementById('modalEditSetoran');
    bootstrap.Modal.getOrCreateInstance(modalElement).show();
});

// =========================================================================
// 5. ANIMASI GRAFIK BATANG VERTIKAL PEKANAN DINAMIS FROM MYSQL
// =========================================================================
const ctxChart = document.getElementById('grafikAnimasi').getContext('2d');
new Chart(ctxChart, {
    type: 'bar',
    data: {
        labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
        datasets: [{
            label: 'Jumlah Santri Lulus Ujian',
            data: dataPekananDinamis, 
            backgroundColor: '#111e38', 
            borderRadius: 4,
            borderWidth: 0,
            barThickness: 45
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        animation: { duration: 1800, easing: 'easeOutElastic' },
        scales: {
            x: {
                grid: { display: false },
                ticks: {
                    font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600', size: 12 },
                    color: '#64748b'
                }
            },
            y: {
                grid: { color: '#f1f5f9' },
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    callback: function(value) { return value + ' Santri'; },
                    font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                    color: '#94a3b8'
                }
            }
        }
    }
});

// =========================================================================
// 6. LOGIKA CANVAS GORESAN TANDA TANGAN DIGITAL
// =========================================================================
const canvas = document.getElementById('canvas-ttd');
if (canvas) {
    const ctxCanvas = canvas.getContext('2d');
    let drawing = false;
    ctxCanvas.lineWidth = 3; ctxCanvas.lineCap = 'round'; ctxCanvas.strokeStyle = '#0f2d19';

    canvas.addEventListener('mousedown', () => drawing = true);
    canvas.addEventListener('mouseup', () => { drawing = false; ctxCanvas.beginPath(); $('#image_ttd').val(canvas.toDataURL()); });
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('touchstart', (e) => { drawing = true; e.preventDefault(); });
    canvas.addEventListener('touchend', () => { drawing = false; ctxCanvas.beginPath(); $('#image_ttd').val(canvas.toDataURL()); });
    canvas.addEventListener('touchmove', (e) => {
        let touch = e.touches[0];
        let mouseEvent = new MouseEvent("mousemove", { clientX: touch.clientX, clientY: touch.clientY });
        canvas.dispatchEvent(mouseEvent);
    });

    function draw(e) {
        if (!drawing) return;
        const rect = canvas.getBoundingClientRect();
        let x = (e.clientX || e.touches[0].clientX) - rect.left;
        let y = (e.clientY || e.touches[0].clientY) - rect.top;
        ctxCanvas.lineTo(x, y); ctxCanvas.stroke(); ctxCanvas.beginPath(); ctxCanvas.moveTo(x, y);
    }
    $('#clear-btn').click(function() { ctxCanvas.clearRect(0, 0, canvas.width, canvas.height); $('#image_ttd').val(""); });
}
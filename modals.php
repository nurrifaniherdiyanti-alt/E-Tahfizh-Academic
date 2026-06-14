<div class="modal fade" id="modalInputUjian" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">Form Kelulusan Ujian & TTD Penguji</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="simpan_ujian.php" method="POST" id="formUjian" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Santri</label>
                        <select name="santri_id" class="form-select" required>
                            <?php
                            $q_santri = mysqli_query($conn, "SELECT * FROM santri");
                            while($s = mysqli_fetch_assoc($q_santri)) {
                                echo "<option value='{$s['id']}'>{$s['nama_santri']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Juz yang Diuji</label>
                            <select name="juz_diuji" class="form-select">
                                <option value="5">5 Juz Pertama</option>
                                <option value="10">10 Juz</option>
                                <option value="30">30 Juz Bil Ghaib</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nilai Kelulusan</label>
                            <select name="nilai" class="form-select">
                                <option value="A">A (Mumtaz)</option>
                                <option value="B">B (Jayyid Jiddan)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Upload Dokumen Pendukung (Multiple)</label>
                        <input type="file" name="dokumen_pendukung[]" class="form-control" multiple required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold d-flex justify-content-between">
                            Goreskan Tanda Tangan Ustadz Penguji (Canvas):
                            <button type="button" class="btn btn-sm btn-outline-danger py-0" id="clear-btn">Clear</button>
                        </label>
                        <center>
                            <canvas id="canvas-ttd" width="500" height="140"></canvas>
                        </center>
                        <input type="hidden" name="image_ttd" id="image_ttd" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahSetoran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Catat Setoran Hafalan Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="aksi_setoran.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="tambah_setoran" value="1">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Santri</label>
                        <select name="santri_id" class="form-select" required>
                            <?php
                            $q_s = mysqli_query($conn, "SELECT * FROM santri");
                            while($s = mysqli_fetch_assoc($q_s)) { echo "<option value='{$s['id']}'>{$s['nama_santri']}</option>"; }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tanggal Setoran</label>
                        <input type="date" name="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Nama Surah</label>
                            <input type="text" name="surah_dihafal" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Rentang Ayat</label>
                            <input type="text" name="ayat_dihafal" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Evaluasi Kelancaran & Tajwid</label>
                        <textarea name="evaluasi_ustadz" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold">Simpan Setoran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditSetoran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">Ubah Data Catatan Setoran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="aksi_setoran.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="ubah_setoran" value="1">
                    <input type="hidden" name="id_setoran" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tanggal Setoran</label>
                        <input type="date" name="tanggal" id="edit_tgl" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Nama Surah</label>
                            <input type="text" name="surah_dihafal" id="edit_surah" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Rentang Ayat</label>
                            <input type="text" name="ayat_dihafal" id="edit_ayat" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Evaluasi Kelancaran & Tajwid</label>
                        <textarea name="evaluasi_ustadz" id="edit_evaluasi" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold text-dark">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditUjian" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">Ubah Data Kelulusan Ujian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="simpan_ujian.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="ubah_ujian" value="1">
                    <input type="hidden" name="id_ujian" id="edit_ujian_id">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Juz yang Diuji</label>
                        <select name="juz_diuji" id="edit_ujian_juz" class="form-select">
                            <option value="5">5 Juz Pertama</option>
                            <option value="10">10 Juz</option>
                            <option value="30">30 Juz Bil Ghaib</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nilai Kelulusan</label>
                        <select name="nilai" id="edit_ujian_nilai" class="form-select">
                            <option value="A">A (Mumtaz)</option>
                            <option value="B">B (Jayyid Jiddan)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Ganti/Perbarui Dokumen Pendukung (Opsional)</label>
                        <input type="file" name="dokumen_pendukung[]" class="form-control" multiple>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold text-dark">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
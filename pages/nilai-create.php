<?php
$pageTitle = "Tambah Data Nilai";
$currentPage = 'nilai';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$errorMessage = '';

try {
    $stmtSiswa = $pdo->query("SELECT id, nama FROM siswa ORDER BY nama");
    $siswa = $stmtSiswa->fetchAll();

    $stmtMapel = $pdo->query("SELECT id, nama FROM mata_pelajaran ORDER BY nama");
    $mataPelajaran = $stmtMapel->fetchAll();

    $stmtKelas = $pdo->query("SELECT id, nama FROM kelas ORDER BY nama");
    $kelas = $stmtKelas->fetchAll();

    $stmtTahun = $pdo->query("SELECT id, nama FROM tahun_ajaran ORDER BY nama");
    $tahunAjaran = $stmtTahun->fetchAll();

    $stmtJenis = $pdo->query("SELECT id, nama FROM nilai_jenis ORDER BY nama");
    $jenisNilai = $stmtJenis->fetchAll();

} catch (PDOException $e) {
    $errorMessage = "Error loading data: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_siswa = $_POST['id_siswa'] ?? '';
    $id_mata_pelajaran = $_POST['id_mata_pelajaran'] ?? '';
    $id_kelas = $_POST['id_kelas'] ?? '';
    $id_tahun_ajaran = $_POST['id_tahun_ajaran'] ?? '';
    $id_jenis_nilai = $_POST['id_jenis_nilai'] ?? '';
    $nilai = $_POST['nilai'] ?? '';
    $tanggal_penilaian = $_POST['tanggal_penilaian'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';

    if (empty($id_siswa) || empty($id_mata_pelajaran) || empty($id_kelas) ||
        empty($id_tahun_ajaran) || empty($id_jenis_nilai) || empty($nilai) ||
        empty($tanggal_penilaian)) {
        $errorMessage = "Harap lengkapi semua kolom wajib.";
    } elseif (!is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
        $errorMessage = "Nilai harus berupa angka antara 0-100.";
    } else {
        try {
            $checkSql = "SELECT COUNT(*) as count FROM nilai 
                        WHERE id_siswa = :id_siswa 
                        AND id_mata_pelajaran = :id_mata_pelajaran 
                        AND id_kelas = :id_kelas 
                        AND id_tahun_ajaran = :id_tahun_ajaran 
                        AND id_jenis_nilai = :id_jenis_nilai";

            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->bindParam(':id_siswa', $id_siswa);
            $checkStmt->bindParam(':id_mata_pelajaran', $id_mata_pelajaran);
            $checkStmt->bindParam(':id_kelas', $id_kelas);
            $checkStmt->bindParam(':id_tahun_ajaran', $id_tahun_ajaran);
            $checkStmt->bindParam(':id_jenis_nilai', $id_jenis_nilai);
            $checkStmt->execute();

            $existingCount = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'];

            if ($existingCount > 0) {
                $errorMessage = "Nilai untuk kombinasi siswa, mata pelajaran, kelas, tahun ajaran, dan jenis nilai ini sudah ada.";
            } else {
                $sql = "INSERT INTO nilai (id_siswa, id_mata_pelajaran, id_kelas, id_tahun_ajaran, id_jenis_nilai, nilai, tanggal_penilaian, keterangan) 
                        VALUES (:id_siswa, :id_mata_pelajaran, :id_kelas, :id_tahun_ajaran, :id_jenis_nilai, :nilai, :tanggal_penilaian, :keterangan)";

                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id_siswa', $id_siswa);
                $stmt->bindParam(':id_mata_pelajaran', $id_mata_pelajaran);
                $stmt->bindParam(':id_kelas', $id_kelas);
                $stmt->bindParam(':id_tahun_ajaran', $id_tahun_ajaran);
                $stmt->bindParam(':id_jenis_nilai', $id_jenis_nilai);
                $stmt->bindParam(':nilai', $nilai);
                $stmt->bindParam(':tanggal_penilaian', $tanggal_penilaian);
                $stmt->bindParam(':keterangan', $keterangan);

                if ($stmt->execute()) {
                    header('Location: ' . htmlspecialchars($urlPrefix) . '/nilai');
                    exit;
                } else {
                    $errorMessage = "Gagal menambahkan data nilai. Silakan coba lagi.";
                }
            }
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
}

ob_start();
?>

    <div class="flex flex-col sm:flex-row items-center justify-between mb-6 gap-4">
        <h1 class="text-2xl font-bold text-primary-700">Tambah Data Nilai</h1>
        <a href="<?= htmlspecialchars($urlPrefix) ?>/nilai"
           class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
            <iconify-icon icon="mdi:arrow-left" width="20" height="20"></iconify-icon>
            Kembali ke Daftar Nilai
        </a>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <?php if ($errorMessage): ?>
            <div class="flex items-center gap-3 mb-6 p-4 rounded-lg bg-status-error-100 text-status-error-700 border border-status-error-200">
                <iconify-icon icon="mdi:alert-circle" width="22" class="shrink-0"></iconify-icon>
                <div class="flex-1"> <?= htmlspecialchars($errorMessage) ?> </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="id_siswa" class="block font-medium mb-1">Siswa <span
                                class="text-status-error-700">*</span></label>
                    <select class="w-full rounded-lg border border-gray-300 focus:border-primary-500 focus:ring-primary-500 bg-white py-2 px-3"
                            id="id_siswa" name="id_siswa" required>
                        <option value="" disabled selected>Pilih Siswa</option>
                        <?php foreach ($siswa as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= (isset($_POST['id_siswa']) && $_POST['id_siswa'] == $s['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="id_mata_pelajaran" class="block font-medium mb-1">Mata Pelajaran <span
                                class="text-status-error-700">*</span></label>
                    <select class="w-full rounded-lg border border-gray-300 focus:border-primary-500 focus:ring-primary-500 bg-white py-2 px-3"
                            id="id_mata_pelajaran" name="id_mata_pelajaran" required>
                        <option value="" disabled selected>Pilih Mata Pelajaran</option>
                        <?php foreach ($mataPelajaran as $mp): ?>
                            <option value="<?= $mp['id'] ?>" <?= (isset($_POST['id_mata_pelajaran']) && $_POST['id_mata_pelajaran'] == $mp['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($mp['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="id_kelas" class="block font-medium mb-1">Kelas <span
                                class="text-status-error-700">*</span></label>
                    <select class="w-full rounded-lg border border-gray-300 focus:border-primary-500 focus:ring-primary-500 bg-white py-2 px-3"
                            id="id_kelas" name="id_kelas" required>
                        <option value="" disabled selected>Pilih Kelas</option>
                        <?php foreach ($kelas as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= (isset($_POST['id_kelas']) && $_POST['id_kelas'] == $k['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="id_tahun_ajaran" class="block font-medium mb-1">Tahun Ajaran <span
                                class="text-status-error-700">*</span></label>
                    <select class="w-full rounded-lg border border-gray-300 focus:border-primary-500 focus:ring-primary-500 bg-white py-2 px-3"
                            id="id_tahun_ajaran" name="id_tahun_ajaran" required>
                        <option value="" disabled selected>Pilih Tahun Ajaran</option>
                        <?php foreach ($tahunAjaran as $ta): ?>
                            <option value="<?= $ta['id'] ?>" <?= (isset($_POST['id_tahun_ajaran']) && $_POST['id_tahun_ajaran'] == $ta['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ta['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="id_jenis_nilai" class="block font-medium mb-1">Jenis Nilai <span
                                class="text-status-error-700">*</span></label>
                    <select class="w-full rounded-lg border border-gray-300 focus:border-primary-500 focus:ring-primary-500 bg-white py-2 px-3"
                            id="id_jenis_nilai" name="id_jenis_nilai" required>
                        <option value="" disabled selected>Pilih Jenis Nilai</option>
                        <?php foreach ($jenisNilai as $jn): ?>
                            <option value="<?= $jn['id'] ?>" <?= (isset($_POST['id_jenis_nilai']) && $_POST['id_jenis_nilai'] == $jn['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($jn['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="nilai" class="block font-medium mb-1">Nilai <span class="text-status-error-700">*</span></label>
                    <input type="number"
                           class="w-full rounded-lg border border-gray-300 focus:border-primary-500 focus:ring-primary-500 py-2 px-3"
                           id="nilai" name="nilai" required min="0" max="100" step="0.01" placeholder="0-100"
                           value="<?= htmlspecialchars($_POST['nilai'] ?? '') ?>">
                    <div class="text-xs text-gray-500 mt-1">Masukkan nilai antara 0-100</div>
                </div>
                <div class="md:col-span-2">
                    <label for="tanggal_penilaian" class="block font-medium mb-1">Tanggal Penilaian <span
                                class="text-status-error-700">*</span></label>
                    <input type="date"
                           class="w-full rounded-lg border border-gray-300 focus:border-primary-500 focus:ring-primary-500 py-2 px-3"
                           id="tanggal_penilaian" name="tanggal_penilaian" required
                           value="<?= htmlspecialchars($_POST['tanggal_penilaian'] ?? date('Y-m-d')) ?>">
                </div>
                <div class="md:col-span-2">
                    <label for="keterangan" class="block font-medium mb-1">Keterangan</label>
                    <textarea
                            class="w-full rounded-lg border border-gray-300 focus:border-primary-500 focus:ring-primary-500 py-2 px-3"
                            id="keterangan" name="keterangan" rows="3"
                            placeholder="Keterangan tambahan (opsional)"><?= htmlspecialchars($_POST['keterangan'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="flex flex-row justify-end gap-2 pt-6">
                <button type="submit"
                        class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
                    <iconify-icon icon="mdi:content-save-outline" width="20" height="20"></iconify-icon>
                    Simpan Data
                </button>
                <button type="reset"
                        class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
                    <iconify-icon icon="mdi:arrow-u-left-top" width="20" height="20"></iconify-icon>
                    Reset Form
                </button>
            </div>
        </form>
    </div>

<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>
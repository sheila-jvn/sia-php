<?php
$pageTitle = "Detail Data Siswa";
$currentPage = 'students'; // Keep 'students' active for navigation

require_once __DIR__ . '/../lib/database.php'; // Adjust path if necessary

$pdo = getDbConnection();

$student = null;
$errorMessage = '';

// Validasi ID siswa dari parameter URL
// Harus ada dan numerik untuk mencegah error SQL dan masalah keamanan
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Ambil record siswa tunggal menggunakan prepared statement
        $sql = "SELECT * FROM siswa WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        // Periksa apakah siswa ditemukan di database
        if (!$student) {
            $errorMessage = "Data siswa dengan ID " . htmlspecialchars($id) . " tidak ditemukan.";
        }
    } catch (PDOException $e) {
        // Tangani error koneksi database atau query
        $errorMessage = "Terjadi kesalahan saat mengambil data: " . $e->getMessage();
    }
} else {
    // Tangani parameter ID yang hilang atau tidak valid
    $errorMessage = "ID siswa tidak valid atau tidak diberikan.";
}

ob_start(); // Start output buffering
?>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold text-primary-700">Detail Data Siswa</h1>
        <div class="flex gap-2">
            <a href="<?= htmlspecialchars($urlPrefix) ?>/students"
               class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
                <iconify-icon icon="cil:arrow-left"></iconify-icon>
                Kembali ke Daftar Siswa
            </a>
            <?php if ($student): ?>
                <!-- Tombol aksi hanya ditampilkan jika data siswa ada -->
                <!-- Tombol Edit dan Delete mempertahankan alur kerja saat ini -->
                <a href="<?= htmlspecialchars($urlPrefix) ?>/students/edit?id=<?= htmlspecialchars($student['id']) ?>"
                   class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-primary-300 text-primary-700 bg-white hover:bg-primary-50 transition">
                    <iconify-icon icon="cil:pencil"></iconify-icon>
                    Edit Data
                </a>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/students/delete?id=<?= htmlspecialchars($student['id']) ?>"
                   class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-status-error-500 text-white hover:bg-status-error-600 transition">
                    <iconify-icon icon="cil:trash"></iconify-icon>
                    Hapus Data
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <?php if ($errorMessage): ?>
            <div class="flex items-center gap-2 p-4 rounded-lg bg-status-error-100 text-status-error-700 border border-status-error-200">
                <iconify-icon icon="cil:warning"></iconify-icon>
                <span><?= htmlspecialchars($errorMessage) ?></span>
            </div>
        <?php elseif ($student): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <div class="text-sm font-semibold text-primary-700 mb-1">ID Siswa</div>
                        <div class="p-3 rounded-lg bg-secondary-50 border border-secondary-200"><?= htmlspecialchars($student['id']) ?></div>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-primary-700 mb-1">NIS (Nomor Induk Siswa)</div>
                        <div class="p-3 rounded-lg bg-secondary-50 border border-secondary-200"><?= htmlspecialchars($student['nis']) ?></div>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-primary-700 mb-1">NISN (Nomor Induk Siswa Nasional)</div>
                        <div class="p-3 rounded-lg bg-secondary-50 border border-secondary-200"><?= htmlspecialchars($student['nisn']) ?></div>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-primary-700 mb-1">Nama Lengkap</div>
                        <div class="p-3 rounded-lg bg-secondary-50 border border-secondary-200"><?= htmlspecialchars($student['nama']) ?></div>
                    </div>
                    <div>
                        <!-- Tampilkan field opsional dengan fallback untuk nilai kosong -->
                        <!-- Menggunakan null coalescing dan operator ternary untuk menampilkan '-' untuk field kosong -->
                        <!-- Ini memberikan UX yang lebih baik daripada menampilkan kotak kosong -->
                        <div class="text-sm font-semibold text-primary-700 mb-1">Nomor Kartu Keluarga</div>
                        <div class="p-3 rounded-lg bg-secondary-50 border border-secondary-200"><?= htmlspecialchars($student['no_kk'] ?: '-') ?></div>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-primary-700 mb-1">Tanggal Lahir</div>
                        <div class="p-3 rounded-lg bg-secondary-50 border border-secondary-200"><?= htmlspecialchars($student['tanggal_lahir']) ?></div>
                    </div>
                    <div>
                        <!-- Konversi nilai jenis kelamin database ke teks yang dapat dibaca untuk tampilan -->
                        <!-- Logika yang sama seperti di halaman daftar siswa utama -->
                        <div class="text-sm font-semibold text-primary-700 mb-1">Jenis Kelamin</div>
                        <div class="p-3 rounded-lg bg-secondary-50 border border-secondary-200"><?= $student['jenis_kelamin'] == '1' ? 'Laki-laki' : 'Perempuan' ?></div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <!-- Field alamat menggunakan whitespace-pre-line untuk mempertahankan line break -->
                        <!-- Ini memungkinkan alamat multi-baris ditampilkan dengan benar -->
                        <div class="text-sm font-semibold text-primary-700 mb-1">Alamat</div>
                        <div class="p-3 rounded-lg bg-secondary-50 border border-secondary-200 whitespace-pre-line"><?= htmlspecialchars($student['alamat']) ?></div>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-primary-700 mb-1">Nama Ayah</div>
                        <div class="p-3 rounded-lg bg-secondary-50 border border-secondary-200"><?= htmlspecialchars($student['nama_ayah'] ?: '-') ?></div>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-primary-700 mb-1">NIK Ayah</div>
                        <div class="p-3 rounded-lg bg-secondary-50 border border-secondary-200"><?= htmlspecialchars($student['nik_ayah'] ?: '-') ?></div>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-primary-700 mb-1">Nama Ibu</div>
                        <div class="p-3 rounded-lg bg-secondary-50 border border-secondary-200"><?= htmlspecialchars($student['nama_ibu'] ?: '-') ?></div>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-primary-700 mb-1">NIK Ibu</div>
                        <div class="p-3 rounded-lg bg-secondary-50 border border-secondary-200"><?= htmlspecialchars($student['nik_ibu'] ?: '-') ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>
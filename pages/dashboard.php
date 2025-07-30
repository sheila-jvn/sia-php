<?php
require_once __DIR__ . '/../lib/database.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $urlPrefix . '/login');
    exit();
}
$pageTitle = "Dashboard";
$currentPage = 'dashboard';
$pdo = getDbConnection();

// Quick stats
$totalStudents = $pdo->query("SELECT COUNT(*) FROM siswa")->fetchColumn();
$totalTeachers = $pdo->query("SELECT COUNT(*) FROM guru")->fetchColumn();
$totalClasses = $pdo->query("SELECT COUNT(*) FROM kelas")->fetchColumn();

// Recent absensi (last 5)
$stmt = $pdo->prepare("SELECT kh.tanggal, s.nama AS nama_siswa, k.nama AS nama_kelas, ks.nama AS status_kehadiran FROM kehadiran kh JOIN siswa s ON kh.id_siswa = s.id JOIN kelas k ON kh.id_kelas = k.id JOIN kehadiran_status ks ON kh.id_status = ks.id ORDER BY kh.tanggal DESC LIMIT 5");
$stmt->execute();
$recentAbsensi = $stmt->fetchAll();

// Recent SPP payments (last 5)
$stmt = $pdo->prepare("SELECT ps.tanggal_bayar, s.nama AS nama_siswa, ps.bulan, ps.jumlah_bayar FROM pembayaran_spp ps JOIN siswa s ON ps.id_siswa = s.id ORDER BY ps.tanggal_bayar DESC, ps.id DESC LIMIT 5");
$stmt->execute();
$recentSPP = $stmt->fetchAll();

ob_start();
?>
<div class="max-w-7xl mx-auto px-4 py-8">
  <h1 class="text-3xl font-bold text-primary-700 mb-6 flex items-center gap-2">
    <iconify-icon icon="cil:locomotive" class="text-primary-500" width="36"></iconify-icon>
    Dashboard
  </h1>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-primary-600 text-white rounded-lg shadow p-6 flex items-center gap-4">
      <iconify-icon icon="cil:people" width="36" class="opacity-80"></iconify-icon>
      <div>
        <div class="text-2xl font-bold"><?= number_format($totalStudents) ?></div>
        <div class="text-primary-100">Siswa</div>
      </div>
    </div>
    <div class="bg-accent-500 text-white rounded-lg shadow p-6 flex items-center gap-4">
      <iconify-icon icon="cil:school" width="36" class="opacity-80"></iconify-icon>
      <div>
        <div class="text-2xl font-bold"><?= number_format($totalTeachers) ?></div>
        <div class="text-accent-100">Guru</div>
      </div>
    </div>
    <div class="bg-secondary-600 text-white rounded-lg shadow p-6 flex items-center gap-4">
      <iconify-icon icon="cil:layers" width="36" class="opacity-80"></iconify-icon>
      <div>
        <div class="text-2xl font-bold"><?= number_format($totalClasses) ?></div>
        <div class="text-secondary-100">Kelas</div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
      <div class="flex items-center gap-2 mb-4">
        <iconify-icon icon="cil:calendar" width="24" class="text-primary-500"></iconify-icon>
        <h2 class="text-lg font-semibold text-primary-700">Absensi Terbaru</h2>
      </div>
      <ul class="divide-y divide-secondary-100">
        <?php if (empty($recentAbsensi)): ?>
          <li class="py-2 text-secondary-400">Belum ada data absensi.</li>
        <?php else: foreach ($recentAbsensi as $abs): ?>
          <li class="py-2 flex items-center gap-3">
            <span class="inline-block w-2 h-2 rounded-full <?php
              $status = strtolower($abs['status_kehadiran']);
              if (strpos($status, 'hadir') !== false) echo 'bg-status-success-500';
              elseif (strpos($status, 'tidak hadir') !== false || strpos($status, 'alpha') !== false) echo 'bg-status-error-500';
              elseif (strpos($status, 'izin') !== false) echo 'bg-status-warning-500';
              elseif (strpos($status, 'sakit') !== false) echo 'bg-status-info-500';
              else echo 'bg-secondary-400';
            ?>"></span>
            <span class="font-medium text-primary-800"><?= htmlspecialchars($abs['nama_siswa']) ?></span>
            <span class="text-secondary-500 text-sm">(<?= htmlspecialchars($abs['nama_kelas']) ?>)</span>
            <span class="ml-auto text-xs text-secondary-400"><?= date('d/m/Y', strtotime($abs['tanggal'])) ?></span>
            <span class="ml-2 text-xs px-2 py-0.5 rounded <?php
              if (strpos($status, 'hadir') !== false) echo 'bg-status-success-100 text-status-success-700';
              elseif (strpos($status, 'tidak hadir') !== false || strpos($status, 'alpha') !== false) echo 'bg-status-error-100 text-status-error-700';
              elseif (strpos($status, 'izin') !== false) echo 'bg-status-warning-100 text-status-warning-700';
              elseif (strpos($status, 'sakit') !== false) echo 'bg-status-info-100 text-status-info-700';
              else echo 'bg-secondary-100 text-secondary-700';
            ?>">
              <?= htmlspecialchars($abs['status_kehadiran']) ?>
            </span>
          </li>
        <?php endforeach; endif; ?>
      </ul>
      <div class="mt-4 text-right">
        <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi" class="text-primary-600 hover:underline text-sm font-medium">Lihat semua absensi &rarr;</a>
      </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
      <div class="flex items-center gap-2 mb-4">
        <iconify-icon icon="cil:wallet" width="24" class="text-accent-500"></iconify-icon>
        <h2 class="text-lg font-semibold text-accent-700">Pembayaran SPP Terbaru</h2>
      </div>
      <ul class="divide-y divide-secondary-100">
        <?php if (empty($recentSPP)): ?>
          <li class="py-2 text-secondary-400">Belum ada pembayaran SPP.</li>
        <?php else: foreach ($recentSPP as $pay): ?>
          <li class="py-2 flex items-center gap-3">
            <span class="font-medium text-accent-800"><?= htmlspecialchars($pay['nama_siswa']) ?></span>
            <span class="text-secondary-500 text-sm">Bulan <?= htmlspecialchars($pay['bulan']) ?></span>
            <span class="ml-auto text-xs text-secondary-400"><?= date('d/m/Y', strtotime($pay['tanggal_bayar'])) ?></span>
            <span class="ml-2 text-xs px-2 py-0.5 rounded bg-accent-100 text-accent-700">Rp <?= number_format($pay['jumlah_bayar'], 0, ',', '.') ?></span>
          </li>
        <?php endforeach; endif; ?>
      </ul>
      <div class="mt-4 text-right">
        <a href="<?= htmlspecialchars($urlPrefix) ?>/spp-history" class="text-accent-600 hover:underline text-sm font-medium">Lihat semua pembayaran &rarr;</a>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <a href="<?= htmlspecialchars($urlPrefix) ?>/students" class="flex flex-col items-center justify-center bg-primary-50 hover:bg-primary-100 border border-primary-200 rounded-lg p-4 transition">
      <iconify-icon icon="cil:people" width="32" class="text-primary-600 mb-2"></iconify-icon>
      <span class="font-medium text-primary-700">Data Siswa</span>
    </a>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/teachers" class="flex flex-col items-center justify-center bg-accent-50 hover:bg-accent-100 border border-accent-200 rounded-lg p-4 transition">
      <iconify-icon icon="cil:school" width="32" class="text-accent-600 mb-2"></iconify-icon>
      <span class="font-medium text-accent-700">Data Guru</span>
    </a>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/classes" class="flex flex-col items-center justify-center bg-secondary-50 hover:bg-secondary-100 border border-secondary-200 rounded-lg p-4 transition">
      <iconify-icon icon="cil:layers" width="32" class="text-secondary-600 mb-2"></iconify-icon>
      <span class="font-medium text-secondary-700">Data Kelas</span>
    </a>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/spp-reports" class="flex flex-col items-center justify-center bg-accent-50 hover:bg-accent-100 border border-accent-200 rounded-lg p-4 transition">
      <iconify-icon icon="cil:wallet" width="32" class="text-accent-600 mb-2"></iconify-icon>
      <span class="font-medium text-accent-700">Laporan SPP</span>
    </a>
  </div>
</div>
<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';


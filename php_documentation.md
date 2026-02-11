# PHP Documentation

Generated on: 2026-01-25T14:29:04.849Z

### index.php

1. <?php
2. 
3. session_start();
4. 
5. // ===================================================================
6. // CONFIGURATION
7. // ===================================================================
8. // Set this to the part of the URL path that comes before your routes.
9. // For example, if your login URL is:
10. // http://localhost/sia-project/public/login
11. // ...then set this variable to '/sia-project/public'
12. require_once __DIR__ . '/../lib/config.php';
13. // ===================================================================
14. 
15. // --- No need to edit below this line ---
16. 
17. $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
18. 
19. // Determine the route by removing the URL prefix
20. $route = '/';
21. if (!empty($urlPrefix) && strpos($requestUri, $urlPrefix) === 0) {
22.     $route = substr($requestUri, strlen($urlPrefix));
23. }
24. 
25. // If the route is empty after stripping the prefix, it's the home page
26. if (empty($route)) {
27.     $route = '/';
28. }
29. 
30. 
31. // Route the request
32. switch ($route) {
33.     case '/':
34.         require __DIR__ . '/../pages/home.php';
35.         break;
36. 
37.     case '/login':
38.         require __DIR__ . '/../pages/login.php';
39.         break;
40. 
41.     case '/dashboard':
42.         require __DIR__ . '/../pages/dashboard.php';
43.         break;
44. 
45.     case '/logout':
46.         require __DIR__ . '/../pages/logout.php';
47.         break;
48. 
49.     case '/students':
50.         require __DIR__ . '/../pages/students.php';
51.         break;
52. 
53.     case '/students/create':
54.         require __DIR__ . '/../pages/students-create.php';
55.         break;
56. 
57.     case '/students/details':
58.         require __DIR__ . '/../pages/students-details.php';
59.         break;
60. 
61.     case '/students/edit':
62.         require __DIR__ . '/../pages/students-edit.php';
63.         break;
64. 
65.     case '/teachers':
66.         require __DIR__ . '/../pages/teachers.php';
67.         break;
68. 
69.     case '/teachers/create':
70.         require __DIR__ . '/../pages/teachers-create.php';
71.         break;
72. 
73.     case '/teachers/details':
74.         require __DIR__ . '/../pages/teachers-details.php';
75.         break;
76. 
77.     case '/teachers/edit':
78.         require __DIR__ . '/../pages/teachers-edit.php';
79.         break;
80. 
81.     case '/teachers/delete':
82.         require __DIR__ . '/../pages/teachers-delete.php';
83.         break;
84.     
85.     case '/teachers/export':
86.         require __DIR__ . '/../pages/teachers-export.php';
87.         break;
88. 
89.     case '/students/export':
90.         require __DIR__ . '/../pages/students-export.php';
91.         break;
92. 
93.     case '/students/delete':
94.         require __DIR__ . '/../pages/students-delete.php';
95.         break;
96. 
97.     case '/nilai':
98.         require __DIR__ . '/../pages/nilai.php';
99.         break;
100. 
101.     case '/nilai/create':
102.         require __DIR__ . '/../pages/nilai-create.php';
103.         break;
104. 
105.     case '/nilai/details':
106.         require __DIR__ . '/../pages/nilai-details.php';
107.         break;
108. 
109.     case '/nilai/edit':
110.         require __DIR__ . '/../pages/nilai-edit.php';
111.         break;
112. 
113.     case '/nilai/delete':
114.         require __DIR__ . '/../pages/nilai-delete.php';
115.         break;
116.     
117.     case '/nilai/export':
118.         require __DIR__ . '/../pages/nilai-export.php';
119.         break;
120. 
121.     case '/classes':
122.         require __DIR__ . '/../pages/classes.php';
123.         break;
124. 
125.     case '/classes/create':
126.         require __DIR__ . '/../pages/classes-create.php';
127.         break;
128. 
129.     case '/classes/details':
130.         require __DIR__ . '/../pages/classes-details.php';
131.         break;
132. 
133.     case '/classes/edit':
134.         require __DIR__ . '/../pages/classes-edit.php';
135.         break;
136. 
137.     case '/classes/delete':
138.         require __DIR__ . '/../pages/classes-delete.php';
139.         break;
140.     
141.     case '/classes/export':
142.         require __DIR__ . '/../pages/classes-export.php';
143.         break;
144.     
145.     case '/absensi':
146.         require __DIR__ . '/../pages/absensi.php';
147.         break;
148. 
149.     case '/absensi/create':
150.         require __DIR__ . '/../pages/absensi-create.php';
151.         break;
152. 
153.     case '/absensi/details':
154.         require __DIR__ . '/../pages/absensi-details.php';
155.         break;
156. 
157.     case '/absensi/edit':
158.         require __DIR__ . '/../pages/absensi-edit.php';
159.         break;
160. 
161.     case '/absensi/delete':
162.         require __DIR__ . '/../pages/absensi-delete.php';
163.         break;
164.     
165.     case '/absensi/export':
166.         require __DIR__ . '/../pages/absensi-export.php';
167.         break;
168. 
169.     case '/spp-students':
170.         require __DIR__ . '/../pages/spp-students.php';
171.         break;
172. 
173.     case '/spp-status':
174.         require __DIR__ . '/../pages/spp-status.php';
175.         break;
176. 
177.     case '/spp-pay':
178.         require __DIR__ . '/../pages/spp-pay.php';
179.         break;
180. 
181.     case '/spp-history':
182.         require __DIR__ . '/../pages/spp-history.php';
183.         break;
184. 
185.     case '/spp-history/export':
186.         require __DIR__ . '/../pages/spp-history-export.php';
187.         break;
188. 
189.     case '/spp-reports':
190.         require __DIR__ . '/../pages/spp-reports.php';
191.         break;
192. 
193.     case '/spp-export-summary':
194.         require __DIR__ . '/../pages/spp-export-summary.php';
195.         break;
196. 
197.     case '/spp-export-detail':
198.         require __DIR__ . '/../pages/spp-export-detail.php';
199.         break;
200. 
201.     case '/spp-print-receipt':
202.         require __DIR__ . '/../pages/spp-print-receipt.php';
203.         break;
204. 
205.     case '/spp-print-monthly':
206.         require __DIR__ . '/../pages/spp-print-monthly.php';
207.         break;
208. 
209.     case '/spp-print-yearly':
210.         require __DIR__ . '/../pages/spp-print-yearly.php';
211.         break;
212. 
213.     default:
214.         http_response_code(404);
215.         echo '<h1>404 Page Not Found</h1>';
216.         break;
217. }
218. 
### database.php

1. <?php
2. require_once __DIR__ . '/config.php';
3. 
4. /**
5.  * Establishes a database connection using PDO.
6.  *
7.  * Uses a static variable to ensure the connection is only made once per request.
8.  * @return PDO The PDO database connection object.
9.  */
10. function getDbConnection() {
11.     global $config;
12.     static $pdo;
13. 
14.     if (!$pdo) {
15.         try {       
16.             $dsn = "mysql:host={$config['host']};dbname={$config['dbname']}";
17. 
18.             $options = [
19.                 PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
20.                 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
21.                 PDO::ATTR_EMULATE_PREPARES   => false,
22.             ];
23. 
24.             $pdo = new PDO($dsn, $config['user'], $config['password'], $options);
25.         } catch (PDOException $e) {
26.             die("Database connection failed: " . $e->getMessage());
27.         }
28.     }
29. 
30.     return $pdo;
31. }
32. 
### config.php

1. <?php
2. // lib/config.php
3. // Shared configuration for the app
4. 
5. // Database configuration
6. $config = [
7.     'host' => '127.0.0.1',
8.     'dbname' => 'sia_php',
9.     'user' => 'root',
10.     'password' => '',
11. ];
12. 
13. // Set this to the part of the URL path that comes before your routes.
14. // For example, if your login URL is:
15. // http://localhost/sia-project/public/login
16. // ...then set this variable to '/sia-project/public'
17. $urlPrefix = '/sia/public';
18. 
19. 
### _layout.php

1. <?php
2. require_once __DIR__ . '/../lib/config.php';
3. 
4. function renderLayout($layoutName = null) {
5.     global $pageTitle, $pageContent;
6.     
7.     if ($layoutName === null) {
8.         $layoutName = 'base';
9.     }
10.     
11.     $layoutFile = __DIR__ . "/_layouts/{$layoutName}.php";
12.     
13.     if (!file_exists($layoutFile)) {
14.         throw new Exception("Layout '{$layoutName}' not found at {$layoutFile}");
15.     }
16.     
17.     require $layoutFile;
18. }
19. 
20. if (!function_exists('extendLayout')) {
21.     function extendLayout($parentLayout) {
22.         global $pageTitle, $pageContent;
23.         
24.         $childContent = $pageContent;
25.         $pageContent = $childContent;
26.         
27.         renderLayout($parentLayout);
28.     }
29. }
30. 
31. renderLayout($layout ?? 'base');
### base.php

1. <!DOCTYPE html>
2. <html lang="en">
3. 
4. <head>
5.     <meta charset="UTF-8">
6.     <meta name="viewport" content="width=device-width, initial-scale=1.0">
7.     <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Untitled' ?></title>
8. 
9.     <!-- Iconify -->
10.     <script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>
11. 
12.     <!-- Tailwind -->
13.     <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
14.     <style type="text/tailwindcss">
15.         @theme {
16.             --color-primary-50: #e4ebfa;
17.             --color-primary-100: #cddaf6;
18.             --color-primary-200: #98b7ee;
19.             --color-primary-300: #5795e5;
20.             --color-primary-400: #3f71b1;
21.             --color-primary-500: #2c5282;
22.             --color-primary-600: #224168;
23.             --color-primary-700: #193354;
24.             --color-primary-800: #10243d;
25.             --color-primary-900: #081628;
26.             --color-primary-950: #030b18;
27. 
28.             --color-secondary-50: #f5f6f8;
29.             --color-secondary-100: #ebeef2;
30.             --color-secondary-200: #d8dde5;
31.             --color-secondary-300: #c5cdd8;
32.             --color-secondary-400: #b1bccb;
33.             --color-secondary-500: #a0aec0;
34.             --color-secondary-600: #7a8898;
35.             --color-secondary-700: #5a6471;
36.             --color-secondary-800: #3c434c;
37.             --color-secondary-900: #20242a;
38.             --color-secondary-950: #13161a;
39. 
40.             --color-accent-50: #fff1eb;
41.             --color-accent-100: #ffe2d7;
42.             --color-accent-200: #ffc5aa;
43.             --color-accent-300: #ffa771;
44.             --color-accent-400: #f88908;
45.             --color-accent-500: #d97706;
46.             --color-accent-600: #ad5e04;
47.             --color-accent-700: #824502;
48.             --color-accent-800: #5a2e01;
49.             --color-accent-900: #341800;
50.             --color-accent-950: #230e00;
51. 
52.             /* Status colors */
53.             --color-status-success-50: #d4fde3;
54.             --color-status-success-100: #9efcc3;
55.             --color-status-success-200: #56ec9c;
56.             --color-status-success-300: #4cd38b;
57.             --color-status-success-400: #41b878;
58.             --color-status-success-500: #38a169;
59.             --color-status-success-600: #2a7e51;
60.             --color-status-success-700: #1e603d;
61.             --color-status-success-800: #124128;
62.             --color-status-success-900: #072515;
63.             --color-status-success-950: #03170b;
64. 
65.             --color-status-warning-50: #fef5ec;
66.             --color-status-warning-100: #feebd7;
67.             --color-status-warning-200: #fcd39f;
68.             --color-status-warning-300: #fcbf55;
69.             --color-status-warning-400: #ebae34;
70.             --color-status-warning-500: #d69e2e;
71.             --color-status-warning-600: #a97c22;
72.             --color-status-warning-700: #7d5b17;
73.             --color-status-warning-800: #543c0c;
74.             --color-status-warning-900: #302104;
75.             --color-status-warning-950: #1f1402;
76. 
77.             --color-status-error-50: #fbeeee;
78.             --color-status-error-100: #f8dcdc;
79.             --color-status-error-200: #f3bcbc;
80.             --color-status-error-300: #ee9696;
81.             --color-status-error-400: #eb7070;
82.             --color-status-error-500: #e53e3e;
83.             --color-status-error-600: #b83030;
84.             --color-status-error-700: #8a2222;
85.             --color-status-error-800: #631515;
86.             --color-status-error-900: #3b0909;
87.             --color-status-error-950: #280404;
88. 
89.             --color-status-info-50: #eef4fd;
90.             --color-status-info-100: #dde9fc;
91.             --color-status-info-200: #b9d4f8;
92.             --color-status-info-300: #96c2f6;
93.             --color-status-info-400: #66adf2;
94.             --color-status-info-500: #4299e1;
95.             --color-status-info-600: #3379b3;
96.             --color-status-info-700: #245b88;
97.             --color-status-info-800: #153c5b;
98.             --color-status-info-900: #092135;
99.             --color-status-info-950: #041423;
100.         }
101.     </style>
102. </head>
103. 
104. <body>
105. <?= $pageContent ?? '' ?>
106. </body>
107. 
108. </html>
### dashboard.php

1. <?php
2. if (!isset($_SESSION['user_id'])) {
3.     header('Location: ' . $urlPrefix . '/login');
4.     exit();
5. }
6. global $urlPrefix, $currentPage;
7. $dashboardContent = $pageContent;
8. 
9. ob_start();
10. ?>
11.     <div class="flex">
12.         <aside class="flex-shrink-0 p-4 bg-secondary-100 border-r border-secondary-300 min-h-screen w-[220px]">
13.             <a href="<?= htmlspecialchars($urlPrefix) ?>/dashboard"
14.                class="flex items-center mb-6 text-primary-700 hover:text-primary-900">
15.                 <iconify-icon icon="mdi:school" width="28" height="28" class="mr-2 text-primary-600"></iconify-icon>
16.                 <span class="text-2xl font-bold tracking-tight text-primary-700">SIA</span>
17.             </a>
18.             <div class="border-t border-secondary-200 my-4"></div>
19.             <ul class="flex flex-col gap-1 mb-6">
20.                 <li>
21.                     <a href="<?= htmlspecialchars($urlPrefix) ?>/dashboard"
22.                        class="block px-4 py-2 rounded-md transition font-medium <?= $currentPage === 'dashboard' ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-primary-50' ?>">Dashboard</a>
23.                 </li>
24.                 <li>
25.                     <a href="<?= htmlspecialchars($urlPrefix) ?>/students"
26.                        class="block px-4 py-2 rounded-md transition font-medium <?= $currentPage === 'students' ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-primary-50' ?>">Siswa</a>
27.                 </li>
28.                 <li>
29.                     <a href="<?= htmlspecialchars($urlPrefix) ?>/teachers"
30.                        class="block px-4 py-2 rounded-md transition font-medium <?= $currentPage === 'teachers' ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-primary-50' ?>">Guru</a>
31.                 </li>
32.                 <li>
33.                     <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi"
34.                        class="block px-4 py-2 rounded-md transition font-medium <?= $currentPage === 'absensi' ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-primary-50' ?>">Absensi</a>
35.                 </li>
36.                 <li>
37.                     <a href="<?= htmlspecialchars($urlPrefix) ?>/classes"
38.                        class="block px-4 py-2 rounded-md transition font-medium <?= $currentPage === 'classes' ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-primary-50' ?>">Kelas</a>
39.                 </li>
40.                 <li>
41.                     <a href="<?= htmlspecialchars($urlPrefix) ?>/nilai"
42.                        class="block px-4 py-2 rounded-md transition font-medium <?= $currentPage === 'nilai' ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-primary-50' ?>">Nilai</a>
43.                 </li>
44.                 <li>
45.                     <a href="<?= htmlspecialchars($urlPrefix) ?>/spp-students"
46.                        class="block px-4 py-2 rounded-md transition font-medium <?= $currentPage === 'spp-students' ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-primary-50' ?>">Daftar
47.                         SPP Siswa</a>
48.                 </li>
49.                 <li>
50.                     <a href="<?= htmlspecialchars($urlPrefix) ?>/spp-history"
51.                        class="block px-4 py-2 rounded-md transition font-medium <?= $currentPage === 'spp-history' ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-primary-50' ?>">Riwayat
52.                         Cicilan SPP</a>
53.                 </li>
54.                 <li>
55.                     <a href="<?= htmlspecialchars($urlPrefix) ?>/spp-reports"
56.                        class="block px-4 py-2 rounded-md transition font-medium <?= $currentPage === 'spp-reports' ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-primary-50' ?>">Laporan/Export
57.                         SPP</a>
58.                 </li>
59.                 <li>
60.                     <a href="<?= htmlspecialchars($urlPrefix) ?>/logout"
61.                        class="block px-4 py-2 rounded-md transition font-medium text-error-600 hover:bg-error-50">Logout</a>
62.                 </li>
63.             </ul>
64.         </aside>
65.         <main class="flex-1 p-6 min-w-0 overflow-x-auto">
66.             <?= $dashboardContent ?>
67.         </main>
68.     </div>
69. <?php
70. $pageContent = ob_get_clean();
71. 
72. extendLayout('base');
### login.php

1. <?php
2. require_once __DIR__ . '/../lib/config.php';
3. $error = '';
4. if ($_SERVER['REQUEST_METHOD'] === 'POST') {
5.     require_once __DIR__ . '/../lib/database.php';
6.     $username = $_POST['username'] ?? '';
7.     $password = $_POST['password'] ?? '';
8.     if (empty($username) || empty($password)) {
9.         $error = 'Username and password are required.';
10.     } else {
11.         $pdo = getDbConnection();
12.         $stmt = $pdo->prepare("SELECT id, password FROM user WHERE username = ?");
13.         $stmt->execute([$username]);
14.         $user = $stmt->fetch();
15. 
16.         // Warning: This is a direct comparison for demonstration purposes ONLY.
17.         // In a real application, you must use password_verify() with hashed passwords.
18.         if ($user && $password === $user['password']) {
19.             session_start(); // Make sure session is started before setting session variables
20.             $_SESSION['user_id'] = $user['id'];
21.             header('Location: ' . $urlPrefix . '/dashboard');
22.             exit();
23.         } else {
24.             $error = 'Invalid username or password.';
25.         }
26.     }
27. }
28. $pageTitle = "Login";
29. ob_start();
30. ?>
31. 
32. <main class="flex items-center justify-center min-h-screen bg-secondary-100">
33.     <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
34.         <div class="text-center mb-6">
35.             <iconify-icon icon="mdi:account-circle" width="48" height="48" class="text-primary-600 mb-2"></iconify-icon>
36.             <h2 class="text-2xl font-bold text-primary-700 mb-0">Login</h2>
37.         </div>
38.         <?php if ($error): ?>
39.             <div class="mb-4 rounded-lg bg-error-100 text-error-700 px-4 py-3 text-sm text-center border border-error-200">
40.                 <?= htmlspecialchars($error) ?>
41.             </div>
42.         <?php endif; ?>
43.         <form method="POST" action="<?= htmlspecialchars($urlPrefix) ?>/login" autocomplete="on" novalidate>
44.             <div class="mb-4">
45.                 <label for="username" class="block text-sm font-medium text-primary-700 mb-1">Username</label>
46.                 <input type="text" id="username" name="username" class="block w-full rounded-md border border-secondary-300 focus:border-primary-600 focus:ring focus:ring-primary-100 px-3 py-2 text-primary-900 bg-secondary-50" required autofocus autocomplete="username" value="<?= isset($username) ? htmlspecialchars($username) : '' ?>">
47.             </div>
48.             <div class="mb-6">
49.                 <label for="password" class="block text-sm font-medium text-primary-700 mb-1">Password</label>
50.                 <input type="password" id="password" name="password" class="block w-full rounded-md border border-secondary-300 focus:border-primary-600 focus:ring focus:ring-primary-100 px-3 py-2 text-primary-900 bg-secondary-50" required autocomplete="current-password">
51.             </div>
52.             <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2 rounded-md transition-colors">Login</button>
53.             <div class="mt-4 text-center">
54.                 <a href="#" class="text-sm text-primary-600 hover:underline">Forgot password?</a>
55.             </div>
56.         </form>
57.     </div>
58. </main>
59. <?php
60. $pageContent = ob_get_clean();
61. $layout = 'base';
62. require __DIR__ . '/_layout.php';
### dashboard.php

1. <?php
2. require_once __DIR__ . '/../lib/database.php';
3. 
4. $pageTitle = "Dashboard";
5. $currentPage = 'dashboard';
6. $pdo = getDbConnection();
7. 
8. // Quick stats
9. $totalStudents = $pdo->query("SELECT COUNT(*) FROM siswa")->fetchColumn();
10. $totalTeachers = $pdo->query("SELECT COUNT(*) FROM guru")->fetchColumn();
11. $totalClasses = $pdo->query("SELECT COUNT(*) FROM kelas")->fetchColumn();
12. 
13. // Recent absensi (last 5)
14. $stmt = $pdo->prepare("SELECT kh.tanggal, s.nama AS nama_siswa, k.nama AS nama_kelas, ks.nama AS status_kehadiran FROM kehadiran kh JOIN siswa s ON kh.id_siswa = s.id JOIN kelas k ON kh.id_kelas = k.id JOIN kehadiran_status ks ON kh.id_status = ks.id ORDER BY kh.tanggal DESC LIMIT 5");
15. $stmt->execute();
16. $recentAbsensi = $stmt->fetchAll();
17. 
18. // Recent SPP payments (last 5)
19. $stmt = $pdo->prepare("SELECT ps.tanggal_bayar, s.nama AS nama_siswa, ps.bulan, ps.jumlah_bayar FROM pembayaran_spp ps JOIN siswa s ON ps.id_siswa = s.id ORDER BY ps.tanggal_bayar DESC, ps.id DESC LIMIT 5");
20. $stmt->execute();
21. $recentSPP = $stmt->fetchAll();
22. 
23. ob_start();
24. ?>
25. <div class="max-w-7xl mx-auto px-4 py-8">
26.   <h1 class="text-3xl font-bold text-primary-700 mb-6 flex items-center gap-2">
27.     <iconify-icon icon="cil:locomotive" class="text-primary-500" width="36"></iconify-icon>
28.     Dashboard
29.   </h1>
30.   <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
31.     <div class="bg-primary-600 text-white rounded-lg shadow p-6 flex items-center gap-4">
32.       <iconify-icon icon="cil:people" width="36" class="opacity-80"></iconify-icon>
33.       <div>
34.         <div class="text-2xl font-bold"><?= number_format($totalStudents) ?></div>
35.         <div class="text-primary-100">Siswa</div>
36.       </div>
37.     </div>
38.     <div class="bg-accent-500 text-white rounded-lg shadow p-6 flex items-center gap-4">
39.       <iconify-icon icon="cil:school" width="36" class="opacity-80"></iconify-icon>
40.       <div>
41.         <div class="text-2xl font-bold"><?= number_format($totalTeachers) ?></div>
42.         <div class="text-accent-100">Guru</div>
43.       </div>
44.     </div>
45.     <div class="bg-secondary-600 text-white rounded-lg shadow p-6 flex items-center gap-4">
46.       <iconify-icon icon="cil:layers" width="36" class="opacity-80"></iconify-icon>
47.       <div>
48.         <div class="text-2xl font-bold"><?= number_format($totalClasses) ?></div>
49.         <div class="text-secondary-100">Kelas</div>
50.       </div>
51.     </div>
52.   </div>
53. 
54.   <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
55.     <div class="bg-white rounded-lg shadow p-6">
56.       <div class="flex items-center gap-2 mb-4">
57.         <iconify-icon icon="cil:calendar" width="24" class="text-primary-500"></iconify-icon>
58.         <h2 class="text-lg font-semibold text-primary-700">Absensi Terbaru</h2>
59.       </div>
60.       <ul class="divide-y divide-secondary-100">
61.         <?php if (empty($recentAbsensi)): ?>
62.           <li class="py-2 text-secondary-400">Belum ada data absensi.</li>
63.         <?php else: foreach ($recentAbsensi as $abs): ?>
64.           <li class="py-2 flex items-center gap-3">
65.             <span class="inline-block w-2 h-2 rounded-full <?php
66.               $status = strtolower($abs['status_kehadiran']);
67.               if (strpos($status, 'hadir') !== false) echo 'bg-status-success-500';
68.               elseif (strpos($status, 'tidak hadir') !== false || strpos($status, 'alpha') !== false) echo 'bg-status-error-500';
69.               elseif (strpos($status, 'izin') !== false) echo 'bg-status-warning-500';
70.               elseif (strpos($status, 'sakit') !== false) echo 'bg-status-info-500';
71.               else echo 'bg-secondary-400';
72.             ?>"></span>
73.             <span class="font-medium text-primary-800"><?= htmlspecialchars($abs['nama_siswa']) ?></span>
74.             <span class="text-secondary-500 text-sm">(<?= htmlspecialchars($abs['nama_kelas']) ?>)</span>
75.             <span class="ml-auto text-xs text-secondary-400"><?= date('d/m/Y', strtotime($abs['tanggal'])) ?></span>
76.             <span class="ml-2 text-xs px-2 py-0.5 rounded <?php
77.               if (strpos($status, 'hadir') !== false) echo 'bg-status-success-100 text-status-success-700';
78.               elseif (strpos($status, 'tidak hadir') !== false || strpos($status, 'alpha') !== false) echo 'bg-status-error-100 text-status-error-700';
79.               elseif (strpos($status, 'izin') !== false) echo 'bg-status-warning-100 text-status-warning-700';
80.               elseif (strpos($status, 'sakit') !== false) echo 'bg-status-info-100 text-status-info-700';
81.               else echo 'bg-secondary-100 text-secondary-700';
82.             ?>">
83.               <?= htmlspecialchars($abs['status_kehadiran']) ?>
84.             </span>
85.           </li>
86.         <?php endforeach; endif; ?>
87.       </ul>
88.       <div class="mt-4 text-right">
89.         <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi" class="text-primary-600 hover:underline text-sm font-medium">Lihat semua absensi &rarr;</a>
90.       </div>
91.     </div>
92.     <div class="bg-white rounded-lg shadow p-6">
93.       <div class="flex items-center gap-2 mb-4">
94.         <iconify-icon icon="cil:wallet" width="24" class="text-accent-500"></iconify-icon>
95.         <h2 class="text-lg font-semibold text-accent-700">Pembayaran SPP Terbaru</h2>
96.       </div>
97.       <ul class="divide-y divide-secondary-100">
98.         <?php if (empty($recentSPP)): ?>
99.           <li class="py-2 text-secondary-400">Belum ada pembayaran SPP.</li>
100.         <?php else: foreach ($recentSPP as $pay): ?>
101.           <li class="py-2 flex items-center gap-3">
102.             <span class="font-medium text-accent-800"><?= htmlspecialchars($pay['nama_siswa']) ?></span>
103.             <span class="text-secondary-500 text-sm">Bulan <?= htmlspecialchars($pay['bulan']) ?></span>
104.             <span class="ml-auto text-xs text-secondary-400"><?= date('d/m/Y', strtotime($pay['tanggal_bayar'])) ?></span>
105.             <span class="ml-2 text-xs px-2 py-0.5 rounded bg-accent-100 text-accent-700">Rp <?= number_format($pay['jumlah_bayar'], 0, ',', '.') ?></span>
106.           </li>
107.         <?php endforeach; endif; ?>
108.       </ul>
109.       <div class="mt-4 text-right">
110.         <a href="<?= htmlspecialchars($urlPrefix) ?>/spp-history" class="text-accent-600 hover:underline text-sm font-medium">Lihat semua pembayaran &rarr;</a>
111.       </div>
112.     </div>
113.   </div>
114. 
115.   <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
116.     <a href="<?= htmlspecialchars($urlPrefix) ?>/students" class="flex flex-col items-center justify-center bg-primary-50 hover:bg-primary-100 border border-primary-200 rounded-lg p-4 transition">
117.       <iconify-icon icon="cil:people" width="32" class="text-primary-600 mb-2"></iconify-icon>
118.       <span class="font-medium text-primary-700">Data Siswa</span>
119.     </a>
120.     <a href="<?= htmlspecialchars($urlPrefix) ?>/teachers" class="flex flex-col items-center justify-center bg-accent-50 hover:bg-accent-100 border border-accent-200 rounded-lg p-4 transition">
121.       <iconify-icon icon="cil:school" width="32" class="text-accent-600 mb-2"></iconify-icon>
122.       <span class="font-medium text-accent-700">Data Guru</span>
123.     </a>
124.     <a href="<?= htmlspecialchars($urlPrefix) ?>/classes" class="flex flex-col items-center justify-center bg-secondary-50 hover:bg-secondary-100 border border-secondary-200 rounded-lg p-4 transition">
125.       <iconify-icon icon="cil:layers" width="32" class="text-secondary-600 mb-2"></iconify-icon>
126.       <span class="font-medium text-secondary-700">Data Kelas</span>
127.     </a>
128.     <a href="<?= htmlspecialchars($urlPrefix) ?>/spp-reports" class="flex flex-col items-center justify-center bg-accent-50 hover:bg-accent-100 border border-accent-200 rounded-lg p-4 transition">
129.       <iconify-icon icon="cil:wallet" width="32" class="text-accent-600 mb-2"></iconify-icon>
130.       <span class="font-medium text-accent-700">Laporan SPP</span>
131.     </a>
132.   </div>
133. </div>
134. <?php
135. $pageContent = ob_get_clean();
136. $layout = 'dashboard';
137. require __DIR__ . '/_layout.php';
138. 
139. 
### spp-pay.php

1. <?php
2. $pageTitle = "Bayar SPP";
3. $currentPage = 'spp-students';
4. 
5. require_once __DIR__ . '/../lib/database.php';
6. 
7. $pdo = getDbConnection();
8. 
9. // Get parameters
10. $studentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
11. $yearId = isset($_GET['year_id']) ? (int)$_GET['year_id'] : 0;
12. $selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';
13. 
14. if (!$studentId) {
15.     header("Location: spp-students");
16.     exit();
17. }
18. 
19. // Fixed SPP amount per month
20. $sppAmount = 650000;
21. 
22. // Define months in order
23. $months = [
24.     'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
25.     'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
26. ];
27. 
28. $success = '';
29. $error = '';
30. $allocation = [];
31. 
32. // Get student information
33. $stmt = $pdo->prepare("SELECT * FROM siswa WHERE id = ?");
34. $stmt->execute([$studentId]);
35. $student = $stmt->fetch();
36. 
37. if (!$student) {
38.     header("Location: spp-students");
39.     exit();
40. }
41. 
42. // Get academic year information
43. if ($yearId) {
44.     $stmt = $pdo->prepare("SELECT * FROM tahun_ajaran WHERE id = ?");
45.     $stmt->execute([$yearId]);
46.     $year = $stmt->fetch();
47. } else {
48.     // Get current academic year
49.     $stmt = $pdo->prepare("SELECT * FROM tahun_ajaran ORDER BY tahun_mulai DESC LIMIT 1");
50.     $stmt->execute();
51.     $year = $stmt->fetch();
52.     $yearId = $year['id'];
53. }
54. 
55. if (!$year) {
56.     header("Location: spp-students");
57.     exit();
58. }
59. 
60. // Process payment if form is submitted
61. if ($_SERVER['REQUEST_METHOD'] === 'POST') {
62.     try {
63.         $startMonth = $_POST['start_month'] ?? '';
64.         $amount = (float)($_POST['amount'] ?? 0);
65.         
66.         if (!$startMonth || $amount <= 0) {
67.             throw new Exception("Mohon lengkapi semua field dengan benar");
68.         }
69.         
70.         if (!in_array($startMonth, $months)) {
71.             throw new Exception("Bulan tidak valid");
72.         }
73.         
74.         // Get current payments for this student and year
75.         $stmt = $pdo->prepare("
76.             SELECT bulan, SUM(jumlah_bayar) as total_paid 
77.             FROM pembayaran_spp 
78.             WHERE id_siswa = ? AND id_tahun_ajaran = ? 
79.             GROUP BY bulan
80.         ");
81.         $stmt->execute([$studentId, $yearId]);
82.         $currentPayments = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
83.         
84.         // Calculate allocation
85.         $remainingAmount = $amount;
86.         $allocation = [];
87.         $startIndex = array_search($startMonth, $months);
88.         
89.         for ($i = $startIndex; $i < count($months) && $remainingAmount > 0; $i++) {
90.             $month = $months[$i];
91.             $alreadyPaid = $currentPayments[$month] ?? 0;
92.             $outstanding = max(0, $sppAmount - $alreadyPaid);
93.             
94.             if ($outstanding > 0) {
95.                 $allocatedAmount = min($remainingAmount, $outstanding);
96.                 $allocation[] = [
97.                     'month' => $month,
98.                     'amount' => $allocatedAmount,
99.                     'already_paid' => $alreadyPaid,
100.                     'new_total' => $alreadyPaid + $allocatedAmount,
101.                     'status' => ($alreadyPaid + $allocatedAmount >= $sppAmount) ? 'Lunas' : 'Belum Lunas'
102.                 ];
103.                 $remainingAmount -= $allocatedAmount;
104.             }
105.         }
106.         
107.         if ($remainingAmount > 0) {
108.             throw new Exception("Pembayaran melebihi total SPP yang belum dibayar. Kelebihan: Rp " . number_format($remainingAmount, 0, ',', '.'));
109.         }
110.         
111.         if (empty($allocation)) {
112.             throw new Exception("Tidak ada pembayaran yang dapat dialokasikan");
113.         }
114.         
115.         // If this is confirmation step, save to database
116.         if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
117.             $pdo->beginTransaction();
118.             
119.             try {
120.                 foreach ($allocation as $alloc) {
121.                     $stmt = $pdo->prepare("
122.                         INSERT INTO pembayaran_spp (id_siswa, id_tahun_ajaran, bulan, tanggal_bayar, jumlah_bayar) 
123.                         VALUES (?, ?, ?, CURDATE(), ?)
124.                     ");
125.                     $stmt->execute([$studentId, $yearId, $alloc['month'], $alloc['amount']]);
126.                 }
127.                 
128.                 $pdo->commit();
129.                 $success = "Pembayaran berhasil disimpan!";
130.                 
131.                 // Clear allocation for display
132.                 $showAllocation = $allocation;
133.                 $allocation = [];
134.                 
135.             } catch (Exception $e) {
136.                 $pdo->rollback();
137.                 throw new Exception("Gagal menyimpan pembayaran: " . $e->getMessage());
138.             }
139.         }
140.     } catch (Exception $e) {
141.         $error = $e->getMessage();
142.     }
143. }
144. 
145. ob_start();
146. ?>
147. 
148. <div class="max-w-7xl mx-auto p-6">
149.     <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start mb-6 gap-4">
150.         <div>
151.             <h1 class="text-3xl font-bold text-primary-800 mb-2">Bayar SPP</h1>
152.             <h2 class="text-xl text-secondary-600 mb-1"><?= htmlspecialchars($student['nama']) ?> (NIS: <?= htmlspecialchars($student['nis'] ?? '-') ?>)</h2>
153.             <p class="text-sm text-secondary-500">Tahun Ajaran: <?= htmlspecialchars($year['nama']) ?></p>
154.         </div>
155.         <a href="spp-status?id=<?= $studentId ?>&year=<?= $yearId ?>" 
156.            class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
157.             <iconify-icon icon="solar:arrow-left-linear" width="20" height="20"></iconify-icon>
158.             Kembali
159.         </a>
160.     </div>
161. 
162.     <?php if ($error): ?>
163.         <div class="bg-status-error-100 border border-status-error-200 text-status-error-700 px-4 py-3 rounded-lg mb-6">
164.             <div class="flex items-center">
165.                 <iconify-icon icon="solar:danger-triangle-bold" class="mr-2 text-lg"></iconify-icon>
166.                 <?= htmlspecialchars($error) ?>
167.             </div>
168.         </div>
169.     <?php endif; ?>
170. 
171.     <?php if ($success): ?>
172.         <div class="bg-status-success-100 border border-status-success-200 text-status-success-700 px-4 py-3 rounded-lg mb-6">
173.             <div class="flex items-center mb-3">
174.                 <iconify-icon icon="solar:check-circle-bold" class="mr-2 text-lg"></iconify-icon>
175.                 <?= htmlspecialchars($success) ?>
176.             </div>
177.             
178.             <?php if (isset($showAllocation)): ?>
179.                 <hr class="border-status-success-200 my-4">
180.                 <h3 class="text-lg font-semibold mb-3">Detail Alokasi Pembayaran:</h3>
181.                 <div class="overflow-x-auto">
182.                     <table class="min-w-full bg-white border border-secondary-200 rounded-lg">
183.                         <thead class="bg-secondary-50">
184.                             <tr>
185.                                 <th class="px-4 py-3 text-left text-xs font-medium text-secondary-700 uppercase tracking-wider border-b border-secondary-200">Bulan</th>
186.                                 <th class="px-4 py-3 text-left text-xs font-medium text-secondary-700 uppercase tracking-wider border-b border-secondary-200">Jumlah Dibayar</th>
187.                                 <th class="px-4 py-3 text-left text-xs font-medium text-secondary-700 uppercase tracking-wider border-b border-secondary-200">Total Setelah Bayar</th>
188.                                 <th class="px-4 py-3 text-left text-xs font-medium text-secondary-700 uppercase tracking-wider border-b border-secondary-200">Status</th>
189.                             </tr>
190.                         </thead>
191.                         <tbody class="divide-y divide-secondary-200">
192.                             <?php foreach ($showAllocation as $alloc): ?>
193.                                 <tr class="even:bg-secondary-50 hover:bg-secondary-100">
194.                                     <td class="px-4 py-3 text-secondary-900 align-middle"><?= htmlspecialchars($alloc['month']) ?></td>
195.                                     <td class="px-4 py-3 text-secondary-900 align-middle">Rp <?= number_format($alloc['amount'], 0, ',', '.') ?></td>
196.                                     <td class="px-4 py-3 text-secondary-900 align-middle">Rp <?= number_format($alloc['new_total'], 0, ',', '.') ?></td>
197.                                     <td class="px-4 py-3 align-middle">
198.                                         <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full <?= $alloc['status'] === 'Lunas' ? 'bg-status-success-100 text-status-success-700' : 'bg-status-warning-100 text-status-warning-700' ?>">
199.                                             <?= $alloc['status'] ?>
200.                                         </span>
201.                                     </td>
202.                                 </tr>
203.                             <?php endforeach; ?>
204.                         </tbody>
205.                     </table>
206.                 </div>
207.             <?php endif; ?>
208.         </div>
209.     <?php endif; ?>
210. 
211.     <?php if (!$success): ?>
212.         <!-- Payment Form -->
213.         <div class="bg-white rounded-lg shadow-md border border-secondary-200">
214.             <div class="px-6 py-4 border-b border-secondary-200">
215.                 <h2 class="text-xl font-semibold text-secondary-800">Form Pembayaran SPP</h2>
216.                 <p class="text-sm text-secondary-600 mt-1">SPP per bulan: Rp <?= number_format($sppAmount, 0, ',', '.') ?></p>
217.             </div>
218.             <div class="p-6">
219.                 <?php if (empty($allocation)): ?>
220.                     <!-- Initial Form -->
221.                     <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
222.                         <div>
223.                             <label for="start_month" class="block text-sm font-medium text-secondary-700 mb-2">Mulai dari Bulan</label>
224.                             <select name="start_month" id="start_month" 
225.                                     class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
226.                                     required>
227.                                 <option value="">Pilih Bulan</option>
228.                                 <?php foreach ($months as $month): ?>
229.                                     <option value="<?= htmlspecialchars($month) ?>" 
230.                                             <?= $month === $selectedMonth ? 'selected' : '' ?>>
231.                                         <?= htmlspecialchars($month) ?>
232.                                     </option>
233.                                 <?php endforeach; ?>
234.                             </select>
235.                             <p class="text-xs text-secondary-500 mt-1">Pembayaran akan dialokasikan mulai dari bulan ini</p>
236.                         </div>
237.                         
238.                         <div>
239.                             <label for="amount" class="block text-sm font-medium text-secondary-700 mb-2">Jumlah Pembayaran</label>
240.                             <div class="relative">
241.                                 <span class="absolute left-3 top-2 text-secondary-500">Rp</span>
242.                                 <input type="number" name="amount" id="amount" 
243.                                        class="w-full pl-8 pr-3 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
244.                                        required>
245.                             </div>
246.                             <p class="text-xs text-secondary-500 mt-1">
247.                                 Dapat lebih dari Rp <?= number_format($sppAmount, 0, ',', '.') ?> untuk pembayaran beberapa bulan sekaligus
248.                             </p>
249.                         </div>
250.                         
251.                         <div class="col-span-full">
252.                             <button type="submit" 
253.                                     class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
254.                                 <iconify-icon icon="solar:calculator-minimalistic-linear" width="20" height="20"></iconify-icon>
255.                                 Hitung Alokasi
256.                             </button>
257.                         </div>
258.                     </form>
259.                 <?php else: ?>
260.                     <!-- Allocation Preview -->
261.                     <div class="bg-accent-100 border border-accent-200 text-accent-700 px-4 py-3 rounded-lg mb-6">
262.                         <h3 class="flex items-center text-lg font-semibold mb-2">
263.                             <iconify-icon icon="solar:info-circle-bold" class="mr-2"></iconify-icon>
264.                             Preview Alokasi Pembayaran
265.                         </h3>
266.                         <p>Berikut adalah alokasi pembayaran yang akan dilakukan:</p>
267.                     </div>
268.                     
269.                     <div class="overflow-x-auto mb-6">
270.                         <table class="min-w-full bg-white border border-secondary-200 rounded-lg">
271.                             <thead class="bg-secondary-50">
272.                                 <tr>
273.                                     <th class="px-4 py-3 text-left text-xs font-medium text-secondary-700 uppercase tracking-wider border-b border-secondary-200">Bulan</th>
274.                                     <th class="px-4 py-3 text-left text-xs font-medium text-secondary-700 uppercase tracking-wider border-b border-secondary-200">Sudah Dibayar</th>
275.                                     <th class="px-4 py-3 text-left text-xs font-medium text-secondary-700 uppercase tracking-wider border-b border-secondary-200">Jumlah Cicilan</th>
276.                                     <th class="px-4 py-3 text-left text-xs font-medium text-secondary-700 uppercase tracking-wider border-b border-secondary-200">Total Setelah Bayar</th>
277.                                     <th class="px-4 py-3 text-left text-xs font-medium text-secondary-700 uppercase tracking-wider border-b border-secondary-200">Status</th>
278.                                 </tr>
279.                             </thead>
280.                             <tbody class="divide-y divide-secondary-200">
281.                                 <?php foreach ($allocation as $alloc): ?>
282.                                     <tr class="even:bg-secondary-50 hover:bg-secondary-100">
283.                                         <td class="px-4 py-3 font-medium text-secondary-900 align-middle"><?= htmlspecialchars($alloc['month']) ?></td>
284.                                         <td class="px-4 py-3 text-secondary-700 align-middle">Rp <?= number_format($alloc['already_paid'], 0, ',', '.') ?></td>
285.                                         <td class="px-4 py-3 text-status-success-700 font-medium align-middle">
286.                                             + Rp <?= number_format($alloc['amount'], 0, ',', '.') ?>
287.                                         </td>
288.                                         <td class="px-4 py-3 font-medium text-secondary-900 align-middle">Rp <?= number_format($alloc['new_total'], 0, ',', '.') ?></td>
289.                                         <td class="px-4 py-3 align-middle">
290.                                             <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full <?= $alloc['status'] === 'Lunas' ? 'bg-status-success-100 text-status-success-700' : 'bg-status-warning-100 text-status-warning-700' ?>">
291.                                                 <?= $alloc['status'] ?>
292.                                             </span>
293.                                         </td>
294.                                     </tr>
295.                                 <?php endforeach; ?>
296.                             </tbody>
297.                             <tfoot class="bg-secondary-50">
298.                                 <tr>
299.                                     <th colspan="2" class="px-4 py-3 text-sm font-medium text-secondary-700 border-t border-secondary-200">Total Pembayaran:</th>
300.                                     <th colspan="3" class="px-4 py-3 text-sm font-medium text-secondary-900 border-t border-secondary-200">
301.                                         Rp <?= number_format(array_sum(array_column($allocation, 'amount')), 0, ',', '.') ?>
302.                                     </th>
303.                                 </tr>
304.                             </tfoot>
305.                         </table>
306.                     </div>
307.                     
308.                     <form method="POST" class="flex flex-col sm:flex-row gap-3">
309.                         <input type="hidden" name="start_month" value="<?= htmlspecialchars($_POST['start_month']) ?>">
310.                         <input type="hidden" name="amount" value="<?= htmlspecialchars($_POST['amount']) ?>">
311.                         <input type="hidden" name="confirm" value="yes">
312.                         
313.                         <button type="submit" 
314.                                 class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-status-success-600 text-white hover:bg-status-success-700 transition">
315.                             <iconify-icon icon="solar:check-circle-bold" width="20" height="20"></iconify-icon>
316.                             Konfirmasi Pembayaran
317.                         </button>
318.                         <a href="spp-pay?student_id=<?= $studentId ?>&year_id=<?= $yearId ?><?= $selectedMonth ? '&month=' . urlencode($selectedMonth) : '' ?>" 
319.                            class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
320.                             <iconify-icon icon="solar:restart-linear" width="20" height="20"></iconify-icon>
321.                             Ubah Jumlah
322.                         </a>
323.                     </form>
324.                 <?php endif; ?>
325.             </div>
326.         </div>
327.     <?php endif; ?>
328. </div>
329. 
330. <script>
331. // Format number input
332. document.getElementById('amount')?.addEventListener('input', function(e) {
333.     let value = e.target.value.replace(/[^\d]/g, '');
334.     e.target.value = value;
335. });
336. </script>
337. 
338. <?php
339. $pageContent = ob_get_clean();
340. $layout = 'dashboard';
341. require __DIR__ . '/_layout.php';
342. ?>
### students.php

1. <?php
2. $pageTitle = "Daftar Siswa";
3. $currentPage = 'students';
4. 
5. require_once __DIR__ . '/../lib/database.php';
6. 
7. $pdo = getDbConnection();
8. 
9. $searchQuery = $_GET['search'] ?? '';
10. $sql = 'SELECT * FROM siswa';
11. $params = [];
12. 
13. if ($searchQuery) {
14.     $sql .= ' WHERE nama LIKE :search_nama OR nis LIKE :search_nis OR nisn LIKE :search_nisn OR alamat LIKE :search_alamat';
15.     $params[':search_nama'] = '%' . $searchQuery . '%';
16.     $params[':search_nis'] = '%' . $searchQuery . '%';
17.     $params[':search_nisn'] = '%' . $searchQuery . '%';
18.     $params[':search_alamat'] = '%' . $searchQuery . '%';
19. }
20. 
21. $sql .= ' ORDER BY nis';
22. 
23. $stmt = $pdo->prepare($sql);
24. $stmt->execute($params);
25. $students = $stmt->fetchAll();
26. 
27. ob_start();
28. 
29. ?>
30.     <h1 class="mb-6 text-2xl font-bold text-primary-700">Data Siswa</h1>
31. 
32.     <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
33.         <form action="" method="GET" class="flex flex-1 gap-2">
34.             <input type="text" name="search"
35.                    class="flex-1 rounded-lg border border-secondary-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white text-sm"
36.                    placeholder="Cari data siswa..." value="<?= htmlspecialchars($searchQuery) ?>">
37.             <button class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition"
38.                     type="submit">
39.                 <iconify-icon icon="cil:search" width="20" height="20"></iconify-icon>
40.                 Cari
41.             </button>
42.             <?php if ($searchQuery): ?>
43.                 <a href="students"
44.                    class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">Reset</a>
45.             <?php endif; ?>
46.         </form>
47.         <div class="flex gap-2">
48.             <a href="<?= htmlspecialchars($urlPrefix) ?>/students/create"
49.                class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
50.                 <iconify-icon icon="cil:plus" width="20" height="20"></iconify-icon>
51.                 Tambah Data
52.             </a>
53.             <a href="students/export<?= $searchQuery ? ('?search=' . urlencode($searchQuery)) : '' ?>"
54.                class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-accent-500 text-white hover:bg-accent-600 transition">
55.                 <iconify-icon icon="cil:file-export" width="20" height="20"></iconify-icon>
56.                 Export Data
57.             </a>
58.         </div>
59.     </div>
60. 
61.     <div class="overflow-x-auto rounded-lg shadow border border-secondary-200 bg-white">
62.         <table class="min-w-full text-sm text-left">
63.             <thead class="bg-primary-100 text-primary-700">
64.             <tr>
65.                 <th class="px-4 py-2 font-semibold">ID</th>
66.                 <th class="px-4 py-2 font-semibold">NIS</th>
67.                 <th class="px-4 py-2 font-semibold">NISN</th>
68.                 <th class="px-4 py-2 font-semibold">Nama</th>
69.                 <th class="px-4 py-2 font-semibold">Tanggal Lahir</th>
70.                 <th class="px-4 py-2 font-semibold">Jenis Kelamin</th>
71.                 <th class="px-4 py-2 font-semibold">Alamat</th>
72.                 <th class="px-4 py-2 font-semibold">Action</th>
73.             </tr>
74.             </thead>
75.             <tbody>
76.             <?php if (count($students) > 0): ?>
77.                 <?php foreach ($students as $siswa): ?>
78.                     <tr class="even:bg-secondary-50 hover:bg-secondary-100">
79.                         <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($siswa['id']) ?></td>
80.                         <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($siswa['nis']) ?></td>
81.                         <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($siswa['nisn']) ?></td>
82.                         <td class="px-4 py-2"><?= htmlspecialchars($siswa['nama']) ?></td>
83.                         <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($siswa['tanggal_lahir']) ?></td>
84.                         <td class="px-4 py-2 whitespace-nowrap"><?= $siswa['jenis_kelamin'] == '1' ? 'Laki-laki' : 'Perempuan' ?></td>
85.                         <td class="px-4 py-2"><?= htmlspecialchars($siswa['alamat']) ?></td>
86.                         <td class="px-4 py-2 whitespace-nowrap flex gap-1">
87.                             <a href="students/details?id=<?= htmlspecialchars($siswa['id']) ?>"
88.                                class="inline-flex items-center justify-center p-2 rounded-lg border border-primary-300 text-primary-700 bg-white hover:bg-primary-50 transition"
89.                                title="Detail">
90.                                 <iconify-icon icon="mdi:eye-outline" width="20" height="20"></iconify-icon>
91.                             </a>
92.                             <a href="students/edit?id=<?= htmlspecialchars($siswa['id']) ?>"
93.                                class="inline-flex items-center justify-center p-2 rounded-lg border border-primary-300 text-primary-700 bg-white hover:bg-primary-50 transition"
94.                                title="Edit">
95.                                 <iconify-icon icon="mdi:pencil-outline" width="20" height="20"></iconify-icon>
96.                             </a>
97.                             <a href="students/delete?id=<?= htmlspecialchars($siswa['id']) ?>"
98.                                class="inline-flex items-center justify-center p-2 rounded-lg bg-status-error-500 text-white hover:bg-status-error-600 transition"
99.                                title="Hapus">
100.                                 <iconify-icon icon="mdi:trash-can-outline" width="20" height="20"></iconify-icon>
101.                             </a>
102.                         </td>
103.                     </tr>
104.                 <?php endforeach; ?>
105.             <?php else: ?>
106.                 <tr>
107.                     <td colspan="8" class="text-center py-8 text-secondary-500">Tidak ada data siswa ditemukan.</td>
108.                 </tr>
109.             <?php endif; ?>
110.             </tbody>
111.         </table>
112.     </div>
113. 
114. <?php
115. $pageContent = ob_get_clean();
116. $layout = 'dashboard';
117. require __DIR__ . '/_layout.php';
118. ?>
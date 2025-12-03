<?php
// --- KONFIGURASI ---
// Ganti dengan informasi database Anda
 $host = 'localhost';
 $dbname = 'db_pos_umkm';
 $user = 'root';
 $pass = '';

// Daftar user yang ingin ditambahkan
// Format: 'username' => ['password' => 'plaintext_password', 'nama_lengkap' => 'Nama Lengkap', 'role' => 'admin' atau 'kasir']
 $users_to_create = [
    'admin2' => [
        'password' => 'password_admin',
        'nama_lengkap' => 'Admin Kedua',
        'role' => 'admin'
    ],
    'kasir2' => [
        'password' => 'password_kasir',
        'nama_lengkap' => 'Kasir Dua',
        'role' => 'kasir'
    ],
    'kasir3' => [
        'password' => 'kasir123',
        'nama_lengkap' => 'Kasir Tiga',
        'role' => 'kasir'
    ],
    'supervisor' => [
        'password' => 'spv123',
        'nama_lengkap' => 'Supervisor Store',
        'role' => 'admin' // Supervisor bisa diberi akses admin
    ]
];

// --- LOGIKA SCRIPT (Tidak perlu mengubah bagian ini) ---
echo "<h1>Script Pembuatan User POS UMKM</h1>";
echo "<p>Sedang mencoba membuat user berikut:</p>";
echo "<ul>";
foreach ($users_to_create as $username => $data) {
    echo "<li><strong>{$username}</strong> ({$data['nama_lengkap']}) - Role: {$data['role']}</li>";
}
echo "</ul>";
echo "<hr>";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");

    $success_count = 0;
    $failure_count = 0;

    foreach ($users_to_create as $username => $data) {
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        
        try {
            $stmt->execute([
                $username,
                $hashed_password,
                $data['nama_lengkap'],
                $data['role']
            ]);
            echo "<p style='color: green;'>✅ Berhasil membuat user: <strong>{$username}</strong></p>";
            $success_count++;
        } catch (PDOException $e) {
            // Cek apakah errornya karena username sudah ada
            if ($e->getCode() == 23000) { // 23000 adalah kode untuk integrity constraint violation (duplicate entry)
                echo "<p style='color: orange;'>⚠️ Gagal membuat user <strong>{$username}</strong>. Username mungkin sudah ada.</p>";
            } else {
                echo "<p style='color: red;'>❌ Gagal membuat user <strong>{$username}</strong>. Error: " . $e->getMessage() . "</p>";
            }
            $failure_count++;
        }
    }
    
    echo "<hr>";
    echo "<h2>Selesai!</h2>";
    echo "<p><strong>{$success_count}</strong> user berhasil dibuat.</p>";
    echo "<p><strong>{$failure_count}</strong> user gagal dibuat.</p>";
    echo "<p><strong style='color: red;'>PENTING: Hapus file ini (create_users.php) dari server setelah digunakan!</strong></p>";

} catch (PDOException $e) {
    die("Koneksi ke database gagal: " . $e->getMessage());
}
?>
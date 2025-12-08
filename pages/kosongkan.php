<?php
session_start();
header('Content-Type: application/json');

// ===== 1. Cek metode request =====
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Gunakan metode POST.'
    ]);
    exit;
}

// ===== 2. Validasi session & role =====
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Session tidak valid. Silakan login ulang.'
    ]);
    exit;
}

if ($_SESSION['role'] !== 'ob') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Akses ditolak. Hanya OB yang boleh mengosongkan bin.'
    ]);
    exit;
}

$id_user = $_SESSION['id_user'];

// ===== 3. Ambil id_bin dari POST =====
$id_bin = isset($_POST['id_bin']) ? intval($_POST['id_bin']) : 0;

if ($id_bin <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'ID tempat sampah tidak valid.'
    ]);
    exit;
}

// ===== 4. Koneksi DB =====
require '../includes/koneksi.php';

// ===== 5. Cek apakah bin ada =====
$sql = "SELECT * FROM tempat_sampah WHERE id_bin = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_bin);
$stmt->execute();
$res = $stmt->get_result();
$bin = $res->fetch_assoc();
$stmt->close();

if (!$bin) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Tempat sampah tidak ditemukan.'
    ]);
    exit;
}

// Simpan data lama untuk riwayat
$status_lama = $bin['status'];
$kapasitas_lama = $bin['kapasitas'];

// ===== 6. Update bin jadi kosong (kapasitas = 0, status = 'Aman') =====
// Status database kamu TIDAK punya "Kosong", jadi kita pakai "Aman".
$kapasitas_baru = 0;
$status_baru = 'Aman';

$update = "UPDATE tempat_sampah 
           SET kapasitas = ?, status = ?, terakhir_update = NOW()
           WHERE id_bin = ?";
$up = $conn->prepare($update);
$up->bind_param("isi", $kapasitas_baru, $status_baru, $id_bin);
$success_update = $up->execute();
$up->close();

if (!$success_update) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal mengupdate data bin.'
    ]);
    exit;
}

// ===== 7. Insert riwayat ke tabel riwayat_kosongkan =====
$insert = "INSERT INTO riwayat_kosongkan (id_user, id_bin, status_sebelumnya, status_setelah)
           VALUES (?, ?, ?, ?)";
$riw = $conn->prepare($insert);
$riw->bind_param("iiss", $id_user, $id_bin, $status_lama, $status_baru);
$riw->execute();
$riw->close();

// ===== 7b. Insert juga ke tabel bin_history (untuk halaman riwayat OB) =====
$insert2 = "INSERT INTO bin_history (id_ob, id_tempat, kapasitas_sebelum, status_sebelum, tindakan)
            VALUES (?, ?, ?, ?, 'Kosongkan')";
$riw2 = $conn->prepare($insert2);

if ($riw2) {
    $riw2->bind_param("iiis", $id_user, $id_bin, $kapasitas_lama, $status_lama);
    $riw2->execute();
    $riw2->close();
}

// ===== 8. Response JSON =====
echo json_encode([
    'success' => true,
    'message' => 'Tempat sampah berhasil dikosongkan.',
    'data' => [
        'id_bin' => $id_bin,
        'kapasitas_sebelum' => $kapasitas_lama,
        'kapasitas_sesudah' => 0,
        'status_sebelum' => $status_lama,
        'status_sesudah' => $status_baru,
    ]
]);
exit;

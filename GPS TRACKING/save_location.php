<?php
header('Content-Type: application/json');

$host = "localhost";
$username = "root";
$password = "";
$dbname = "gps_tracking";

$conn = new mysqli($host, $username, $password, $dbname);

// Cek Koneksi
if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi database gagal: " . $conn->connect_error
    ]);
    exit();
}

// Memastikan permintaan menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Ambil data yang dikirim dari AJAX JS
    $latitude  = isset($_POST['latitude']) ? $_POST['latitude'] : null;
    $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : null;
    $speed     = isset($_POST['speed']) ? $_POST['speed'] : 0;
    $accuracy  = isset($_POST['accuracy']) ? $_POST['accuracy'] : null;

    if ($latitude && $longitude) {
        
        // Gunakan Prepared Statement untuk keamanan dari SQL Injection
        $stmt = $conn->prepare("INSERT INTO location_logs (latitude, longitude, speed, accuracy) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ddds", $latitude, $longitude, $speed, $accuracy);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Lokasi berhasil disimpan ke database SQLyog!",
                "data" => [
                    "lat" => $latitude,
                    "lng" => $longitude,
                    "time" => date('Y-m-d H:i:s')
                ]
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Gagal menyimpan ke database: " . $stmt->error
            ]);
        }

        $stmt->close();
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Data koordinat tidak lengkap."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Metode HTTP tidak diizinkan."
    ]);
}

$conn->close();
?>
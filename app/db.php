<?php

$host = getenv('DB_HOST') ?: 'db';
$db   = getenv('DB_NAME') ?: 'qlhtsv';
$user = getenv('DB_USER') ?: 'qlhtsv_user';
$pass = getenv('DB_PASSWORD') ?: 'qlhtsv_password';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Không thể kết nối cơ sở dữ liệu: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

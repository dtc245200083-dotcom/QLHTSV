<?php

require_once 'db.php';

header('Content-Type: text/plain; version=0.0.4');

$result = $conn->query("SELECT COUNT(*) AS total FROM students");
$total_students = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) AS total FROM classes");
$total_classes = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) AS total FROM grades");
$total_grades = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT AVG(score) AS avg_score FROM grades");
$avg_score = $result->fetch_assoc()['avg_score'] ?? 0;

echo "# HELP qlhtsv_students_total Total students\n";
echo "# TYPE qlhtsv_students_total gauge\n";
echo "qlhtsv_students_total " . $total_students . "\n";

echo "# HELP qlhtsv_classes_total Total classes\n";
echo "# TYPE qlhtsv_classes_total gauge\n";
echo "qlhtsv_classes_total " . $total_classes . "\n";

echo "# HELP qlhtsv_grades_total Total grades\n";
echo "# TYPE qlhtsv_grades_total gauge\n";
echo "qlhtsv_grades_total " . $total_grades . "\n";

echo "# HELP qlhtsv_average_score Average student score\n";
echo "# TYPE qlhtsv_average_score gauge\n";
echo "qlhtsv_average_score " . floatval($avg_score) . "\n";

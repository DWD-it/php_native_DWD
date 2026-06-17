<?php
require_once '../config/database.php';
require_once '../classes/University.php';

header('Content-Type: application/json');

$university = new University($conn);
$data = $university->getCoursesByDepartmentStats();

$labels = [];
$values = [];

foreach ($data as $dept) {
    $labels[] = $dept['dept_name'];
    $values[] = $dept['courses_count'];
}

echo json_encode(['labels' => $labels, 'values' => $values]);
?>
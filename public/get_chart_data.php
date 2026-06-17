<?php
require_once __DIR__ . '/../classes/Department.php';
require_once __DIR__ . '/../classes/Course.php';

header('Content-Type: application/json');

$dept = new Department();
$course = new Course();

$departments = $dept->getAll();
$labels = [];
$values = [];

foreach ($departments as $d) {
    $courses = $course->getByDepartment($d['id']);
    $labels[] = $d['name'];
    $values[] = count($courses);
}

echo json_encode(['labels' => $labels, 'values' => $values]);
?>
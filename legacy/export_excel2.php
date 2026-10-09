<?php
session_start();
$connect = require_once __DIR__ . '/database.php';
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

// نفس كود الفلترة زي ما عندك في الصفحة الرئيسية
$conditions = [];
$params = [];

if (!empty($_GET['patientName'])) {
    $conditions[] = "patient_Name LIKE ?";
    $params[] = "%" . $_GET['patientName'] . "%";
}
if (!empty($_GET['appointmentDate'])) {
    $conditions[] = "appointmentDate = ?";
    $params[] = $_GET['appointmentDate'];
}
if (!empty($_GET['patientNum'])) {
    $conditions[] = "patient_Num LIKE ?";
    $params[] = "%" . $_GET['patientNum'] . "%";
}
if (!empty($_GET['adrs'])) {
    $conditions[] = "adrs LIKE ?";
    $params[] = "%" . $_GET['adrs'] . "%";
}

$sql = "SELECT * FROM appointment";
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}
$sql .= " ORDER BY postingDate DESC";

$stmt = $connect->prepare($sql);
if (!empty($params)) {
    $types = str_repeat("s", count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Header علشان يعمل Download كـ Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=appointments_" . date("Y-m-d") . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// طباعة الجدول
echo "<table border='1'>";
echo "<tr>
        <th>م</th>
        <th>اسم المريض</th>
        <th>العنوان</th>
        <th>رقم الموبايل</th>
        <th>نوع الحجز</th>
        <th>سعر الكشف</th>
        <th>سعر الجبس</th>
        <th>الإجمالي</th>
      </tr>";

$totalPaid = 0;
while ($row = $result->fetch_assoc()) {
    $rowTotal = $row['paid'] + $row['over_paid'];
    $totalPaid += $rowTotal;

    echo "<tr>
            <td>{$row['apid']}</td>
            <td>{$row['patient_Name']}</td>
            <td>{$row['adrs']}</td>
            <td>{$row['patient_Num']}</td>
            <td>{$row['typ']}</td>
            <td>{$row['paid']}</td>
            <td>{$row['over_paid']}</td>
            <td>{$rowTotal}</td>
          </tr>";
}
echo "<tr style='font-weight:bold; background:#d4edda'>
        <td colspan='7'>الإجمالي</td>
        <td>{$totalPaid}</td>
      </tr>";
echo "</table>";
exit;
?>

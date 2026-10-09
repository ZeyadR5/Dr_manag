<?php
require_once 'config.php';

// استقبال مدخلات البحث
$searchDate = $_GET['date'] ?? '';
$searchName = $_GET['name'] ?? '';
$searchId   = $_GET['id'] ?? '';
$page       = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage    = 10;
$startIndex = ($page - 1) * $perPage;

date_default_timezone_set("Africa/Cairo");
$today = date("Ymd");

// إعداد استعلام البحث باستخدام Expand
$searchQuery = [
  "Level" => "Study",
  "Expand" => true,
  "Limit" => 1000,
  "Query" => []
];

// ✅ إذا لم يتم إدخال اسم أو ID، نبحث حسب التاريخ فقط
if (empty($searchName) && empty($searchId)) {
  $searchQuery["Query"]["StudyDate"] = $searchDate ? str_replace('-', '', $searchDate) : $today;
}

if ($searchName) {
  $searchQuery["Query"]["PatientName"] = "*$searchName*";
}

if ($searchId) {
  $searchQuery["Query"]["PatientID"] = "*$searchId*";
}

// إرسال الطلب باستخدام التوثيق
$context  = createOrthancContext('POST', $searchQuery);
$response = @file_get_contents("$orthancUrl/tools/find", false, $context);

if ($response === false) {
    $allStudies = [];
    $error_msg = "⚠️ فشل الاتصال بخادم الأشعة (Orthanc). تأكد من أنه قيد التشغيل.";
} else {
    $allStudies = json_decode($response, true);
    if (!is_array($allStudies)) {
        $allStudies = [];
        $error_msg = "⚠️ تلقى الخادم استجابة غير صالحة. تحقق من بيانات الاعتماد.";
    }
}

// فرز الدراسات حسب التاريخ والوقت تنازلياً (الأحدث أولاً)
usort($allStudies, function($a, $b) {
    $dateA = $a['MainDicomTags']['StudyDate'] ?? '00000000';
    $timeA = $a['MainDicomTags']['StudyTime'] ?? '000000';
    $dateB = $b['MainDicomTags']['StudyDate'] ?? '00000000';
    $timeB = $b['MainDicomTags']['StudyTime'] ?? '000000';
    
    // إنشاء قيمة موحدة للفرز (التاريخ + الوقت)
    $valueA = $dateA . $timeA;
    $valueB = $dateB . $timeB;
    
    // فرز تنازلي (الأحدث أولاً)
    return $valueB <=> $valueA;
});

$total = count($allStudies);
$studies = array_slice($allStudies, $startIndex, $perPage);

echo "<!DOCTYPE html>
<html lang='ar'>
<head>
  <meta charset='UTF-8'>
  <title>📅 بحث في حالات الأشعة</title>
  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
  <script src='tailwind.js'></script>
  <style>
    body { direction: rtl; padding: 40px; font-family: 'Cairo', sans-serif; background-color: #f8f9fa; }
    table { background-color: white; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    .search-box { margin-bottom: 30px; padding: 20px; background: #fff; border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
    .header-bar { background-color: #0d6efd; padding: 10px 20px; border-radius: 8px; margin-bottom: 30px; }
    .header-bar a { margin-left: 10px; }
  </style>
</head>
<body>
<div class='container'>

  <!-- هيدر بأزرار -->
     <div class='header-bar d-flex justify-content-start'>
        <a href='new_appoint.php' class='btn btn-light'>➕ حجز جديد</a>
        <a href='reserv.php' class='btn btn-warning'>📅 الحجز</a>
        <a href='index.php' class='btn btn-light'>🏠 الرئيسية</a>
    </div>
    <img src='WhatsApp Image 2025-09-16 at 12.40.57_91b1f616.jpg' alt='شعار العيادة' class='logo-img flex mx-auto mb-4' style='max-height: 300px;'>
    <h2 class='mb-4'>🔍 بحث في حالات الأشعة</h2>";
  
  if (isset($error_msg)) {
    echo "<div class='alert alert-danger' role='alert'>$error_msg</div>";
  }

  echo "<form method='get' class='search-box row g-3'>
    <div class='col-md-4'>
      <label for='date' class='form-label'>📅 تاريخ الفحص</label>
      <input type='date' id='date' name='date' class='form-control' value='" . htmlspecialchars($searchDate) . "'>
    </div>
    <div class='col-md-4'>
      <label for='name' class='form-label'>👤 اسم المريض</label>
      <input type='text' id='name' name='name' class='form-control' placeholder='أدخل الاسم' value='" . htmlspecialchars($searchName) . "'>
    </div>
    <div class='col-md-4'>
      <label for='id' class='form-label'>🆔 Patient ID</label>
      <input type='text' id='id' name='id' class='form-control' placeholder='أدخل الـ ID' value='" . htmlspecialchars($searchId) . "'>
    </div>
    <input type='hidden' name='page' value='1'>
    <div class='col-12 text-center'>
      <button type='submit' class='btn btn-primary px-4 mt-3'>🔍 بحث</button>
    </div>
  </form>

  <form method='post' action='export_excel.php' class='mb-4 text-center'>
    <input type='hidden' name='date' value='" . htmlspecialchars($searchDate) . "'>
    <input type='hidden' name='name' value='" . htmlspecialchars($searchName) . "'>
    <input type='hidden' name='id' value='" . htmlspecialchars($searchId) . "'>
    <button type='submit' class='btn btn-success px-4'>📥 تصدير الحالات إلى Excel</button>
  </form>

  <table class='table table-bordered'>
    <thead class='table-light'>
      <tr>
        <th>🧑 اسم المريض</th>
        <th>🗓️ تاريخ الفحص</th>
        <th>🕒 وقت الفحص</th>
        <th>🆔 Patient ID</th>
        <th>📷 عرض الصور</th>
      </tr>
    </thead>
    <tbody>";


foreach ($studies as $study) {
    $id = $study['ID'] ?? '';
    $tags = $study['MainDicomTags'] ?? [];

    $dicomDate = $tags['StudyDate'] ?? '';
    $formattedDate = ($dicomDate) ? substr($dicomDate, 0, 4) . '/' . substr($dicomDate, 4, 2) . '/' . substr($dicomDate, 6, 2) : '';
    
    $dicomTime = $tags['StudyTime'] ?? '';
    $formattedTime = ($dicomTime) ? substr($dicomTime, 0, 2) . ':' . substr($dicomTime, 2, 2) . ':' . substr($dicomTime, 4, 2) : '';

    $rawName = $study['PatientMainDicomTags']['PatientName'] ?? 'غير معروف';
    $patientName = preg_replace('/[^a-zA-Z0-9\s]/', '', $rawName);

    $patientId = $study['PatientMainDicomTags']['PatientID'] ?? '';

    echo "<tr>
        <td>" . htmlspecialchars($patientName) . "</td>
        <td>" . htmlspecialchars($formattedDate) . "</td>
        <td>" . htmlspecialchars($formattedTime) . "</td>
        <td>" . htmlspecialchars($patientId) . "</td>
        <td><a href='view.php?id=$id' target='_blank' class='btn btn-primary'>عرض الصور</a></td>
    </tr>";
}

echo "</tbody></table>";

$totalPages = ceil($total / $perPage);
echo "<nav class='text-center'><ul class='pagination justify-content-center'>";
if ($page > 1) {
    $prevPage = $page - 1;
    echo "<li class='page-item'><a class='page-link' href='?page=$prevPage&date=$searchDate&name=$searchName&id=$searchId'>السابق</a></li>";
}
if ($page < $totalPages) {
    $nextPage = $page + 1;
    echo "<li class='page-item'><a class='page-link' href='?page=$nextPage&date=$searchDate&name=$searchName&id=$searchId'>التالي</a></li>";
}
echo "</ul></nav>";

echo "</div></body></html>";
?>
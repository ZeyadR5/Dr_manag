<?php
require_once 'config.php';

// استلام مدخلات البحث
$searchDate = $_POST['date'] ?? '';
$searchName = $_POST['name'] ?? '';
$searchId   = $_POST['id'] ?? '';

date_default_timezone_set("Africa/Cairo");
$today = date("Ymd");

$searchQuery = [
  "Level" => "Study",
  "Limit" => 1000,
  "Query" => []
];

if ($searchDate) {
  $searchQuery["Query"]["StudyDate"] = str_replace('-', '', $searchDate);
} else {
  $searchQuery["Query"]["StudyDate"] = $today;
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
$studyIds = json_decode($response, true);

if (!is_array($studyIds)) {
    die("❌ فشل تحميل البيانات من Orthanc. تحقق من الاتصال وبيانات الاعتماد.");
}

// إعداد رأس Excel
header("Content-Type: application/vnd.ms-excel; charset=UTF-16LE");
header("Content-Disposition: attachment; filename=studies_" . date("Y-m-d") . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
echo chr(255) . chr(254); // BOM

function toUTF16LE($text) {
    return mb_convert_encoding($text, 'UTF-16LE', 'UTF-8');
}

// طباعة رأس الجدول
echo toUTF16LE("👤 اسم المريض\t📅 تاريخ الفحص\t🆔 Patient ID\n");

// تحميل التفاصيل وطباعة الصفوف
$detailContext = createOrthancContext();
foreach ($studyIds as $studyId) {
    $study = json_decode(@file_get_contents("$orthancUrl/studies/$studyId", false, $detailContext), true);

    $dicomDate = $study['MainDicomTags']['StudyDate'] ?? '';
    $formattedDate = ($dicomDate) ? substr($dicomDate, 0, 4) . '/' . substr($dicomDate, 4, 2) . '/' . substr($dicomDate, 6, 2) : '';
    $rawName = $study['PatientMainDicomTags']['PatientName'] ?? 'غير معروف';
    $patientName = preg_replace('/[^a-zA-Z0-9\s]/u', '', $rawName);
    $patientId = $study['PatientMainDicomTags']['PatientID'] ?? '';

    echo toUTF16LE("$patientName\t$formattedDate\t$patientId\n");
}
?>

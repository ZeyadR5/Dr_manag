<?php
session_start();

$conn = require_once __DIR__ . '/database.php';

// التحقق من وجود معرف الحجز
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("معرف الحجز غير موجود");
}

$appointment_id = intval($_GET['id']);

//  بيانات الحجز
$sql = "SELECT * FROM appointment WHERE apid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("الحجز غير موجود");
}

$appointment = $result->fetch_assoc();

// معالجة تحديث البيانات
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $name = trim($_POST['name'] ?? '');
    $phnumber = trim($_POST['phnumber'] ?? '');
    $typ = trim($_POST['typ'] ?? '');
    $adrs = trim($_POST['adrs'] ?? '');
    $over_price = trim($_POST['over_price_g'] ?? '');
    $price = trim($_POST['Payed'] ?? '');
    $Date = trim($_POST['Date'] ?? '');
    $Time = trim($_POST['Time'] ?? '');
    $about = trim($_POST['about'] ?? '');

    // تحديث البيانات في قاعدة البيانات
    $update_sql = "UPDATE appointment SET 
        patient_Name = ?, 
        patient_Num = ?, 
        appointmentDate = ?, 
        appointmentTime = ?, 
        paid = ?, 
        over_paid = ?, 
        typ = ?, 
        commnet = ?, 
        adrs = ? 
        WHERE apid = ?";
    
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param(
        "sssssssssi",
        $name, $phnumber,
        $Date, $Time, $price, $over_price,
        $typ, $about, $adrs, $appointment_id
    );

    if ($update_stmt->execute()) {
        echo "<script>
            alert('تم التحديث بنجاح');
            window.location.href = 'reserv.php';
        </script>";
        exit;
    } else {
        echo "<script>alert('حدث خطأ أثناء التحديث');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الحجز</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='tailwind.js'></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="./photo/l-gh.png">
</head>
<body>
<div class="min-h-full">
    <header class="bg-white shadow">
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 bg-blue-900">
    <h1 class="text-3xl font-bold tracking-tight text-white text-center"> DR / Ahmed Nafady </h1>
</div>

        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 text-end">تعديل الحجز</h1>
        </div>

    </header>
    <main>

        <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">

            <div class="header-bar d-flex justify-content-end">
                <a href="new_appoint.php" class="btn btn-light">➕ حجز جديد</a>
                <a href="reserv.php" class="btn btn-warning">📅 الحجز</a>
                <a href="index.php" class="btn btn-light">🏠 الرئيسية</a>
            </div>

            <form method="post">
                <div class="space-y-12">
                    <div class="border-b border-gray-900/10 pb-12">
                        <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <input hidden type="text" name="ID" value="<?php echo htmlspecialchars($appointment_id); ?>">
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium leading-6 text-gray-900 text-end">الاسم</label>
                                <input required type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($appointment['patient_Name']); ?>">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium leading-6 text-gray-900 text-end">رقم الهاتف</label>
                                <input required name="phnumber" type="tel" class="form-control" value="<?php echo htmlspecialchars($appointment['patient_Num']); ?>">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium leading-6 text-gray-900 text-end">نوع الحجز</label>
                                <select name="typ" id="typ" class="form-control">
                                    <option value="كشف" <?php echo ($appointment['typ'] == 'كشف') ? 'selected' : ''; ?>>كشف</option>
                                    <option value="استشاره" <?php echo ($appointment['typ'] == 'استشاره' || $appointment['typ'] == 'استشارة') ? 'selected' : ''; ?>>استشاره</option>
                                </select>
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium leading-6 text-gray-900 text-end">العنوان</label>
                                <input name="adrs" type="text" class="form-control" value="<?php echo htmlspecialchars($appointment['adrs']); ?>">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium leading-6 text-gray-900 text-end">سعر الجبس</label>
                                <input type="number" name="over_price_g" class="form-control" value="<?php echo htmlspecialchars($appointment['over_paid']); ?>">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium leading-6 text-gray-900 text-end">سعر الكشف</label>
                                <input type="number" name="Payed" class="form-control" value="<?php echo htmlspecialchars($appointment['paid']); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium leading-6 text-gray-900 text-end">التاريخ</label>
                        <input type="date" name="Date" class="form-control" value="<?php echo htmlspecialchars($appointment['appointmentDate']); ?>">
                    </div>
                    <!-- <div class="sm:col-span-2">
                        <label class="block text-sm font-medium leading-6 text-gray-900 text-end">الوقت</label>
                        <input type="time" name="Time" class="form-control" value="<?php echo htmlspecialchars($appointment['appointmentTime']); ?>">
                    </div> -->
                    <div class="col-span-full">
                        <label class="block text-sm font-medium leading-6 text-gray-900 text-end">تعليق</label>
                        <textarea name="about" rows="3" class="form-control"><?php echo htmlspecialchars($appointment['commnet']); ?></textarea>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-x-6 m-6">
                    <a href="reserv.php" class="btn btn-light">الغاء</a>
                    <button type="submit" name="update" class="btn btn-primary">تحديث</button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
<?php $conn->close(); ?>
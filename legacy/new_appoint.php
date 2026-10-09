<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();

$conn = require_once __DIR__ . '/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Save'])) {
    $name           = trim($_POST['name'] ?? '');
    $phnumber       = trim($_POST['phnumber'] ?? '');
    $typ            = trim($_POST['typ'] ?? '');
    $adrs           = trim($_POST['adrs'] ?? '');
    $over_price     = trim($_POST['over_price_g'] ?? '');
    $price          = trim($_POST['Payed'] ?? '');
    $Date           = trim($_POST['Date'] ?? '');
    $Time           = trim($_POST['Time'] ?? '');
    $about          = trim($_POST['about'] ?? '');

    $stmt = $conn->prepare("INSERT INTO appointment 
        ( patient_Name, patient_Num, appointmentDate, appointmentTime, paid, over_paid , typ, commnet, adrs) 
        VALUES ( ?, ?, ?, ?, ?, ?, ? , ? , ?)");

    if ($stmt) {
        $stmt->bind_param(
            "sssssssss",
            $name, $phnumber,
            $Date, $Time,$price ,$over_price,
            $typ , $about , $adrs
        );

        if ($stmt->execute()) {
            echo "<script>
                alert('تم الحجز بنجاح');
                window.location.href = 'new_appoint.php';
            </script>";
            $stmt->close();
            $conn->close();
            exit;
        } else {
            echo "<script>alert('حدث خطأ أثناء الحجز');</script>";
        }
    } else {
        echo "<script>alert('فشل في تجهيز العملية');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجز جديد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='tailwind.js'></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="./photo/l-gh.png">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
    <style>
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo-img {
            max-height: 200px;
            width: auto;
        }
        @media (max-width: 768px) {
            .logo-img {
                max-height: 50px;
            }
        }
    </style>
</head>
<body>
<div class="min-h-full">
    <header class="bg-white shadow">
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 bg-blue-900">
    <img src="WhatsApp Image 2025-09-16 at 12.40.57_0f290d95.jpg" alt="شعار العيادة" class="logo-img">
    <h1 class="text-3xl font-bold tracking-tight text-white text-center"> DR / Ahmed Nafady </h1>
</div>

        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 text-end">امر جديد</h1>
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
                            <input hidden type="text" name="ID" value="<?php echo htmlspecialchars($userid ?? ''); ?>">
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium leading-6 text-gray-900 text-end">الاسم</label>
                                <input required type="text" name="name" class="form-control">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium leading-6 text-gray-900 text-end">رقم الهاتف</label>
                                <input required name="phnumber" type="text" class="form-control">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium leading-6 text-gray-900 text-end">نوع الحجز</label>
                                <select name="typ" id="typ" class="form-control">
                                    <option value="كشف">كشف</option>
                                    <option value="استشاره">استشاره</option>
                                </select>
                                
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium leading-6 text-gray-900 text-end">العنوان</label>
                                <input name="adrs" type="text" class="form-control">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium leading-6 text-gray-900 text-end">سعر الجبس</label>
                                <input  type="number" name="over_price_g" class="form-control">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium leading-6 text-gray-900 text-end">سعر الكشف</label>
                                <input  type="number" name="Payed" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium leading-6 text-gray-900 text-end">التاريخ</label>
                        <input readonly type="date" name="Date" value="<?php echo date('Y-m-d'); ?>" class="form-control">
                    </div>
                    <div class="col-span-full">
                        <label class="block text-sm font-medium leading-6 text-gray-900 text-end">تعليق</label>
                        <textarea name="about" rows="3" class="form-control"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-x-6 m-6">
                    <button type="button" class="btn btn-light">الغاء</button>
                    <button type="submit" name="Save" class="btn btn-primary">اضافه</button>

                    
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
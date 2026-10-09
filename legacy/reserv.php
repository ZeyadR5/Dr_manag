<?php
session_start();
$connect = require_once __DIR__ . '/database.php';

// معالجة طلب الحذف
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_stmt = $connect->prepare("DELETE FROM appointment WHERE apid = ?");
    $delete_stmt->bind_param("i", $delete_id);
    if ($delete_stmt->execute()) {
        
        echo "<script>window.location.href = 'reserv.php';</script>";
        exit();
    } else {
        
    }
}

// معالجة تحديث حالة الحجز
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $apid = $_POST['apid'];
    $status = isset($_POST['status']) ? 1 : 0; // 1 يعني تمت، 0 يعني لم تتم
    
    $update_stmt = $connect->prepare("UPDATE appointment SET statue = ? WHERE apid = ?");
    $update_stmt->bind_param("ii", $status, $apid);
    if ($update_stmt->execute()) {
       
        // إعادة التوجيه إلى نفس الصفحة للحفاظ على معايير البحث
        $redirect_url = 'reserv.php';
        if (!empty($selectedDate)) {
            $redirect_url .= '?selectedDate=' . $selectedDate;
        }
        echo "<script>window.location.href = '$redirect_url';</script>";
        exit();
    } else {
        
    }
}

// تحديد التاريخ المطلوب (اليوم افتراضيًا أو التاريخ المحدد في البحث)
$selectedDate = date('Y-m-d');
if (!empty($_POST['appointmentDate'])) {
    $selectedDate = $_POST['appointmentDate'];
} elseif (isset($_GET['selectedDate'])) {
    $selectedDate = $_GET['selectedDate'];
}

// معالجة طلب عرض الكل
if (isset($_GET['show_all'])) {
    $selectedDate = '';
}

// باقي كود البحث والاستعلام
$conditions = [];
$params = [];
$types = "";

if (!empty($selectedDate)) {
    $conditions[] = "appointmentDate = ?";
    $params[] = $selectedDate;
    $types .= "s";
}

// اسم المريض
if (!empty($_POST['patientName'])) {
    $conditions[] = "patient_Name LIKE ?";
    $params[] = "%" . $_POST['patientName'] . "%";
    $types .= "s";
}

// رقم الموبايل
if (!empty($_POST['patientNum'])) {
    $conditions[] = "patient_Num LIKE ?";
    $params[] = "%" . $_POST['patientNum'] . "%";
    $types .= "s";
}

// العنوان
if (!empty($_POST['adrs'])) {
    $conditions[] = "adrs LIKE ?";
    $params[] = "%" . $_POST['adrs'] . "%";
    $types .= "s";
}

// بناء الاستعلام - تم التعديل لترتيب من الأحدث إلى الأقدم
if (count($conditions) > 0) {
    $sql = "SELECT * FROM appointment WHERE " . implode(" AND ", $conditions) . " ORDER BY postingDate ASC, apid ASC";
} else {
    $sql = "SELECT * FROM appointment ORDER BY postingDate DESC, apid DESC";
}

$stmt = $connect->prepare($sql);

// ربط القيم إذا كانت هناك معايير بحث
if (count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// تجميع البيانات حسب النوع
$appointmentsByType = [
    'كشف' => [],
    'استشاره' => [],
    'استشارة' => [],
    'أخرى' => []
];

$totalPaid = 0;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rowTotal = $row['paid'] + $row['over_paid'];
        $totalPaid += $rowTotal;
        
        $type = $row['typ'];
        if ($type == 'كشف') {
            $appointmentsByType['كشف'][] = $row;
        } elseif ($type == 'استشاره' || $type == 'استشارة') {
            $appointmentsByType['استشاره'][] = $row;
        } else {
            $appointmentsByType['أخرى'][] = $row;
        }
    }
    
    // عكس ترتيب البيانات لعرض الأحدث أولاً (في الأعلى)
    foreach ($appointmentsByType as $key => $value) {
        $appointmentsByType[$key] = array_reverse($value);
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجوزات اليوم</title>
    <link rel="stylesheet" href="../css/Reservations.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>

	* {
   	font-size: 20px !important;
	}

        .header-bar {
            background-color: #f8f9fa;
            padding: 10px;
            border-bottom: 2px solid #ddd;
        }
        .today-header {
            background-color: #e8f5e9;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }
        .type-header {
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
            text-align: center;
            font-weight: bold;
        }
        .kashf-header {
            background-color: #d4edda;
        }
        .esteshara-header {
            background-color: #f8d7da;
        }
        .other-header {
            background-color: #fff3cd;
        }
        /* تنسيق خلايا أسماء المرضى في الكشوفات (أخضر) */
        .kashf-table .patient-name-cell {
            background-color: #d4edda;
            font-weight: bold;
            min-width: 200px !important; /* زيادة عرض عمود الاسم */
            width: 200px !important;
	    
        }
        /* تنسيق خلايا أسماء المرضى في الاستشارات (أحمر) */
        .esteshara-table .patient-name-cell {
            background-color: #f8d7da;
            font-weight: bold;
            min-width: 200px !important; /* زيادة عرض عمود الاسم */
            width: 200px !important;
        }
        /* زيادة عرض عمود الاسم في جدول الأنواع الأخرى */
        #otherTable td:nth-child(11),
        #otherTable th:nth-child(11) {
            min-width: 200px !important;
            width: 200px !important;
        }
        body {
            direction: rtl;
            text-align: right;
        }
        /* تنسيق عناوين الجداول القابلة للترتيب */
        .sortable {
            cursor: pointer;
            position: relative;
        }
        .sortable:hover {
            background-color: #f0f0f0;
        }
        .sortable::after {
            content: "↕";
            position: absolute;
            left: 5px;
            opacity: 0.5;
        }
        .sortable.asc::after {
            content: "↑";
            opacity: 1;
        }
        .sortable.desc::after {
            content: "↓";
            opacity: 1;
        }
        /* تنسيق العمود التسلسلي */
        .sequence-column {
            width: 50px;
            text-align: center;
            font-weight: bold;
        }
        /* تنسيق صف الحجز المكتمل */
        .completed-appointment {
            background-color: #e8f5e9 !important;
        }
        /* تنسيق زر الحالة */
        .status-checkbox {
            transform: scale(1.2);
            cursor: pointer;
        }
        .status-label {
            cursor: pointer;
            margin-right: 5px;
        }
        /* تنسيق النماذج داخل الجداول */
        .status-form {
            display: inline-block;
            margin: 0;
        }
        /* زيادة عرض أعمدة الاسم في جميع الجداول */
        .patient-name-header {
            min-width: 200px !important;
            width: 230px !important;
        }
    </style>
</head>

<body>
    <div class="header-bar d-flex justify-content-start">
        <a href="new_appoint.php" class="btn btn-light me-2">➕ حجز جديد</a>
        <a href="reserv.php" class="btn btn-warning me-2">📅 الحجز</a>
        <a href="index.php" class="btn btn-light">🏠 الرئيسية</a>
    </div>
    
    <div class="container py-4">
        
        <div class="today-header">
            حجوزات تاريخ: <?php echo !empty($selectedDate) ? $selectedDate : 'الكل'; ?>
            <?php if ($selectedDate == date('Y-m-d')): ?>
                (اليوم)
            <?php endif; ?>
        </div>

        <form method="POST" class="mb-4">
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="patientName" class="form-control"
                        placeholder="اسم المريض"
                        value="<?php echo isset($_POST['patientName']) ? htmlspecialchars($_POST['patientName']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <input type="date" name="appointmentDate" class="form-control"
                        value="<?php echo isset($_POST['appointmentDate']) ? htmlspecialchars($_POST['appointmentDate']) : $selectedDate; ?>">
                </div>
                <div class="col-md-3">
                    <input type="text" name="patientNum" class="form-control"
                        placeholder="رقم الموبايل"
                        value="<?php echo isset($_POST['patientNum']) ? htmlspecialchars($_POST['patientNum']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <input type="text" name="adrs" class="form-control"
                        placeholder="العنوان"
                        value="<?php echo isset($_POST['adrs']) ? htmlspecialchars($_POST['adrs']) : ''; ?>">
                </div>
            </div>
            <div class="mt-3 text-center">
                <button type="submit" class="btn btn-primary">بحث</button>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">إلغاء</a>
                <a href="?show_all=1" class="btn btn-info">عرض الكل</a>
            </div>
        </form>
  <!-- قسم الكشوفات -->
        <?php if (!empty($appointmentsByType['كشف'])): 
            $kashfCount = count($appointmentsByType['كشف']);
        ?>
        <div class="type-header kashf-header">الكشوفات</div>
        <table class="table table-striped table-hover table-bordered border-gray-400 mb-4 kashf-table" id="kashfTable">
            <thead>
                <tr>
                    <th class="text-center sequence-column">#</th>
                    <th class="text-center sortable patient-name-header" data-sort="patient_Name">اسم المريض</th>
                    <th class="text-center sortable" data-sort="adrs">العنوان</th>
                    <th class="text-center sortable" data-sort="patient_Num">رقم الموبايل</th>
                    <th class="text-center sortable" data-sort="over_paid">سعر الكشف</th>
                    <th class="text-center sortable" data-sort="paid">سعر الجبس</th>
                    <th class="text-center sortable" data-sort="total">الإجمالي</th>
                    <th class="text-center">ملاحظه</th>
                    <th class="text-center">تعديل</th>
                    <th class="text-center">الغاء</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($appointmentsByType['كشف'] as $index => $row) {
                    $rowTotal = $row['paid'] + $row['over_paid'];
                    $sequenceNumber = $kashfCount - $index; // ترقيم من الأسفل إلى الأعلى
                    $isCompleted = ($row['statue'] == 1);
                    $rowClass = $isCompleted ? 'completed-appointment' : '';
                    ?>
                    <tr class="<?php echo $rowClass; ?>">
                        <td class="text-center sequence-column"><?php echo $sequenceNumber; ?></td>
                        <td class="text-center patient-name-cell"><?php echo $row['patient_Name']; ?></td>
                        <td class="text-center"><?php echo $row['adrs']; ?></td>
                        <td class="text-center"><?php echo $row['patient_Num']; ?></td>
                        <td class="text-center" data-sort-value="<?php echo $row['paid']; ?>"><?php echo $row['paid']; ?></td>
                        <td class="text-center" data-sort-value="<?php echo $row['over_paid']; ?>"><?php echo $row['over_paid']; ?></td>
                        <td class="text-center" data-sort-value="<?php echo $rowTotal; ?>"><?php echo number_format($rowTotal, 2); ?> EGP</td>
                        <td class="text-center"><?php echo $row['commnet']; ?></td>
                        <td class="text-center">
                            <a href="edit_appointment.php?id=<?php echo $row['apid']; ?>" class="btn btn-sm btn-primary px-1 py-0.5 text-[15px]">تعديل</a>
                        </td>
                        <td class="text-center">
                            <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $row['apid']; ?>)" class="btn btn-sm btn-danger px-1 py-0.5 text-[15px]">حذف</a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php endif; ?>

        <!-- قسم الاستشارات -->
        <?php if (!empty($appointmentsByType['استشاره'])): 
            $estesharaCount = count($appointmentsByType['استشاره']);
        ?>
        <div class="type-header esteshara-header">الاستشارات</div>
        <table class="table table-striped table-hover table-bordered border-gray-400 mb-4 esteshara-table" id="estesharaTable">
            <thead>
                <tr>
                    <th class="text-center sequence-column">#</th>
                    <th class="text-center sortable patient-name-header" data-sort="patient_Name">اسم المريض</th>
                    <th class="text-center sortable" data-sort="adrs">العنوان</th>
                    <th class="text-center sortable" data-sort="patient_Num">رقم الموبايل</th>
                    <th class="text-center sortable" data-sort="over_paid">سعر الكشف</th>
                    <th class="text-center sortable" data-sort="paid">سعر الجبس</th>
                    <th class="text-center sortable" data-sort="total">الإجمالي</th>
                    <th class="text-center">ملاحظه</th>
                    <th class="text-center">تعديل</th>
                    <th class="text-center">الغاء</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($appointmentsByType['استشاره'] as $index => $row) {
                    $rowTotal = $row['paid'] + $row['over_paid'];
                    $sequenceNumber = $estesharaCount - $index; // ترقيم من الأسفل إلى الأعلى
                    $isCompleted = ($row['statue'] == 1);
                    $rowClass = $isCompleted ? 'completed-appointment' : '';
                    ?>
                    <tr class="<?php echo $rowClass; ?>">
                        <td class="text-center sequence-column"><?php echo $sequenceNumber; ?></td>
                        <td class="text-center patient-name-cell"><?php echo $row['patient_Name']; ?></td>
                        <td class="text-center"><?php echo $row['adrs']; ?></td>
                        <td class="text-center"><?php echo $row['patient_Num']; ?></td>
                        <td class="text-center" data-sort-value="<?php echo $row['paid']; ?>"><?php echo $row['paid']; ?></td>
                        <td class="text-center" data-sort-value="<?php echo $row['over_paid']; ?>"><?php echo $row['over_paid']; ?></td>
                        <td class="text-center" data-sort-value="<?php echo $rowTotal; ?>"><?php echo number_format($rowTotal, 2); ?> EGP</td>
                        <td class="text-center"><?php echo $row['commnet']; ?></td>
                        <td class="text-center">
                            <a href="edit_appointment.php?id=<?php echo $row['apid']; ?>" class="btn btn-primary px-1 py-0.5 text-[15px]">تعديل</a>
                        </td>
                        <td class="text-center">
                            <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $row['apid']; ?>)" class="btn btn-sm btn-danger px-1 py-0.5 text-[15px]">حذف</a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php endif; ?>

        <!-- قسم الأنواع الأخرى -->
        <?php if (!empty($appointmentsByType['أخرى'])): 
            $otherCount = count($appointmentsByType['أخرى']);
        ?>
        <div class="type-header other-header">أنواع أخرى</div>
        <table class="table table-striped table-hover table-bordered border-gray-400 mb-4" id="otherTable">
            <thead>
                <tr>
                    <th class="text-center sequence-column">#</th>
                    <th class="text-center">الغاء</th>
                    <th class="text-center">تعديل</th>
                    <th class="text-center">الحالة</th>
                    <th class="text-center">ملاحظه</th>
                    <th class="text-center sortable" data-sort="total">الإجمالي</th>
                    <th class="text-center sortable" data-sort="over_paid">سعر الجبس</th>
                    <th class="text-center sortable" data-sort="paid">سعر الكشف</th>
                    <th class="text-center sortable" data-sort="patient_Num">رقم الموبايل</th>
                    <th class="text-center sortable" data-sort="adrs">العنوان</th>
                    <th class="text-center sortable patient-name-header" data-sort="patient_Name">اسم المريض</th>
                    <th class="text-center sortable" data-sort="typ">نوع الحجز</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($appointmentsByType['أخرى'] as $index => $row) {
                    $rowTotal = $row['paid'] + $row['over_paid'];
                    $sequenceNumber = $otherCount - $index; // ترقيم من الأسفل إلى الأعلى
                    $isCompleted = ($row['statue'] == 1);
                    $rowClass = $isCompleted ? 'completed-appointment' : '';
                    ?>
                    <tr class="<?php echo $rowClass; ?>">
                        <td class="text-center sequence-column"><?php echo $sequenceNumber; ?></td>
                        <td class="text-center">
                            <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $row['apid']; ?>)" class="btn btn-sm btn-danger">حذف</a>
                        </td>
                        <td class="text-center">
                            <a href="edit_appointment.php?id=<?php echo $row['apid']; ?>" class="btn btn-sm btn-primary">تعديل</a>
                        </td>
                        <td class="text-center">
                            <form method="POST" class="status-form">
                                <input type="hidden" name="apid" value="<?php echo $row['apid']; ?>">
                                <input type="hidden" name="selectedDate" value="<?php echo $selectedDate; ?>">
                                <input type="checkbox" name="status" class="status-checkbox" 
                                    <?php echo $isCompleted ? 'checked' : ''; ?> 
                                    onchange="this.form.submit()">
                                <input type="hidden" name="update_status" value="1">
                            </form>
                            <span class="status-label"> </span>
                        </td>
                        <td class="text-center"><?php echo $row['commnet']; ?></td>
                        <td class="text-center" data-sort-value="<?php echo $rowTotal; ?>"><?php echo number_format($rowTotal, 2); ?> EGP</td>
                        <td class="text-center" data-sort-value="<?php echo $row['over_paid']; ?>"><?php echo $row['over_paid']; ?></td>
                        <td class="text-center" data-sort-value="<?php echo $row['paid']; ?>"><?php echo $row['paid']; ?></td>
                        <td class="text-center"><?php echo $row['patient_Num']; ?></td>
                        <td class="text-center"><?php echo $row['adrs']; ?></td>
                        <td class="text-center"><?php echo $row['patient_Name']; ?></td>
                        <td class="text-center"><?php echo $row['typ']; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php endif; ?>

        <!-- الإجمالي العام -->
        <?php if ($result && $result->num_rows > 0): ?>
        <div class="alert alert-info text-center">
            <h5>الإجمالي العام: <?php echo number_format($totalPaid, 2); ?> EGP</h5>
        </div>
        <?php else: ?>
        <div class="alert alert-warning text-center">
            لا توجد حجوزات لهذا التاريخ
        </div>
        <?php endif; ?>

        <div class="mb-3 text-end">
            <a href="export_excel2.php?<?php echo http_build_query($_POST); ?>&selectedDate=<?php echo $selectedDate; ?>" 
               class="btn btn-success">
               📊 تصدير إلى Excel
            </a>
        </div>

    </div>

    <script>
    function confirmDelete(id) {
        if (confirm('هل أنت متأكد من أنك تريد حذف هذا الحجز؟')) {
            window.location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?delete_id=' + id + '&selectedDate=<?php echo $selectedDate; ?>';
        }
    }

    // دالة لترتيب الجداول
    document.addEventListener('DOMContentLoaded', function() {
        // إضافة وظيفة الترتيب لجميع الجداول
        initTableSorting('kashfTable');
        initTableSorting('estesharaTable');
        initTableSorting('otherTable');
    });

    function initTableSorting(tableId) {
        const table = document.getElementById(tableId);
        if (!table) return;
        
        const headers = table.querySelectorAll('th.sortable');
        const tbody = table.querySelector('tbody');
        
        headers.forEach(header => {
            header.addEventListener('click', () => {
                const columnIndex = Array.from(headers).indexOf(header) + 1; // +1 لأننا أضفنا عمود التسلسل
                const sortKey = header.getAttribute('data-sort');
                const currentIsAscending = header.classList.contains('asc');
                
                // إعادة تعليمات الترتيب لجميع الرؤوس
                headers.forEach(h => {
                    h.classList.remove('asc', 'desc');
                });
                
                // تعيين الترتيب الجديد
                header.classList.remove('asc', 'desc');
                header.classList.add(currentIsAscending ? 'desc' : 'asc');
                
                // ترتيب البيانات
                sortTable(tbody, columnIndex, !currentIsAscending, sortKey);
            });
        });
    }

    function sortTable(tbody, columnIndex, ascending, sortKey) {
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            let aValue, bValue;
            
            // الحصول على القيمة من عمود معين
            if (sortKey === 'total') {
                aValue = parseFloat(a.cells[columnIndex].getAttribute('data-sort-value') || 0);
                bValue = parseFloat(b.cells[columnIndex].getAttribute('data-sort-value') || 0);
            } else {
                aValue = a.cells[columnIndex].getAttribute('data-sort-value') || a.cells[columnIndex].textContent.trim();
                bValue = b.cells[columnIndex].getAttribute('data-sort-value') || b.cells[columnIndex].textContent.trim();
                
                // محاولة تحويل إلى أرقام إذا أمكن
                if (!isNaN(aValue) && !isNaN(bValue)) {
                    aValue = parseFloat(aValue);
                    bValue = parseFloat(bValue);
                }
            }
            
            // المقارنة
            if (aValue < bValue) return ascending ? -1 : 1;
            if (aValue > bValue) return ascending ? 1 : -1;
            return 0;
        });
        
        // إعادة ترتيب الصفوف في الجدول
        rows.forEach(row => tbody.appendChild(row));
        
        // تحديث الأرقام التسلسلية بعد الترتيب
        updateSequenceNumbers(tbody);
    }
    
    function updateSequenceNumbers(tbody) {
        const rows = tbody.querySelectorAll('tr');
        const rowCount = rows.length;
        
        rows.forEach((row, index) => {
            const sequenceNumber = rowCount - index;
            row.cells[0].textContent = sequenceNumber;
        });
    }
    </script>
</body>
</html>
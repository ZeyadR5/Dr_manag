<?php
require_once 'config.php';

$id = $_GET['id'];

// استخدام التوثيق للجلب
$context = createOrthancContext();
$study = json_decode(@file_get_contents("$orthancUrl/studies/$id", false, $context), true);

if (!is_array($study)) {
    die("⚠️ فشل تحميل بيانات الدراسة من Orthanc. تحقق من الاتصال وبيانات الاعتماد.");
}

$rawName = $study['PatientMainDicomTags']['PatientName'] ?? 'غير معروف';
$patientName = preg_replace('/[^a-zA-Z0-9\s]/', '', $rawName);

$instances = json_decode(@file_get_contents("$orthancUrl/studies/$id/instances", false, $context), true);

if (!is_array($instances)) {
    $instances = [];
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <script src='tailwind.js'></script>
  <title>DICOM Viewer <?= htmlspecialchars($patientName) ?></title>
  <style>
    body {
      font-family: Arial;
      direction: rtl;
      background-color: #f0f0f0;
      padding: 0;
      margin: 0;
    }

    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 20px;
      background-color: #007bff;
      color: white;
    }

    .top-bar .back-button a {
      color: white;
      text-decoration: none;
      font-weight: bold;
    }

    .viewer-wrapper {
      display: flex;
      height: calc(100vh - 60px);
    }

    .thumbnails {
      width: 150px;
      overflow-y: auto;
      background-color: #ddd;
      padding: 10px;
    }

    .thumbnails img {
      width: 100%;
      margin-bottom: 10px;
      cursor: pointer;
      border: 2px solid transparent;
      transition: border 0.2s;
    }

    .thumbnails img:hover {
      border-color: #007bff;
    }

    .main-viewer {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      background: #fff;
      position: relative;
      overflow: hidden;
    }

    .main-image {
      max-width: 90%;
      max-height: 90%;
      border: 4px solid #222;
      transition: transform 0.1s ease, filter 0.3s ease;
      transform-origin: center center;
      cursor: grab;
    }

    .zoom-controls {
      position: absolute;
      top: 10px;
      left: 10px;
      background: rgba(255,255,255,0.8);
      padding: 5px 10px;
      border-radius: 6px;
      z-index: 1000;
    }

    .zoom-controls button {
      font-size: 16px;
      margin: 0 5px;
      cursor: pointer;
    }

    .adjustment-panel {
      position: absolute;
      left: 20px;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(255,255,255,0.8);
      padding: 15px;
      border-radius: 8px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 15px;
      z-index: 1000;
      width: 60px;
    }

    .adjustment-panel h3 {
      margin: 0;
      font-size: 14px;
      writing-mode: vertical-rl;
      text-orientation: mixed;
      transform: rotate(180deg);
    }

    .control-group {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
    }

    .control-group label {
      font-weight: bold;
      writing-mode: vertical-rl;
      text-orientation: mixed;
      transform: rotate(180deg);
    }

    .control-group input {
      width: 100px;
      height: 20px;
      transform: rotate(-90deg);
      margin: 30px 0;
    }

    .reset-btn {
      background-color: #f44336;
      color: white;
      border: none;
      padding: 8px 12px;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
      margin-top: 5px;
    }

    .reset-btn:hover {
      background-color: #d32f2f;
    }

    .value-display {
      font-size: 12px;
      margin-top: 5px;
      background: #eee;
      padding: 2px 5px;
      border-radius: 3px;
      min-width: 30px;
      text-align: center;
    }

    .rotate-controls {
      position: absolute;
      bottom: 20px;
      right: 20px;
      background: rgba(255,255,255,0.8);
      padding: 10px 15px;
      border-radius: 8px;
      z-index: 1000;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
    }

    .rotate-btn {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
      min-width: 120px;
      text-align: center;
    }

    .rotate-btn:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>

  <div class="top-bar">
    <div><strong>👤 اسم الحالة:</strong> <?= htmlspecialchars($patientName) ?></div>
    <div class="back-button"><a href="index.php">⬅️ العودة</a></div>
  </div>

  <div class="viewer-wrapper">
    <div class="thumbnails">
      <?php foreach ($instances as $instance): 
        $imgUrl = "$orthancUrl/instances/{$instance['ID']}/rendered"; ?>
        <img src="<?= $imgUrl ?>" data-full="<?= $imgUrl ?>" onclick="showImage(this)">
      <?php endforeach; ?>
    </div>

    <div class="main-viewer">
      <div class="zoom-controls">
        <button onclick="resetZoom()">🔄 إعادة التكبير</button> 
      </div>
      
      <div class="adjustment-panel">
        <div class="control-group">
          <label for="brightness">السطوع</label>
          <input type="range" id="brightness" min="-100" max="100" value="0" oninput="adjustImage()">
          <div class="value-display" id="brightness-value">0</div>
        </div>
        <div class="control-group">
          <label for="contrast">التباين</label>
          <input type="range" id="contrast" min="-100" max="100" value="0" oninput="adjustImage()">
          <div class="value-display" id="contrast-value">0</div>
        </div>
        <button class="reset-btn" onclick="resetAdjustments()">إعادة الضبط</button>
      </div>
      
      <!-- زر التدوير في الزاوية اليمنى السفلية -->
      <div class="rotate-controls">
        <button class="rotate-btn" onclick="rotateImage()">🔄 تدوير (0°)</button>
      </div>
      
      <img src="<?= "$orthancUrl/instances/{$instances[0]['ID']}/rendered" ?>" id="mainImage" class="main-image">
    </div>
  </div>

  <script>
    const mainImage = document.getElementById('mainImage');
    const brightnessSlider = document.getElementById('brightness');
    const contrastSlider = document.getElementById('contrast');
    const brightnessValue = document.getElementById('brightness-value');
    const contrastValue = document.getElementById('contrast-value');
    const rotateButton = document.querySelector('.rotate-btn');
    let isDragging = false, startX = 0, startY = 0;
    let rotation = 0; // زاوية التدوير الحالية

    mainImage.dataset.scale = "1";
    mainImage.dataset.posX = "0";
    mainImage.dataset.posY = "0";
    mainImage.dataset.rotation = "0"; // تخزين حالة التدوير

    function applyTransform() {
      const scale = parseFloat(mainImage.dataset.scale);
      const posX = parseFloat(mainImage.dataset.posX);
      const posY = parseFloat(mainImage.dataset.posY);
      const rotation = parseFloat(mainImage.dataset.rotation);
      
      // تطبيق التدوير بالإضافة إلى التكبير والتحريك
      mainImage.style.transform = `rotate(${rotation}deg) scale(${scale}) translate(${posX}px, ${posY}px)`;
    }

    function resetZoom() {
      mainImage.dataset.scale = "1";
      mainImage.dataset.posX = "0";
      mainImage.dataset.posY = "0";
      applyTransform();
    }

    function showImage(el) {
      mainImage.src = el.dataset.full;
      resetZoom();
      resetAdjustments();
      resetRotation(); // إعادة تعيين التدوير عند تغيير الصورة
    }

    function adjustImage() {
      const brightness = brightnessSlider.value;
      const contrast = contrastSlider.value;
      
      // تحديث قيم العرض
      brightnessValue.textContent = brightness;
      contrastValue.textContent = contrast;
      
      mainImage.style.filter = `brightness(${100 + parseInt(brightness)}%) contrast(${100 + parseInt(contrast)}%)`;
    }

    function resetAdjustments() {
      brightnessSlider.value = 0;
      contrastSlider.value = 0;
      brightnessValue.textContent = "0";
      contrastValue.textContent = "0";
      mainImage.style.filter = 'brightness(100%) contrast(100%)';
    }

    // دالة تدوير الصورة
    function rotateImage() {
      rotation = (rotation + 90) % 360; // زيادة التدوير بمقدار 90 درجة
      mainImage.dataset.rotation = rotation;
      applyTransform();
      
      // تحديث نص الزر ليعكس الزاوية الحالية
      rotateButton.textContent = `🔄 تدوير (${rotation}°)`;
    }

    // إعادة تعيين التدوير إلى الصفر
    function resetRotation() {
      rotation = 0;
      mainImage.dataset.rotation = "0";
      applyTransform();
      rotateButton.textContent = `🔄 تدوير (0°)`;
    }

    mainImage.addEventListener('wheel', function(e) {
      e.preventDefault();
      let scale = parseFloat(mainImage.dataset.scale);
      scale += (e.deltaY < 0) ? 0.1 : -0.1;
      scale = Math.max(0.2, scale);
      mainImage.dataset.scale = scale;
      applyTransform();
    });

    mainImage.addEventListener('mousedown', function(e) {
      e.preventDefault();
      isDragging = true;
      startX = e.clientX;
      startY = e.clientY;
      mainImage.style.cursor = 'grabbing';
    });

    mainImage.addEventListener('mousemove', function(e) {
      if (!isDragging) return;
      e.preventDefault();
      let dx = e.clientX - startX;
      let dy = e.clientY - startY;
      startX = e.clientX;
      startY = e.clientY;
      let posX = parseFloat(mainImage.dataset.posX) + dx;
      let posY = parseFloat(mainImage.dataset.posY) + dy;
      mainImage.dataset.posX = posX;
      mainImage.dataset.posY = posY;
      applyTransform();
    });

    mainImage.addEventListener('mouseup', () => { isDragging = false; mainImage.style.cursor = 'grab'; });
    mainImage.addEventListener('mouseleave', () => { isDragging = false; mainImage.style.cursor = 'grab'; });

    // Double click zoom
    mainImage.addEventListener('dblclick', function(e) {
      e.preventDefault();
      const rect = mainImage.getBoundingClientRect();
      const offsetX = e.clientX - rect.left;
      const offsetY = e.clientY - rect.top;

      let scale = parseFloat(mainImage.dataset.scale);
      scale += 0.5;
      mainImage.dataset.scale = scale;

      const centerX = rect.width / 2;
      const centerY = rect.height / 2;
      const deltaX = (offsetX - centerX);
      const deltaY = (offsetY - centerY);

      let posX = parseFloat(mainImage.dataset.posX) - deltaX * 0.5;
      let posY = parseFloat(mainImage.dataset.posY) - deltaY * 0.5;

      mainImage.dataset.posX = posX;
      mainImage.dataset.posY = posY;

      applyTransform();
    });

    // تهيئة أولية
    resetAdjustments();
  </script>

</body>
</html>
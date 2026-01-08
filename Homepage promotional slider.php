<?php
$images = [
    "Begie Modern New Collection Instagram Post.png",
    "1600w-_gIFXUJM76g.webp",
    "1600w-WaLf4Ccx5Ns.webp"
];
$totalSlides = count($images);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Homepage Slider</title>

    <!-- MERGED CSS -->
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="slider-container">

<div class="slides" id="slideBox">
    <?php foreach ($images as $img): ?>
        <div class="slide">
            <img src="<?= htmlspecialchars($img) ?>" alt="Slider Image">
        </div>
    <?php endforeach; ?>
</div>


    <div class="prev" onclick="moveSlide(-1)">&#10094;</div>
    <div class="next" onclick="moveSlide(1)">&#10095;</div>

    <div class="dots">
        <?php for ($i = 0; $i < $totalSlides; $i++): ?>
            <span class="dot <?= $i === 0 ? 'active-dot' : '' ?>"
                  onclick="currentSlide(<?= $i ?>)"></span>
        <?php endfor; ?>
    </div>

</div>

<!-- MERGED JS -->
<script src="script.js"></script>

</body>
</html>

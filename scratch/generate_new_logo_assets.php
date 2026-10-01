<?php

$srcPath = 'C:/Users/MM/.gemini/antigravity-ide/brain/bfc9453f-1f61-4c5b-b0cc-a49f90fa47c2/.user_uploaded/media_1790830053541.png';
if (!file_exists($srcPath)) {
    die("Source file not found: $srcPath\n");
}

$src = imagecreatefrompng($srcPath);
$sw = imagesx($src);
$sh = imagesy($src);

// Full logo bounds with slight padding
$paddingX = 4;
$paddingY = 4;
$logoMinX = max(0, 27 - $paddingX);
$logoMinY = max(0, 10 - $paddingY);
$logoMaxX = min($sw - 1, 299 + $paddingX);
$logoMaxY = min($sh - 1, 64 + $paddingY);

$cropW = $logoMaxX - $logoMinX + 1;
$cropH = $logoMaxY - $logoMinY + 1;

// 1. Create transparent full logo (with alpha blending)
$fullLogo = imagecreatetruecolor($cropW, $cropH);
imagealphablending($fullLogo, false);
imagesavealpha($fullLogo, true);
$transparent = imagecolorallocatealpha($fullLogo, 0, 0, 0, 127);
imagefilledrectangle($fullLogo, 0, 0, $cropW, $cropH, $transparent);

for ($y = 0; $y < $cropH; $y++) {
    for ($x = 0; $x < $cropW; $x++) {
        $rgb = imagecolorat($src, $logoMinX + $x, $logoMinY + $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;

        $luminance = ($r * 0.299 + $g * 0.587 + $b * 0.114);

        if ($luminance >= 252) {
            $alpha = 127; // transparent
        } elseif ($luminance <= 220) {
            $alpha = 0;   // opaque
        } else {
            // Anti-aliased edge smoothing
            $factor = ($luminance - 220) / (252 - 220);
            $alpha = (int)round($factor * 127);
        }

        if ($alpha < 127) {
            $col = imagecolorallocatealpha($fullLogo, $r, $g, $b, $alpha);
            imagesetpixel($fullLogo, $x, $y, $col);
        }
    }
}

// 2. High-Resolution 2x / 3x versions for razor sharp display on high-DPI
$scale = 3;
$hiResW = $cropW * $scale;
$hiResH = $cropH * $scale;

$hiResLogo = imagecreatetruecolor($hiResW, $hiResH);
imagealphablending($hiResLogo, false);
imagesavealpha($hiResLogo, true);
$trans = imagecolorallocatealpha($hiResLogo, 0, 0, 0, 127);
imagefilledrectangle($hiResLogo, 0, 0, $hiResW, $hiResH, $trans);
imagecopyresampled($hiResLogo, $fullLogo, 0, 0, 0, 0, $hiResW, $hiResH, $cropW, $cropH);

// 3. White background version
$fullLogoWhite = imagecreatetruecolor($hiResW, $hiResH);
$white = imagecolorallocate($fullLogoWhite, 255, 255, 255);
imagefilledrectangle($fullLogoWhite, 0, 0, $hiResW, $hiResH, $white);
imagealphablending($fullLogoWhite, true);
imagecopy($fullLogoWhite, $hiResLogo, 0, 0, 0, 0, $hiResW, $hiResH);

// Save full logos
imagepng($hiResLogo, 'c:/xampp/htdocs/door-step-bd-admin-api/public/logo.png');
imagejpeg($fullLogoWhite, 'c:/xampp/htdocs/door-step-bd-admin-api/public/logo.jpg', 95);

if (!is_dir('c:/xampp/htdocs/door-step-bd-admin-api/public/images')) {
    mkdir('c:/xampp/htdocs/door-step-bd-admin-api/public/images', 0777, true);
}
imagepng($hiResLogo, 'c:/xampp/htdocs/door-step-bd-admin-api/public/images/logo.png');
imagejpeg($fullLogoWhite, 'c:/xampp/htdocs/door-step-bd-admin-api/public/images/logo.jpg', 95);

echo "Full logos generated.\n";

// 4. Square Icon (Factory + Cloud Symbol) for Favicons & App Icons
$iconMinX = 27;
$iconMaxX = 86;
$iconMinY = 10;
$iconMaxY = 64;

$iconW = $iconMaxX - $iconMinX + 1;
$iconH = $iconMaxY - $iconMinY + 1;
$maxDim = max($iconW, $iconH);

// Create transparent square canvas with nice internal margin
$canvasSize = (int)round($maxDim * 1.15); // 15% breathing room
$squareIcon = imagecreatetruecolor($canvasSize, $canvasSize);
imagealphablending($squareIcon, false);
imagesavealpha($squareIcon, true);
$trans = imagecolorallocatealpha($squareIcon, 0, 0, 0, 127);
imagefilledrectangle($squareIcon, 0, 0, $canvasSize, $canvasSize, $trans);

$offsetX = (int)round(($canvasSize - $iconW) / 2);
$offsetY = (int)round(($canvasSize - $iconH) / 2);

for ($y = 0; $y < $iconH; $y++) {
    for ($x = 0; $x < $iconW; $x++) {
        $rgb = imagecolorat($src, $iconMinX + $x, $iconMinY + $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;

        $luminance = ($r * 0.299 + $g * 0.587 + $b * 0.114);

        if ($luminance >= 252) {
            $alpha = 127;
        } elseif ($luminance <= 220) {
            $alpha = 0;
        } else {
            $factor = ($luminance - 220) / (252 - 220);
            $alpha = (int)round($factor * 127);
        }

        if ($alpha < 127) {
            $col = imagecolorallocatealpha($squareIcon, $r, $g, $b, $alpha);
            imagesetpixel($squareIcon, $offsetX + $x, $offsetY + $y, $col);
        }
    }
}

// Function to generate resized icons
function createResizedIcon($sourceImg, $targetSize, $outputPath) {
    $sw = imagesx($sourceImg);
    $sh = imagesy($sourceImg);
    $resized = imagecreatetruecolor($targetSize, $targetSize);
    imagealphablending($resized, false);
    imagesavealpha($resized, true);
    $trans = imagecolorallocatealpha($resized, 0, 0, 0, 127);
    imagefilledrectangle($resized, 0, 0, $targetSize, $targetSize, $trans);
    imagecopyresampled($resized, $sourceImg, 0, 0, 0, 0, $targetSize, $targetSize, $sw, $sh);
    imagepng($resized, $outputPath);
    imagedestroy($resized);
}

createResizedIcon($squareIcon, 16, 'c:/xampp/htdocs/door-step-bd-admin-api/public/favicon-16x16.png');
createResizedIcon($squareIcon, 32, 'c:/xampp/htdocs/door-step-bd-admin-api/public/favicon-32x32.png');
createResizedIcon($squareIcon, 48, 'c:/xampp/htdocs/door-step-bd-admin-api/public/favicon-48x48.png');
createResizedIcon($squareIcon, 64, 'c:/xampp/htdocs/door-step-bd-admin-api/public/favicon.png');
createResizedIcon($squareIcon, 180, 'c:/xampp/htdocs/door-step-bd-admin-api/public/apple-touch-icon.png');
createResizedIcon($squareIcon, 192, 'c:/xampp/htdocs/door-step-bd-admin-api/public/android-chrome-192x192.png');
createResizedIcon($squareIcon, 512, 'c:/xampp/htdocs/door-step-bd-admin-api/public/android-chrome-512x512.png');

// Copy 32x32 to favicon.ico
copy('c:/xampp/htdocs/door-step-bd-admin-api/public/favicon-32x32.png', 'c:/xampp/htdocs/door-step-bd-admin-api/public/favicon.ico');

echo "Favicons and app icons generated successfully.\n";

<?php

$srcPath = 'C:/Users/MM/.gemini/antigravity-ide/brain/b00f6f97-fa73-40fd-8c88-b95ddb728ec7/door_step_bd_logo_1789541592047.jpg';
if (!file_exists($srcPath)) {
    die("Source file not found: $srcPath\n");
}

$src = imagecreatefromjpeg($srcPath);
$sw = imagesx($src);
$sh = imagesy($src);

// Full logo bounds with slight padding
$logoMinX = max(0, 167 - 20);
$logoMinY = max(0, 245 - 20);
$logoMaxX = min($sw - 1, 1097 + 20);
$logoMaxY = min($sh - 1, 602 + 20);

$cropW = $logoMaxX - $logoMinX + 1;
$cropH = $logoMaxY - $logoMinY + 1;

// 1. Create transparent full logo
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

        if ($luminance >= 250) {
            $alpha = 127; // completely transparent
        } elseif ($luminance <= 220) {
            $alpha = 0;   // completely opaque
        } else {
            // Anti-aliased edge smoothing
            $factor = ($luminance - 220) / (250 - 220);
            $alpha = (int)round($factor * 127);
        }

        if ($alpha < 127) {
            $col = imagecolorallocatealpha($fullLogo, $r, $g, $b, $alpha);
            imagesetpixel($fullLogo, $x, $y, $col);
        }
    }
}

// 2. Create white-bg full logo (JPEG / standard PNG)
$fullLogoWhite = imagecreatetruecolor($cropW, $cropH);
$white = imagecolorallocate($fullLogoWhite, 255, 255, 255);
imagefilledrectangle($fullLogoWhite, 0, 0, $cropW, $cropH, $white);
imagecopy($fullLogoWhite, $src, 0, 0, $logoMinX, $logoMinY, $cropW, $cropH);

// Save full logos
imagepng($fullLogo, 'c:/xampp/htdocs/door-step-bd-admin-api/public/logo.png');
imagejpeg($fullLogoWhite, 'c:/xampp/htdocs/door-step-bd-admin-api/public/logo.jpg', 95);

if (!is_dir('c:/xampp/htdocs/door-step-bd-admin-api/public/images')) {
    mkdir('c:/xampp/htdocs/door-step-bd-admin-api/public/images', 0777, true);
}
imagepng($fullLogo, 'c:/xampp/htdocs/door-step-bd-admin-api/public/images/logo.png');
imagejpeg($fullLogoWhite, 'c:/xampp/htdocs/door-step-bd-admin-api/public/images/logo.jpg', 95);

echo "Full logos generated successfully.\n";

// 3. Create Square Icon (Door + Bag Symbol) for Favicons & App Icons
$iconMinX = max(0, 167 - 10);
$iconMinY = max(0, 245 - 10);
$iconMaxX = 472;
$iconMaxY = min($sh - 1, 602 + 10);

$iconW = $iconMaxX - $iconMinX + 1;
$iconH = $iconMaxY - $iconMinY + 1;
$maxDim = max($iconW, $iconH);

// Create square canvas with transparent background
$squareIcon = imagecreatetruecolor($maxDim, $maxDim);
imagealphablending($squareIcon, false);
imagesavealpha($squareIcon, true);
$transparent = imagecolorallocatealpha($squareIcon, 0, 0, 0, 127);
imagefilledrectangle($squareIcon, 0, 0, $maxDim, $maxDim, $transparent);

$offsetX = (int)round(($maxDim - $iconW) / 2);
$offsetY = (int)round(($maxDim - $iconH) / 2);

for ($y = 0; $y < $iconH; $y++) {
    for ($x = 0; $x < $iconW; $x++) {
        $rgb = imagecolorat($src, $iconMinX + $x, $iconMinY + $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;

        $luminance = ($r * 0.299 + $g * 0.587 + $b * 0.114);

        if ($luminance >= 250) {
            $alpha = 127;
        } elseif ($luminance <= 220) {
            $alpha = 0;
        } else {
            $factor = ($luminance - 220) / (250 - 220);
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

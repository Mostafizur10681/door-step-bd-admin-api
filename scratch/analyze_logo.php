<?php

$srcPath = 'C:/Users/MM/.gemini/antigravity-ide/brain/bfc9453f-1f61-4c5b-b0cc-a49f90fa47c2/.user_uploaded/media_1790830053541.png';
$src = imagecreatefrompng($srcPath);
$w = imagesx($src);
$h = imagesy($src);

echo "Dimensions: $w x $h\n";

$minX = $w; $minY = $h; $maxX = 0; $maxY = 0;
$nonWhitePixels = 0;

for ($y = 0; $y < $h; $y++) {
    for ($x = 0; $x < $w; $x++) {
        $rgba = imagecolorat($src, $x, $y);
        $a = ($rgba >> 24) & 0x7F;
        $r = ($rgba >> 16) & 0xFF;
        $g = ($rgba >> 8) & 0xFF;
        $b = $rgba & 0xFF;
        
        // check if not pure white / transparent
        $isWhiteOrTransparent = ($a >= 120) || ($r > 245 && $g > 245 && $b > 245);
        if (!$isWhiteOrTransparent) {
            $nonWhitePixels++;
            if ($x < $minX) $minX = $x;
            if ($x > $maxX) $maxX = $x;
            if ($y < $minY) $minY = $y;
            if ($y > $maxY) $maxY = $y;
        }
    }
}

echo "Content Bounds: minX=$minX, minY=$minY, maxX=$maxX, maxY=$maxY (width=" . ($maxX - $minX + 1) . ", height=" . ($maxY - $minY + 1) . ")\n";

// Let's check icon bounds (left part of image)
$iconMinX = $minX; $iconMaxX = 0; $iconMinY = $h; $iconMaxY = 0;
// Icon is approximately from minX to around x=90
for ($y = $minY; $y <= $maxY; $y++) {
    for ($x = $minX; $x <= min($minX + 100, $w - 1); $x++) {
        $rgba = imagecolorat($src, $x, $y);
        $a = ($rgba >> 24) & 0x7F;
        $r = ($rgba >> 16) & 0xFF;
        $g = ($rgba >> 8) & 0xFF;
        $b = $rgba & 0xFF;
        
        $isWhiteOrTransparent = ($a >= 120) || ($r > 245 && $g > 245 && $b > 245);
        if (!$isWhiteOrTransparent) {
            if ($x > $iconMaxX) $iconMaxX = $x;
            if ($y < $iconMinY) $iconMinY = $y;
            if ($y > $iconMaxY) $iconMaxY = $y;
        }
    }
}
echo "Icon Bounds: minX=$iconMinX, minY=$iconMinY, maxX=$iconMaxX, maxY=$iconMaxY\n";

<?php

$srcPath = 'C:/Users/MM/.gemini/antigravity-ide/brain/bfc9453f-1f61-4c5b-b0cc-a49f90fa47c2/.user_uploaded/media_1790830053541.png';
$src = imagecreatefrompng($srcPath);
$sw = imagesx($src);
$sh = imagesy($src);

// Find exact non-white bounding box for full logo
$minX = $sw; $minY = $sh; $maxX = 0; $maxY = 0;

for ($y = 0; $y < $sh; $y++) {
    for ($x = 0; $x < $sw; $x++) {
        $rgba = imagecolorat($src, $x, $y);
        $r = ($rgba >> 16) & 0xFF;
        $g = ($rgba >> 8) & 0xFF;
        $b = $rgba & 0xFF;
        $a = ($rgba >> 24) & 0x7F;

        // Is this pixel part of the logo?
        // Note: The background is white (r>250, g>250, b>250) or transparent (a>120)
        if ($a < 120 && ($r < 248 || $g < 248 || $b < 248)) {
            if ($x < $minX) $minX = $x;
            if ($x > $maxX) $maxX = $x;
            if ($y < $minY) $minY = $y;
            if ($y > $maxY) $maxY = $y;
        }
    }
}

echo "Logo bounds: minX=$minX, minY=$minY, maxX=$maxX, maxY=$maxY\n";
echo "Width=" . ($maxX - $minX + 1) . ", Height=" . ($maxY - $minY + 1) . "\n";

// Factory icon bounds: x from minX up to the gap before the text 'DOORSTEP'
// We know gap is around x=87..98
$iconMinX = $minX;
$iconMaxX = 86;
$iconMinY = $minY;
$iconMaxY = $maxY;

// Find precise icon bounds
$iMinY = $sh; $iMaxY = 0; $iMinX = $sw; $iMaxX = 0;
for ($y = 0; $y < $sh; $y++) {
    for ($x = 0; $x <= 88; $x++) {
        $rgba = imagecolorat($src, $x, $y);
        $r = ($rgba >> 16) & 0xFF;
        $g = ($rgba >> 8) & 0xFF;
        $b = $rgba & 0xFF;
        $a = ($rgba >> 24) & 0x7F;
        if ($a < 120 && ($r < 248 || $g < 248 || $b < 248)) {
            if ($x < $iMinX) $iMinX = $x;
            if ($x > $iMaxX) $iMaxX = $x;
            if ($y < $iMinY) $iMinY = $y;
            if ($y > $iMaxY) $iMaxY = $y;
        }
    }
}

echo "Precise icon bounds: minX=$iMinX, minY=$iMinY, maxX=$iMaxX, maxY=$iMaxY\n";
echo "Icon Width=" . ($iMaxX - $iMinX + 1) . ", Icon Height=" . ($iMaxY - $iMinY + 1) . "\n";

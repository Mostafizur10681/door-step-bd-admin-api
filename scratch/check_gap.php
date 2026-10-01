<?php

$srcPath = 'C:/Users/MM/.gemini/antigravity-ide/brain/bfc9453f-1f61-4c5b-b0cc-a49f90fa47c2/.user_uploaded/media_1790830053541.png';
$src = imagecreatefrompng($srcPath);

$rgba = imagecolorat($src, 0, 0);
$a = ($rgba >> 24) & 0x7F;
$r = ($rgba >> 16) & 0xFF;
$g = ($rgba >> 8) & 0xFF;
$b = $rgba & 0xFF;
echo "Corner (0,0) RGBA: r=$r, g=$g, b=$b, a=$a\n";

// Let's check where the factory icon is and where the text begins
// Let's scan columns from x=27 to 299 to find the gap between icon and text
for ($x = 27; $x <= 299; $x++) {
    $hasPixel = false;
    for ($y = 10; $y <= 64; $y++) {
        $rgba = imagecolorat($src, $x, $y);
        $r = ($rgba >> 16) & 0xFF;
        $g = ($rgba >> 8) & 0xFF;
        $b = $rgba & 0xFF;
        if ($r < 240 || $g < 240 || $b < 240) {
            $hasPixel = true;
            break;
        }
    }
    if (!$hasPixel) {
        echo "Empty vertical column at x=$x\n";
    }
}

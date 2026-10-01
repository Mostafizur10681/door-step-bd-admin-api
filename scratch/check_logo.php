<?php

$srcPath = 'C:/Users/MM/.gemini/antigravity-ide/brain/bfc9453f-1f61-4c5b-b0cc-a49f90fa47c2/.user_uploaded/media_1790830053541.png';
if (!file_exists($srcPath)) {
    die("File not found\n");
}

$info = getimagesize($srcPath);
echo "Image dimensions: {$info[0]} x {$info[1]}, type: {$info['mime']}\n";

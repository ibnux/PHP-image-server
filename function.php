<?php
/**
 * Sort url function
 * base62 encode
 * https://stackoverflow.com/questions/4964197/converting-a-number-base-10-to-base-62-a-za-z0-9
 */
function encode62($num, $b=62) {
    $base='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $r = $num  % $b ;
    $res = $base[$r];
    $q = floor($num/$b);
    while ($q) {
        $r = $q % $b;
        $q =floor($q/$b);
        $res = $base[$r].$res;
    }
    return $res;
}


/**
 * Sort url function
 * base62 decode
 * https://stackoverflow.com/questions/4964197/converting-a-number-base-10-to-base-62-a-za-z0-9
 */
function decode62( $num, $b=62) {
    $base='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $limit = strlen($num);
    $res=strpos($base,$num[0]);
    for($i=1;$i<$limit;$i++) {
        $res = $b * $res + strpos($base,$num[$i]);
    }
    return $res;
}


/**
 * https://gist.github.com/janzikan/2994977
 * Resize image - preserve ratio of width and height.
 * resizeImage('image.jpg', 'resized.jpg', 200, 200);
 * @param string $sourceImage path to source JPEG image
 * @param string $targetImage path to final JPEG image file
 * @param int $maxWidth maximum width of final image (value 0 - width is optional)
 * @param int $maxHeight maximum height of final image (value 0 - height is optional)
 * @param int $quality quality of final image (0-100)
 * @return bool
 */
function resizeImage($sourceImage, $targetImage, $maxWidth, $maxHeight, $quality = 80)
{
    // Obtain image from given source file.
    if (!$image = @imagecreatefromjpeg($sourceImage))
    {
        return false;
    }

    // Get dimensions of source image.
    list($origWidth, $origHeight) = getimagesize($sourceImage);

    //cek apakah ukuran lebih besar dari batas maksimal
    if($origWidth>$maxWidth || $origHeight>$maxHeight){

        if ($maxWidth == 0)
        {
            $maxWidth  = $origWidth;
        }

        if ($maxHeight == 0)
        {
            $maxHeight = $origHeight;
        }

        // Calculate ratio of desired maximum sizes and original sizes.
        $widthRatio = $maxWidth / $origWidth;
        $heightRatio = $maxHeight / $origHeight;

        // Ratio used for calculating new image dimensions.
        $ratio = min($widthRatio, $heightRatio);

        // Calculate new image dimensions.
        $newWidth  = (int)$origWidth  * $ratio;
        $newHeight = (int)$origHeight * $ratio;

        // Create final image with new dimensions.
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        imagejpeg($newImage, $targetImage, $quality);

        // Free up the memory.
        imagedestroy($image);
        imagedestroy($newImage);
    }else{
        imagedestroy($image);
        if(move_uploaded_file($sourceImage,$targetImage)){
            return true;
        }else{
            return false;
        }
    }
    return true;
}

<?php
/**
 * Created by iBNuX
 */

 
/**
 * Image Resizer
 * /r/w/h/m/i/file.jpg
 * resize
 * width
 * height
 * mode
 * 0 proportional
 * 1 crop
 * 2 square from width
 * 
 * i/1/1.jpg
 * crop 512
 * r/512/512/1/i/1/1.jpg
*/


ini_set("memory_limit","80M");
//ini_set("show_error",false);
include('SimpleImage.php');

# prevent creation of new directories
$is_locked = false;

$paths = explode("/",explode("?",$_SERVER['REQUEST_URI'])[0]);

if($paths[1]!='r'){
    header("HTTP/1.0 404 Not Found");
	echo "Not found.\n";
	die();
}

$w = $paths[2]*1; //width
$h = $paths[3]*1; //height
$m = $paths[4]*1; //tipe
$f = $paths[5]."/".$paths[6]."/".$paths[7]; //file



$orig_file = $f;
$newFile = "r/$w/$h/$m/$f";
if(!file_exists("r/$w/$h/$m/".$paths[5]."/".$paths[6]."/"))
    mkdir("r/$w/$h/$m/".$paths[5]."/".$paths[6]."/",0755,true);

//jika file asli tidak ada, end
if(!file_exists($orig_file)){
	header("HTTP/1.0 404 Not Found");
	echo "Not found.\n";
	die();
}

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');
header("Content-Type: image/jpeg");

//jika sudah ada
if(file_exists($newFile)){
	readfile($newFile);
	die();
}

$new_width = $w;
if(!empty($h))
	$new_height = $h;
else
	$new_height = $w;

$image = new abeautifulsite\SimpleImage($orig_file);

# Crop
if ($m == "1")
{
	$image->thumbnail($new_width, $new_height);
}
else if ($m == "2")
{
	$image->fit_to_width($new_width);
}else{
    # aspect ratio resize
	$image->best_fit($new_width, $new_height);
}

# save and return the resized image file
$image->save($newFile,100,"jpg");

readfile($newFile);

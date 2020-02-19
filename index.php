<?php
/**
 * Created by iBNuX
 */

include 'vendor/autoload.php';
include 'config.php';
include 'function.php';

use \Firebase\JWT\JWT;
use Medoo\Medoo;

$userID = 0;

if($is_auth){
    if(empty($_SERVER['HTTP_X_ACCESS_TOKEN'])){
        if(file_exists($_FILES['photo']['tmp_name']))unlink($_FILES['photo']['tmp_name']);
        die(json_encode(array("status"=>"failed","message"=>'unauthorized')));
    }

    //AUTH CEHCK
    try{
        $decoded = JWT::decode($_SERVER['HTTP_X_ACCESS_TOKEN'], $jwt_secret, array('HS256'));
        //TODO change $decoded->userId to userid or unique id from jwt provided
        $userID = $decoded->userId;
        if($userID<1){
            if(file_exists($_FILES['photo']['tmp_name']))unlink($_FILES['photo']['tmp_name']);
            die(json_encode(array("status"=>"failed","message"=>'unauthorized')));
        }
    }catch(Exception $e){
        if(file_exists($_FILES['photo']['tmp_name']))unlink($_FILES['photo']['tmp_name']);
        die(json_encode(array("status"=>"failed","message"=>$e->getMessage())));
    }
}

$db = new Medoo([
	// required
	'database_type' => 'mysql',
	'database_name' => $db_name,
	'server' => $db_host,
	'username' => $db_user,
	'password' => $db_pass
]);

if(isset($_FILES['photo']) && isset($_FILES['photo']['name']))
{
	if(!$_FILES['photo']['error'])
	{
		if($_FILES["photo"]["type"]=="image/jpeg" || $_FILES["photo"]["type"]=="image/jpg"){
			if($_FILES['photo']['size'] > (10024000)) //can't be larger than 10 MB
			{
				if(file_exists($_FILES['photo']['tmp_name']))unlink($_FILES['photo']['tmp_name']);
				die(json_encode(array("status"=>"failed","message"=>'File size is to large.')));
			}else
			{
                $md5 = md5_file($_FILES['photo']['tmp_name']);
                $path = $db->get("images",'path',['md5'=>$md5]);
                if(!empty($path)){
                    //Jika sudah ada, balikin
                    if(file_exists($_FILES['photo']['tmp_name']))unlink($_FILES['photo']['tmp_name']);
					die(json_encode(array("status"=>"success","data"=>$path)));
                }
                $db->insert("images",['user_id'=>$userID, 'md5'=>$md5, 'size'=>filesize($_FILES['photo']['tmp_name']), 
                        'realname'=>$_FILES['photo']['name'], 'upload_date'=>date("Y-m-d H:i:s"),'path'=>'', 'content_type'=>$_FILES["photo"]["type"]]);
                $id = $db->id();
                if($id<1){
                    if(file_exists($_FILES['photo']['tmp_name']))unlink($_FILES['photo']['tmp_name']);
				    die(json_encode(array("status"=>"failed","message"=>'failed insert to DB')));
                }
                $kode = encode62($id);
                if(strlen($kode)>1){
                    $folder = substr($kode,0,2);
                }else{
                    $folder = substr($kode,0,1);
                }
                if(!file_exists("i/$folder/")) mkdir("i/$folder/");
				$path = "i/$folder/$kode.jpg";
				if(file_exists($path)){
                    //bisa jadi sebelumnya belum ke update
                    $db->update("images",['path'=>$path],['id'=>$id]);
					if(file_exists($_FILES['photo']['tmp_name']))unlink($_FILES['photo']['tmp_name']);
					die(json_encode(array("status"=>"success","data"=>$path)));
				}else{
                    if($is_resize){
                        //resize
                        if(resizeImage($_FILES['photo']['tmp_name'],$path, $max_wh, $max_wh, $img_quality)){
                            $db->update("images",['path'=>$path],['id'=>$id]);
                            if(file_exists($_FILES['photo']['tmp_name']))unlink($_FILES['photo']['tmp_name']);
                            die(json_encode(array("status"=>"success","data"=>$path)));
                        }else{
                            if(file_exists($_FILES['photo']['tmp_name']))unlink($_FILES['photo']['tmp_name']);
                            die(json_encode(array("status"=>"failed","message"=>'Error: failed to upload image..')));
                        }
                    }else{
                        //don't resize
                        if(move_uploaded_file($_FILES['photo']['tmp_name'],$path)){
                            $db->update("images",['path'=>$path],['id'=>$id]);
                            if(file_exists($_FILES['photo']['tmp_name']))unlink($_FILES['photo']['tmp_name']);
                            die(json_encode(array("status"=>"success","data"=>$path)));
                        }else{
                            if(file_exists($_FILES['photo']['tmp_name']))unlink($_FILES['photo']['tmp_name']);
                            die(json_encode(array("status"=>"failed","message"=>'Error: failed to upload image..')));
                        }
                    }
				}
			}
		}else{
			if(file_exists($_FILES['photo']['tmp_name']))unlink($_FILES['photo']['tmp_name']);
			die(json_encode(array("status"=>"failed","message"=>'Please upload only jpeg/jpg')));
		}
	}else{
		//set that to be the returned message
		if(file_exists($_FILES['photo']['tmp_name']))unlink($_FILES['photo']['tmp_name']);
		die(json_encode(array("status"=>"failed","message"=>'Error: Your upload triggered the following error:  '.$_FILES['photo']['error'])));
	}
}else
    die(json_encode(array("status"=>"failed","message"=>'Nothing to upload')));
<?php
/**
 * Created by PhpStorm.
 * User: yangtianfang
 * Date: 13/11/2016
 * Time: 10:19 PM
 */


define("HOST", "localhost");
define("DATABASE", "wordpress");
define("USERNAME", "wordpress");
define("PASSWORD", "wordpress");

function get_userinfo()
{
    $userIDs = $_POST["userIDs"];

    $userIDStr = "(";

    for($i = 0; $i < count($userIDs); $i++)
    {
        $userIDStr = $userIDStr . $userIDs[$i] . ",";
    }

    $userIDStr = $userIDStr . "0)";

    $query = "SELECT user_id, meta_key, meta_value FROM wp_usermeta where (meta_key='first_name'
 or meta_key='Passport' or meta_key='Passport_blur') and user_id in ".$userIDStr. "order by user_id, meta_key";

    $mysqli= mysqli_connect(HOST, DATABASE, USERNAME, PASSWORD);

    if ($mysqli) {

        $resultSet = $mysqli->query($query);
        $temp = Array();
        $data = Array();
        $firstName = "";
        $n = -1;

        while ($row = $resultSet->fetch_assoc()) {
            if ($row["meta_key"] == "first_name") {
                $firstName = $row["meta_value"];
            }
            else if($row["meta_key"] == "Passport"){
                $passport = $row["meta_value"];
                $temp[++$n] = array("userID"=> $row["user_id"], "name" => $firstName, "passport" => $passport);
            }
            else if($row["meta_key"] == "Passport_blur") {
                $temp[$n]["passport_blur"] = $row["meta_value"];
            }
        }

        $data["data"] = $temp;

        echo json_encode($data);
    }
}

function upload_id()
{
    $user_ID = $_POST["userID"];

    //If no passport is uploaded, exit
    if(!$_FILES['passport']['tmp_name'])
        return;

    $target_dir = "/wp-content/uploads/ID/";
    $target_file_name = basename($_FILES['passport']['name']);
    $uploadOk = 0;
    $imageFileType = pathinfo($target_file_name,PATHINFO_EXTENSION);
    $target_file = "/var/www/wordpress". $target_dir . "user_" . $user_ID ."_passport." . $imageFileType;
    $target_file1 = $target_dir . "user_" . $user_ID ."_passport." . $imageFileType;
    $target_file_blur = "/var/www/wordpress". $target_dir . "user_" . $user_ID ."_passport_blur." . $imageFileType;
    $target_file_blur1 = $target_dir . "user_" . $user_ID ."_passport_blur." . $imageFileType;
    $pp_expire_date = $_POST['pp_expiry_date'];

    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES['passport']['tmp_name']);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            //add_action( 'user_profile_update_errors', 'file_upload_error_not_image');
            return;
        }
    }
    // Check file size
    if($_FILES["passport"]["size"] > 500000) {
        //add_action( 'user_profile_update_errors', 'file_upload_error_too_large');
        return;
    }
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" )
    {
        //add_action( 'user_profile_update_errors', 'file_upload_error_invalid_format');
        return;
    }
    // if everything is ok, try to upload file
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES['passport']['tmp_name'], $target_file)) {
            $mysqli= mysqli_connect(HOST, DATABASE, USERNAME, PASSWORD);
            $im = new Imagick($target_file);
            $im_b = clone $im;
            $im_b->gaussianBlurImage(10, 10);
            $im_b->writeImage($target_file_blur);

            $query1 = "insert into wp_usermeta (user_id,meta_key,meta_value) values(".$user_ID.", 'Passport', '".$target_file1."')";
            $query2 = "insert into wp_usermeta (user_id,meta_key,meta_value) values(".$user_ID.",'passport_expire_date','".$pp_expire_date."')";
            $query3 = "insert into wp_usermeta (user_id,meta_key,meta_value) values(".$user_ID.", 'Passport_blur', '".$target_file_blur1."')";
            if ($mysqli) {
                if($mysqli->query($query1))
                    $mysqli->query($query2);
                $mysqli->query($query3);
            }
        }
    }

    header("Location: http://".$_SERVER[HTTP_HOST].$_POST['prevURL']);
    die();
}


switch($_POST["action"])
{
    case "getInfo":
        get_userinfo();
        break;
    case "uploadID":
        upload_id();
        break;
    default:
        break;
}


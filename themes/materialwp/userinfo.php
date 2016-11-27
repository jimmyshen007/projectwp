<?php
/**
 * Created by PhpStorm.
 * User: yangtianfang
 * Date: 13/11/2016
 * Time: 10:19 PM
 */

function get_userinfo()
{
    //global $wpdb;

    $userIDs = $_POST["userIDs"];

    $userIDStr = "(";

    for($i = 0; $i < count($userIDs); $i++)
    {
        $userIDStr = $userIDStr . $userIDs[$i] . ",";
    }

    $userIDStr = $userIDStr . "0)";

    $query = "SELECT user_id, meta_key, meta_value FROM wp_usermeta where (meta_key='first_name' or meta_key='last_name' or meta_key='Passport') and user_id in ".$userIDStr. "order by user_id, umeta_id";

    $mysqli= mysqli_connect("localhost", "wordpress", "wordpress", "wordpress");

    if ($mysqli) {

        $resultSet = $mysqli->query($query);
        $temp = Array();
        $data = Array();
        $firstName = "";
        $n = 0;
        $m = 0;

        while ($row = $resultSet->fetch_assoc()) {
            if ($row["meta_key"] == "first_name")
                $firstName = $row["meta_value"];
            else if($row["meta_key"] == "last_name"){
                $fullName = $firstName . " ". $row["meta_value"];
            }
            else if($row["meta_key"] == "Passport"){
                $passport = $row["meta_value"];
                $temp[$n++] = array("userID"=> $row["user_id"], "name" => $fullName, "passport" => $passport);
            }
        }

        $data["data"] = $temp;

        echo json_encode($data);
    }






    //$results = $wpdb->get_results( $query, ARRAY_A );

}

get_userinfo();

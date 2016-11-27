<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/27/16
 * Time: 12:09 AM
 */

// Method: POST, PUT, GET etc
// Data: array("param" => "value") ==> index.php?param=value
function CallAPI($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}

function cs_set_logged_in_cookie($logged_in_cookie, $expire, $expiration, $user_id, $scheme){
    if ( ! empty($_POST['log']) ) {
        $user_login = $_POST['log'];
        $user_login = sanitize_user($user_login);
    }
    if ( ! empty($_POST['pwd']) )
        $user_password = $_POST['pwd'];
    $result = CallAPI('POST', $_SERVER['HTTP_ORIGIN'] . '/wordpress/wp-json/jwt-auth/v1/token', array('username'=>$user_login,
        'password'=>$user_password));
    $result = json_decode($result);
    setcookie('API_TOKEN', $result->token, $expire, '/', null, false, true);
}

add_action( 'set_logged_in_cookie', cs_set_logged_in_cookie, 10, 5);

function cs_clear_cookie(){
    setcookie('API_TOKEN', ' ', time() - YEAR_IN_SECONDS, '/',   null);
}

add_action( 'clear_auth_cookie', cs_clear_cookie, 10);
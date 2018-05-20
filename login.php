<?php
require './include/init.php';

if (isPost()) {
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);
    $remember = (int) filter_input(INPUT_POST, "remember", FILTER_SANITIZE_NUMBER_INT);

    $hash = PasswordHash($password);
    $query = "SELECT * FROM user WHERE email='$email' AND password='$hash'";
//    $res = mysqli_query($db, $query);
//    if ($res == false && DEBUG) {
//        echo mysqli_error($db);
//    }
//    
//    $user = mysqli_fetch_assoc($res);
    
    $res = mysqliQuery($query);
    $user = $res->fetch_assoc();

    if ($user !== null) {
        $_SESSION['login'] = true;
        $_SESSION['user'] = $user;
        // md5, sha1, sha256, sha512 
        $secret = md5($password . $user['id']);
        if ($remember) {
            setcookie("login", 1, time() + 3600);
            setcookie("user_id", $user['id'], time() + 3600);
            setcookie("secret", $secret, time() + 3600);
        }

        redirect("gallery.php");
    }
}
?><!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <form method="post" action="">
            <label>Email: <input type="text" name="email"></label><br>
            <label>Password: <input type="password" name="password"></label><br>
            <label>Remember Me: <input type="checkbox" name="remember" value="1"></label><br>

            <input type="submit" value="ورود">
        </form>
    </body>
</html>

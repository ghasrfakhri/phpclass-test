<?php
require './include/init.php';
$msg = "";
if (isPost()) {
    $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_EMAIL);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);
    
    $hash = PasswordHash($password);
    $query = "INSERT INTO user SET name='$name', email='$email', password='$hash'";
    $res = mysqli_query($db, $query);
    if ($res == false && mysqli_errno($db) == 1062) {
        $msg = "Email already Exists";
    } else {
        $msg = "Regster Complete";

        $id = mysqli_insert_id($db);

        $_SESSION['login'] = true;
        $_SESSION['user'] = ["id" => $id, "name" => $name, "email" => $email];
        redirect("index.php");
    }
}
?><!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
<?= $msg ?>
        <form method="post" action="">
            <label>Name: <input type="text" name="name"></label><br>
            <label>Email: <input type="text" name="email"></label><br>
            <label>Password: <input type="password" name="password"></label><br>
            <label>Confirm Password: <input type="password" name="cpassword"></label><br>
            <input type="submit" value="register">
        </form>
    </body>
</html>

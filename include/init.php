<?php

define("SALT", "vkhsdksdfhskdfsdjkfkgjsdf");
define("DEBUG", 1);

session_start();

//$db = mysqli_connect("localhost", "root", "", "gallery");
//$db->query("SET NAMES 'utf8'");

$db = new mysqli("localhost", "root", "", "shop");
$db->query("SET NAMES 'utf8'");

/**
 * 
 * @global mysqli $db
 * @param type $query
 * @return mysqli_result
 */
function mysqliQuery($query) {
    global $db;

    $result = $db->query($query);

    if ($result == false) {
        if (DEBUG) {
            echo "Error:<br>";
            echo mysqli_error($db) . "<br>";
            echo $query;
        } else {
            echo "Database Error";
        }
    }

    return $result;
}

function isPost() {
    return filter_input(INPUT_SERVER, "REQUEST_METHOD") == "POST";
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function getReferer(){
    return filter_input(INPUT_SERVER, "HTTP_REFERER");
}

function isLogin() {
    if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
        return true;
    } else {
        if (filter_input(INPUT_COOKIE, "login") == 1 && !empty(filter_input(INPUT_COOKIE, "user_id")) && !empty(filter_input(INPUT_COOKIE, "secret"))) {
            $userId = filter_input(INPUT_COOKIE, "user_id");
            $secret = filter_input(INPUT_COOKIE, "secret");

            $user = getUserById($userId);
            if ($secret == md5($user['password'] . $user['id'] . SALT)) {
                $_SESSION['login'] = true;
                $_SESSION['user'] = $user;
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

function getUserById($userId) {
    global $db;
    $query = "SELECT * FROM user WHERE id = $userId";
    $res = mysqli_query($db, $query);
    if ($res == false) {
        echo mysqli_error($db);
    }
    return mysqli_fetch_assoc($res);
}

function getLoginUserId() {
    return $_SESSION['user']['id'];
}

function getExt($filename) {
    return substr($filename, strrpos($filename, ".") + 1);
}

function PasswordHash($password) {
    return sha1($password . md5($password) . SALT);
}

function getProduct($id) {
    $query = "SELECT * FROM product WHERE id=$id";
    $result = mysqliQuery($query);
    return $result->fetch_assoc();
}

function getProductComments($productId) {
    $query = "SELECT *, comment.id as comment_id FROM comment INNER JOIN user ON user.id=comment.user_id WHERE product_id=$productId";
    $result = mysqliQuery($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function addProductComment($productId, $comment) {
    $userId = getLoginUserId();

    $query = "INSERT INTO comment SET user_id=$userId, product_id=$productId, comment='$comment'";
    mysqliQuery($query);
}


function commentVote($commentId, $userId, $vote) {
    $query = "REPLACE INTO comments_votes SET user_id=$userId, comment_id=$commentId,vote=$vote";
    $result = mysqliQuery($query);

    $query = "UPDATE comment SET "
            . "         up_vote=(SELECT COUNT(*) FROM comments_votes WHERE comment_id=$commentId AND vote>0), "
            . "         down_vote=(SELECT COUNT(*) FROM comments_votes WHERE comment_id=$commentId AND vote<0) "
            . "WHERE id=$commentId";
    $result = mysqliQuery($query);
}

function hasVote($commentId, $userId, $vote){
    $query = "SELECT COUNT(*) FROM comments_votes WHERE comment_id=$commentId AND user_id=$userId AND vote=$vote";
    $result = mysqliQuery($query);
    list($c) = $result->fetch_row();
    return $c;
}

function revokeCommentVote($commentId, $userId, $vote){
    $query = "DELETE FROM comments_votes WHERE comment_id=$commentId AND user_id=$userId";
    $result = mysqliQuery($query);
    
    $query = "UPDATE comment SET "
            . "         up_vote=(SELECT COUNT(*) FROM comments_votes WHERE comment_id=$commentId AND vote>0), "
            . "         down_vote=(SELECT COUNT(*) FROM comments_votes WHERE comment_id=$commentId AND vote<0) "
            . "WHERE id=$commentId";
    $result = mysqliQuery($query);
}

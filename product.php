<?php
require './include/init.php';

$id = (int) filter_input(INPUT_GET, "id");

$product = getProduct($id);

if (!$product) {
    echo "No product fouund with this id.";
}

if (isPost()) {
    $comment = filter_input(INPUT_POST, "comment");
    addProductComment($id, $comment);
}

$comments = getProductComments($id);
?><!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <h1><?= $product['name'] ?></h1>
        <span><?= $product['price'] ?></span>
        <p><?= $product['description'] ?></p>

        <?php if (isLogin()) { ?>
            <form method="post" action="">
                Comment: <textarea name="comment"></textarea>
                <br>
                <input type="submit" value="Send">
            </form>    
            <?php
        }
        if (isLogin()) {
            $userId = getLoginUserId();
        }
        foreach ($comments as $comment) {
            echo "$comment[comment] $comment[name]";
            if (isLogin()) {
                echo "($comment[up_vote]";
                if (hasVote($comment['comment_id'], $userId, 1)) {
                    echo "<a href='comment_vote_revoke.php?id=$comment[comment_id]'>x</a>";
                } else {
                    echo "<a href='comment_vote.php?id=$comment[comment_id]&vote=1'>+</a>";
                }
                echo "$comment[down_vote]";
                if (hasVote($comment['comment_id'], $userId, -1)) {
                    echo "<a href='comment_vote_revoke.php?id=$comment[comment_id]'>x</a>)";
                } else {
                    echo "<a href='comment_vote.php?id=$comment[comment_id]&vote=-1'>-</a>)";
                }
            } else {
                echo "($comment[up_vote] $comment[down_vote])<br>";
            }
            echo "<br>";
        }
        ?>

    </body>
</html>

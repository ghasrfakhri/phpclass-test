<?php

require './include/init.php';
if (!isLogin()) {
    redirect("index.php");
}

$commentId = (int) filter_input(INPUT_GET, "id");
$vote = (int) filter_input(INPUT_GET, "vote");

if ($vote > 0) {
    $vote = 1;
} else {
    $vote = -1;
}

$userId = getLoginUserId();

$query = "INSERT INTO comments_votes SET user_id=$userId, comment_id=$commentId,vote=$vote";
$result = mysqliQuery($query);

if ($result == true) {
    if ($vote > 0) {
        $query = "UPDATE comment SET up_vote=up_vote+1 WHERE id=$commentId";
    } else {
        $query = "UPDATE comment SET down_vote=down_vote+1 WHERE id=$commentId";
    }
    $result = mysqliQuery($query);
}
redirect("product.php?id=1");

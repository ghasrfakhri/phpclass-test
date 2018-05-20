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

commentVote($commentId, $userId, $vote);

if(getReferer()){
    redirect(getReferer());
}else{
    redirect("index.php");
}

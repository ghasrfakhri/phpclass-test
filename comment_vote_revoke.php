<?php

require './include/init.php';
if (!isLogin()) {
    redirect("index.php");
}
$commentId = (int) filter_input(INPUT_GET, "id");
$userId = getLoginUserId();
revokeCommentVote($commentId, $userId, $vote);
redirect("product.php?id=1");

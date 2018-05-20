<?php

require './include/init.php';

unset($_SESSION['login']);
unset($_SESSION['user']);
redirect("index.php");

<?php

// Your logout code goes here.
// end session
session_start();
session_destroy();
header('Location: index.php');
?>

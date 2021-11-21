<?php
session_start();
session_destroy(); //sesionları silip login.php ye gönlendiriyoruz.
header("location:login.php");
?>
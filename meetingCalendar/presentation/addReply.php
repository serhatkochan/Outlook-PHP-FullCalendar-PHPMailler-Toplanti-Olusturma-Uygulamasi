<?php
if(isset($_POST)){
    require_once('../business/BusinessManager.php');
    $businessManager = new BusinessManager('MySQLDAO');

    $reply = '';
    $uid = '';
    $usernameAttendee = '';
    $realnameAttendee = '';
    $mailAttendee = '';

    if(isset($_POST['reply']) && isset($_POST['uid']) && isset($_POST['realnameAttendee']) && isset($_POST['mailAttendee']) && isset($_POST['usernameAttendee'])){
        $reply = htmlspecialchars(strip_tags(addslashes(trim($_POST['reply']))));
        $uid = htmlspecialchars(strip_tags(addslashes(trim($_POST['uid']))));
        $usernameAttendee = htmlspecialchars(strip_tags(addslashes(trim($_POST['usernameAttendee']))));
        $realnameAttendee = htmlspecialchars(strip_tags(addslashes(trim($_POST['realnameAttendee']))));
        $mailAttendee = htmlspecialchars(strip_tags(addslashes(trim($_POST['mailAttendee']))));
    }
    if(empty($reply) || empty($uid) || empty($realnameAttendee) || empty($mailAttendee)){
        die('Bilinmeyen bir hata oluştu. Lütfen yöneticiniz ile iletişime geçin.');
    }

    $businessManager->cevapOlustur($uid, $usernameAttendee, $reply); // veritabanina kayit eder
    $businessManager->toplantiDavetiyesiGonder($uid, $realnameAttendee, $mailAttendee, $reply); // kabul veya belki ise davetiye yollar.

    die('cevap olusturuldu');
}
else{
    die('post gelmedi');
}



?>
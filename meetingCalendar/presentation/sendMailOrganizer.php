<?php
if(isset($_POST)){
    require_once ('../business/BusinessManager.php');
    $businessManager = new BusinessManager('MySQLDAO');

    $description = '';
    $usernameAttendee = '';
    $mailOrganizer = '';
    $mailAttendee = '';
    $realnameOrganizer = '';
    $realnameAttendee = '';
    $uid = '';
    $reply = '';
    if(isset($_POST['mailDescription'])){
        $description = htmlspecialchars(strip_tags(addslashes(trim($_POST['mailDescription']))));
    }
    if(empty($description)){
        die('Lütfen mesajı boş bırakmayın.');
    }
    if(isset($_POST['replyMailUsernameAttendee']) && isset($_POST['replyMailMailOrganizer']) && isset($_POST['replyMailMailAttendee']) && isset($_POST['replyMailRealnameOrganizer']) &&
        isset($_POST['replyMailRealnameAttendee']) && isset($_POST['replyMailUid']) && isset($_POST['replyMailSend'])){
        $mailOrganizer = htmlspecialchars(strip_tags(addslashes(trim($_POST['replyMailMailOrganizer']))));
        $mailAttendee = htmlspecialchars(strip_tags(addslashes(trim($_POST['replyMailMailAttendee']))));
        $realnameOrganizer = htmlspecialchars(strip_tags(addslashes(trim($_POST['replyMailRealnameOrganizer']))));
        $realnameAttendee = htmlspecialchars(strip_tags(addslashes(trim($_POST['replyMailRealnameAttendee']))));
        $uid = htmlspecialchars(strip_tags(addslashes(trim($_POST['replyMailUid']))));
        $reply = htmlspecialchars(strip_tags(addslashes(trim($_POST['replyMailSend']))));
        $usernameAttendee = htmlspecialchars(strip_tags(addslashes(trim($_POST['replyMailUsernameAttendee']))));
    }
    if(empty($mailOrganizer) || empty($mailAttendee) || empty($realnameOrganizer) || empty($realnameAttendee) || empty($uid) || empty($reply) ){
        die('Bilinmeyen bir hata olustu. Lütfen yöneticinize bildirin.');
    }


    $businessManager->toplantiDavetiyesiGonder($uid, $realnameAttendee, $mailAttendee, $reply);
    $businessManager->organizereMailGonder($realnameOrganizer, $mailOrganizer, $realnameAttendee, $description, $uid);
    $businessManager->cevapOlustur($uid, $usernameAttendee, $reply);


    die('cevap olusturuldu');
}
else{
    die('else post gelmedi');
}

?>
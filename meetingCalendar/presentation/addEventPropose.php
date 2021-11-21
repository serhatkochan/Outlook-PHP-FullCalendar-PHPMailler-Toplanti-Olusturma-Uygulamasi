<?php
if(isset($_POST)){
    require_once ('../business/BusinessManager.php');
    $businessManager = new BusinessManager('MySQLDAO');

    $message = '';
    $summary = '';
    $usernameOrganizer = '';
    $realnameOrganizer = '';
    $usernameAttendee = '';
    $startDate = '';
    $startTime = '';
    $endDate = '';
    $endTime = '';
    $location = '';
    $priority = '';
    $class = '';
    $description = '';
    $uid = '';

    if(isset($_POST['uid'])){ // organize edenin gercek ismi
        $uid = htmlspecialchars(strip_tags(addslashes(trim($_POST['uid']))));
    }
    if(empty($uid)){
        die('Bilinmeyen bir hata olustu. Lütfen yöneticinize bildirin.');
    }
    if(isset($_POST['proposeSummary'])){ // baslik post edildiyse
        $summary = htmlspecialchars(strip_tags(addslashes(trim($_POST['proposeSummary']))));
    }
    if(empty($summary)){
        $message .= ', Toplantı Başlığı';
    }
    if(isset($_POST['realnameOrganizer'])){ // organize edenin gercek ismi
        $realnameOrganizer = htmlspecialchars(strip_tags(addslashes(trim($_POST['realnameOrganizer']))));
    }
    if(empty($realnameOrganizer)){
        die('Bilinmeyen bir hata olustu. Lütfen yöneticinize bildirin.');
    }
    if(isset($_POST['proposeRealnameAttendee'])){
        $realnamePropose = htmlspecialchars(strip_tags(addslashes(trim($_POST['proposeRealnameAttendee']))));
    }
    if(empty($realnamePropose)){
        die('Bilinmeyen bir hata olustu. Lütfen yöneticinize bildirin.');
    }
    if(isset($_POST['mailOrganizer'])){ // organize edenin gercek ismi
        $mailOrganizer = htmlspecialchars(strip_tags(addslashes(trim($_POST['mailOrganizer']))));
    }
    if(empty($mailOrganizer)){
        die('Bilinmeyen bir hata olustu. Lütfen yöneticinize bildirin.');
    }
    if(isset($_POST['mailAttendee'])){ // organize edenin gercek ismi
        $mailAttendee = htmlspecialchars(strip_tags(addslashes(trim($_POST['mailAttendee']))));
    }
    if(empty($mailAttendee)){
        die('2Bilinmeyen bir hata olustu. Lütfen yöneticinize bildirin.');
    }
    if(isset($_POST['proposeAttendeeUsername'])){ // oneriyi yapanin usernamesi
        $usernamePropose = htmlspecialchars(strip_tags(addslashes(trim($_POST['proposeAttendeeUsername']))));
    }
    if(empty($usernamePropose)){
        die('Bilinmeyen bir hata olustu. Lütfen yöneticinize bildirin.');
    }
    if(isset($_POST['proposeUsernameAttendee'])){ // önerilen katilimcilar
        $usernameAttendee = $_POST['proposeUsernameAttendee'];
    }
    if(empty($usernameAttendee)){ // post edilsede bir sekilde bos gelebilir kontrolu.
        $message .= ', Katılımcı Bilgisi';
    }
    if(isset($_POST['proposeStartDate']) && isset($_POST['proposeStartTime']) && isset($_POST['proposeEndDate']) && isset($_POST['proposeEndTime'])){
        $startDate = htmlspecialchars(strip_tags(addslashes(trim($_POST['proposeStartDate']))));
        $startTime = htmlspecialchars(strip_tags(addslashes(trim($_POST['proposeStartTime']))));
        $endDate = htmlspecialchars(strip_tags(addslashes(trim($_POST['proposeEndDate']))));
        $endTime = htmlspecialchars(strip_tags(addslashes(trim($_POST['proposeEndTime']))));
    }
    if(empty($startDate) || empty($startTime) || empty($endDate) || empty($endTime)){
        $message .= ', Tarih Bilgisi';
    }
    if(isset($_POST['proposeLocation'])){
        $location = htmlspecialchars(strip_tags(addslashes(trim($_POST['proposeLocation']))));
    }
    else if(isset($_POST['proposeLocationForeign'])){
        $location = htmlspecialchars(strip_tags(addslashes(trim($_POST['proposeLocationForeign']))));
    }
    if(empty($location)){
        $message .= ', Konum Bilgisi';
    }
    if(isset($_POST['proposePriority'])){
        $priority = htmlspecialchars(strip_tags(addslashes(trim($_POST['proposePriority']))));
    }
    if(empty($priority)){
        $message .= ', Önem Düzeyi';
    }
    if(isset($_POST['proposeClass'])){
        $class = htmlspecialchars(strip_tags(addslashes(trim($_POST['proposeClass']))));
    }
    else{
        $class = 'PUBLIC';
    }
    if(empty($class)){
        $message = 'Bilinmeyen bir hata olustu. Lütfen yöneticinize bildirin.';
    }
    if(isset($_POST['proposeDescription'])){
        $description = htmlspecialchars(strip_tags(addslashes(trim($_POST['proposeDescription']))));
    }
    if(isset($_POST['reply'])){
        $reply = htmlspecialchars(strip_tags(addslashes(trim($_POST['reply']))));
    }
    if(empty($reply)){
        $message = 'Bilinmeyen bir hata olustu. Lütfen yöneticinize bildirin.';
    }

    if(!empty($message)){
        $message .= ' Boş Bırakılmamalı.';
        $message = trim(ltrim($message, ','));
        die($message);
    }


    // oneriyi veritabanina kayit eder, organizere oneri var maili gider.
    $businessManager->oneriOlustur($uid, $mailOrganizer, $mailAttendee, $realnameOrganizer, $usernamePropose, $realnamePropose,
        $summary, $usernameAttendee, $startDate, $startTime, $endDate, $endTime,
        $location, $priority, $class, $description, $reply);
    // verilen cevaba gore katilimciya .ics uzantili toplanti davetiyesi gider.
    $businessManager->toplantiDavetiyesiGonder($uid, $realnamePropose, $mailAttendee, $reply);
    $businessManager->cevapOlustur($uid, $usernamePropose, $reply); // veritabanina kayit edilir cevap.

    die('cevap olusturuldu');
}
?>
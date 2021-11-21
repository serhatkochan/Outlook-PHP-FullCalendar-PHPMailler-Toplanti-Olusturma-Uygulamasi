<?php
if(isset($_POST)){
    require_once ('../business/BusinessManager.php');
    $businessManager = new BusinessManager('MySQLDAO');

    $message = '';
    $summary = '';
    $startDate = '';
    $startTime = '';
    $endDate = '';
    $endTime = '';
    $location = '';
    $priority = '';
    $class = '';
    $description = '';
    $uid = '';
    $sequance = '';
    $delete = '';
    $usernameOrganizer = '';
    $realnameOrganizer = '';
    $usernameOldAttendee = '';

    if(isset($_POST['changeUid'])){ // organize edenin gercek ismi
        $uid = htmlspecialchars(strip_tags(addslashes(trim($_POST['changeUid']))));
    }
    if(empty($uid)){
        die('Bilinmeyen bir hata olustu. Lütfen yöneticinize bildirin.');
    }
    if(isset($_POST['usernameOldAttendee'])){ // organize edenin gercek ismi
        $usernameOldAttendee = htmlspecialchars(strip_tags(addslashes(trim($_POST['usernameOldAttendee']))));
    }
    if(empty($usernameOldAttendee)){
        die('Bilinmeyen bir hata olustu. Lütfen yöneticinize bildirin.');
    }
    if(isset($_POST['changeSequance'])){
        $sequance = htmlspecialchars(strip_tags(addslashes(trim($_POST['changeSequance']))));
    }
    if(empty($sequance)){
        $sequance = 0;
    }
    if(isset($_POST['changeUsernameOrganizer'])){ // organize edenin gercek ismi
        $usernameOrganizer = htmlspecialchars(strip_tags(addslashes(trim($_POST['changeUsernameOrganizer']))));
    }
    if(empty($usernameOrganizer)){
        die('Bilinmeyen bir hata olustu. Lütfen yöneticinize bildirin.');
    }
    if(isset($_POST['changeRealnameOrganizer'])){ // organize edenin gercek ismi
        $realnameOrganizer = htmlspecialchars(strip_tags(addslashes(trim($_POST['changeRealnameOrganizer']))));
    }
    if(empty($realnameOrganizer)){
        die('Bilinmeyen bir hata olustu. Lütfen yöneticinize bildirin.');
    }
    if(isset($_POST['changeDelete'])){
        $delete = htmlspecialchars(strip_tags(addslashes(trim($_POST['changeDelete']))));
    }
    if(isset($_POST['changeSummary'])){ // baslik post edildiyse
        $summary = htmlspecialchars(strip_tags(addslashes(trim($_POST['changeSummary']))));
    }
    if(empty($summary)){
        $message .= ', Toplantı Başlığı';
    }
    if(isset($_POST['changeUsernameAttendee'])){ // önerilen katilimcilar
        $usernameAttendee = $_POST['changeUsernameAttendee'];
    }
    if(empty($usernameAttendee)){ // post edilsede bir sekilde bos gelebilir kontrolu.
        $message .= ', Katılımcı Bilgisi';
    }
    if(isset($_POST['changeStartDate']) && isset($_POST['changeStartTime']) && isset($_POST['changeEndDate']) && isset($_POST['changeEndTime'])){
        $startDate = htmlspecialchars(strip_tags(addslashes(trim($_POST['changeStartDate']))));
        $startTime = htmlspecialchars(strip_tags(addslashes(trim($_POST['changeStartTime']))));
        $endDate = htmlspecialchars(strip_tags(addslashes(trim($_POST['changeEndDate']))));
        $endTime = htmlspecialchars(strip_tags(addslashes(trim($_POST['changeEndTime']))));
    }
    if(empty($startDate) || empty($startTime) || empty($endDate) || empty($endTime)){
        $message .= ', Tarih Bilgisi';
    }
    if(isset($_POST['changeLocation'])){
        $location = htmlspecialchars(strip_tags(addslashes(trim($_POST['changeLocation']))));
    }
    else if(isset($_POST['changeLocationForeign'])){
        $location = htmlspecialchars(strip_tags(addslashes(trim($_POST['changeLocationForeign']))));
    }
    if(empty($location)){
        $message .= ', Konum Bilgisi';
    }
    if(isset($_POST['changePriority'])){
        $priority = htmlspecialchars(strip_tags(addslashes(trim($_POST['changePriority']))));
    }
    if(empty($priority)){
        $message .= ', Önem Düzeyi';
    }
    if(isset($_POST['changeClass'])){
        $class = htmlspecialchars(strip_tags(addslashes(trim($_POST['changeClass']))));
    }
    else{
        $class = 'PUBLIC';
    }
    if(isset($_POST['changeDescription'])){
        $description = htmlspecialchars(strip_tags(addslashes(trim($_POST['changeDescription']))));
    }
    if(!empty($message)){
        $message .= ' Boş Bırakılmamalı.';
        $message = trim(ltrim($message, ','));
        die($message);
    }

    if($delete == 'delete'){
        echo '<script>location.href = "index.php?ok=ok"</script>';
        $businessManager->toplantiSil($uid);
    }
    else{
        // veritabanindaki toplantiyi gunceller, katilimcilara guncellemeyle ilgili mail gonderir.
        $businessManager->toplantiGuncelle($uid, $summary, $usernameOrganizer, $realnameOrganizer, $usernameOldAttendee, $usernameAttendee, $startDate, $startTime, $endDate, $endTime,
            $location, $priority, $class, $description, $sequance);
    }

    echo('islem tamamlandi');
}

?>
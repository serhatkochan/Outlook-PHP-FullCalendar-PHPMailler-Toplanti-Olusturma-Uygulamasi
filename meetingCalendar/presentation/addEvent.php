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
    if(isset($_POST['summary'])){ // baslik post edildiyse
        $summary = htmlspecialchars(strip_tags(addslashes(trim($_POST['summary']))));
        if(empty($summary)){
            $message .= ', Toplantı Başlığı';
        }
    }
    if(isset($_POST['usernameOrganizer'])){
        $usernameOrganizer = htmlspecialchars(strip_tags(addslashes(trim($_POST['usernameOrganizer']))));
        if(empty($usernameOrganizer)){
            die('Bilinmeyen bir hata olustu. Lütfen yöneticinize bildirin.');
        }
    }
    if(isset($_POST['realnameOrganizer'])){
        $realnameOrganizer = htmlspecialchars(strip_tags(addslashes(trim($_POST['realnameOrganizer']))));
        if(empty($realnameOrganizer)){
            die('Bilinmeyen bir hata olustu. Lütfen yöneticinize bildirin.');
        }
    }
    if(isset($_POST['usernameAttendee'])){
        $usernameAttendee = $_POST['usernameAttendee'];
        if(empty($usernameAttendee)){ // post edilsede bir sekilde bos gelebilir kontrolu.
            $message .= ', Katılımcı Bilgisi';
        }
    }
    else{
        $message .= ', Katılımcı Bilgisi';
    }
    if(isset($_POST['startDate']) && isset($_POST['startTime']) && isset($_POST['endDate']) && isset($_POST['endTime'])){
        $startDate = htmlspecialchars(strip_tags(addslashes(trim($_POST['startDate']))));
        $startTime = htmlspecialchars(strip_tags(addslashes(trim($_POST['startTime']))));
        $endDate = htmlspecialchars(strip_tags(addslashes(trim($_POST['endDate']))));
        $endTime = htmlspecialchars(strip_tags(addslashes(trim($_POST['endTime']))));
        if(empty($startDate) || empty($startTime) || empty($endDate) || empty($endTime)){
            $message .= ', Tarih Bilgisi';
        }
    }
    if(isset($_POST['location'])){
        $location = htmlspecialchars(strip_tags(addslashes(trim($_POST['location']))));
    }
    else if(isset($_POST['locationForeign'])){
        $location = htmlspecialchars(strip_tags(addslashes(trim($_POST['locationForeign']))));
    }
    if(empty($location)){
        $message .= ', Konum Bilgisi';
    }
    if(isset($_POST['priority'])){
        $priority = htmlspecialchars(strip_tags(addslashes(trim($_POST['priority']))));
        if(empty($priority)){
            $message .= ', Önem Düzeyi';
        }
    }
    if(!empty($message)){
        $message .= ' Boş Bırakılmamalı.';
        $message = trim(ltrim($message, ','));
        die($message);
    }
    if(isset($_POST['class'])){
        $class = htmlspecialchars(strip_tags(addslashes(trim($_POST['class']))));
        if(empty($class)){
            $message = 'Bilinmeyen bir hata olustu. Lütfen yöneticinize bildirin.';
        }
    }
    else{
        $class = 'PUBLIC';
    }
    if(isset($_POST['description'])){
        $description = htmlspecialchars(strip_tags(addslashes(trim($_POST['description']))));
    }

    $businessManager->toplantiOlustur($summary, $usernameOrganizer, $realnameOrganizer, $usernameAttendee, $startDate, $startTime, $endDate, $endTime,
        $location, $priority, $class, $description);

    die('olusturuldu');
}
else{
    die('post gelmedi');
}

?>
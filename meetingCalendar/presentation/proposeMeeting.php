<?php
require_once('../business/BusinessManager.php');
$businessManager = new BusinessManager('MySQLDAO');


$uid = ''; // get olarak uid gelir
$usernamePropose = ''; // get olarak katilimci usernamesi gelirse onun onerisini gosterecez.
$usernameOrganizer = '';
$usernameAttendeeTotal = ''; // toplantidaki katilimcilar
$realnameOrganizer = ''; // x kisinin toplantisi
$realnamePropose = ''; // x kisisinin onerisi
$mailOrganizer = '';
$mailPropose= '';
$summary = '';
$dtStart = '';
$dtEnd = '';
$location = '';
$class = '';
$priority = '';
$description = '';
$sequance = '';

$toplantiBilgileri = ''; // toplanti bilgilerini tutacaz.
$modalHedarMessage = '';
$reply = '';
$textDegisiklikYap = 'Toplantıda Değişiklik Yap';
$displayOneriyiKabulEt = 'none';

if(isset($_GET['usernamePropose'])){
    $usernamePropose = htmlspecialchars(strip_tags(addslashes(trim($_GET['usernamePropose']))));
    $textDegisiklikYap = 'Öneride Değişiklik Yap';
    $displayOneriyiKabulEt = 'inline';
}
if(isset($_GET['uid'])){
    $uid = htmlspecialchars(strip_tags(addslashes(trim($_GET['uid']))));
}
if(isset($_GET['reply'])){
    $reply = htmlspecialchars(strip_tags(addslashes(trim($_GET['reply']))));
}
if(empty($usernamePropose) && empty($uid)){
    echo '<script>location.href = "index.php?ok=yok"</script>';
    exit();
}

if(!isset($_SESSION)) {
    session_start();
}
// giris yapilmadiysa gidilmek istenilen sayfaya yonlendirecez.
if (isset($_SESSION)) {  // tekrar login ekranina girilmek istenirse index.php ekranina atacagiz.
    if (!(isset($_SESSION['username']) && isset($_SESSION['realname']) && isset($_SESSION['mail']) && isset($_SESSION['rankName']))) {
        if(empty($usernamePropose)){
            echo '<script>location.href = "login.php?url=proposeMeeting&uid='. $uid. '"</script>';
        }
        else{
            echo '<script>location.href = "login.php?url=proposeMeeting&uid='. $uid. '&usernamePropose='. $usernamePropose. '"</script>';
        }
        exit();
    }
}
$toplantiBilgileri = $businessManager->toplantiGetir($uid);
if(empty($toplantiBilgileri)){
    echo '<script>location.href = "index.php?ok=yok"</script>';
    exit();
}
foreach ($toplantiBilgileri as $toplantiBilgileriDetay){ // baslangic olarak toplanti bilgilerini alir
    $usernameOrganizer = $toplantiBilgileriDetay['usernameOrganizer'];
    $usernameAttendeeTotal = $toplantiBilgileriDetay['usernameAttendee'];
    $summary = htmlspecialchars(strip_tags(addslashes(trim($toplantiBilgileriDetay['summary']))));
    $dtStart = $toplantiBilgileriDetay['dtStart'];
    $dtEnd = $toplantiBilgileriDetay['dtEnd'];
    $location = htmlspecialchars(strip_tags(addslashes(trim($toplantiBilgileriDetay['location']))));
    $class = $toplantiBilgileriDetay['class'];
    $priority = $toplantiBilgileriDetay['priority'];
    $description = htmlspecialchars(strip_tags(addslashes(trim($toplantiBilgileriDetay['description']))));
    $sequance = $toplantiBilgileriDetay['sequance'];
    $usernameOldAttendee = $toplantiBilgileriDetay['usernameAttendee'];
}
if(!empty($usernamePropose)){ // oneri bilgilerini getirir
    $toplantiBilgileri = $businessManager->oneriGetir($uid, $usernamePropose);
    foreach ($toplantiBilgileri as $toplantiBilgileriDetay){
        $usernameAttendeeTotal = $toplantiBilgileriDetay['usernameAttendee'];
        $summary = htmlspecialchars(strip_tags(addslashes(trim($toplantiBilgileriDetay['summary']))));
        $dtStart = $toplantiBilgileriDetay['dtStart'];
        $dtEnd = $toplantiBilgileriDetay['dtEnd'];
        $location = htmlspecialchars(strip_tags(addslashes(trim($toplantiBilgileriDetay['location']))));
        $class = $toplantiBilgileriDetay['class'];
        $priority = $toplantiBilgileriDetay['priority'];
        $description = htmlspecialchars(strip_tags(addslashes(trim($toplantiBilgileriDetay['description']))));
    }
}


$toplantiOnerileri = $businessManager->oneriGetir($uid);

$usernameAttendeeExplode = explode(';',$usernameAttendeeTotal); // tek tek arama yapilacak ve denk gelirse tik atacak.
$dtStartExplode = explode(' ',$dtStart); // startDate, startTime olarak ayıracaz ve eşleştirecez.
$dtEndExplode = explode(' ',$dtEnd);

require_once ('alertModal.php');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Meeting Calendar</title>
    <style type="text/css">

        a{
            border-radius: 7px; line-height: 35px; margin-right: 3px; padding: 10px; letter-spacing: 1.4px; text-decoration: none; color:#fff;
        }
        .dropdown p{
            border-radius: 7px; line-height: 35px; margin-right: 3px; padding: 5px 20px; letter-spacing: 1.4px; text-decoration: none;
        }

        .dropbtn {
            background-color: #1A915D;
            color: white;
            padding: 6px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            border-radius: 7px;
            min-width: 50px;
        }


        /* The container <div> - needed to position the dropdown content */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        /* Dropdown Content (Hidden by Default) */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 250px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            left: 0px;
        }

        /* Links inside the dropdown */
        .dropdown-content a{
            color: black;
            padding: 3px 3px;
            text-decoration: none;
            display: block;
        }

        /* Change color of dropdown links on hover */
        .dropdown-content a:hover {background-color: #f1f1f1}
        .dropdown-content button:hover {background-color: #f1f1f1}
        .dropdown-content button{
            color: black;
            padding: 10px 5px;
            text-decoration: none;
            background-color: #f9f9f9;
            display: block;
            border: none;
            font-size: 15px;
            text-decoration-color: black;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;

        }

        /* Show the dropdown menu on hover */
        .dropdown:hover .dropdown-content {
            display: block;
        }

        #btnMailGonder:hover{
            cursor: pointer;
            border: 2px solid #e3e3e3;
        }


    </style>
    <link href="./css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/loader.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

</head>
<body>
<script src="./js/jquery.js"></script>
<script src="./js/bootstrap.min.js"></script>
<script src='js/moment.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

<!-- meetingModal baslangici // baslangic olarak ekranda bu gozukecek -->
<div id="meetingModal" tabindex="-1" role="dialog" aria-labelledby="modalHeader">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formMeeting" class="form-horizontal">
                <div class="modal-header text-center">
                    <div class="dropdown">
                        <p class="dropbtn btn btn-default">Diğer Bilgileri Gör</p>
                        <div class="dropdown-content">
                            <a href="proposeMeeting.php?uid=<?php echo $uid; ?>" style="text-decoration: none;">Toplantı Bilgileri</a>
                            <a href="proposeMeeting.php?uid=<?php echo $uid; ?>&reply=true" style="text-decoration: none;">Katılım Cevapları</a>
                            <?php
                            foreach ($toplantiOnerileri as $toplantiOnerileriDetay){ // bu uid bilgisine ait toplantiya yapilan tum oneriler gelir.
                                $usernameProposeTemp = $toplantiOnerileriDetay['usernamePropose'];
                                $onerenBilgileri = $businessManager->userGetir($usernameProposeTemp);
                                foreach ($onerenBilgileri as $onerenBilgileriDetay){ // oneren katilimcinin linki
                                    echo '<a href="proposeMeeting.php?uid='.$uid.'&usernamePropose='.$usernameProposeTemp.'" style="text-decoration: none;">'.$onerenBilgileriDetay['realname'].' Önerisi</a>';
                                }

                            }
                            ?>
                        </div>
                    </div>
                    <p style="color: #a94442">Bu toplantı toplam <?php echo $sequance ?> kez revize edilmiştir.</p>
                </div>

                <div class="modal-header">
                    <h4 class="modal-title" id="modalHeader">@ToplantiBilgileri</h4>
                </div>

                <div id="asBody" class="modal-body">

                    <!-- Başlık -->
                    <div class="form-group">
                        <img for="summary" src="https://img.icons8.com/windows/32/000000/tag-window.png" class="col-sm-1 control-label"/>
                        <div class="col-sm-10">
                            <input disabled type="text" name="summary" class="form-control" id="summary" placeholder="Toplantı başlığı">
                        </div>
                    </div>

                    <!-- Katılımcıları Davet Et -->
                    <div class="form-group">
                        <img for="usernameAttendee[]" src="https://img.icons8.com/ios/50/000000/contact-card.png" class="col-sm-1 control-label"/>
                        <div class="col-sm-10">
                            <!-- Sayfanın en üstündeki scripler ve linkler dahil edildi -->
                            <select id="usernameAttendee[]" name="usernameAttendee[]" title="Toplantıya katılacak kişileri seçin" class="selectpicker form-control" multiple  data-live-search="true" >
                                <?php // davet gonderilecek kullanici id lerini alacaz
                                $users = $businessManager->userGetir();
                                foreach ($users as $user){ // ekrana kullanici bilgilerini yazdirir
                                    if($usernameOrganizer == $user['username']){
                                        $realnameOrganizer = $user['realname'];
                                        $mailOrganizer = $user['mail'];
                                        $modalHedarMessage = $realnameOrganizer. ' ('. $mailOrganizer. ') Toplantısı.';
                                    }
                                    if($usernamePropose == $user['username']){
                                        $realnamePropose = $user['realname'];
                                        $mailPropose = $user['mail'];
                                        $modalHedarMessage = $realnamePropose. ' ('. $mailPropose. ') Önerisi.';
                                    }
                                    foreach($usernameAttendeeExplode as $usernameAttendeeDetay){
                                        if($usernameAttendeeDetay == $user['username']){
                                            echo '<option disabled selected value="'.$user['username'].'">'. $user['realname']. '('.$user['mail'].')</option>';
                                            $denkMi = 1;
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- başlangıç tarihi -->
                    <div class="form-group ">
                        <img class="col-sm-1 control-label" for="startDate" src="https://img.icons8.com/material-outlined/24/000000/clock--v1.png" />
                        <div class="col-sm-8 ">
                            <input disabled type="date" name="startDate" class="form-control" id="startDate" placeholder="Başlangıç Tarihi">
                        </div> <!-- startDate 2021-06-21 şeklinde randevu başlangıç tarihini verecek. -->
                        <div class="col-sm-2">
                            <input disabled style="margin-top: 5px" required="required" name="startTime" id="startTime" type="time">
                        </div>
                    </div>

                    <!-- Bitiş tarihi -->
                    <div class="form-group ">
                        <span class="col-sm-1 control-label"></span>
                        <div class="col-sm-8">
                            <input disabled type="date" name="endDate" class="form-control" id="endDate" placeholder="Bitiş Tarihi">
                        </div> <!--endDate bitiş tarihini verecek. -->
                        <div class="col-sm-2">
                            <input disabled style="margin-top: 5px" required="required" name="endTime" id="endTime" type="time">

                        </div>
                    </div>

                    <!-- Konum  -->
                    <div class="form-group">
                        <img for="roomId" class="col-sm-1 control-label" src="https://img.icons8.com/ios/50/000000/pointer.png"/>
                        <div class="col-sm-8">
                            <select disabled id="location" name="location" title="Toplantı odasını seçin." class="form-control">
                                <option hidden value="">Toplantı odasını seçin</option>
                                <?php // davet gonderilecek kullanici id lerini alacaz
                                $rooms = $businessManager->odaGetir();
                                $varMi = 0;
                                foreach ($rooms as $room){ // veritabanindaki tbl_room buraya ekleniyor.
                                    if($location == $room['roomName']){
                                        echo '<option disabled selected value="'.$room['roomName'].'">'.$room['roomName'].'</option>';
                                        $varMi = 1;
                                    }
                                }
                                ?>
                            </select>
                        </div> <!-- Toplantinin yapilacağı konumu getirecek -->
                        <div class="col-sm-2"> <!-- buton -->
                            <input disabled class="btn btn-default" style="width: 100%" name="btnLocation" id="btnLocation" type="button" value="Diğer">
                        </div>
                    </div>

                    <!-- Konum2  -->
                    <div class="form-group" id="konum2Div" style="display: none">
                        <span class="col-sm-1 control-label"></span>
                        <div class="col-sm-10">
                            <input disabled type="text" name="locationForeign" class="form-control" id="locationForeign" placeholder="Kendin gir">
                        </div> <!-- Toplantinin yapilacağı konumu getirecek -->
                    </div>
                    <script>
                        document.getElementById("btnLocation").onclick = function() { // digerKonum butonuna tiklandiginda
                            if(document.getElementById("konum2Div").style.display == 'none'){
                                document.getElementById("konum2Div").style.display = "block";
                                document.getElementById("location").disabled = true;
                                document.getElementById("locationForeign").disabled = false;
                                document.getElementById("location").value = "";
                                document.getElementById("btnLocation").value = "Odalar";
                            }
                            else{
                                document.getElementById("konum2Div").style.display = "none";
                                document.getElementById("location").disabled = false;
                                document.getElementById("locationForeign").disabled = true;
                                document.getElementById("locationForeign").value = "";
                                document.getElementById("btnLocation").value = "Diğer";
                            }
                        }
                    </script>

                    <!-- Önem Düzeyi -->
                    <div class="form-group">
                        <img for="priority" class="col-sm-1 control-label" src="https://img.icons8.com/ios/50/000000/high-importance.png"/>
                        <div class="col-sm-6">
                            <select disabled name="priority" class="form-control" id="priority">
                                <option hidden value="">Önem derecesi seçin.</option>
                                <option style="color: rgb(231, 72, 86);" value="1">&#9724; Yüksek Önem Düzeyi</option>
                                <option style="color: #1A915D"   value="5">&#9724; Normal önem düzeyi</option>
                                <option style="color: rgb(0, 188, 242);" value="9">&#9724; Düşük Önem Düzeyi</option>
                            </select>
                        </div> <!-- Toplantının önem bilgisi seçilebilir. Varsayılan olarak Normaldir. -->

                        <img for="class" class="col-sm-1 control-label" src="https://img.icons8.com/ios/50/000000/lock--v1.png"/>
                        <span class="col-sm-2 text-muted" style="padding-top: 8px; user-select: none;">Özel</span>
                        <div class="col-sm-2">
                            <div class="checkbox">
                                <label>
                                    <input disabled name="class" id="class" type="checkbox" value="PRIVATE">
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Açıklama  -->
                    <div class="form-group ">
                        <img for="description" class="col-sm-1 control-label" src="https://img.icons8.com/material-outlined/24/000000/create-new.png"/>
                        <div class="col-sm-10">
                            <textarea disabled name="description" class="form-control" id="description" rows="3" placeholder="Açıklama ekleyin"></textarea>
                        </div>
                    </div> <!-- Kullanıcı isterse açıklama girebilir. -->
                </div>

                <!-- toplanti cevaplari gormek istenirse gosterilecek. -->
                <div id="replyBody" class="modal-body">
                    <ul class="list-group">
                        <?php
                        $toplantiCevaplari = $businessManager->cevapGetir($uid); // toplantinin butun cevaplarini getirir
                        if(empty($toplantiCevaplari)){
                            echo '<li class="list-group-item">Gelen cevap yok.</li>';
                        }
                        else{ // toplantiya herhangi bir kullanici cevap verdiyse ekrana cevabi yazdiracaz.
                            foreach($toplantiCevaplari as $toplantiCevaplariDetay){ // butun cevaplari tek tek ekrana yazdiracaz
                                $katilimciBilgileri = $businessManager->userGetir($toplantiCevaplariDetay['usernameAttendee']); // cevap veren kullanicinin bilgileri
                                foreach ($katilimciBilgileri as $katilimciBilgileriDetay){ // cevap veren kullanicinin bilgileri
                                    $realnameAttendee = $katilimciBilgileriDetay['realname'];
                                    $mailAttendee = $katilimciBilgileriDetay['mail'];
                                }
                                if($toplantiCevaplariDetay['reply'] == 'Accept'){ // verilen cevapright
                                    echo '<li class="list-group-item list-group-item-success">'.$realnameAttendee. ' ('. $mailAttendee. ')<span style="padding-right: 15px" class="navbar-right">Accept</span></li>';
                                }
                                else if($toplantiCevaplariDetay['reply'] == 'Tentative'){
                                    echo '<li class="list-group-item list-group-item-info">'.$realnameAttendee. ' ('. $mailAttendee. ')<span style="padding-right: 15px" class="navbar-right">Tentative</span></li>';
                                }
                                else if($toplantiCevaplariDetay['reply'] == 'Decline'){
                                    echo '<li class="list-group-item list-group-item-danger">'.$realnameAttendee. ' ('. $mailAttendee. ')<span style="padding-right: 15px" class="navbar-right">Decline</span></li>';
                                }
                            }
                        }
                        ?>
                    </ul>
                </div>

                <div class="modal-footer" style="text-align: center; padding: 3rem;">
                    <div id="loaderEkrani"></div>
                    <div id="butonEkrani">
                        <div id="btnOneriyiKabulEt" class="dropbtn btn list-group-item-success" style="padding: 1.4rem 1.5rem 1.2rem 1.2rem; display: none;">
                            <img style="width: 2rem; margin-right: .6rem;" src="https://img.icons8.com/material-outlined/24/000000/thumb-up.png"/>Öneriyi Kabul Et
                        </div>
                        <div id="btnToplantiDegisikligi" class="dropbtn btn list-group-item-warning" style="padding: 1.4rem 1.5rem 1.2rem 1.2rem;">
                            <img style="width: 2rem; margin-right: .6rem;" src="https://img.icons8.com/fluent-systems-regular/48/000000/change.png"/><span id="textDegisiklikYap">@DegisiklikYapButonu</span>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
<!-- meetingModal bitişi // baslangic olarak ekranda bu gozukecek -->
<!-- changeModal baslangici düzenleme yapılacak modal-->
<div class="modal fade" id="changeModal" tabindex="-1" role="dialog" aria-labelledby="modalHeader">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formChange" class="form-horizontal">

                <div class="modal-header">
                    <h4 class="modal-title" id="headerTitle">Toplantıda Değişiklik Yap</h4>
                </div>

                <div class="modal-body">

                    <!-- Başlık -->
                    <div class="form-group">
                        <img for="changeSummary" src="https://img.icons8.com/windows/32/000000/tag-window.png" class="col-sm-1 control-label"/>
                        <div class="col-sm-10">
                            <input type="text" name="changeSummary" class="form-control" id="changeSummary" placeholder="Toplantı başlığı">
                        </div>
                    </div>

                    <!-- Katılımcıları Davet Et -->
                    <div class="form-group">
                        <img for="changeUsernameAttendee[]" src="https://img.icons8.com/ios/50/000000/contact-card.png" class="col-sm-1 control-label"/>
                        <div class="col-sm-10">
                            <!-- Sayfanın en üstündeki scripler ve linkler dahil edildi -->
                            <select id="changeUsernameAttendee[]" name="changeUsernameAttendee[]" title="Toplantıya katılacak kişileri seçin" class="selectpicker form-control" multiple data-live-search="true" >
                                <?php // davet gonderilecek kullanici id lerini alacaz
                                foreach ($users as $user){ // ekrana kullanici bilgilerini yazdirir
                                    $denkMi = 0;
                                    foreach($usernameAttendeeExplode as $usernameAttendeeDetay){
                                        if($usernameAttendeeDetay == $user['username']){
                                            echo '<option selected value="'.$user['username'].'">'. $user['realname']. '('.$user['mail'].')</option>';
                                            $denkMi = 1;
                                        }
                                    }
                                    if($denkMi == 1){
                                        $denkMi = 0;
                                    }
                                    else{
                                        echo '<option value="'.$user['username'].'">'. $user['realname']. '('.$user['mail'].')</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- başlangıç tarihi -->
                    <div class="form-group ">
                        <img class="col-sm-1 control-label" for="changeStartDate" src="https://img.icons8.com/material-outlined/24/000000/clock--v1.png" />
                        <div class="col-sm-8 ">
                            <input type="date" name="changeStartDate" class="form-control" id="changeStartDate" placeholder="Başlangıç Tarihi">
                        </div> <!-- startDate 2021-06-21 şeklinde randevu başlangıç tarihini verecek. -->
                        <div class="col-sm-2">
                            <input style="margin-top: 5px" name="changeStartTime" id="changeStartTime" type="time">
                        </div>
                    </div>

                    <!-- Bitiş tarihi -->
                    <div class="form-group ">
                        <span class="col-sm-1 control-label"></span>
                        <div class="col-sm-8">
                            <input type="date" name="changeEndDate" class="form-control" id="changeEndDate" placeholder="Bitiş Tarihi">
                        </div> <!--endDate bitiş tarihini verecek. -->
                        <div class="col-sm-2">
                            <input style="margin-top: 5px" name="changeEndTime" id="changeEndTime" type="time">

                        </div>
                    </div>

                    <!-- Konum  -->
                    <div class="form-group">
                        <img for="changeLocation" class="col-sm-1 control-label" src="https://img.icons8.com/ios/50/000000/pointer.png"/>
                        <div class="col-sm-8">
                            <select id="changeLocation" name="changeLocation" title="Toplantı odasını seçin." class="form-control">
                                <option hidden value="">Toplantı odasını seçin</option>
                                <?php // davet gonderilecek kullanici id lerini alacaz
                                $varMi = 0;
                                foreach ($rooms as $room){ // veritabanindaki tbl_room buraya ekleniyor.
                                    if($location == $room['roomName']){
                                        echo '<option selected value="'.$room['roomName'].'">'.$room['roomName'].'</option>';
                                        $varMi = 1;
                                    }
                                    else{
                                        echo '<option value="'.$room['roomName'].'">'.$room['roomName'].'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div> <!-- Toplantinin yapilacağı konumu getirecek -->
                        <div class="col-sm-2"> <!-- buton -->
                            <input class="btn btn-default" style="width: 100%" name="btnChangeLocation" id="btnChangeLocation" type="button" value="Diğer">
                        </div>
                    </div>

                    <!-- Konum2  -->
                    <div class="form-group" id="changeKonum2Div" style="display: none">
                        <span class="col-sm-1 control-label"></span>
                        <div class="col-sm-10">
                            <input disabled type="text" name="changeLocationForeign" class="form-control" id="changeLocationForeign" placeholder="Kendin gir">
                        </div> <!-- Toplantinin yapilacağı konumu getirecek -->
                    </div>
                    <script>
                        document.getElementById("btnChangeLocation").onclick = function() { // digerKonum butonuna tiklandiginda
                            if(document.getElementById("changeKonum2Div").style.display == 'none'){
                                document.getElementById("changeKonum2Div").style.display = "block";
                                document.getElementById("changeLocation").disabled = true;
                                document.getElementById("changeLocationForeign").disabled = false;
                                document.getElementById("changeLocation").value = "";
                                document.getElementById("btnChangeLocation").value = "Odalar";
                            }
                            else{
                                document.getElementById("changeKonum2Div").style.display = "none";
                                document.getElementById("changeLocation").disabled = false;
                                document.getElementById("changeLocationForeign").disabled = true;
                                document.getElementById("changeLocationForeign").value = "";
                                document.getElementById("btnChangeLocation").value = "Diğer";
                            }

                        }
                    </script>

                    <!-- Önem Düzeyi -->
                    <div class="form-group">
                        <img for="changePriority" class="col-sm-1 control-label" src="https://img.icons8.com/ios/50/000000/high-importance.png"/>
                        <div class="col-sm-6">
                            <select name="changePriority" class="form-control" id="changePriority">
                                <option hidden value="">Önem derecesi seçin.</option>
                                <option style="color: rgb(231, 72, 86);" value="1">&#9724; Yüksek Önem Düzeyi</option>
                                <option style="color: #1A915D"   value="5">&#9724; Normal önem düzeyi</option>
                                <option style="color: rgb(0, 188, 242);" value="9">&#9724; Düşük Önem Düzeyi</option>
                            </select>
                        </div> <!-- Toplantının önem bilgisi seçilebilir. Varsayılan olarak Normaldir. -->

                        <img for="changeClass" class="col-sm-1 control-label" src="https://img.icons8.com/ios/50/000000/lock--v1.png"/>
                        <span class="col-sm-2 text-muted" style="padding-top: 8px;">Özel</span>
                        <div class="col-sm-2">
                            <div class="checkbox">
                                <label>
                                    <input name="changeClass" id="changeClass" type="checkbox" value="PRIVATE">
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Açıklama  -->
                    <div class="form-group ">
                        <img for="changeDescription" class="col-sm-1 control-label" src="https://img.icons8.com/material-outlined/24/000000/create-new.png"/>
                        <div class="col-sm-10">
                            <textarea name="changeDescription" class="form-control" id="changeDescription" rows="3" placeholder="Açıklama ekleyin"></textarea>
                        </div>
                    </div> <!-- Kullanıcı isterse açıklama girebilir. -->

                    <!-- Toplantı iptal edilmek istenirse tıklanacak. -->
                    <div id="deleteGroup" name="deleteGroup" class="form-group" style="display: none;">
                        <img for="changeDelete" class="col-sm-1 control-label" src="https://img.icons8.com/material-outlined/24/000000/delete-sign.png"/>
                        <span class="col-sm-3 text-danger" style="padding-top: 7px;">Toplantıyı iptal et.</span>
                        <div class="col-sm-5">
                            <div class="checkbox">
                                <label >
                                    <input value="delete" name="changeDelete" id="changeDelete" type="checkbox">
                                </label>
                            </div>
                        </div>
                    </div>


                </div>
                
                <input hidden id="changeUid" name="changeUid" type="hidden">
                <input hidden id="changeUsernameOrganizer" name="changeUsernameOrganizer" type="hidden">
                <input hidden id="changeRealnameOrganizer" name="changeRealnameOrganizer" type="hidden">
                <input hidden id="changeSequance" name="changeSequance" type="hidden">
                <input hidden id="usernameOldAttendee" name="usernameOldAttendee" type="hidden">

                <div class="modal-footer">
                    <span id="changeModalMessage" class="" style="display: block; text-align: ; padding-bottom: 1.5rem; color: #c7254e; user-select: none; "></span>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Çıkış</button>
                    <span id="btnDegisiklikleriOnayla" name="btnDegisiklikleriOnayla" type="button" class="btn btn-primary">@Onayla ve Gönder</span> <!-- test clasini kaldirdim kontrol et -->
                </div>
            </form>
        </div>
    </div>
</div>
<!-- changeModal bitişi düzenleme yapılacak modal -->


<!-- Toplantıda veya öneride değişiklik yapma modalının açılışı -->
<script>
    $("#btnToplantiDegisikligi").click(function (){
        if('<?php echo $usernamePropose; ?>' == ''){
            $('#btnDegisiklikleriOnayla').html('Toplantıyı Onayla ve Gönder');
            document.getElementById('deleteGroup').style.display = 'block';
        }
        else{
            $('#btnDegisiklikleriOnayla').html('Öneriyi Onayla ve Gönder');
            document.getElementById('deleteGroup').style.display = 'none';
        }
        $('#changeModal').modal('show');
    });
</script>
<!--Toplantıda veya öneride değişiklik yapma modalinin bitisi -->

<!-- eger hicbir degisiklik yapmadan oneri kabul ediliyorsa veya toplantida degisiklik yapiliyorsa  -->
<script>
    function toplantiDegisikligiGonder(){
        $('#loaderEkrani').html('<span class="loader" role="status" aria-hidden="true"></span>Loading...').addClass('disabled');
        document.getElementById('butonEkrani').style.display = 'none';
        //$('#changeModal').modal('hide');
        $('#changeModal').modal('hide');
        $.ajax({
            url: 'proposeIslemleri.php',
            type: "POST",
            data: $('#formChange').serialize(),
            success: function(rep) {
                document.getElementById('changeModalMessage').style.display = 'block';
                if(rep == 'islem tamamlandi'){
                    location.href = "proposeMeeting.php?uid=<?php echo $uid; ?>&ok=ok";
                }
                else{
                    $('#changeModalMessage').html(rep);
                }
                var delayInMilliseconds = 500; // .5 second
                setTimeout(function() { // en az yarim saniye spinner gozuksun
                    $('#loaderEkrani').html('').removeClass('disabled');
                    document.getElementById('butonEkrani').style.display = 'block';
                    $('#changeModal').modal('show');
                }, delayInMilliseconds);
            }
        });
    }
    $('#btnOneriyiKabulEt').click(function (){
        toplantiDegisikligiGonder();
    })
</script>
<script>
    if('<?php if(isset($_GET['ok'])){echo 'ok';} ?>' == 'ok'){
        $('#alertModal').modal('show');
    }
</script>
<!-- Öneriyi onaylıyorsa onaylıyorsa -->
<script>
    $("#btnDegisiklikleriOnayla").click(function (){
        toplantiDegisikligiGonder();
    })
</script>
<!-- toplanti cevaplari gormek istenirse body kismini none yapacaz. -->
<script>
    if('<?php echo $reply;?>' != 'true'){
        document.getElementById('asBody').style.display = 'block';
        document.getElementById('replyBody').style.display = 'none';
        document.getElementById('btnToplantiDegisikligi').style.display = 'inline';
    }
    else{
        document.getElementById('asBody').style.display = 'none';
        document.getElementById('replyBody').style.display = 'block';
        document.getElementById('btnToplantiDegisikligi').style.display = 'none ';
    }
</script>

<!-- Formların yüklenirken aldığı değerler -->
<script> // varsayılan olarak degerleri atayacaz.
    $('#modalHeader').html('<?php echo $modalHedarMessage; ?>');
    $('#textDegisiklikYap').html('<?php echo $textDegisiklikYap; ?>');
    $('#headerTitle').html('<?php echo $textDegisiklikYap; ?>');
    $('#summary').val('<?php echo $summary; ?>');
    $('#changeSummary').val('<?php echo $summary; ?>');
    $('#startDate').val('<?php echo $dtStartExplode[0]; ?>');
    $('#changeStartDate').val('<?php echo $dtStartExplode[0]; ?>');
    $('#endDate').val('<?php echo $dtEndExplode[0]; ?>');
    $('#changeEndDate').val('<?php echo $dtEndExplode[0]; ?>');
    $('#startTime').val('<?php echo $dtStartExplode[1]; ?>');
    $('#changeStartTime').val('<?php echo $dtStartExplode[1]; ?>');
    $('#endTime').val('<?php echo $dtEndExplode[1]; ?>');
    $('#changeEndTime').val('<?php echo $dtEndExplode[1]; ?>');
    if(0 == '<?php echo $varMi; ?>'){
        document.getElementById("konum2Div").style.display = "block";
        document.getElementById("changeKonum2Div").style.display = "block";
        document.getElementById("location").disabled = true;
        document.getElementById("changeLocation").disabled = true;
        //document.getElementById("locationForeign").disabled = false;
        document.getElementById("changeLocationForeign").disabled = false;
        document.getElementById("locationForeign").value = '<?php echo $location; ?>';
        document.getElementById("changeLocationForeign").value = '<?php echo $location; ?>';
        document.getElementById("location").value = "";
        document.getElementById("changeLocation").value = "";
        document.getElementById("btnLocation").value = "Odalar";
        document.getElementById("btnChangeLocation").value = "Odalar";
    }
    $('#priority').val('<?php echo $priority; ?>')
    $('#changePriority').val('<?php echo $priority; ?>')
    if('<?php echo $class; ?>' == 'PRIVATE'){
        $('#class').prop("checked", true);
        $('#changeClass').prop("checked", true);
    }
    else{
        $('#class').prop("checked", false);
        $('#changeClass').prop("checked", false);
    }
    $('#description').val('<?php echo $description; ?>');
    $('#changeDescription').val('<?php echo $description; ?>');
    $('#uid').val('<?php echo $uid; ?>');
    $('#changeUid').val('<?php echo $uid; ?>');
    $('#changeUsernameOrganizer').val('<?php echo $usernameOrganizer; ?>');
    $('#changeRealnameOrganizer').val('<?php echo $realnameOrganizer; ?>');
    $('#changeSequance').val('<?php echo $sequance; ?>');
    $('#usernameOldAttendee').val('<?php echo $usernameOldAttendee; ?>');
    document.getElementById('btnOneriyiKabulEt').style.display = '<?php echo $displayOneriyiKabulEt; ?>';


</script>

</body>
</html>
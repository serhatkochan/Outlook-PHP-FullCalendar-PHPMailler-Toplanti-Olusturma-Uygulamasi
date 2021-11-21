<?php
require_once('../business/BusinessManager.php');
$businessManager = new BusinessManager('MySQLDAO');

$uid = '';
$usernameAttendee = '';
$usernameAttendeeTotal = ''; // toplantidaki katilimcilar
$realnameOrganizer = '';
$realnameAttendee = '';
$mailOrganizer = '';
$mailAttendee = '';
$summary = '';
$dtStart = '';
$dtEnd = '';
$location = '';
$class = '';
$priority = '';
$description = '';


if(isset($_GET['usernameAttendee']) && isset($_GET['uid'])){
    $usernameAttendee = htmlspecialchars(strip_tags(addslashes(trim($_GET['usernameAttendee']))));
    $uid = htmlspecialchars(strip_tags(addslashes(trim($_GET['uid']))));

    $toplantiBilgileri = $businessManager->toplantiGetir($uid);

    if(empty($usernameAttendee) || empty($uid)){
        echo '<script>location.href = "index.php?ok=yok"</script>';
        exit();
    }
    if(empty($toplantiBilgileri)){
        echo '<script>location.href = "index.php?ok=yok"</script>';
        exit();
    }
    if(!isset($_SESSION)) {
        session_start();
    }
    if (isset($_SESSION)) {  // tekrar login ekranina girilmek istenirse index.php ekranina atacagiz.
        if (!(isset($_SESSION['username']) && isset($_SESSION['realname']) && isset($_SESSION['mail']) && isset($_SESSION['rankName']))) {
            echo '<script>location.href = "login.php?url=mailReply&usernameAttendee='. $usernameAttendee. '&uid='. $uid. '"</script>';
            exit();
        }
    }
    foreach ($toplantiBilgileri as $toplantiBilgileriDetay){
        $usernameOrganizer = $toplantiBilgileriDetay['usernameOrganizer'];
        $usernameAttendeeTotal = $toplantiBilgileriDetay['usernameAttendee'];
        $summary = htmlspecialchars(strip_tags(addslashes(trim($toplantiBilgileriDetay['summary']))));
        $dtStart = $toplantiBilgileriDetay['dtStart'];
        $dtEnd = $toplantiBilgileriDetay['dtEnd'];
        $location = htmlspecialchars(strip_tags(addslashes(trim($toplantiBilgileriDetay['location']))));
        $class = $toplantiBilgileriDetay['class'];
        $priority = $toplantiBilgileriDetay['priority'];
        $description = htmlspecialchars(strip_tags(addslashes(trim($toplantiBilgileriDetay['description']))));
    }
    $usernameAttendeeExplode = explode(';',$usernameAttendeeTotal); // tek tek arama yapilacak ve denk gelirse tik atacak.
    $dtStartExplode = explode(' ',$dtStart); // startDate, startTime olarak ayıracaz ve eşleştirecez.
    $dtEndExplode = explode(' ',$dtEnd);
}
else{
    echo 'sayfa yok';
}

require_once ('alertModal.php');
?>
</body>
</html>
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
            <form id="meetingForm" class="form-horizontal">

                <div class="modal-header">
                    <h4 class="modal-title" id="modalHeader">@ToplantiBilgileri</h4>
                </div>

                <div class="modal-body">

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
                                    }
                                    if($usernameAttendee == $user['username']){
                                        $realnameAttendee = $user['realname'];
                                        $mailAttendee = $user['mail'];
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

                <div class="modal-footer" style="text-align: center">
                    <div id="dropdownButtons">
                        <div class="dropdown">
                            <div class="dropbtn btn list-group-item-success" style="padding: 1.4rem 1.5rem 1.2rem 1.2rem;">
                                <img style="width: 2rem; margin-right: .6rem;" src="https://img.icons8.com/material-outlined/24/000000/thumb-up.png"/>Accept
                            </div>
                            <div class="dropdown-content">
                                <span id="btnAcceptPropose" type="button" class="btn">Evet ve Düzenleme Öner</span>
                                <span id="btnAcceptSendMessage" type="button" class="btn">Evet ve Mesaj Gönder</span>
                                <span id="btnAcceptNoMessage" type="button" class="btn">Evet ve Mesaj Gönderme</span>
                            </div>
                        </div>
                        <div class="dropdown">
                            <div class="dropbtn btn list-group-item-info" style="padding: 1.4rem 1.5rem 1.2rem 1.2rem;">
                                <img style="width: 2rem; margin-right: .6rem;" src="https://img.icons8.com/material-outlined/24/000000/question-mark.png"/>Tentative
                            </div>
                            <div class="dropdown-content">
                                <span id="btnTentativePropose" type="button" class="btn">Belki ve Düzenleme Öner</span>
                                <span id="btnTentativeSendMessage" type="button" class="btn">Belki ve Mesaj Gönder</span>
                                <span id="btnTentativeNoMessage" type="button" class="btn">Belki ve Mesaj Gönderme</span>
                            </div>
                        </div>
                        <div class="dropdown">
                            <div class="dropbtn btn list-group-item-danger" style="padding: 1.4rem 1.5rem 1.2rem 1.2rem;">
                                <img style="width: 2rem; margin-right: .6rem;" src="https://img.icons8.com/material-outlined/24/000000/thumbs-down.png"/>Decline
                            </div>
                            <div class="dropdown-content">
                                <span id="btnDeclinePropose" type="button" class="btn">Reddet ve Düzenleme Öner</span>
                                <span id="btnDeclineSendMessage" type="button" class="btn">Reddet ve Mesaj Gönder</span>
                                <span id="btnDeclineNoMessage" type="button" class="btn">Reddet ve Mesaj Gönderme</span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- meetingModal bitişi // baslangic olarak ekranda bu gozukecek -->

<!-- proposeModal baslangici -->
<div class="modal fade" id="proposeModal" tabindex="-1" role="dialog" aria-labelledby="modalHeader">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formPropose" class="form-horizontal">

                <div class="modal-header">
                    <h4 class="modal-title">Değişiklik Öner</h4>
                </div>

                <div class="modal-body">

                    <!-- Başlık -->
                    <div class="form-group">
                        <img for="proposeSummary" src="https://img.icons8.com/windows/32/000000/tag-window.png" class="col-sm-1 control-label"/>
                        <div class="col-sm-10">
                            <input type="text" name="proposeSummary" class="form-control" id="proposeSummary" placeholder="Toplantı başlığı">
                        </div>
                    </div>

                    <!-- Katılımcıları Davet Et -->
                    <div class="form-group">
                        <img for="proposeUsernameAttendee[]" src="https://img.icons8.com/ios/50/000000/contact-card.png" class="col-sm-1 control-label"/>
                        <div class="col-sm-10">
                            <!-- Sayfanın en üstündeki scripler ve linkler dahil edildi -->
                            <select id="proposeUsernameAttendee[]" name="proposeUsernameAttendee[]" title="Toplantıya katılacak kişileri seçin" class="selectpicker form-control" multiple data-live-search="true" >
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
                        <img class="col-sm-1 control-label" for="proposeStartDate" src="https://img.icons8.com/material-outlined/24/000000/clock--v1.png" />
                        <div class="col-sm-8 ">
                            <input type="date" name="proposeStartDate" class="form-control" id="proposeStartDate" placeholder="Başlangıç Tarihi">
                        </div> <!-- startDate 2021-06-21 şeklinde randevu başlangıç tarihini verecek. -->
                        <div class="col-sm-2">
                            <input style="margin-top: 5px" name="proposeStartTime" id="proposeStartTime" type="time">
                        </div>
                    </div>

                    <!-- Bitiş tarihi -->
                    <div class="form-group ">
                        <span class="col-sm-1 control-label"></span>
                        <div class="col-sm-8">
                            <input type="date" name="proposeEndDate" class="form-control" id="proposeEndDate" placeholder="Bitiş Tarihi">
                        </div> <!--endDate bitiş tarihini verecek. -->
                        <div class="col-sm-2">
                            <input style="margin-top: 5px" name="proposeEndTime" id="proposeEndTime" type="time">

                        </div>
                    </div>

                    <!-- Konum  -->
                    <div class="form-group">
                        <img for="proposeLocation" class="col-sm-1 control-label" src="https://img.icons8.com/ios/50/000000/pointer.png"/>
                        <div class="col-sm-8">
                            <select id="proposeLocation" name="proposeLocation" title="Toplantı odasını seçin." class="form-control">
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
                            <input class="btn btn-default" style="width: 100%" name="btnProposeLocation" id="btnProposeLocation" type="button" value="Diğer">
                        </div>
                    </div>

                    <!-- Konum2  -->
                    <div class="form-group" id="proposeKonum2Div" style="display: none">
                        <span class="col-sm-1 control-label"></span>
                        <div class="col-sm-10">
                            <input disabled type="text" name="proposeLocationForeign" class="form-control" id="proposeLocationForeign" placeholder="Kendin gir">
                        </div> <!-- Toplantinin yapilacağı konumu getirecek -->
                    </div>
                    <script>
                        document.getElementById("btnProposeLocation").onclick = function() { // digerKonum butonuna tiklandiginda
                            if(document.getElementById("proposeKonum2Div").style.display == 'none'){
                                document.getElementById("proposeKonum2Div").style.display = "block";
                                document.getElementById("proposeLocation").disabled = true;
                                document.getElementById("proposeLocationForeign").disabled = false;
                                document.getElementById("proposeLocation").value = "";
                                document.getElementById("btnProposeLocation").value = "Odalar";
                            }
                            else{
                                document.getElementById("proposeKonum2Div").style.display = "none";
                                document.getElementById("proposeLocation").disabled = false;
                                document.getElementById("proposeLocationForeign").disabled = true;
                                document.getElementById("proposeLocationForeign").value = "";
                                document.getElementById("btnProposeLocation").value = "Diğer";
                            }
                        }
                    </script>

                    <!-- Önem Düzeyi -->
                    <div class="form-group">
                        <img for="proposePriority" class="col-sm-1 control-label" src="https://img.icons8.com/ios/50/000000/high-importance.png"/>
                        <div class="col-sm-6">
                            <select name="proposePriority" class="form-control" id="proposePriority">
                                <option hidden value="">Önem derecesi seçin.</option>
                                <option style="color: rgb(231, 72, 86);" value="1">&#9724; Yüksek Önem Düzeyi</option>
                                <option style="color: #1A915D"   value="5">&#9724; Normal önem düzeyi</option>
                                <option style="color: rgb(0, 188, 242);" value="9">&#9724; Düşük Önem Düzeyi</option>
                            </select>
                        </div> <!-- Toplantının önem bilgisi seçilebilir. Varsayılan olarak Normaldir. -->

                        <img for="proposeClass" class="col-sm-1 control-label" src="https://img.icons8.com/ios/50/000000/lock--v1.png"/>
                        <span class="col-sm-2 text-muted" style="padding-top: 8px;">Özel</span>
                        <div class="col-sm-2">
                            <div class="checkbox">
                                <label>
                                    <input name="proposeClass" id="proposeClass" type="checkbox" value="PRIVATE">
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Açıklama  -->
                    <div class="form-group ">
                        <img for="proposeDescription" class="col-sm-1 control-label" src="https://img.icons8.com/material-outlined/24/000000/create-new.png"/>
                        <div class="col-sm-10">
                            <textarea name="proposeDescription" class="form-control" id="proposeDescription" rows="3" placeholder="Açıklama ekleyin"></textarea>
                        </div>
                    </div> <!-- Kullanıcı isterse açıklama girebilir. -->


                </div>
                <input hidden id="mailOrganizer" name="mailOrganizer" type="hidden">
                <input hidden id="mailAttendee" name="mailAttendee" type="hidden">
                <input hidden id="realnameOrganizer" name="realnameOrganizer" type="hidden" >
                <input hidden id="proposeRealnameAttendee" name="proposeRealnameAttendee" type="hidden">
                <input hidden id="proposeAttendeeUsername" name="proposeAttendeeUsername" type="hidden">
                <input hidden id="uid" name="uid" type="hidden">
                <input hidden id="reply" name="reply" type="hidden">

                <div class="modal-footer">
                    <span id="proposeModalMessage" class="" style="display: block; text-align: ; padding-bottom: 1.5rem; color: #c7254e; user-select: none; "></span>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Çıkış</button>
                    <span id="btnSubmitProposeModal" name="btnSubmitProposeModal" type="button" class="btn btn-primary">Öneriyi Onayla ve Gönder</span>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- proposeModal bitişi -->

<!-- modalMail baslangici // Düzenleyiciye mail gönderecek modal -->
<div id="modalMail" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" style="height: 50%; padding-top: 10%;">
        <div class="modal-content" style="height: 100%; overflow: visible;">
            <div class="modal-header">
                <input value="Kime:  <?php echo $realnameOrganizer. ' ('. $mailOrganizer. ')'; ?>"  type="button" class="form-control text-left">
            </div>
            <div class="modal-body" style="height: 80%; overflow: auto;">
                <div>
                     <span class="col-sm-1 text-center" style="margin-top: .4rem; padding-top: 1.2rem;" type="button" id="btnMailGonder" name="btnMailGonder">
                         <img src="https://img.icons8.com/material-outlined/24/000000/filled-sent.png"/>
                         <p>Gönder</p>
                     </span>
                    <form id="formSendMail" style="display: inline;">
                        <div class="col-sm-11">
                            <textarea style="margin-bottom: 1.2rem; resize: none;" name="mailDescription" class="form-control" id="mailDescription" rows="7" placeholder="Mesajınızı yazın."></textarea>
                            <div style="text-align: right;">
                                <span id="mailModalMessage" class="" style="padding-bottom: 1.5rem; color: #c7254e; user-select: none; margin-right: .5rem;"></span>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Çıkış</button>
                            </div>
                        </div>
                        <input hidden id="replyMailUsernameAttendee" name="replyMailUsernameAttendee" type="hidden">
                        <input hidden id="replyMailMailOrganizer" name="replyMailMailOrganizer" type="hidden">
                        <input hidden id="replyMailMailAttendee" name="replyMailMailAttendee" type="hidden">
                        <input hidden id="replyMailRealnameOrganizer" name="replyMailRealnameOrganizer" type="hidden" >
                        <input hidden id="replyMailRealnameAttendee" name="replyMailRealnameAttendee" type="hidden">
                        <input hidden id="replyMailUid" name="replyMailUid" type="hidden">
                        <input hidden id="replyMailSend" name="replyMailSend" type="hidden">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- modalMail bitisi // düzenleyice mail gönderecek modal bitisi -->


<!-- Düzenleme Öner modalının açılışı -->
<script>
    $("#btnAcceptPropose").click(function (){
        $("#reply").val('Accept');
        $('#proposeModal').modal('show');
    });
    $("#btnTentativePropose").click(function (){
        $("#reply").val('Tentative');
        $('#proposeModal').modal('show');
    });
    $("#btnDeclinePropose").click(function (){
        $("#reply").val('Decline');
        $('#proposeModal').modal('show');
    });
</script>
<!-- Öneriyi göndermeyi onaylıyorsa -->
<script>
    $("#btnSubmitProposeModal").click(function (){
        $('#btnSubmitProposeModal').html('<span class="loader" role="status" aria-hidden="true"></span>Loading...').addClass('disabled');
        $.ajax({
            url: 'addEventPropose.php',
            type: "POST",
            data: $('#formPropose').serialize(),
            success: function(rep) {
                document.getElementById('proposeModalMessage').style.display = 'block';
                if(rep == 'cevap olusturuldu'){
                    location.href = "mailReply.php?usernameAttendee=<?php echo $usernameAttendee; ?>&uid=<?php echo $uid; ?>&ok=ok";
                }
                else{
                    $('#proposeModalMessage').html(rep);
                }
                var delayInMilliseconds = 500; // .5 second
                setTimeout(function() { // en az yarim saniye spinner gozuksun
                    $('#btnSubmitProposeModal').html('Öneriyi Onayla ve Gönder').removeClass('disabled');
                }, delayInMilliseconds);
            }
        });
    })
</script>
<!-- Mesaj gönderme modalinin acilisi, mesaj gonderdiiginde organizere mail gider -->
<script>
    $("#btnAcceptSendMessage").click(function (){
        $('#replyMailMailOrganizer').val('<?php echo $mailOrganizer; ?>');
        $('#replyMailMailAttendee').val('<?php echo $mailAttendee; ?>');
        $('#replyMailRealnameOrganizer').val('<?php echo $realnameOrganizer; ?>');
        $('#replyMailRealnameAttendee').val('<?php echo $realnameAttendee; ?>');
        $('#replyMailUid').val('<?php echo $uid; ?>');
        $("#replyMailSend").val('Accept');
        $('#modalMail').modal('show');
    });
    $("#btnTentativeSendMessage").click(function (){
        $('#replyMailMailOrganizer').val('<?php echo $mailOrganizer; ?>');
        $('#replyMailMailAttendee').val('<?php echo $mailAttendee; ?>');
        $('#replyMailRealnameOrganizer').val('<?php echo $realnameOrganizer; ?>');
        $('#replyMailRealnameAttendee').val('<?php echo $realnameAttendee; ?>');
        $('#replyMailUid').val('<?php echo $uid; ?>');
        $("#replyMailSend").val('Tentative');
        $('#modalMail').modal('show');
    });
    $("#btnDeclineSendMessage").click(function (){
        $('#replyMailMailOrganizer').val('<?php echo $mailOrganizer; ?>');
        $('#replyMailMailAttendee').val('<?php echo $mailAttendee; ?>');
        $('#replyMailRealnameOrganizer').val('<?php echo $realnameOrganizer; ?>');
        $('#replyMailRealnameAttendee').val('<?php echo $realnameAttendee; ?>');
        $('#replyMailUid').val('<?php echo $uid; ?>');
        $("#replyMailSend").val('Decline');
        $('#modalMail').modal('show');
    });
</script>
<!-- mail gönder butonuna tıklandığında -->
<script>
    $("#btnMailGonder").click(function (){
        $('#btnMailGonder').html('<span class="loader" role="status" aria-hidden="true"></span>Loading...').addClass('disabled');
        $.ajax({
            url: 'sendMailOrganizer.php',
            type: "POST",
            data: $('#formSendMail').serialize(),
            success: function(rep) {
                if(rep == 'cevap olusturuldu'){
                    location.href = "mailReply.php?usernameAttendee=<?php echo $usernameAttendee; ?>&uid=<?php echo $uid; ?>&ok=ok";
                }
                else{
                    $('#mailModalMessage').html(rep);
                }
                var delayInMilliseconds = 500; // .5 second
                setTimeout(function() { // en az yarim saniye spinner gozuksun
                    $('#btnMailGonder').html('<img src="https://img.icons8.com/material-outlined/24/000000/filled-sent.png"/><p>Gönder</p>').removeClass('disabled');
                }, delayInMilliseconds);
            }
        });
    });
</script>
<!-- Öneri veya Mesaj göndermek istemezse, sadece islem yapacagi zaman. -->
<script>
    $("#btnAcceptNoMessage").click(function (){
        $('#dropdownButtons').html('<span class="loader" role="status" aria-hidden="true"></span>Loading...').addClass('disabled');
        $.ajax({
            url: 'addReply.php',
            type: "POST",
            data: {reply: 'Accept', usernameAttendee: '<?php echo $usernameAttendee; ?>',
                realnameAttendee: '<?php echo $realnameAttendee; ?>', mailAttendee: '<?php echo $mailAttendee; ?>', uid: '<?php echo $uid; ?>'},
            success: function(rep) {
                var delayInMilliseconds = 500; // .5 second
                setTimeout(function() { // en az yarim saniye spinner gozuksun
                }, delayInMilliseconds);
                if(rep == 'cevap olusturuldu'){
                    location.href = "mailReply.php?usernameAttendee=<?php echo $usernameAttendee; ?>&uid=<?php echo $uid; ?>&ok=ok";
                }
                else{
                    alert(rep);
                }
            }
        });
    });
    $("#btnTentativeNoMessage").click(function (){
        $('#dropdownButtons').html('<span class="loader" role="status" aria-hidden="true"></span>Loading...').addClass('disabled');
        $.ajax({
            url: 'addReply.php',
            type: "POST",
            data: {reply: 'Tentative', usernameAttendee: '<?php echo $usernameAttendee; ?>',
                realnameAttendee: '<?php echo $realnameAttendee; ?>', mailAttendee: '<?php echo $mailAttendee; ?>', uid: '<?php echo $uid; ?>'},
            success: function(rep) {
                var delayInMilliseconds = 500; // .5 second
                setTimeout(function() { // en az yarim saniye spinner gozuksun
                }, delayInMilliseconds);
                if(rep == 'cevap olusturuldu'){
                    location.href = "mailReply.php?usernameAttendee=<?php echo $usernameAttendee; ?>&uid=<?php echo $uid; ?>&ok=ok";
                }
                else{
                    alert(rep);
                }
            }
        });
    });
    $("#btnDeclineNoMessage").click(function (){
        $('#dropdownButtons').html('<span class="loader" role="status" aria-hidden="true"></span>Loading...').addClass('disabled');
        $.ajax({
            url: 'addReply.php',
            type: "POST",
            data: {reply: 'Decline', usernameAttendee: '<?php echo $usernameAttendee; ?>',
                realnameAttendee: '<?php echo $realnameAttendee; ?>', mailAttendee: '<?php echo $mailAttendee; ?>', uid: '<?php echo $uid; ?>'},
            success: function(rep) {
                var delayInMilliseconds = 500; // .5 second
                setTimeout(function() { // en az yarim saniye spinner gozuksun
                }, delayInMilliseconds);
                if(rep == 'cevap olusturuldu'){
                    location.href = "mailReply.php?usernameAttendee=<?php echo $usernameAttendee; ?>&uid=<?php echo $uid; ?>&ok=ok";
                }
                else{
                    alert(rep);
                }
            }
        });
    });
</script>
<script>
    if('<?php if(isset($_GET['ok'])){echo 'ok';} ?>' == 'ok'){
        $('#alertModal').modal('show');
    }
</script>

<!-- Formların yüklenirken aldığı değerler -->
<script> // varsayılan olarak degerleri atayacaz.
    $('#modalHeader').html('<?php echo $realnameOrganizer. ' ('. $mailOrganizer;?>) Toplantısı');
    $('#proposeModalHeader').html('<?php echo $realnameOrganizer;?> Toplantısı');
    $('#summary').val('<?php echo $summary; ?>');
    $('#proposeSummary').val('<?php echo $summary; ?>');
    $('#startDate').val('<?php echo $dtStartExplode[0]; ?>');
    $('#proposeStartDate').val('<?php echo $dtStartExplode[0]; ?>');
    $('#endDate').val('<?php echo $dtEndExplode[0]; ?>');
    $('#proposeEndDate').val('<?php echo $dtEndExplode[0]; ?>');
    $('#startTime').val('<?php echo $dtStartExplode[1]; ?>');
    $('#proposeStartTime').val('<?php echo $dtStartExplode[1]; ?>');
    $('#endTime').val('<?php echo $dtEndExplode[1]; ?>');
    $('#proposeEndTime').val('<?php echo $dtEndExplode[1]; ?>');
    if(0 == '<?php echo $varMi; ?>'){
        document.getElementById("konum2Div").style.display = "block";
        document.getElementById("location").disabled = true;
        //document.getElementById("locationForeign").disabled = false;
        document.getElementById("locationForeign").value = '<?php echo $location; ?>';
        document.getElementById("location").value = "";
        document.getElementById("btnLocation").value = "Odalar";

        document.getElementById("proposeKonum2Div").style.display = "block";
        document.getElementById("proposeLocation").disabled = true;
        document.getElementById("proposeLocationForeign").disabled = false;
        document.getElementById("proposeLocationForeign").value = '<?php echo $location; ?>';
        document.getElementById("proposeLocation").value = "";
        document.getElementById("btnProposeLocation").value = "Odalar";
    }
    $('#priority').val('<?php echo $priority; ?>')
    $('#proposePriority').val('<?php echo $priority; ?>')
    if('<?php echo $class; ?>' == 'PRIVATE'){
        $('#class').prop("checked", true);
        $('#proposeClass').prop("checked", true);
    }
    else{
        $('#class').prop("checked", false);
        $('#proposeClass').prop("checked", false);
    }
    $('#description').val('<?php echo $description; ?>');
    $('#proposeDescription').val('<?php echo $description; ?>');
    $('#proposeAttendeeUsername').val('<?php echo $usernameAttendee; ?>');
    $('#proposeRealnameAttendee').val('<?php echo $realnameAttendee; ?>');
    $('#mailOrganizer').val('<?php echo $mailOrganizer; ?>');
    $('#mailAttendee').val('<?php echo $mailAttendee; ?>');
    $('#realnameOrganizer').val('<?php echo $realnameOrganizer; ?>');
    $('#replyMailUsernameAttendee').val('<?php echo $usernameAttendee; ?>');
    $('#uid').val('<?php echo $uid; ?>');





</script>

</body>
</html>

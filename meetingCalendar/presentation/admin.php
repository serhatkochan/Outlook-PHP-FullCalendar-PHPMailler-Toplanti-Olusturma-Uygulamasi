<?php
if(!isset($_SESSION)) {
    session_start();
}

if(!isset($_SESSION['username']) && !isset($_SESSION['realname']) && !isset($_SESSION['mail']) && !isset($_SESSION['rankName'])){
    echo '<script>location.href = "login.php"</script>';
    exit();
}
else{
    if($_SESSION['rankName'] != 'Admin'){ // admin giris yaptiysa sayfaya girer.
        echo '<script>location.href = "login.php"</script>';
        exit();
    }
}

require_once('../business/BusinessManager.php');
$businessManager = new BusinessManager('MySQLDAO');


///// guvenlik acigi olmamasi icin boyle kullan !
$StringIfade = '';
$deneme = htmlspecialchars(strip_tags(addslashes(trim($StringIfade))));

$getOk = ''; // yapilan islemin sonucu
if(isset($_GET['ok'])){
    $getOk = htmlspecialchars(strip_tags(addslashes(trim($_GET['ok']))));
}

?>

<?php require_once('alertModal.php') ?>

<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Meeting Calendar</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="css/loader.css">

    <!-- Custom CSS -->
    <style>
        .message{
            display: none;
        }
        .message{
            color: #c7254e;
            cursor: default;
            user-select: none;
        }
        body {
            padding-top: 70px;
            /* .navbar-fixed-top. için gerekli.*/
        }
        a{
            border-radius: 7px; line-height: 35px; margin-right: 3px; padding: 10px; letter-spacing: 1.4px; text-decoration: none; color:#fff;
        }
        .dropdown p{
            border-radius: 7px; line-height: 35px; margin-right: 3px; padding: 1px 20px; letter-spacing: 1.4px; text-decoration: none;
        }

        .dropbtn {
            background-color: #222222;
            color: #9d9d9d;
            padding: 6px;
            border: none;
            cursor: pointer;
            border-radius: 7px;
            min-width: 50px;
            z-index: 5;
            text-decoration: none;
            font-size: 18px;
        }
        .dropbtn:hover{
            color: white;
        }
        /* The container <div> - needed to position the dropdown content */
        .dropdown {
            position: relative;
            display: inline-block;
            cursor: pointer;
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
            padding: 2rem;
        }
        #buttonlar .dropdown-content{
            padding: 4px;
        }

        /* Links inside the dropdown */
        .dropdown-content a{
            color: black;
            padding: 3px 3px;
            text-decoration: none;
            display: block;
        }

        /* Change color of dropdown links on hover */
        .dropdown-content a:hover {background-color: #f1f1f1}{
            color: black;
            padding: 8px 5px;
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
    </style>


</head>

<body>

<!-- navbar -->
<nav class="navbar navbar-inverse" role="navigation">

    <div class="container" style="z-index: 50; margin-right: 26rem;">
        <div class="">
            <a href="index.php" class="dropbtn" style="text-decoration: none;">Bütün Odalar</a>
            <div class="dropdown">
                <p class="dropbtn">Diğer Odalar
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                         width="10" height="13"
                         viewBox="0 0 172 172"
                         style=" fill:#000000;"><g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-size="none" style="mix-blend-mode: normal"><path d="M0,172v-172h172v172z" fill="none"></path><g fill="#9d9d9d"><path d="M154.75969,48.10625c-0.90031,0.02688 -1.76031,0.40313 -2.39187,1.06156l-66.36781,66.36781l-66.36781,-66.36781c-0.645,-0.67188 -1.53187,-1.03469 -2.45906,-1.04813c-1.41094,0.01344 -2.66063,0.86 -3.19813,2.15c-0.52406,1.30344 -0.215,2.78156 0.79281,3.7625l68.8,68.8c1.34375,1.34375 3.52062,1.34375 4.86437,0l68.8,-68.8c1.02125,-0.98094 1.33031,-2.49937 0.79281,-3.80281c-0.55094,-1.30344 -1.84094,-2.15 -3.26531,-2.12313z"></path></g></g></svg>
                </p>
                <div class="dropdown-content">
                    <?php
                    $rooms = $businessManager->odaGetir();
                    if(!empty($rooms)){
                        foreach ($rooms as $roomsDetay){
                            echo ' <a style="text-decoration: none; class="navbar-brand" href="index.php?roomName='. $roomsDetay['roomName'].'">'. $roomsDetay['roomName'].'</a>';
                        }
                    }// room bilgisini gondermezsek tum toplantilari listeleriz.
                    ?>
                </div>
            </div>
            <div class="navbar-header navbar-right" style="margin-right: 22rem; padding-top: 5px;">
                <?php
                if(isset($_SESSION['rankName'])){
                    if($_SESSION['rankName'] == 'Admin')
                        echo '<a style="text-decoration: none;" class="dropbtn" href="admin.php">Admin Paneli</a>';
                }
                ?>
                <a style="text-decoration: none;" class="dropbtn" href="exit.php">Oturumdan Çık</a>
            </div>
        </div>
    </div>
</nav>



<div id="buttonlar" class="container" style="margin: auto;">
    <!--buttonlar-->
    <div class="row">
        <div class="text-center">
                <div class="list-group">
                    <!-- Kullanici islemleri Buttonlari -->
                    <div class="dropdown">
                        <p class="list-group-item">Kullanıcı işlemleri</p>
                        <div class="dropdown-content">
                            <a href="admin.php?islem=kullaniciListele" style="text-decoration: none; color: #3b4656;">Kullanici Listele</a>
                        </div>
                    </div>
                    <!-- Rank Buttonlari -->
                    <div class="dropdown">
                        <p class="list-group-item">Rank İşlemleri</p>
                        <div class="dropdown-content">
                            <a href="admin.php?islem=rankListele" style="text-decoration: none; color: #3b4656;">Rank Listele</a>
                            <a href="admin.php?islem=rankEkle" style="text-decoration: none; color: #3b4656;">Rank Ekle</a>
                        </div>
                    </div>
                    <!-- Oda Buttonlari -->
                    <div class="dropdown">
                        <p class="list-group-item">Oda İşlemleri</p>
                        <div class="dropdown-content">
                            <a href="admin.php?islem=odaListele" style="text-decoration: none; color: #3b4656;">Oda Listele</a>
                            <a href="admin.php?islem=odaEkle" style="text-decoration: none; color: #3b4656;">Oda Ekle</a>
                        </div>
                    </div>
                </div>

        </div>
    </div>

    <!-- kullaniciListele Listele baslagncii -->
    <div style="display: none;" id="kullaniciListele" name="kullaniciListele" class="row">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Username</th>
                <th scope="col">Realname</th>
                <th scope="col">Mail</th>
                <th scope="col">Rank</th>
                <th scope="col">Eylem</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(isset($_GET['islem'])){
                if($_GET['islem'] == 'kullaniciListele'){
                    $kullaniciListele = $businessManager->userGetir();
                    if(!empty($kullaniciListele)){
                        foreach ($kullaniciListele as $kullaniciDetay){
                            echo '<tr>';
                            echo '<th scope="row">'. $kullaniciDetay['username']. '</th>';
                            echo '<td>'. $kullaniciDetay['realname']. '</td>';
                            echo '<td>'. $kullaniciDetay['mail']. '</td>';
                            echo '<td>'. $kullaniciDetay['rankName']. '</td>';
                            echo '<td>';
                            echo '<a style="margin-left: 3px" type="button" class="btn btn-default" href=admin.php?islem=kullaniciDuzenle&username='.$kullaniciDetay['username'].'>Düzenle</a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    }
                    else{
                        echo '<tr>';
                        echo '<th scope="row">Kullanıcı yok.</th>';
                        echo '</tr>';
                    }
                }
            }
            ?>
            </tbody>
        </table>
    </div> <!-- kullaniciListele bitisi -->
    <!-- kullaniciDuzenle baslangici -->
    <div style="display: none;" id="kullaniciDuzenle" name="kullaniciDuzenle" class="row text-center">
        <form id="formKullaniciDuzenle" action="" method="POST">
            <?php
            $username = '';
            $name = '';
            $mail = '';
            $userRankName = '';
            $userRankId = '';
            if(isset($_GET['islem'])){ // kullanici bilgilerini duzenlemek istiyorsa kontrolu
                if($_GET['islem'] == 'kullaniciDuzenle'){
                    if(isset($_GET['username'])){
                        $kullaniciBilgileri = $businessManager->userGetir($_GET['username']);
                        foreach ($kullaniciBilgileri as $kullaniciBilgileriDetay){
                            $username = $kullaniciBilgileriDetay['username'];
                            $realname = $kullaniciBilgileriDetay['realname'];
                            $mail = $kullaniciBilgileriDetay['mail'];
                            $userRankName = $kullaniciBilgileriDetay['rankName'];
                        }
                    }
                }
            }
            ?>
            <input type="hidden" value="<?php echo $username; ?>" id="username" name="username">
            <div class="form-group col-sm-12">
                <label for="username">Username: <?php echo $username; ?></label>
            </div>
            <div class="form-group col-sm-12">
                <label for="realname">Realname: <?php echo $realname; ?></label>
            </div>
            <div class="form-group col-sm-12">
                <label for="mail">Mail: <?php echo $mail; ?></label>
            </div>
            <div class="form-group col-sm-12">
                <label for="rank" style="display: inline-block">Rank: </label>
                <select required="true" id="rank" name="rank" class="form-control" style="width: 200px; display: inline-block; margin-left: 12px">
                    <?php
                    $ranks = $businessManager->rankGetir(); // butun ranklar ekranda listelenlir
                    foreach ($ranks as $rankDetay){
                        if($userRankName == $rankDetay['rankName']){
                            $userRankId = $rankDetay['idRank'];
                            echo '<option selected value="'. $rankDetay['idRank']. '">'.$rankDetay['rankName']. '</option>';
                        }
                        else{
                            echo '<option value="'. $rankDetay['idRank']. '">'.$rankDetay['rankName']. '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-group col-sm-12">
                <p id="messageKullaniciDuzenle" class="message"></p>
            </div>
            <button onclick="submitKullaniciDuzenle()" id="btnKullaniciDuzenle" style="" type="button" class="btn btn-primary">Onayla ve Kullanıcı Düzenle</button>
        </form>
        <script>
            function submitKullaniciDuzenle() {
                var selectRank = document.getElementById('rank').value;
                if('<?php echo $userRankId; ?>' == selectRank){
                    document.getElementById('messageKullaniciDuzenle').style.display = 'block';
                    $('#messageKullaniciDuzenle').html('Bu kullanici zaten ' + '<?php echo $userRankName; ?>' + ' Rankına sahip.');
                }
                else{
                    $('#btnKullaniciDuzenle').html('<span class="loader" role="status" aria-hidden="true"></span>Loading...').addClass('disabled');
                    $.ajax({
                        url: 'adminIslemleri.php?islem=update',
                        type: "POST",
                        data: $('#formKullaniciDuzenle').serialize(),
                        success: function(rep) {
                            if(rep == 'ok'){
                                location.href = "admin.php?islem=kullaniciListele&ok=ok";
                            }
                            document.getElementById('messageKullaniciDuzenle').style.display = 'block';
                            $('#messageKullaniciDuzenle').html(rep);

                            var delayInMilliseconds = 500; // .5 second
                            setTimeout(function() { // en az yarim saniye spinner gozuksun
                                $('#btnKullaniciDuzenle').html('Onayla ve Kullanıcıyı Düzenle').removeClass('disabled');
                            }, delayInMilliseconds);
                        }
                    });
                }

            }
        </script>
    </div> <!-- kullaniciDuzenle bitisi-->

    <!-- form olusturulup form ile id gonderip, silme islemi yapilabilir mi kontrol et. -->
    <!-- rankListele baslagncii -->
    <div style="display: none;" id="rankListele" name="rankListele" class="row">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">idRank</th>
                <th scope="col">Rank Name</th>
                <th scope="col">Eylem</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(isset($_GET['islem'])){
                if($_GET['islem'] == 'rankListele'){
                    $rankListele = $businessManager->rankGetir();
                    if(!empty($rankListele)){
                        foreach ($rankListele as $rankDetay){
                            echo '<tr>';
                            echo '<th scope="row">'. $rankDetay['idRank']. '</th>';
                            echo '<td>'. $rankDetay['rankName']. '</td>';
                            echo '<td>';
                            echo '<a style="margin-left: 3px" type="button" class="btn btn-default" href=admin.php?islem=rankDuzenle&idRank='.$rankDetay['idRank'].'>Düzenle</a>';
                            echo '<button id="btnRankSil'. $rankDetay['idRank'].'" onclick="submitRankSil('. $rankDetay['idRank']. ')" style="margin-left: 3px" type="button" class="btn btn-default" >Sil</a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    }
                    else{
                        echo '<tr>';
                        echo '<th scope="row">Rank yok.</th>';
                        echo '</tr>';
                    }
                }
            }
            ?>
            </tbody>
            <script>
                function submitRankSil(idRank) { // idRanki parametre olarak gonderiyoruz, bu idRanki post edip sildirecegiz.
                    var btnName = '#btnRankSil' + idRank;
                    $(btnName).html('<span class="loader" role="status" aria-hidden="true"></span>Loading...').addClass('disabled');
                    if(confirm("Rank silinecek emin misin?")){
                        $.ajax({
                            url: 'adminIslemleri.php?islem=sil',
                            type: "POST",
                            data: {idRank:idRank},
                            success: function(rep) {
                                if(rep == 'ok'){
                                    location.href = "admin.php?islem=rankListele&ok=ok";
                                }
                                var delayInMilliseconds = 500; // .5 second
                                setTimeout(function() { // en az yarim saniye spinner gozuksun
                                    $(btnName).html('Sil').removeClass('disabled');
                                    alert(rep)
                                    location.href = 'admin.php?islem=rankListele';
                                }, delayInMilliseconds);
                            }
                        });
                    }
                    else{
                        alert('Silme işlemi iptal edildi.');
                        $(btnName).html('Sil').removeClass('disabled');
                    }
                }
            </script>
        </table>
    </div> <!-- rankListele bitisi -->
    <!-- rankEkle -->
    <div style="display: none;" id="rankEkle" name="rankEkle" class="row text-center">
        <form id="formRankEkle" action="" method="POST">
            <div class="form-group col-md-12">
                <label for="rankName" style="display: block;">Rank</label>
                <input style="width: 200px; display:inline-block;" type="text" class="form-control" id="rankName" name="rankName" placeholder="Rank">
            </div>
            <div class="form-group col-sm-12">
                <p id="messageRankEkle" class="message"></p>
            </div>
            <button onclick="submitRankEkle()" id="btnRankEkle" style="" type="button" class="btn btn-primary">Onayla ve Rank Düzenle</button>
        </form>
        <script>
            function submitRankEkle() {
                $('#btnRankEkle').html('<span class="loader" role="status" aria-hidden="true"></span>Loading...').addClass('disabled');
                $.ajax({
                    url: 'adminIslemleri.php?islem=ekle',
                    type: "POST",
                    data: $('#formRankEkle').serialize(),
                    success: function(rep) {
                        if(rep == 'ok'){
                            location.href = "admin.php?islem=rankListele&ok=ok";
                        }
                        document.getElementById('messageRankEkle').style.display = 'block';
                        $('#messageRankEkle').html(rep);

                        var delayInMilliseconds = 500; // .5 second
                        setTimeout(function() { // en az yarim saniye spinner gozuksun
                            $('#btnRankEkle').html('Onayla ve Rank Ekle').removeClass('disabled');
                        }, delayInMilliseconds);
                    }
                });
            }
        </script>
    </div> <!-- rankEkle bitisi -->
    <!-- rankDuzenle baslangici -->
    <div style="display: none;" id="rankDuzenle" name="rankDuzenle" class="row">
        <form id="formRankDuzenle" action="" method="POST" class="text-center">
            <?php
            $idRank = '';
            $rankName = '';
            if(isset($_GET['islem'])){ // kullanici bilgilerini duzenlemek istiyorsa kontrolu
                if($_GET['islem'] == 'rankDuzenle'){
                    if(isset($_GET['idRank'])){
                        $rankBilgileri = $businessManager->rankGetir($_GET['idRank']);
                        foreach ($rankBilgileri as $rankDetay){
                            $idRank = $rankDetay['idRank'];
                            $rankName = $rankDetay['rankName'];
                        }
                    }
                }
            }
            ?>
            <input type="hidden" value="<?php echo $idRank; ?>" id="idRank" name="idRank">
            <div class="form-group col-md-12">
                <label for="rankName" style="display: block;">Rank</label>
                <input value="<?php echo $rankName; ?>" style="width: 200px; display:inline-block;" required="required" type="text" class="form-control" id="duzenleRankName" name="duzenleRankName" placeholder="Rank">
            </div>
            <div class="form-group col-sm-12">
                <p id="messageRankDuzenle" class="message"></p>
            </div>
            <button onclick="submitRankDuzenle()" id="btnRankDuzenle" style="" type="button" class="btn btn-primary">Onayla ve Rank Düzenle</button>
        </form>
        <script>
            function submitRankDuzenle() {
                var duzenleRankName = document.getElementById('duzenleRankName').value;
                if('<?php echo $rankName; ?>' == duzenleRankName){
                    document.getElementById('messageRankDuzenle').style.display = 'block';
                    $('#messageRankDuzenle').html('Değiştirmeye çalıştığınız Rankın ismi zaten ' + '<?php echo $rankName; ?>.');
                }
                else{
                    $('#btnRankDuzenle').html('<span class="loader" role="status" aria-hidden="true"></span>Loading...').addClass('disabled');
                    $.ajax({
                        url: 'adminIslemleri.php?islem=update',
                        type: "POST",
                        data: $('#formRankDuzenle').serialize(),
                        success: function(rep) {
                            if(rep == 'ok'){
                                location.href = "admin.php?islem=rankListele&ok=ok";
                            }
                            document.getElementById('messageRankDuzenle').style.display = 'block';
                            $('#messageRankDuzenle').html(rep);

                            var delayInMilliseconds = 500; // .5 second
                            setTimeout(function() { // en az yarim saniye spinner gozuksun
                                $('#btnRankDuzenle').html('Onayla ve Rank Düzenle').removeClass('disabled');
                            }, delayInMilliseconds);
                        }
                    });
                }

            }
        </script>
    </div> <!-- rankDuzenle bitisi -->

    <!-- odaListele baslagncii -->
    <div style="display: none;" id="odaListele" name="odaListele" class="row">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">idRoom</th>
                <th scope="col">Room Name</th>
                <th scope="col">Room Color</th>
                <th scope="col">Eylem</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(isset($_GET['islem'])){
                if($_GET['islem'] == 'odaListele'){
                    $odaListele = $businessManager->odaGetir();
                    if(!empty($odaListele)){
                        foreach ($odaListele as $odaDetay){
                            echo '<tr>';
                            echo '<th scope="row">'. $odaDetay['idRoom']. '</th>';
                            echo '<td>'. $odaDetay['roomName']. '</td>';
                            echo '<td  style="color: '. $odaDetay['roomColor']. '">&#9724; '. $odaDetay['roomColor']. '</td>';
                            echo '<td>';
                            echo '<a style="margin-left: 3px" type="button" class="btn btn-default" href=admin.php?islem=odaDuzenle&idRoom='.$odaDetay['idRoom'].'>Düzenle</a>';
                            echo '<button id="btnOdaSil'. $odaDetay['idRoom'].'" onclick="submitOdaSil('. $odaDetay['idRoom']. ')" style="margin-left: 3px" type="button" class="btn btn-default" >Sil</a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    }
                    else{
                        echo '<tr>';
                        echo '<th scope="row">Oda yok.</th>';
                        echo '</tr>';
                    }
                }
            }
            ?>
            </tbody>
            <script>
                function submitOdaSil(idRoom) { // idRanki parametre olarak gonderiyoruz, bu idRanki post edip sildirecegiz.
                    var btnName = '#btnOdaSil' + idRoom;
                    $(btnName).html('<span class="loader" role="status" aria-hidden="true"></span>Loading...').addClass('disabled');
                    if(confirm("Oda silinecek emin misin?")){
                        $.ajax({
                            url: 'adminIslemleri.php?islem=sil',
                            type: "POST",
                            data: {idRoom:idRoom},
                            success: function(rep) {
                                if(rep == 'ok'){
                                    location.href = "admin.php?islem=odaListele&ok=ok";
                                }
                                var delayInMilliseconds = 500; // .5 second
                                setTimeout(function() { // en az yarim saniye spinner gozuksun
                                    $(btnName).html('Sil').removeClass('disabled');
                                    alert(rep)
                                    location.href = 'admin.php?islem=odaListele';
                                }, delayInMilliseconds);
                            }
                        });
                    }
                    else{
                        alert('Silme işlemi iptal edildi.');
                        $(btnName).html('Sil').removeClass('disabled');
                    }
                }
            </script>
        </table>
    </div> <!-- odaListele bitisi -->
    <!-- odaEkle -->
    <div style="display: none;" id="odaEkle" name="odaEkle" class="row">
        <form id="formOdaEkle" action="" method="POST">
            <div class="form-group col-md-6">
                <label for="roomName" style="">Room Name</label>
                <input type="text" class="form-control" id="roomName" name="roomName" placeholder="Room Name">
            </div>
            <div class="form-group col-md-6">
                <label for="roomColor" style="">Room Color</label>
                <input type="color" class="form-control" id="roomColor" name="roomColor" placeholder="Room Color">
            </div>
            <div class="form-group col-sm-12">
                <p id="messageOdaEkle" class="message"></p>
            </div>
            <button onclick="submitOdaEkle()" id="btnOdaEkle" style="" type="button" class="btn btn-primary">Onayla ve Oda Ekle</button>
        </form>
        <script>
            function submitOdaEkle() {
                $('#btnOdaEkle').html('<span class="loader" role="status" aria-hidden="true"></span>Loading...').addClass('disabled');
                $.ajax({
                    url: 'adminIslemleri.php?islem=ekle',
                    type: "POST",
                    data: $('#formOdaEkle').serialize(),
                    success: function(rep) {
                        if(rep == 'ok'){
                            location.href = "admin.php?islem=odaListele&ok=ok";
                        }
                        document.getElementById('messageOdaEkle').style.display = 'block';
                        $('#messageOdaEkle').html(rep);

                        var delayInMilliseconds = 500; // .5 second
                        setTimeout(function() { // en az yarim saniye spinner gozuksun
                            $('#btnOdaEkle').html('Onayla ve Oda Ekle').removeClass('disabled');
                        }, delayInMilliseconds);
                    }
                });
            }
        </script>
    </div> <!-- odaEkle bitisi -->
    <!-- odaDuzenle baslangici -->
    <div style="display: none;" id="odaDuzenle" name="odaDuzenle" class="row">
        <form id="formOdaDuzenle" action="" method="POST" class="text-center">
            <?php
            $idRoom = '';
            $roomName = '';
            $roomColor = '';
            if(isset($_GET['islem'])){ // kullanici bilgilerini duzenlemek istiyorsa kontrolu
                if($_GET['islem'] == 'odaDuzenle'){
                    if(isset($_GET['idRoom'])){
                        $odaBilgileri = $businessManager->odaGetir($_GET['idRoom']);
                        foreach ($odaBilgileri as $odaDetay){
                            $idRoom = $odaDetay['idRoom'];
                            $roomName = $odaDetay['roomName'];
                            $roomColor = $odaDetay['roomColor'];
                        }
                    }
                }
            }
            ?>
            <input type="hidden" value="<?php echo $idRoom; ?>" id="idRoom" name="idRoom">
            <div class="form-group col-md-6">
                <label for="roomName" style="">Room Name</label>
                <input value="<?php echo $roomName; ?>" required="required"  type="text" class="form-control" id="duzenleRoomName" name="duzenleRoomName" placeholder="Room Name">
            </div>
            <div class="form-group col-md-6">
                <label for="roomColor" style="">Room Color</label>
                <?php
                if($roomColor){
                    echo '<input value="'. $roomColor. '" required="required"  type="color" class="form-control" id="duzenleRoomColor" name="duzenleRoomColor" placeholder="Room Color">';
                }
                else{
                    echo '<input value="#000000" required="required"  type="color" class="form-control" id="duzenleRoomColor" name="duzenleRoomColor" placeholder="Room Color">';
                }
                ?>
            </div>
            <div class="form-group col-sm-12">
                <p id="messageOdaDuzenle" class="message"></p>
            </div>
            <button onclick="submitOdaDuzenle()" id="btnOdaDuzenle" style="" type="button" class="btn btn-primary">Onayla ve Oda Düzenle</button>
        </form>
        <script>
            function submitOdaDuzenle() {
                var duzenleRoomName = document.getElementById('duzenleRoomName').value;
                var duzenleRoomColor = document.getElementById('duzenleRoomColor').value;
                if((duzenleRoomName == '<?php echo $roomName; ?>') && (duzenleRoomColor == '<?php echo $roomColor; ?>')){
                    document.getElementById('messageOdaDuzenle').style.display = 'block';
                    $('#messageOdaDuzenle').html('Değiştirmeye çalıştığınız odanin bilgileri ayni. ');
                }
                else{
                    $('#btnOdaDuzenle').html('<span class="loader" role="status" aria-hidden="true"></span>Loading...').addClass('disabled');
                    $.ajax({
                        url: 'adminIslemleri.php?islem=update',
                        type: "POST",
                        data: $('#formOdaDuzenle').serialize(),
                        success: function(rep) {
                            if(rep == 'ok'){
                                location.href = "admin.php?islem=odaListele&ok=ok";
                            }
                            document.getElementById('messageOdaDuzenle').style.display = 'block';
                            $('#messageOdaDuzenle').html(rep);

                            var delayInMilliseconds = 500; // .5 second
                            setTimeout(function() { // en az yarim saniye spinner gozuksun
                                $('#btnOdaDuzenle').html('Onayla ve Oda Düzenle').removeClass('disabled');
                            }, delayInMilliseconds);
                        }
                    });
                }

            }
        </script>
    </div> <!-- odaDuzenle bitisi -->


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<!-- tiklanan butonlara gore ekranda o butona ait bilgiler gosterilecek, gizlenecek. -->
<script>
    var islem = '';
    if('<?php
        if(isset($_GET['islem'])){
            echo 'islemVar';
        }
        ?>' == 'islemVar'){
        var islem = '<?php if(isset($_GET['islem'])){echo $_GET['islem'];} ?>';
        if(islem == 'kullaniciListele'){
            document.getElementById('kullaniciListele').style.display = 'block';
            document.getElementById('kullaniciDuzenle').style.display = 'none';
            document.getElementById('rankListele').style.display = 'none';
            document.getElementById('rankEkle').style.display = 'none';
            document.getElementById('rankDuzenle').style.display = 'none';
            document.getElementById('odaListele').style.display = 'none';
            document.getElementById('odaEkle').style.display = 'none';
            document.getElementById('odaDuzenle').style.display = 'none';
        }
        else if(islem == 'kullaniciEkle'){
            document.getElementById('kullaniciListele').style.display = 'none';
            document.getElementById('kullaniciDuzenle').style.display = 'none';
            document.getElementById('rankListele').style.display = 'none';
            document.getElementById('rankEkle').style.display = 'none';
            document.getElementById('rankDuzenle').style.display = 'none';
            document.getElementById('odaListele').style.display = 'none';
            document.getElementById('odaEkle').style.display = 'none';
            document.getElementById('odaDuzenle').style.display = 'none';
        }
        else if(islem == 'kullaniciDuzenle'){
            document.getElementById('kullaniciListele').style.display = 'none';
            document.getElementById('kullaniciDuzenle').style.display = 'block';
            document.getElementById('rankListele').style.display = 'none';
            document.getElementById('rankEkle').style.display = 'none';
            document.getElementById('rankDuzenle').style.display = 'none';
            document.getElementById('odaListele').style.display = 'none';
            document.getElementById('odaEkle').style.display = 'none';
            document.getElementById('odaDuzenle').style.display = 'none';
        }
        else if(islem == 'rankListele'){
            document.getElementById('kullaniciListele').style.display = 'none';
            document.getElementById('kullaniciDuzenle').style.display = 'none';
            document.getElementById('rankListele').style.display = 'block';
            document.getElementById('rankEkle').style.display = 'none';
            document.getElementById('rankDuzenle').style.display = 'none';
            document.getElementById('odaListele').style.display = 'none';
            document.getElementById('odaEkle').style.display = 'none';
            document.getElementById('odaDuzenle').style.display = 'none';
        }
        else if(islem== 'rankEkle'){
            document.getElementById('kullaniciListele').style.display = 'none';
            document.getElementById('kullaniciDuzenle').style.display = 'none';
            document.getElementById('rankListele').style.display = 'none';
            document.getElementById('rankEkle').style.display = 'block';
            document.getElementById('rankDuzenle').style.display = 'none';
            document.getElementById('odaListele').style.display = 'none';
            document.getElementById('odaEkle').style.display = 'none';
            document.getElementById('odaDuzenle').style.display = 'none';
        }
        else if(islem == 'rankDuzenle'){
            document.getElementById('kullaniciListele').style.display = 'none';
            document.getElementById('kullaniciDuzenle').style.display = 'none';
            document.getElementById('rankListele').style.display = 'none';
            document.getElementById('rankEkle').style.display = 'none';
            document.getElementById('rankDuzenle').style.display = 'block';
            document.getElementById('odaListele').style.display = 'none';
            document.getElementById('odaEkle').style.display = 'none';
            document.getElementById('odaDuzenle').style.display = 'none';
        }
        else if(islem == 'odaListele'){
            document.getElementById('kullaniciListele').style.display = 'none';
            document.getElementById('kullaniciDuzenle').style.display = 'none';
            document.getElementById('rankListele').style.display = 'none';
            document.getElementById('rankEkle').style.display = 'none';
            document.getElementById('rankDuzenle').style.display = 'none';
            document.getElementById('odaListele').style.display = 'block';
            document.getElementById('odaEkle').style.display = 'none';
            document.getElementById('odaDuzenle').style.display = 'none';
        }
        else if(islem == 'odaEkle'){
            document.getElementById('kullaniciListele').style.display = 'none';
            document.getElementById('kullaniciDuzenle').style.display = 'none';
            document.getElementById('rankListele').style.display = 'none';
            document.getElementById('rankEkle').style.display = 'none';
            document.getElementById('rankDuzenle').style.display = 'none';
            document.getElementById('odaListele').style.display = 'none';
            document.getElementById('odaEkle').style.display = 'block';
            document.getElementById('odaDuzenle').style.display = 'none';
        }
        else if(islem == 'odaDuzenle'){
            document.getElementById('kullaniciListele').style.display = 'none';
            document.getElementById('kullaniciDuzenle').style.display = 'none';
            document.getElementById('rankListele').style.display = 'none';
            document.getElementById('rankEkle').style.display = 'none';
            document.getElementById('rankDuzenle').style.display = 'none';
            document.getElementById('odaListele').style.display = 'none';
            document.getElementById('odaEkle').style.display = 'none';
            document.getElementById('odaDuzenle').style.display = 'block';
        }
    }
</script>

<script>
    if('<?php if(!empty($getOk)){echo $getOk;} ?>' == 'ok'){
        $('#alertModal').modal('show');
    }
</script>


</body>
</html>

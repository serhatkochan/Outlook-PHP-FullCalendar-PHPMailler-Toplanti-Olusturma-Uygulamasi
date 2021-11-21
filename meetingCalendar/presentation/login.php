<?php
if(!isset($_SESSION)){ // session baslamadiysa baslat
    session_start();
}

if (isset($_SESSION)) {  // tekrar login ekranina girilmek istenirse index.php ekranina atacagiz.
    if (isset($_SESSION['username']) && isset($_SESSION['realname']) && isset($_SESSION['mail']) && isset($_SESSION['rankName'])) {
        echo '<script>location.href = "index.php"</script>';
        exit();
    }
}
$url = '';
$totalUrl = '';
if(isset($_GET['url']) && isset($_GET['uid'])){ // giris yapilmadan bir sayfaya gitmek istiyorsa ilk önce giris yaptırıp sonra gitmek istedigi sayfaya yonlendirecez.
    $url = htmlspecialchars(strip_tags(addslashes(trim($_GET['url']))));
    $uid = htmlspecialchars(strip_tags(addslashes(trim($_GET['uid']))));
    if($url == 'proposeMeeting'){
        if(isset($_GET['usernamePropose'])){
            $usernamePropose = htmlspecialchars(strip_tags(addslashes(trim($_GET['usernamePropose']))));
            $totalUrl = 'proposeMeeting.php?uid='. $uid . '&usernamePropose='. $usernamePropose;
        }
        else{
            $totalUrl = 'proposeMeeting.php?uid='. $uid;
        }
    }
    else if($url == 'mailReply'){
        if(isset($_GET['usernameAttendee'])){
            $usernameAttendee = htmlspecialchars(strip_tags(addslashes(trim($_GET['usernameAttendee']))));
            $totalUrl = 'mailReply.php?uid='. $uid . '&usernameAttendee='. $usernameAttendee;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Meeting Calendar</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/loader.css">
    <link rel="stylesheet" href="css/loginForm.css">
</head>
<body>
    <div class="container" style="margin-top: 25rem; width: 35%">

        <form action="" method="POST">
            <h2>Login</h2>
            <div class="input-container">
                <i class="fa fa-user icon"></i>
                <input class="input-field" type="text" placeholder="Username" name="username" id="username">
            </div>

            <div class="input-container">
                <i class="fa fa-key icon"></i>
                <input class="input-field" type="password" placeholder="Password" name="password" id="password">

            </div>

            <div class="message" id="message">$message</div>
            <span onclick="submitLogin()" type="button" class="btn btn-primary" id="btnLogin">Submit</span>

        </form>

        <script src="js/jquery.js"></script>
        <script src="js/bootstrap.min.js"></script>

        <script>
            function submitLogin(){
                var username = document.getElementById('username').value;
                var password = document.getElementById('password').value;
                if(username && password){ // bos birikilan input yoksa
                    $('#btnLogin').html('<span class="loader" role="status" aria-hidden="true"></span>Loading...').addClass('disabled');
                    $.ajax({
                        url: 'loginControl.php?',
                        type: "POST",
                        data: {username:username, password:password},
                        success: function(rep) {
                            if(rep == '1'){ // bos veri girilmistir
                                document.getElementById('message').style.display = 'block';
                                $('#message').html('<p>Kullanıcı Adı veya Şifrenizi Yanlış Girdiniz !</p>');
                            }
                            else if(rep == '2'){ // baglanti sorunu
                                document.getElementById('message').style.display = 'block';
                                $('#message').html('<p>Yerel Ağa Bağlanırken Bir Sorun Oluştu !</p>');
                            }
                            else if(rep == '3'){ // kayit yok
                                document.getElementById('message').style.display = 'block';
                                $('#message').html('<p>Sistemde Kaydınız Bulunamadı !</p>');
                            }
                            else if(rep == '99'){ // kayit vardir.
                                if('<?php echo $totalUrl; ?>' == ''){
                                    location.href = "index.php";
                                }
                                else{
                                    location.href = "<?php echo $totalUrl; ?>";
                                }
                                document.getElementById('message').style.display = 'block';
                                $('#message').html('<p>Yönlendiriliyor...</p>');
                            }
                            else{ // not almadigimiz bir hata gelirse?
                                document.getElementById('message').style.display = 'block';
                                $('#message').html('<p>Bilinmeyen Bir Hata Oluştu. Lütfen Yönetici ile İletişime Geçin.</p>');
                            }

                            var delayInMilliseconds = 500; // .5 second
                            setTimeout(function() { // en az yarim saniye spinner gozuksun
                                $('#btnLogin').html('Submit').removeClass('disabled');
                            }, delayInMilliseconds);
                        }
                    });
                }
                else{ // inputlardan herhangi biri bossa hata mesaji yazdirir
                    document.getElementById('message').style.display = 'block';
                    $('#message').html('<p>Kullanıcı Adı veya Şifrenizi Yanlış Girdiniz !</p>');
                }
            }
        </script>
    </div>


</body>

</html>

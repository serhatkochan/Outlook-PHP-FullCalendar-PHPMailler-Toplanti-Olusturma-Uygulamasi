<?php
if(!isset($_SESSION)) {
    session_start();
}
if(isset($_POST['username']) && isset($_POST['password'])){ //girilen bilgileri alır

    $username=htmlspecialchars(strip_tags(addslashes(trim($_POST['username']))));
    $password=htmlspecialchars(strip_tags(addslashes(trim($_POST['password']))));
    if(empty($username) && empty($password)){
        die ('1'); // kullanici adi ve sifre bir sekilde bos geldiyse hata mesaji yazdiracaz
    }
    else{ // username ve password var ise
        require_once ('../business/BusinessManager.php');
        $businessManager = new BusinessManager('MySQLDAO'); // BusineesManager sinifindan bir nesne olusturduk.

        $kullaniciBilgileri = $businessManager->userGetir($username, $password);

        // isArray(..)
        if(empty($kullaniciBilgileri)){
            die ('3'); // kullaniciBilgisi gelmediyse kullanici yok mesaji yazdiracaz.
        }
        else{
            if(is_array($kullaniciBilgileri)){
                foreach ($kullaniciBilgileri as $kullaniciBilgileriDetay){
                    $_SESSION['username'] = $kullaniciBilgileriDetay['username'];
                    $_SESSION['realname'] = $kullaniciBilgileriDetay['realname'];
                    $_SESSION['mail'] = $kullaniciBilgileriDetay['mail'];
                    $_SESSION['rankName'] = $kullaniciBilgileriDetay['rankName'];
                }
                die ('99'); // kullanici var, butun islemler yapildi.
            }
            else{
                die ('2'); // veritabani baglanti hatasi vardir
            }

        }
    }

}
else{ // girilen bilgiler eksikse
    exit();
}


?>
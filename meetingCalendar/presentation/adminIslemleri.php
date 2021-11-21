<?php


if(isset($_POST)){ // post islemi yapildiysa admin islemleri yapilacak.

    require_once('../business/BusinessManager.php');
    $businessManager = new BusinessManager('MySQLDAO');

    if(isset($_GET['islem'])){ // eger bir islem yapilmasi istendiyse
        if($_GET['islem'] == 'update'){ // update islemi isteniyorsa
            if(isset($_POST['username']) && isset($_POST['rank'])){ // kullanici ranki duzenleme islemi yapiliyorsa
                $username = htmlspecialchars(trim($_POST['username']));
                $idRank = htmlspecialchars(trim($_POST['rank']));
                if(empty($username) || empty($idRank)){
                    die('Düzgün formatta bilgileri giriniz.');
                }
                $businessManager->userRankIslemi($username, $idRank);
                die('ok');
            }
            else if(isset($_POST['idRank']) && isset($_POST['duzenleRankName'])){ // rank duzenleme islemi yapilacaksa
                $idRank = htmlspecialchars(trim($_POST['idRank']));
                $rankName = htmlspecialchars(trim($_POST['duzenleRankName']));
                if(empty($idRank) || empty($rankName)){
                    die('Düzgün formatta bilgileri giriniz.');
                }
                $businessManager->rankGuncelle($idRank, $rankName);
                die('ok');
            }
            else if(isset($_POST['idRoom']) && isset($_POST['duzenleRoomName']) && isset($_POST['duzenleRoomColor'])){
                $idRoom = htmlspecialchars(strip_tags(addslashes(trim($_POST['idRoom']))));
                $roomName = htmlspecialchars(strip_tags(addslashes(trim($_POST['duzenleRoomName']))));
                $roomColor = htmlspecialchars(strip_tags(addslashes(trim($_POST['duzenleRoomColor']))));
                if(empty($idRoom) || empty($roomName) || empty($roomColor)){
                    die('Düzgün formatta bilgileri giriniz.');
                }
                $businessManager->odaGuncelle($idRoom, $roomName, $roomColor);
                die('ok');

            }
        }
        else if($_GET['islem'] == 'ekle'){ // ekleme islemi yapilmak isteniyoras
            if(isset($_POST['rankName'])){ // eger rank eklenmek isteniyorsa
                $rankName = htmlspecialchars(strip_tags(addslashes(trim($_POST['rankName']))));
                if(empty($rankName)){
                    die('Duzgun formatta bilgileri giriniz.');
                }
                $businessManager->rankOlustur($rankName);
                die('ok');
            }
            else if(isset($_POST['roomName']) && isset($_POST['roomColor'])){ // oda ekleme islemi yapilmak isteniyorsa
                $roomName = htmlspecialchars(strip_tags(addslashes(trim($_POST['roomName']))));
                $roomColor = htmlspecialchars(strip_tags(addslashes(trim($_POST['roomColor']))));
                if(empty($roomName) || empty($roomColor)){
                    die('Düzgün formatta bilgileri giriniz.');
                }
                $businessManager->odaOlustur($roomName,$roomColor);
                die('ok');
            }
        }
        else if($_GET['islem'] == 'sil'){ // silme islemi yapilmak isteniyorsa
            if(isset($_POST['idRank'])){ // eger rank silinmek isteniyorsa
                $idRank = htmlspecialchars(strip_tags(addslashes(trim($_POST['idRank']))));
                if(empty($idRank)){ // boyle bir sey mumkun degil
                    die('Beklenmeyen bir hata olustu. Lütfen yöneticinize bildirin.');
                }
                $businessManager->rankSil($idRank);
                die('ok');
            }
            else if(isset($_POST['idRoom'])){ // oda silinmek isteniyorsa
                $idRoom = htmlspecialchars(strip_tags(addslashes(trim($_POST['idRoom']))));
                if(empty($idRoom)){
                    die('Beklenmeyen bir hata olustu. Lütfen yöneticinize bildirin.');
                }
                $businessManager->odaSil($idRoom);
                die('ok');
            }
        }
    }
}
else{
    // böyle bir sayfa yok uyarisi verilebilir.
}


?>
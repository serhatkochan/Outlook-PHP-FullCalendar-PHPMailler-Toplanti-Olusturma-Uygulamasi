<?php
//require_once('../database/MySQLDAO.php');
class BusinessManager{
    private $baseDAO;
    private $mailController;

    public function __construct($baseDAO){
        require_once('../database/'.$baseDAO.'.php'); // istedigimiz veritabanı sınfıını buraya dahil ediyoruz;
        require_once('../business/MailController.php');
        $this->mailController = new MailController();
        $this->baseDAO = new $baseDAO;
    }

    // ***** toplanti islemleri *****
    // toplantiyi veritabanina kayit eder, katilimcilara detaylari gor diye mial gonderir.
    public function toplantiOlustur($summary, $usernameOrganizer, $realnameOrganizer, $usernameAttendee, $startDate, $startTime, $endDate, $endTime,
                                    $location, $priority, $class, $description){

        $dtStart = $startDate. ' '. $startTime;
        if(strlen($dtStart) < 19){
            $dtStart .= ':00';
        }
        $dtEnd = $endDate. ' '. $endTime;
        if(strlen($dtEnd) < 19){
            $dtEnd .= ':00';
        }

        if($this->zamanAraligiSorgula($dtStart, $dtEnd, $location) != 'oda bos'){
            die($location. ' Bu tarih araliginda müsait değil.');
        }

        $created = date('Y-m-d H:i:s',time());
        $sequance = 0;
        $uid = $dtStart[0]. $dtStart[3]. $dtEnd[1]. $dtEnd[2]. '-'. date('YmdHis', time()). '@'. $usernameOrganizer[0]. '.c';

        $usernameAttendeeTotal = ''; // katilimcilar veritabanina kayit edilecek
        $attendeeTotalDetay = array(); // katilimcilarin isim, mail bilgileri tutulacak.
        foreach ($usernameAttendee as $username){ // tek tek butun katilimcilari gezecez, bilgileri alacaz.
            $usernameAttendeeTotal .= $username. ';';
            $user = $this->userGetir($username);
            foreach ($user as $userDetay){
                $attendeeTotalDetay[] = array(
                    'usernameAttendee' => $userDetay['username'],
                    'realnameAttendee' => $userDetay['realname'],
                    'mailAttendee' => $userDetay['mail']
                );
            }
        }
        // veritabanina kayit eder
        $this->baseDAO->toplantiOlustur($uid, $usernameOrganizer, $usernameAttendeeTotal, $summary, $dtStart, $dtEnd,
                                       $location, $class, $priority, $description, $created, $sequance);

        // toplanti bilgisini gonderir. detaylari gor derse
        $this->mailController->toplantiBilgisiGonder($uid, $realnameOrganizer, $attendeeTotalDetay, $summary, $dtStart, $dtEnd,
            $location, $class, $priority, $description);
    }
    // verdigimiz parametrelere gore toplantiyi getirir. parametre vermezsek tüm toplantıyı listele.
    public function toplantiGetir($uid = NULL, $dtStart = NULL, $dtEnd = NULL, $location = NULL){
        return $this->baseDAO->toplantiGetir($uid, $dtStart, $dtEnd, $location);
    }
    // toplanti guncellenecegi veya silinecegi zaman veritabanina kayit eder, kullanicilara mail gider.
    public function toplantiGuncelle($uid, $summary, $usernameOrganizer, $realnameOrganizer, $usernameOldAttendee, $usernameAttendee, $startDate, $startTime, $endDate, $endTime,
                                     $location, $priority, $class, $description, $sequance){

        $dtStart = $startDate. ' '. $startTime;
        if(strlen($dtStart) < 19){
            $dtStart .= ':00';
        }
        $dtEnd = $endDate. ' '. $endTime;
        if(strlen($dtEnd) < 19){
            $dtEnd .= ':00';
        }
        if($this->zamanAraligiSorgula($dtStart, $dtEnd, $location, $uid) != 'oda bos'){
            die($location. ' Bu tarih araliginda müsait değil.');
        }

        $lastModified = date('Y-m-d H:i:s',time()); // toplantinin guncellendigi tarih
        $sequance = $sequance + 1; // toplantinin guncelleme sayisini 1 arttirdik.

        $attendeeTotalDetayNow = array(); // toplantiya zaten katilan kullanicilari tutacak.
        $attendeeTotalDetayNew = array(); // toplantiya yeni katilacak olan katilimcilari tutacak.
        $attendeeTotalDetayDelete = array(); // toplantidan cikartilan katilimcilari tutacak.
        $attendeeTotalDetay = array(); // eger katilimcilar ayni ise katilimcilarin username, realname, mail bilgileri tutulacak.
        $usernameAttendeeTotal = ''; // gonderilen katilimcilarin veritabanina kayit edilecegi bicimi

        $usernameOldAttendeeExplode = explode(';', $usernameOldAttendee); // eski katilimcilarin usernameleri
        // eger katilimcilarda degisiklik yapildiysa
        if($usernameAttendee != $usernameOldAttendeeExplode){ // bu sorgu calisiyor mu kontrol et.
            foreach ($usernameOldAttendeeExplode as $usernameOld){ // eski katilimci var mi yoksa cikarildi mi kontrolu
                if($usernameOld != ''){ // boşta string olmasin
                    $varMi = 0;
                    foreach ($usernameAttendee as $usernameSend){ // gonderilen katilimcilarin usernameleri
                        if($usernameOld == $usernameSend){
                            $varMi = 1;
                            $user = $this->userGetir($usernameOld); // gonderilen username ile kullanici bilgilerini getiriyoruz.
                            foreach ($user as $userDetay){
                                $attendeeTotalDetayNow[] = array( // katilimcinin detaylari aktarildi
                                    'usernameAttendee' => $userDetay['username'],
                                    'realnameAttendee' => $userDetay['realname'],
                                    'mailAttendee' => $userDetay['mail']
                                );
                            }
                        }
                        if($usernameSend == ''){
                            $varMi = 1; // bazen bos gelebilir, bos gelirse varMi 1 olsun, ekstradan user eklemesin.
                        }
                    }
                    if($varMi == 0){ // eger eski katilimci yeni gelen listede yoksa silinenler listesine aliyoruz
                        $user = $this->userGetir($usernameOld); // gonderilen username ile kullanici bilgilerini getiriyoruz.
                        foreach ($user as $userDetay){
                            $attendeeTotalDetayDelete[] = array( // katilimcinin detaylari aktarildi
                                'usernameAttendee' => $userDetay['username'],
                                'realnameAttendee' => $userDetay['realname'],
                                'mailAttendee' => $userDetay['mail']
                            );
                        }
                    }
                }
            } // eski katilimci kontrolunun bitisi
            foreach($usernameAttendee as $usernameSend){ // gonderilen katilimci listesinde yeni eklenen katilimcilarin bilgisini alacaz
                if($usernameSend != ''){
                    $varMi = 0;
                    $usernameAttendeeTotal .= $usernameSend. ';'; // veritabanina kayit edilis bicimi
                    foreach ($usernameOldAttendeeExplode as $usernameOld){ // eski katilimci bilgileir
                        if($usernameOld != ''){
                            if($usernameSend == $usernameOld){ // yeni katilimci eklenmis demektir.
                                $varMi = 1;
                            }
                        }
                    }
                    if($varMi == 0){
                        $user = $this->userGetir($usernameSend); // yeni katilimci bilgileri
                        foreach ($user as $userDetay){
                            $attendeeTotalDetayNew[] = array( // katilimcinin detaylari aktarildi
                                'usernameAttendee' => $userDetay['username'],
                                'realnameAttendee' => $userDetay['realname'],
                                'mailAttendee' => $userDetay['mail']
                            );
                        }
                    }
                }
            }
        }
        else{ // eger katilimcilar ayni ise
            foreach ($usernameAttendee as $username){ // tek tek butun katilimcilari gezecez, bilgileri alacaz.
                if($username != ''){
                    $usernameAttendeeTotal .= $username. ';';
                    $user = $this->userGetir($username);
                    foreach ($user as $userDetay){
                        $attendeeTotalDetay[] = array(
                            'usernameAttendee' => $userDetay['username'],
                            'realnameAttendee' => $userDetay['realname'],
                            'mailAttendee' => $userDetay['mail']
                        );
                    }
                }
            }
        }


        // toplantiyi veritabaninda gunceller.
        $this->baseDAO->toplantiGuncelle($uid, $usernameOrganizer, $usernameAttendeeTotal, $summary, $dtStart, $dtEnd, $location, $class, $priority, $description, $lastModified, $sequance);
        // yeni eklenenlere, cikartilanlara veya zaten olanlara toplantiyla ilgili bilgi gidecek.
        $this->baseDAO->cevapSil($uid); // toplantiya verilen cevaplari siler.
        $this->guncellemeDavetiyesiGonder($uid, $realnameOrganizer, $attendeeTotalDetay, $attendeeTotalDetayNow, $attendeeTotalDetayNew, $attendeeTotalDetayDelete, $summary, $dtStart, $dtEnd,
            $location, $class, $priority, $description);
    }
    // toplanti tamamen silinmek istendiginde veritabindan siler, kullanicilara iptalle ilgili .ics bilgisi gider.
    public function toplantiSil($uid){
        $toplantiBilgileri = $this->baseDAO->toplantiGetir($uid);

        foreach ($toplantiBilgileri as $toplantiDetay){
            $attendeeExplode = explode(';', $toplantiDetay['usernameAttendee']);
            foreach ($attendeeExplode as $attendeeDetay){
                if(!empty($attendeeDetay)){
                    $user = $this->userGetir($attendeeDetay); // yeni katilimci bilgileri
                    foreach ($user as $userDetay){
                        $attendeeTotalDetay[] = array( // katilimcinin detaylari aktarildi
                            'usernameAttendee' => $userDetay['username'],
                            'realnameAttendee' => $userDetay['realname'],
                            'mailAttendee' => $userDetay['mail']
                        );
                    }
                }
            }
        }
        $this->baseDAO->toplantiSil($uid);
        $this->mailController->toplantiIptalDavetiyesiGonder($uid, $attendeeTotalDetay, $toplantiBilgileri);
        $this->baseDAO->oneriSil($uid); // toplantiya yapilan onerileri siler.
        $this->baseDAO->cevapSil($uid); // toplantiya verilen cevaplari siler.

    }

    //***** oneri islemleri *****
    // oneriyi veritabanina kayit eder, organizere oneri var detaylari gor diye mail gider.
    public function oneriOlustur($uid, $mailOrganizer, $mailAttendee, $realnameOrganizer, $usernamePropose, $realnamePropose, $summary, $usernameAttendee, $startDate, $startTime, $endDate, $endTime,
                                 $location, $priority, $class, $description, $reply){
        $dtStart = $startDate. ' '. $startTime;
        if(strlen($dtStart) < 19){
            $dtStart .= ':00';
        }
        $dtEnd = $endDate. ' '. $endTime;
        if(strlen($dtEnd) < 19){
            $dtEnd .= ':00';
        }

        if($this->zamanAraligiSorgula($dtStart, $dtEnd, $location, $uid) != 'oda bos'){
            die($location. ' Bu tarih araliginda müsait değil.');
        }
        //die('$dtStart: '. $dtStart. '<br> $dtEnd: '. $dtEnd. '<br> $location: '. $location. '<br> $uid: '. $uid. '<br>');

        $usernameAttendeeTotal = ''; // katilimcilar veritabanina kayit edilecek
        foreach ($usernameAttendee as $username){ // tek tek butun katilimcilari gezecez, bilgileri alacaz.
            $usernameAttendeeTotal .= $username. ';';
        }
        // oneriyi veritabanina kayit edecez
        $oneriVarMi = $this->baseDAO->oneriGetir($uid, $usernamePropose);
        if(empty($oneriVarMi)){
            $this->baseDAO->oneriOlustur($uid, $usernamePropose, $usernameAttendeeTotal, $summary, $dtStart, $dtEnd, $location, $class, $priority, $description);
        }
        else{
            $this->baseDAO->oneriGuncelle($uid, $usernamePropose, $usernameAttendeeTotal, $summary, $dtStart, $dtEnd, $location, $class, $priority, $description);
        }

        // oneriyi organize edene mail gonderecez
        $this->mailController->degisiklikOnerisiGonder($uid, $mailOrganizer, $realnameOrganizer, $usernamePropose, $realnamePropose, $summary, $dtStart, $dtEnd,
            $location, $priority, $class, $description);
    }
    // butun onerileri veya o kullanicinin onerisini getirir
    public function oneriGetir($uid, $usernamePropose = NULL){
        return $this->baseDAO->oneriGetir($uid, $usernamePropose);
    }


    // ***** user islemleri *****
    // verilen bilgilere gore userin idUser, name, mail, rankName bilgilerini getirir.
    public function userGetir($username = NULL, $password = NULL){
        $kullanicilariListele = $this->baseDAO->ldapUserGetir($username, $password);
        // $kullanicilariListele: username, name, mail bilgilerini tutar.
        $kullaniciBilgileri = array(); // butun degerleri bunda tutacaz.
        if(!empty($kullanicilariListele)){ // kullaniciBilgileri bos degilse
            foreach ($kullanicilariListele as $kullaniciDetay){ // tek tek butun kullanicilari veya kullaniciyi diziye atayacagiz
                $rankBilgisi = $this->baseDAO->userRankGetir($kullaniciDetay['username']);
                $rankName = '';
                // $rankBilgilsi: idRank, rankName bilgilerini tutar.
                if(empty($rankBilgisi)){ // eger userin ranki yoksa varsayilan olarak member olsun.
                    $rankName = 'Member';
                }
                else{ // eger veritabaninda usernameye ait rank varsa o idRanki getirecez
                    foreach ($rankBilgisi as $rankBilgisiDetay){
                        $userRank = $this->baseDAO->rankGetir($rankBilgisiDetay['idRank']);
                        foreach ($userRank as $userRankDetay){
                            $rankName = $userRankDetay['rankName'];
                        }
                    }
                }
                $kullaniciBilgileri[] = array('username' => $kullaniciDetay['username'],
                                              'realname' => $kullaniciDetay['realname'],
                                              'mail' => $kullaniciDetay['mail'],
                                              'rankName' => $rankName);
            }
        }
        return $kullaniciBilgileri;
    }
    // kullaniciya rank ekleme, duzenleme, silme islemini yapar.
    public function userRankIslemi($username, $idRank){
        $userRank = $this->baseDAO->userRankGetir($username);
        if(empty($userRank)){ // eger kullanicinin raki bossa yeni bir tbl_user_rank tablosuna o kullaniciyi ranki ile birlikte ekler.
            $this->baseDAO->userRankOlustur($username, $idRank);
        }
        else{ // eger kullanicinin bir ranki varsa
            $rank = $this->baseDAO->rankGetir($idRank);
            foreach ($rank as $rankDetay){
                if($rankDetay['rankName'] == 'Member'){ // eger rank Member ise tablodan silinecek.
                    $this->baseDAO->userRankSil($username);
                }
                else{ // eger denk degilse ranki baska bir rank ile degistirmek istiyordur guncelleneyelim.
                    $this->baseDAO->userRankGuncelle($username, $idRank);
                }
            }
        }
    }


    // ***** rank islemleri *****
    // idRank, rankName getirir. idRank verirsek o idRankin bilgilerini getirir
    public function rankGetir($idRank = NULL){
        return $this->baseDAO->rankGetir($idRank);
    }
    public function rankOlustur($rankName){
        $this->baseDAO->rankOlustur($rankName);
    }
    public function rankGuncelle($idRank, $rankName){
        $this->baseDAO->rankGuncelle($idRank, $rankName);
    }
    // rank silinirken tbl_user_rank tablosundaki bu idRanke ait verilerde silinir.
    public function rankSil($idRank){
        $userRank = $this->baseDAO->userRankGetir(NULL, $idRank); // bu idRanka ait veri var mi
        if(!empty($userRank)){ // eger varsa
            foreach ($userRank as $useRankDetay){
                $this->baseDAO->userRankSil(NULL, $idRank); // o idRanka ait butun kullanicilari tbl_user_rank tablosundan silecegiz.
            }
        }
        $this->baseDAO->rankSil($idRank); // en son idRanka sahip ranki tbl_rank tablosundan silecegiz.
    }


    // ***** oda islemleri *****
    // verilen bilgilere gore oda olusturur

    public function odaOlustur($roomName, $roomColor){
        $this->baseDAO->odaOlustur($roomName, $roomColor);
    }
    public function odaGuncelle($idRoom, $roomName = NULL, $roomColor = NULL){
        $this->baseDAO->odaGuncelle($idRoom, $roomName, $roomColor);
    }
    // parametre girilmezse butun odalar gelir, idRoom gilirse o odanin bilgileri gelir
    public function odaGetir($idRoom = NULL){
        return $this->baseDAO->odaGetir($idRoom);
    }
    public function odaSil($idRoom){
        $this->baseDAO->odaSil($idRoom);
    }

    // ***** cevap islemleri *****
    // katilimcinin verdigi cevap veritabanina kayit olur.
    public function cevapOlustur($uid, $usernameAttendee, $reply){
        $cevapVarMi = $this->baseDAO->cevapGetir($uid, $usernameAttendee); // bu kullanicinin cevabi var mi kontrol eder.
        if(empty($cevapVarMi)){
            $this->baseDAO->cevapOlustur($uid, $usernameAttendee, $reply);
        }
        else{
            $this->baseDAO->cevapGuncelle($uid, $usernameAttendee, $reply);
        }

    }
    // katilimci cevaplarini getirir
    public function cevapGetir($uid, $usernameAttendee = NULL){
        return $this->baseDAO->cevapGetir($uid, $usernameAttendee);
    }


    // ***** mail islemleri *****
    // katilimci evet veya belki dediyse .ics uzantili takvim davetiyesi gidecek.
    public function toplantiDavetiyesiGonder($uid, $realnamePropose, $mailAttendee, $reply){
        if(($reply == 'Accept') || ($reply == 'Tentative')){
            $toplantiBilgileri = $this->baseDAO->toplantiGetir($uid);
            $this->mailController->toplantiDavetiyesiGonder($uid, $realnamePropose, $mailAttendee, $toplantiBilgileri);
        }
    }
    // toplanti cevap kisminda katilmici organize edene mesaj gondermek isteyebilir.
    public function organizereMailGonder($realnameOrganizer, $mailOrganizer, $realnameAttendee, $description, $uid){
        $this->mailController->organizereMailGonder($realnameOrganizer, $mailOrganizer, $realnameAttendee, $description, $uid);
    }
    public function guncellemeDavetiyesiGonder($uid, $realnameOrganizer, $attendeeTotalDetay, $attendeeTotalDetayNow, $attendeeTotalDetayNew, $attendeeTotalDetayDelete, $summary, $dtStart, $dtEnd,
                                               $location, $class, $priority, $description){
        if(!empty($attendeeTotalDetayNow)){
            $this->mailController->toplantiGuncellemeBilgisiGonder($uid, $realnameOrganizer, $attendeeTotalDetayNow, $summary, $dtStart, $dtEnd,
                $location, $class, $priority, $description);
        }
        if(!empty($attendeeTotalDetayNew)){
            // toplantiya yeni katilan kullanicilara toplanti var bilgisi gidecek.
            $this->mailController->toplantiBilgisiGonder($uid, $realnameOrganizer, $attendeeTotalDetayNew, $summary, $dtStart, $dtEnd,
                $location, $class, $priority, $description);
        }
        if(!empty($attendeeTotalDetayDelete)){
            // toplantidan cikartilan katilimcilara toplanti iptal edildi bilgisi gidecek.
            $toplantiBilgileri = $this->baseDAO->toplantiGetir($uid);
            $this->mailController->toplantiIptalDavetiyesiGonder($uid, $attendeeTotalDetayDelete, $toplantiBilgileri);
        }
        if(!empty($attendeeTotalDetay)){ // eger katilimcilar ayni ise
            $this->mailController->toplantiGuncellemeBilgisiGonder($uid, $realnameOrganizer, $attendeeTotalDetay, $summary, $dtStart, $dtEnd,
                $location, $class, $priority, $description);
        }
    }


    // secilen tarihte o odada toplanti var mi kontrolleri yapilacak
    // toplanti olusturulurken uid verilmesine gerek yok. dolayısıyla toplanti olusturulurken butun toplantilari kontrol eder. kontrol edecez.
    public function zamanAraligiSorgula($startTotalDate, $endTotalDate, $location, $uid = NULL){
        $baslangic = $this->baseDAO->zamanAraliginiSorgulaStart($startTotalDate, $endTotalDate, $location, $uid);
        $bitis = $this->baseDAO->zamanAraliginiSorgulaEnd($startTotalDate, $endTotalDate, $location, $uid);

        // eger gonderilen bir uid varsa, gelen toplanti araligi bizim toplantimiza esitse bunu degisebiliriz.

        if(empty($baslangic) && empty($bitis)){ // iki araliktada bir toplanti yoksa
            return 'oda bos';
        }
        return 'oda dolu'; // eger toplanti varsa var doner.
    }



}
?>
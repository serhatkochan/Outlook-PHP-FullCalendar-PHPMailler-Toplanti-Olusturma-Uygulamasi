<?php

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

class MailController{
    private $port = 587;
    private $host = 'smtp.live.com'; // hotmail icin .ics ve mail gonderimi hatasiz calisiyor.
    private $username = "";
    private $password = "";

    // toplanti var, detaylari gor mesaji gonderir.
    public function toplantiBilgisiGonder($uid, $realnameOrganizer, $attendeeTotalDetay, $summary, $dtStart, $dtEnd,
                                          $location, $class, $priority, $description){
        $mail = new \PHPMailer\PHPMailer\PHPMailer(); // PHPMailler sifini kullanacaz.
        $mail->SMTPDebug = 0; // son kullanıcının SMTP teslimat raporunu görmesini engellemek için
        $mail->isSMTP(); // smtp protokolü kullanacaz
        $mail->SMTPKeepAlive = true; // canlı tut
        $mail->SMTPAuth = true; //doğrulama
        $mail->SMTPSecure = 'tls'; // veya ssl
        $mail->CharSet = 'utf-8';
        $mail->Encoding = 'base64';
        $mail->Port = $this->port; // default 25. 465, 587 // 587 ile hotmail gonderildi
        $mail->Host = $this->host; // hotmail icin stmp yi kullanacaz // smtp.gmail.com // gmail ile ics gondeirminde sorun var. ics formatiyla ilgili.

        $mail->Username = $this->username; // takvime giris yapan kullanicinin id pwsi.
        $mail->Password = $this->password;

        // normalde organize edenin bilgileri gelir.
        // ortak mail kullanilacaksa o mailin bilgileri girilmeli.
        $mail->setFrom($this->username);

        $priorityDetay = '';
        if($priority == 1){
            $priorityDetay = 'Yüksek Önem Düzeyi.';
        }
        else if($priority == 9){
            $priorityDetay = 'Düşük Önem Düzeyi';
        }
        else if($priority == 5){
            $priorityDetay = 'Normal Önem Düzeyi';
        }

        foreach ($attendeeTotalDetay as $user) { // atthendeeTotalDetay dizisini yazdirir.
            // mailin kime gonderilecegi bilgisi
            $mail->addAddress($user['mailAttendee'], $user['realnameAttendee']);

            // html içeriği olacak diyoruz
            $mail->isHTML(true);

            // mailin body kisminda bir html template yolluyoruz.
            $body = file_get_contents('../presentation/mail-template/mail-invate-template.html'); // kullanacagimiz mail template
            $gelen = ['@realnameAttendee', '@realnameOrganizer', '@summary', '@dtStart', '@dtEnd',
                        '@location', '@class', '@priority', '@description', '@uid', '@usernameAttendee']; // mail template icerisindeki
            $giden = [$user['realnameAttendee'], $realnameOrganizer, $summary, $dtStart, $dtEnd,
                        $location, $class, $priorityDetay, $description, $uid, $user['usernameAttendee']];
            $body = str_replace($gelen,$giden,$body);

            // toplanti basligi
            $mail->Subject = $summary;
            // body yerine msgHTML icerigi yolladik.
            $mail->Body = 'Test';
            $mail->msgHTML($body);

            try {
                $mail->send(); // mail gonderilecek, hata alirsak ekrana verecek.
            } catch (Exception $e) {
                die ("Mail gönderimi başarısız" . $mail->ErrorInfo);
            }

            $mail->ClearAddresses(); // mail gonderimi yapildiktan sonra diger kullaniciya gecebilmek icin
            $mail->ClearAttachments(); // gonderdigimiz adresleri ve attachemnt olaylarini sildik.
        }
    }

    // katilimci degisiklik onerdi diye organize edene mail gidecek
    public function degisiklikOnerisiGonder($uid, $mailOrganizer, $realnameOrganizer, $usernamePropose, $realnamePropose, $summary, $dtStart, $dtEnd,
                                            $location, $priority, $class, $description){
        $mail = new \PHPMailer\PHPMailer\PHPMailer(); // PHPMailler sifini kullanacaz.
        //$mail = new PHPMailer();
        //$mail->SMTPDebug = 2; // test amacli
        $mail->SMTPDebug = 0; // son kullanıcının SMTP teslimat raporunu görmesini engellemek için
        $mail->isSMTP(); // smtp protokolü kullanacaz
        $mail->SMTPKeepAlive = true; // canlı tut
        $mail->SMTPAuth = true; //doğrulama
        $mail->SMTPSecure = 'tls'; // veya ssl
        $mail->CharSet = 'utf-8';
        $mail->Encoding = 'base64';
        $mail->Port = $this->port; // default 25. 465, 587 // 587 ile hotmail gonderildi
        $mail->Host = $this->host; // hotmail icin stmp yi kullanacaz // smtp.gmail.com // gmail ile ics gondeirminde sorun var. ics formatiyla ilgili.

        $mail->Username = $this->username; // takvime giris yapan kullanicinin id pwsi.
        $mail->Password = $this->password;


        $mail->setFrom($this->username);
        //$mail->addAddress($attendeeDetay["attendeeEmail"]);
        $mail->addAddress($mailOrganizer, $realnameOrganizer); // organize edenin mail adresi

        $mail->isHTML(true); // html içeriği olacak diyoruz

        $priorityDetay = '';
        if($priority == 1){
            $priorityDetay = 'Yüksek Önem Düzeyi.';
        }
        else if($priority == 9){
            $priorityDetay = 'Düşük Önem Düzeyi';
        }
        else if($priority == 5){
            $priorityDetay = 'Normal Önem Düzeyi';
        }

        // mailin body kisminda bir html template yolluyoruz.
        $body = file_get_contents('../presentation/mail-template/mail-propose-template.html'); // kullanacagimiz mail template
        $gelen = ['@realnamePropose', '@realnameOrganizer', '@summary', '@dtStart', '@dtEnd',
            '@location', '@class', '@priority', '@description', '@uid' , '@usernamePropose']; // mail template icerisindeki
        $giden = [$realnamePropose, $realnameOrganizer, $summary, $dtStart, $dtEnd,
            $location, $class, $priorityDetay, $description, $uid, $usernamePropose];
        $body = str_replace($gelen,$giden,$body);


        $mail->Subject = 'Toplantı Hk.';
        $mail->Body = 'Toplantı Bilgileri';
        $mail->msgHTML($body);

        try {
            $mail->send(); // mail gonderilecek, hata alirsak ekrana verecek.
        } catch (Exception $e) {
            echo "Mail gönderimi başarısız" . $mail->ErrorInfo;
        }

        $mail->ClearAddresses(); // mail gonderimi yapildiktan sonra diger kullaniciya gecebilmek icin
        $mail->ClearAttachments(); // gonderdigimiz adresleri ve attachemnt olaylarini sildik.
    }

    // kabul edene veya belki diyene .ics uzantılı takvim davetiyesi gidecek.
    public function toplantiDavetiyesiGonder($uid, $realnameAttendee, $mailAttendee, $toplantiBilgileri){
        $mail = new \PHPMailer\PHPMailer\PHPMailer(); // PHPMailler sifini kullanacaz.
        $mail->SMTPDebug = 0; // son kullanıcının SMTP teslimat raporunu görmesini engellemek için
        $mail->isSMTP(); // smtp protokolü kullanacaz
        $mail->SMTPKeepAlive = true; // canlı tut
        $mail->SMTPAuth = true; //doğrulama
        $mail->SMTPSecure = 'tls'; // veya ssl
        $mail->CharSet = 'utf-8';
        $mail->Encoding = 'base64';
        $mail->Port = $this->port; // default 25. 465, 587 // 587 ile hotmail gonderildi
        $mail->Host = $this->host; // hotmail icin stmp yi kullanacaz // smtp.gmail.com // gmail ile ics gondeirminde sorun var. ics formatiyla ilgili.


        foreach ($toplantiBilgileri as $toplantiBilgileriDetay){
            $summary = $toplantiBilgileriDetay['summary'];
            $class = $toplantiBilgileriDetay['class'];
            $created = $toplantiBilgileriDetay['created'];
            $lastModified = $toplantiBilgileriDetay['lastModified'];
            $dtStart = $toplantiBilgileriDetay['dtStart'];
            $dtEnd = $toplantiBilgileriDetay['dtEnd'];
            $location = $toplantiBilgileriDetay['location'];
            $description = $toplantiBilgileriDetay['description'];
            $priority = $toplantiBilgileriDetay['priority'];
            $sequance = $toplantiBilgileriDetay['sequance'];
        }

        $mail->Username = $this->username; // takvime giris yapan kullanicinin id pwsi.
        $mail->Password = $this->password;

        $mail->setFrom($this->username); // organize edenin bilgileri


        if(empty($description)){
            $description = '\n';
        }

        $dtStartExplode = explode(' ', $dtStart);
        $dtEndExplode = explode(' ', $dtEnd);
        $dtStartDateExplode = explode('-', $dtStartExplode[0]);
        $dtEndDateExplode = explode('-', $dtEndExplode[0]);
        $dtStartTimeExplode = explode(':', $dtStartExplode[1]);
        $dtEndTimeExplode = explode(':', $dtEndExplode[1]);

        $createdExplode = explode(' ', $created);
        $createdDateExplode = explode('-', $createdExplode[0]);
        $createdTimeExplode = explode(':', $createdExplode[1]);
        $createdTotalDateMailFormat = implode($createdDateExplode).'T'.implode($createdTimeExplode).'Z';

        $lastModifiedExplode = explode(' ', $lastModified);
        $lastModifiedDateExplode = explode('-', $lastModifiedExplode[0]);
        $lastModifiedTimeExplode = explode(':', $lastModifiedExplode[1]);
        $lastModifiedTotalDateMailFormat = implode($lastModifiedDateExplode).'T'.implode($lastModifiedTimeExplode).'Z';

        // zamani istedigimiz formata donusturecez.
        $startTotalDateMailFormat = implode($dtStartDateExplode).'T'.$dtStartTimeExplode[0].$dtStartTimeExplode[1].'00';
        $endTotalDateMailFormat = implode($dtEndDateExplode).'T'.$dtEndTimeExplode[0].$dtEndTimeExplode[1].'00';

        //$mail->addAddress($attendeeDetay["attendeeEmail"]);
        $mail->addAddress($mailAttendee, $realnameAttendee); // mailin kime gidecegi
        $method = 'REQUEST'; //CANCEL

        $mail->isHTML(true); // html içeriği olacak diyoruz,
        $icalFormat = 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//A//B//Microsoft Corporation//Outlook 16.0 MIMEDIR//EN
CALSCALE:GREGORIAN
METHOD:'.$method.'
BEGIN:VEVENT
DTSTAMP:' . $createdTotalDateMailFormat . '
CREATED:' . $createdTotalDateMailFormat . '
LAST-MODIFIED:' . $lastModifiedTotalDateMailFormat . '
DTSTART:' . $startTotalDateMailFormat . '
DTEND:' . $endTotalDateMailFormat . '
LOCATION:' . $location . '
UID:' . $uid . '
ATTENDEE;CN=' . $mailAttendee . ';RSVP=TRUE:mailto:' . $mailAttendee . '
CLASS:' . $class . '
DESCRIPTION:' . $description . '
PRIORITY:' . $priority . '
SEQUENCE:'.$sequance.'
STATUS:CONFIRMED
SUMMARY:' . $summary . '
TRANSP:OPAQUE
BEGIN:VALARM
TRIGGER:-PT15M
ACTION:DISPLAY
DESCRIPTION:Reminder
END:VALARM
END:VEVENT
BEGIN:VCALENDAR';


        $body = file_get_contents('../presentation/mail-template/mail-thank-template.html'); // kullanacagimiz mail template
        $gelen = ["@realnameAttendee"]; // mail template icerisindeki
        $giden = [$realnameAttendee];

        $body = str_replace($gelen,$giden,$body);


        $mail->Subject = $summary;
        $mail->Body = 'Toplantı Bilgisi';
        //$mail->Body = $body;
        $mail->msgHTML($body);

        $mail->ContentType = 'application/ics';
        $mail->Ical = $icalFormat; // ical format

        try {
            $mail->send(); // mail gonderilecek, hata alirsak ekrana verecek.
        } catch (Exception $e) {
            echo "Mail gönderimi başarısız" . $mail->ErrorInfo;
        }

        $mail->ClearAddresses(); // mail gonderimi yapildiktan sonra diger kullaniciya gecebilmek icin
        $mail->ClearAttachments(); // gonderdigimiz adresleri ve attachemnt olaylarini sildik.
    }

    // toplanti iptal edilirse katilimcilara toplantiyi takvimlerinden silmeleri icin .ics uzantili takvim davetiyesi gonderilir.
    public function toplantiIptalDavetiyesiGonder($uid, $attendeeTotalDetayDelete, $toplantiBilgileri){
        $mail = new \PHPMailer\PHPMailer\PHPMailer(); // PHPMailler sifini kullanacaz.
        $mail->SMTPDebug = 0; // son kullanıcının SMTP teslimat raporunu görmesini engellemek için
        $mail->isSMTP(); // smtp protokolü kullanacaz
        $mail->SMTPKeepAlive = true; // canlı tut
        $mail->SMTPAuth = true; //doğrulama
        $mail->SMTPSecure = 'tls'; // veya ssl
        $mail->CharSet = 'utf-8';
        $mail->Encoding = 'base64';
        $mail->Port = $this->port; // default 25. 465, 587 // 587 ile hotmail gonderildi
        $mail->Host = $this->host; // hotmail icin stmp yi kullanacaz // smtp.gmail.com // gmail ile ics gondeirminde sorun var. ics formatiyla ilgili.


        foreach ($toplantiBilgileri as $toplantiBilgileriDetay){
            $summary = $toplantiBilgileriDetay['summary'];
            $class = $toplantiBilgileriDetay['class'];
            $created = $toplantiBilgileriDetay['created'];
            $lastModified = $toplantiBilgileriDetay['lastModified'];
            $dtStart = $toplantiBilgileriDetay['dtStart'];
            $dtEnd = $toplantiBilgileriDetay['dtEnd'];
            $location = $toplantiBilgileriDetay['location'];
            $description = $toplantiBilgileriDetay['description'];
            $priority = $toplantiBilgileriDetay['priority'];
            $sequance = $toplantiBilgileriDetay['sequance'];
        }


        $mail->Username = $this->username; // takvime giris yapan kullanicinin id pwsi.
        $mail->Password = $this->password;

        $mail->setFrom($this->username); // organize edenin bilgileri


        if(empty($description)){
            $description = '\n';
        }

        $dtStartExplode = explode(' ', $dtStart);
        $dtEndExplode = explode(' ', $dtEnd);
        $dtStartDateExplode = explode('-', $dtStartExplode[0]);
        $dtEndDateExplode = explode('-', $dtEndExplode[0]);
        $dtStartTimeExplode = explode(':', $dtStartExplode[1]);
        $dtEndTimeExplode = explode(':', $dtEndExplode[1]);

        $createdExplode = explode(' ', $created);
        $createdDateExplode = explode('-', $createdExplode[0]);
        $createdTimeExplode = explode(':', $createdExplode[1]);
        $createdTotalDateMailFormat = implode($createdDateExplode).'T'.implode($createdTimeExplode).'Z';

        $lastModifiedExplode = explode(' ', $lastModified);
        $lastModifiedDateExplode = explode('-', $lastModifiedExplode[0]);
        $lastModifiedTimeExplode = explode(':', $lastModifiedExplode[1]);
        $lastModifiedTotalDateMailFormat = implode($lastModifiedDateExplode).'T'.implode($lastModifiedTimeExplode).'Z';

        // zamani istedigimiz formata donusturecez.
        $startTotalDateMailFormat = implode($dtStartDateExplode).'T'.$dtStartTimeExplode[0].$dtStartTimeExplode[1].'00';
        $endTotalDateMailFormat = implode($dtEndDateExplode).'T'.$dtEndTimeExplode[0].$dtEndTimeExplode[1].'00';

        $priorityDetay = '';
        if($priority == 1){
            $priorityDetay = 'Yüksek Önem Düzeyi.';
        }
        else if($priority == 9){
            $priorityDetay = 'Düşük Önem Düzeyi';
        }
        else if($priority == 5){
            $priorityDetay = 'Normal Önem Düzeyi';
        }

        //$mail->addAddress($attendeeDetay["attendeeEmail"]);
        foreach ($attendeeTotalDetayDelete as $user){ // cikartilan katilimcilarin bilgileri
            $mail->addAddress($user['mailAttendee'], $user['realnameAttendee']); // mailin kime gidecegi
            $method = 'CANCEL'; //CANCEL

            $mail->isHTML(true); // html içeriği olacak diyoruz,
            $icalFormat = 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//A//B//Microsoft Corporation//Outlook 16.0 MIMEDIR//EN
CALSCALE:GREGORIAN
METHOD:'.$method.'
BEGIN:VEVENT
DTSTAMP:' . $createdTotalDateMailFormat . '
CREATED:' . $createdTotalDateMailFormat . '
LAST-MODIFIED:' . $lastModifiedTotalDateMailFormat . '
DTSTART:' . $startTotalDateMailFormat . '
DTEND:' . $endTotalDateMailFormat . '
LOCATION:' . $location . '
UID:' . $uid . '
ATTENDEE;CN=' . $user['mailAttendee'] . ';RSVP=TRUE:mailto:' . $user['realnameAttendee'] . '
CLASS:' . $class . '
DESCRIPTION:' . $description . '
PRIORITY:' . $priority . '
SEQUENCE:'.$sequance.'
STATUS:CONFIRMED
SUMMARY:' . $summary . '
TRANSP:OPAQUE
BEGIN:VALARM
TRIGGER:-PT15M
ACTION:DISPLAY
DESCRIPTION:Reminder
END:VALARM
END:VEVENT
BEGIN:VCALENDAR';


            $body = file_get_contents('../presentation/mail-template/mail-delete-template.html'); // kullanacagimiz mail template
            $gelen = ['@realnameAttendee', '@summary', '@dtStart', '@dtEnd',
                '@location', '@class', '@priority', '@description', '@uid', '@usernameAttendee']; // mail template icerisindeki
            $giden = [$user['realnameAttendee'], $summary, $dtStart, $dtEnd,
                $location, $class, $priorityDetay, $description, $uid, $user['usernameAttendee']];
            $body = str_replace($gelen,$giden,$body);

            $body = str_replace($gelen,$giden,$body);


            $mail->Subject = $summary;
            $mail->Body = 'Toplantı Bilgisi';
            //$mail->Body = $body;
            $mail->ContentType = 'application/ics';
            $mail->msgHTML($body);
            $mail->Ical = $icalFormat; // ical format

            try {
                $mail->send(); // mail gonderilecek, hata alirsak ekrana verecek.
            } catch (Exception $e) {
                echo "Mail gönderimi başarısız" . $mail->ErrorInfo;
            }

            $mail->ClearAddresses(); // mail gonderimi yapildiktan sonra diger kullaniciya gecebilmek icin
            $mail->ClearAttachments(); // gonderdigimiz adresleri ve attachemnt olaylarini sildik.
        }

    }

    // guncelleme yapilan toplanti icin davetiye gonderir yine detaylari gor der.
    public function toplantiGuncellemeBilgisiGonder($uid, $realnameOrganizer, $attendeeTotalDetay, $summary, $dtStart, $dtEnd,
                                                    $location, $class, $priority, $description){
        $mail = new \PHPMailer\PHPMailer\PHPMailer(); // PHPMailler sifini kullanacaz.
        $mail->SMTPDebug = 0; // son kullanıcının SMTP teslimat raporunu görmesini engellemek için
        $mail->isSMTP(); // smtp protokolü kullanacaz
        $mail->SMTPKeepAlive = true; // canlı tut
        $mail->SMTPAuth = true; //doğrulama
        $mail->SMTPSecure = 'tls'; // veya ssl
        $mail->CharSet = 'utf-8';
        $mail->Encoding = 'base64';
        $mail->Port = $this->port; // default 25. 465, 587 // 587 ile hotmail gonderildi
        $mail->Host = $this->host; // hotmail icin stmp yi kullanacaz // smtp.gmail.com // gmail ile ics gondeirminde sorun var. ics formatiyla ilgili.

        $mail->Username = $this->username; // takvime giris yapan kullanicinin id pwsi.
        $mail->Password = $this->password;

        // normalde organize edenin bilgileri gelir.
        // ortak mail kullanilacaksa o mailin bilgileri girilmeli.
        $mail->setFrom($this->username);

        $priorityDetay = '';
        if($priority == 1){
            $priorityDetay = 'Yüksek Önem Düzeyi.';
        }
        else if($priority == 9){
            $priorityDetay = 'Düşük Önem Düzeyi';
        }
        else if($priority == 5){
            $priorityDetay = 'Normal Önem Düzeyi';
        }


        foreach ($attendeeTotalDetay as $user) { // atthendeeTotalDetay dizisini yazdirir.
            // mailin kime gonderilecegi bilgisi
            $mail->addAddress($user['mailAttendee'], $user['realnameAttendee']);

            // html içeriği olacak diyoruz
            $mail->isHTML(true);

            // mailin body kisminda bir html template yolluyoruz.
            $body = file_get_contents('../presentation/mail-template/mail-update-template.html'); // kullanacagimiz mail template
            $gelen = ['@realnameAttendee', '@realnameOrganizer', '@summary', '@dtStart', '@dtEnd',
                '@location', '@class', '@priority', '@description', '@uid', '@usernameAttendee']; // mail template icerisindeki
            $giden = [$user['realnameAttendee'], $realnameOrganizer, $summary, $dtStart, $dtEnd,
                $location, $class, $priorityDetay, $description, $uid, $user['usernameAttendee']];
            $body = str_replace($gelen,$giden,$body);

            // toplanti basligi
            $mail->Subject = $summary;
            // body yerine msgHTML icerigi yolladik.
            $mail->Body = 'Test';
            $mail->msgHTML($body);

            try {
                $mail->send(); // mail gonderilecek, hata alirsak ekrana verecek.
            } catch (Exception $e) {
                die ("Mail gönderimi başarısız" . $mail->ErrorInfo);
            }

            $mail->ClearAddresses(); // mail gonderimi yapildiktan sonra diger kullaniciya gecebilmek icin
            $mail->ClearAttachments(); // gonderdigimiz adresleri ve attachemnt olaylarini sildik.
        }
    }

    // katilimci cevap verirken kullanacaya mesaj atmak isterse calisir
    public function organizereMailGonder($realnameOrganizer, $mailOrganizer, $realnameAttendee, $description, $uid){        $mail = new \PHPMailer\PHPMailer\PHPMailer(); // PHPMailler sifini kullanacaz.
        $mail->SMTPDebug = 0; // son kullanıcının SMTP teslimat raporunu görmesini engellemek için
        $mail->isSMTP(); // smtp protokolü kullanacaz
        $mail->SMTPKeepAlive = true; // canlı tut
        $mail->SMTPAuth = true; //doğrulama
        $mail->SMTPSecure = 'tls'; // veya ssl
        $mail->CharSet = 'utf-8';
        $mail->Encoding = 'base64';
        $mail->Port = $this->port; // default 25. 465, 587 // 587 ile hotmail gonderildi
        $mail->Host = $this->host; // hotmail icin stmp yi kullanacaz // smtp.gmail.com // gmail ile ics gondeirminde sorun var. ics formatiyla ilgili.

        $mail->Username = $this->username; // takvime giris yapan kullanicinin id pwsi.
        $mail->Password = $this->password;

        // normalde organize edenin bilgileri gelir.
        // ortak mail kullanilacaksa o mailin bilgileri girilmeli.
        $mail->setFrom($this->username);

        // mailin kime gonderilecegi bilgisi
        $mail->addAddress($mailOrganizer, $realnameOrganizer);

        // html içeriği olacak diyoruz
        $mail->isHTML(true);

        // mailin body kisminda bir html template yolluyoruz.
        $body = file_get_contents('../presentation/mail-template/mail-message-template.html'); // kullanacagimiz mail template
        $gelen = ['@realnameAttendee', '@realnameOrganizer', '@description', '@uid']; // mail template icerisindeki
        $giden = [$realnameAttendee, $realnameOrganizer, $description, $uid];
        $body = str_replace($gelen,$giden,$body);

        // toplanti basligi
        $mail->Subject = 'Toplantı Hk.';
        // body yerine msgHTML icerigi yolladik.
        $mail->Body = 'Test';
        $mail->msgHTML($body);

        try {
            $mail->send(); // mail gonderilecek, hata alirsak ekrana verecek.
        } catch (Exception $e) {
            die ("Mail gönderimi başarısız" . $mail->ErrorInfo);
        }

        $mail->ClearAddresses(); // mail gonderimi yapildiktan sonra diger kullaniciya gecebilmek icin
        $mail->ClearAttachments(); // gonderdigimiz adresleri ve attachemnt olaylarini sildik.
    }




} // class bitisi

?>
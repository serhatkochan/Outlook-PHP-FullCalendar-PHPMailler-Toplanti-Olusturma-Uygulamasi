<?php
class MySQLDAO{

    private $host = 'localhost';
    private $dbName = 'db_meeting_calendar';
    private $dbUserName = 'root';
    private $dbUserPassword = '';
    private $connection = NULL;

    public function veritabaninaBaglan(){
        try {
            $this->connection  = new PDO("mysql:host=$this->host;dbname=$this->dbName;charset=utf8", $this->dbUserName, $this->dbUserPassword);
        } catch (PDOException $pe) {
            die("Veritabanına baglanti saglanamadi" . $pe->getMessage());
        }
    }
    // veritabanından cikacak.
    public function veritabanındanCik(){
        $this->connection = null;
        /*
        if($this->connection == null){
            echo 'veritabanından cikildi';
        }
        */
    }

    // verilen toplanti bilgilerine gore yeni bir toplanti olusturacak.
    public function toplantiOlustur($uid, $usernameOrganizer, $usernameAttendee, $summary, $dtStart, $dtEnd, $location,
                                    $class, $priority, $description, $created, $sequance)
    {
        $this->veritabaninaBaglan();
        $sql = "INSERT INTO
                tbl_meeting(uid, usernameOrganizer,  usernameAttendee, summary, dtStart, dtEnd, location,
                                class, priority, description, created, lastModified, sequance)
                values ('$uid', '$usernameOrganizer', '$usernameAttendee', '$summary' , '$dtStart', '$dtEnd',
                         '$location', '$class', '$priority', '$description', '$created', '$created', '$sequance')";

        $query = $this->connection->prepare( $sql );
        if ($query == false) {
            print_r($this->connection->errorInfo());
            die ('Veritabanı hatası prepare');
        }
        $sth = $query->execute();
        if ($sth == false) {
            print_r($query->errorInfo());
            die ('Veritabanı hatası execute');
        }
        $this->veritabanındanCik();
    }
    // istenilen toplantida yapilan degisikliklere gore toplantiyi yeni haliyle kayit edecek
    public function toplantiGuncelle($uid, $usernameOrganizer, $usernameAttendee, $summary, $dtStart, $dtEnd, $location, $class, $priority, $description, $lastModified, $sequance){
        $this->veritabaninaBaglan();

        $sql = "UPDATE tbl_meeting 
                SET usernameOrganizer = '$usernameOrganizer', usernameAttendee = '$usernameAttendee', summary = '$summary', dtStart = '$dtStart', dtEnd = '$dtEnd', location = '$location',
                    class = '$class', priority = '$priority', description = '$description', lastModified = '$lastModified', sequance = '$sequance'
                WHERE `uid`='$uid'";

        $query = $this->connection->prepare( $sql );
        if ($query == false) {
            print_r($this->connection->errorInfo());
            die ('toplantiCevabiniGuncelleVeritabanı hatası prepare');
        }
        $sth = $query->execute();
        if ($sth == false) {
            print_r($query->errorInfo());
            die ('toplantiCevabiniGuncelle Veritabanı hatası execute');
        }
        $this->veritabanındanCik();
    }
    // istenilen toplantiyi siler
    public function toplantiSil($uid)
    {
        $this->veritabaninaBaglan();
        $sql = "DELETE FROM  tbl_meeting where `uid`='$uid'";

        $req = $this->connection->prepare($sql);
        $req->execute();

        $query = $this->connection->prepare($sql );
        if ($query == false) {
            print_r($this->connection->errorInfo());
            die ('Veritabanı hatası prepare');
        }
        $res = $query->execute();
        if ($res == false) {
            print_r($query->errorInfo());
            die ('Veritabanı hatası execute');
        }
        $this->veritabanındanCik();
    }
    // verdigimiz parametrelere gore toplantiyi getirir. parametre vermezsek tüm toplantıyı listele.
    // primary key, eşşsiz
    public function toplantiGetir($uid = NULL, $dtStart = NULL, $dtEnd = NULL, $location = NULL){
        $where = '';
        if($uid){ // uid geldiyse uide gore toplanti var mi onun aramasini yapar.
            $where = "WHERE `uid`='$uid'";
        }
        else if($location){ // uid gelmediyse locationu kontrol eder
            if($dtStart){ // eger zaman bilgileri geldiyse bu zaman araliginda ve konumda olan toplantilari getirir
                $where = "WHERE `location`='$location' AND `dtStart` BETWEEN '$dtStart' AND '$dtStart'";
            }
            else if($dtEnd){
                $where = "WHERE `location`='$location' AND `dtEnd` BETWEEN '$dtEnd' AND '$dtEnd'";
            }
            else{
                $where = "WHERE `location`='$location'"; // zaman bilgisi gelmediyse sadece konum bilgisine gore getirir
            }
        }
        $this->veritabaninaBaglan();
        $sql = "SELECT * FROM tbl_meeting ". $where;
        $res = $this->connection->prepare($sql);
        $res->execute();

        $toplantiBiglileri = $res->fetchAll();

        $this->veritabanındanCik();
        return $toplantiBiglileri;
    }


    // o uide ait toplanti icin oneri olusturur
    public function oneriOlustur($uid, $usernamePropose, $usernameAttendee, $summary, $dtStart, $dtEnd, $location, $class, $priority, $description){
        $this->veritabaninaBaglan();
        $sql = "INSERT INTO
                tbl_propose(uid, usernamePropose, usernameAttendee, summary, dtStart, dtEnd, location, class, priority, description)
                values ('$uid', '$usernamePropose', '$usernameAttendee', '$summary' , '$dtStart', '$dtEnd', '$location', '$class', '$priority', '$description')";

        $query = $this->connection->prepare( $sql );
        if ($query == false) {
            print_r($this->connection->errorInfo());
            die ('Veritabanı hatası prepare');
        }
        $sth = $query->execute();
        if ($sth == false) {
            print_r($query->errorInfo());
            die ('Veritabanı hatası execute');
        }
        $this->veritabanındanCik();
    }
    // oneri yapan kisi yeni bir oneri sundugunda eski onerisini guncelleyecegiz.
    public function oneriGuncelle($uid, $usernamePropose, $usernameAttendee, $summary, $dtStart, $dtEnd, $location, $class, $priority, $description){
        $this->veritabaninaBaglan();
        // o uid ve oneri yapanin idsine gore oneriyi guncelle
        $sql = "UPDATE tbl_propose 
                SET usernameAttendee = '$usernameAttendee', summary = '$summary', dtStart = '$dtStart', dtEnd = '$dtEnd', location = '$location', 
                    class = '$class', priority = '$priority', description = '$description'
                WHERE `uid`='$uid' AND `usernamePropose`='$usernamePropose'";

        $query = $this->connection->prepare( $sql );
        if ($query == false) {
            print_r($this->connection->errorInfo());
            die ('toplantiCevabiniGuncelleVeritabanı hatası prepare');
        }
        $sth = $query->execute();
        if ($sth == false) {
            print_r($query->errorInfo());
            die ('toplantiCevabiniGuncelle Veritabanı hatası execute');
        }
        $this->veritabanındanCik();
    }
    // kisi toplantidan cikartildiginda onerisi silinir veya toplanti iptal edildiginde butun oneriler silinir.
    public function oneriSil($uid, $idProposeAttendee = NULL){
        if($idProposeAttendee){ // eger oneriyi yapna kullanicinin idsi geldiyse o kullanicinin toplantisi silinir.
            $where = "WHERE `uid`='$uid' AND `idProposeAttendee`='$idProposeAttendee'";
        }
        else{ // gelmediyse o uide ait butun toplanti onerileri silinir.
            $where = "WHERE `uid`='$uid'";
        }
        $this->veritabaninaBaglan();
        $sql = "DELETE FROM  tbl_propose ". $where;

        $req = $this->connection->prepare($sql);
        $req->execute();

        $query = $this->connection->prepare($sql );
        if ($query == false) {
            print_r($this->connection->errorInfo());
            die ('Veritabanı hatası prepare');
        }
        $res = $query->execute();
        if ($res == false) {
            print_r($query->errorInfo());
            die ('Veritabanı hatası execute');
        }
        $this->veritabanındanCik();
    }
    // o uide ait butun onerileri getirir
    public function oneriGetir($uid, $usernamePropose = NULL){
        $where = '';
        if($usernamePropose){
            $where = "WHERE `uid`='$uid' AND `usernamePropose`='$usernamePropose'";
        }
        else{
            $where = "WHERE `uid`='$uid'";
        }
        $this->veritabaninaBaglan(); // password bilgisini geri dondurmeyecegiz.
        $sql = "SELECT * FROM tbl_propose ". $where;
        $res = $this->connection->prepare($sql);
        $res->execute();

        $kullaniciBilgileri = $res->fetchAll();

        $this->veritabanındanCik();
        return $kullaniciBilgileri; // alınan kullanici bilgilerini geri döndürsün.
    }


    // kisi toplantiya katilim cevabini verdiginde o kisinin idsi ile cevap olusturacagiz.
    public function cevapOlustur($uid, $usernameAttendee, $reply){
        $this->veritabaninaBaglan();
        $sql = "INSERT INTO
                tbl_reply(uid, usernameAttendee, reply)
                values ('$uid', '$usernameAttendee', '$reply')";

        $query = $this->connection->prepare($sql);
        if ($query == false) {
            print_r($this->connection->errorInfo());
            die ('toplantiyaCevapVer Veritabanı hatası prepare');
        }
        $sth = $query->execute();
        if ($sth == false) {
            print_r($query->errorInfo());
            die ('toplantiyaCevapVer Veritabanı hatası execute');
        }
        $this->veritabanındanCik();
    }
    // eger kisi toplanti cevabini guncellediyse veritabanindan guncellenir
    public function cevapGuncelle($uid, $usernameAttendee, $reply){
        $this->veritabaninaBaglan();

        $sql = "UPDATE tbl_reply 
                SET reply = '$reply' WHERE `usernameAttendee`='$usernameAttendee' AND `uid`='$uid'";

        $query = $this->connection->prepare( $sql );
        if ($query == false) {
            print_r($this->connection->errorInfo());
            die ('toplantiCevabiniGuncelleVeritabanı hatası prepare');
        }
        $sth = $query->execute();
        if ($sth == false) {
            print_r($query->errorInfo());
            die ('toplantiCevabiniGuncelle Veritabanı hatası execute');
        }
        $this->veritabanındanCik();
    }
    // toplanti iptal edilirse silinir
    public function cevapSil($uid){
        $where = "WHERE `uid`='$uid'";
        $this->veritabaninaBaglan();
        $sql = "DELETE FROM  tbl_reply ". $where;

        $req = $this->connection->prepare($sql);
        $req->execute();

        $query = $this->connection->prepare($sql );
        if ($query == false) {
            print_r($this->connection->errorInfo());
            die ('Veritabanı hatası prepare');
        }
        $res = $query->execute();
        if ($res == false) {
            print_r($query->errorInfo());
            die ('Veritabanı hatası execute');
        }
        $this->veritabanındanCik();
    }
    // eger sadece uid gelirse o toplantiya ait butun cevaplar gelir, $usernameAttendee gelirse katilimcinin cevabini aliriz
    public function cevapGetir($uid, $usernameAttendee = NULL){
        $where = '';
        if(!empty($usernameAttendee)){ // idAttendee geldiyse katilimcinin verdigi cevabi arar
            $where = "WHERE `uid`='$uid' AND `usernameAttendee`='$usernameAttendee'";
        }
        else{ // else, toplantiya ait butun cevaplari getirir
            $where = "WHERE `uid`='$uid'";
        }
        $this->veritabaninaBaglan();
        $sql = "SELECT * FROM tbl_reply ". $where;
        $res = $this->connection->prepare($sql);
        $res->execute();

        $toplantiBilgileri = $res->fetchAll();

        $this->veritabanındanCik();
        return $toplantiBilgileri;
    }


    // girdigimiz parametrelere gore olusturdugumuz ldapUser tablosundan kullanici bilgilerini getirecek
    // username, realname, mail bilgilerini bi arrayde tutacaz tutacaz.
    public function ldapUserGetir($username = NULL, $password = NULL){
        $where = '';
        if($username && $password){ // username ve password yollarsak bu bilgilere gore kullanici arayacak ve getirecek
            $where = "WHERE `username`='$username' AND `password`='$password'";
        }
        else if($username){ // sadece verilen usernameya ait bilgiler gelsin
            $where = "WHERE `username`='$username'";
        }
        $this->veritabaninaBaglan(); // password bilgisini geri dondurmeyecegiz.
        $sql = "SELECT username, realname, mail FROM tbl_ldap_user ". $where;
        $res = $this->connection->prepare($sql);
        $res->execute();

        $kullaniciBilgileri = $res->fetchAll();

        $this->veritabanındanCik();
        return $kullaniciBilgileri; // alınan kullanici bilgilerini geri döndürsün.

    }
    // ldap'dan aldıgimiz username bilgisin username olarak kullanacaz, verilen yetki icinse yetki id'yi atayacagiz.
    public function userRankOlustur($username, $idRank){
        $this->veritabaninaBaglan();
        $sql = "INSERT INTO
                tbl_user_rank(username, idRank)
                values ('$username', '$idRank')";

        $query = $this->connection->prepare( $sql );
        if ($query == false) {
            print_r($this->connection->errorInfo());
            die ('Veritabanı hatası prepare');
        }
        $sth = $query->execute();
        if ($sth == false) {
            print_r($query->errorInfo());
            die ('Veritabanı hatası execute');
        }
        $this->veritabanındanCik();
    }
    // username bilgisine gore yeni bir idRank atanabilir // sadece admin ve nember olan tabloda guncelleme islemi kullanilmamali.
    public function userRankGuncelle($username, $idRank){
        $this->veritabaninaBaglan();
        $sql = "UPDATE tbl_user_rank 
                SET idRank = '$idRank'
                WHERE `userName`='$username'";

        $query = $this->connection->prepare( $sql );
        if ($query == false) {
            print_r($this->connection->errorInfo());
            die ('toplantiCevabiniGuncelleVeritabanı hatası prepare');
        }
        $sth = $query->execute();
        if ($sth == false) {
            print_r($query->errorInfo());
            die ('toplantiCevabiniGuncelle Veritabanı hatası execute');
        }
        $this->veritabanındanCik();
    }
    // username bilgisine gore verilen yetki sifirlanirsa, yani member(varsayılan kullanici) olursa tablodan silicez
    public function userRankSil($username = NULL, $idRank = NULL){ // eger idRank gelirse o idRanka sahip butun kullanicilar bu tablodan silinir.
        $where = '';
        if($username){
            $where = "WHERE `username`='$username'";
        }
        else if($idRank){
            $where = "WHERE `idRank`='$idRank'";
        }
        $this->veritabaninaBaglan();
        $sql = "DELETE FROM tbl_user_rank ". $where;

        $req = $this->connection->prepare($sql);
        $req->execute();

        $query = $this->connection->prepare($sql );
        if ($query == false) {
            print_r($this->connection->errorInfo());
            die ('Veritabanı hatası prepare');
        }
        $res = $query->execute();
        if ($res == false) {
            print_r($query->errorInfo());
            die ('Veritabanı hatası execute');
        }
        $this->veritabanındanCik();
    }
    // o userin yetkisi var mi kontrolu saglanacak, $username gelmezse tum yetkisi olan userleri getirir veya idRank gelirse o idRanka sahip kullanicilari getirir
    public function userRankGetir($username=NULL, $idRank = NULL){
        $where = '';
        if($username){
            $where = "WHERE `username`='$username'";
        }
        else if($idRank){
            $where = "WHERE `idRank`='$idRank'";
        }
        $this->veritabaninaBaglan(); // password bilgisini geri dondurmeyecegiz.
        $sql = "SELECT * FROM tbl_user_rank ". $where;
        $res = $this->connection->prepare($sql);
        $res->execute();

        $kullaniciBilgileri = $res->fetchAll();

        $this->veritabanındanCik();
        return $kullaniciBilgileri; // alınan kullanici bilgilerini geri döndürsün.
    }


    // admin tablosunda yeni bir rank olusturmak istenebilir.
    public function rankOlustur($rankName){
        $this->veritabaninaBaglan();
        $sql = "INSERT INTO
                tbl_rank(rankName)
                values ('$rankName')";

        $query = $this->connection->prepare( $sql );
        if ($query == false) {
            print_r($this->connection->errorInfo());
            die ('Veritabanı hatası prepare');
        }
        $sth = $query->execute();
        if ($sth == false) {
            print_r($query->errorInfo());
            die ('Veritabanı hatası execute');
        }
        $this->veritabanındanCik();
    }
    // olusturulan ranki guncelleme
    public function rankGuncelle($idRank, $rankName){
        $this->veritabaninaBaglan();
        $sql = "UPDATE tbl_rank 
                SET rankName = '$rankName'
                WHERE `idRank`='$idRank'";

        $query = $this->connection->prepare( $sql );
        if ($query == false) {
            print_r($this->connection->errorInfo());
            die ('toplantiCevabiniGuncelleVeritabanı hatası prepare');
        }
        $sth = $query->execute();
        if ($sth == false) {
            print_r($query->errorInfo());
            die ('toplantiCevabiniGuncelle Veritabanı hatası execute');
        }
        $this->veritabanındanCik();
    }
    // rank silme
    public function rankSil($idRank){
        $this->veritabaninaBaglan();
        $sql = "DELETE FROM  tbl_rank WHERE `idRank`='$idRank'";

        $req = $this->connection->prepare($sql);
        $req->execute();

        $query = $this->connection->prepare($sql );
        if ($query == false) {
            print_r($this->connection->errorInfo());
            die ('Veritabanı hatası prepare');
        }
        $res = $query->execute();
        if ($res == false) {
            print_r($query->errorInfo());
            die ('Veritabanı hatası execute');
        }
        $this->veritabanındanCik();
    }
    // verilen idRank bilgisine gore rankin adini getirir, id
    public function rankGetir($idRank = NULL){
        $where = '';
        if($idRank){
            $where = "WHERE `idRank`='$idRank'";
        }
        $this->veritabaninaBaglan(); // password bilgisini geri dondurmeyecegiz.
        $sql = "SELECT * FROM tbl_rank ". $where;
        $res = $this->connection->prepare($sql);
        $res->execute();

        $kullaniciBilgileri = $res->fetchAll();

        $this->veritabanındanCik();
        return $kullaniciBilgileri; // alınan kullanici bilgilerini geri döndürsün.
    }


    // admin panelinden oda olusturulabilecek
    public function odaOlustur($roomName, $roomColor){
        $this->veritabaninaBaglan();
        $sql = "INSERT INTO
                tbl_room(roomName, roomColor)
                values ('$roomName', '$roomColor')";

        $query = $this->connection->prepare($sql);
        if ($query == false) {
            print_r($this->connection->errorInfo());
            die ('toplantiyaCevapVer Veritabanı hatası prepare');
        }
        $sth = $query->execute();
        if ($sth == false) {
            print_r($query->errorInfo());
            die ('toplantiyaCevapVer Veritabanı hatası execute');
        }
        $this->veritabanındanCik();
    }
    // oda guncellenirse kullanici sadece roomName veya roomColor guncellemek isteyebilir veya ikiside.
    public function odaGuncelle($idRoom, $roomName = NULL, $roomColor = NULL){
        $set = "";
        if($roomName && $roomColor){
            $set = "SET `roomName`='$roomName', `roomColor`='$roomColor'";
            $query = "UPDATE users SET username = '$roomName', password = '$roomColor' WHERE id = $idRoom ";
        }
        else if($roomName){
            $set = "SET `roomName`='$roomName'";
        }
        else if($roomColor){
            $set = "SET `roomColor`='$roomColor'";
        }
        $this->veritabaninaBaglan();

        $sql = "UPDATE tbl_room "
            . $set. " WHERE `idRoom`='$idRoom'";

        $query = $this->connection->prepare( $sql );
        if ($query == false) {
            print_r($this->connection->errorInfo());
            die ('toplantiCevabiniGuncelleVeritabanı hatası prepare');
        }
        $sth = $query->execute();
        if ($sth == false) {
            print_r($query->errorInfo());
            die ('toplantiCevabiniGuncelle Veritabanı hatası execute');
        }
        $this->veritabanındanCik();
    }
    // oda silinmek istenirse
    public function odaSil($idRoom){
        $where = '';
        $where = "WHERE `idRoom`='$idRoom'";
        $this->veritabaninaBaglan();
        $sql = "DELETE FROM  tbl_room ". $where;

        $req = $this->connection->prepare($sql);
        $req->execute();

        $query = $this->connection->prepare($sql );
        if ($query == false) {
            print_r($this->connection->errorInfo());
            die ('Veritabanı hatası prepare');
        }
        $res = $query->execute();
        if ($res == false) {
            print_r($query->errorInfo());
            die ('Veritabanı hatası execute');
        }
        $this->veritabanındanCik();
    }
    // parametre girilmezse butun odalar gelir, idRoom gilirse o odanin bilgileri gelir
    public function odaGetir($idRoom = NULL){
        $where = '';
        if($idRoom){
            $where = "WHERE `idRoom`='$idRoom'";
        }
        $this->veritabaninaBaglan();
        $sql = "SELECT * FROM tbl_room ". $where;
        $res = $this->connection->prepare($sql);
        $res->execute();

        $toplantiBiglileri = $res->fetchAll();

        $this->veritabanındanCik();
        return $toplantiBiglileri;
    }


    // secilen tarihte o odada toplanti var mi kontrolleri yapilacak
    public function zamanAraliginiSorgulaStart($startTotalDate, $endTotalDate, $location, $uid=NULL){ // toplanti herhangi bir toplantinin baslangic tarihiyle eslesiyor mu
        $this->veritabaninaBaglan();

        $sql = '';
        if(!empty($uid)){
            $sql = "SELECT * FROM tbl_meeting WHERE `uid`<>'$uid' AND `location`='$location' AND `dtStart` BETWEEN '$startTotalDate' AND '$endTotalDate' ";
        }
        else{
            $sql = "SELECT * FROM tbl_meeting WHERE `location`='$location' AND `dtStart` BETWEEN '$startTotalDate' AND '$endTotalDate' ";
        }
        $res = $this->connection->prepare($sql);
        $res->execute();

        $varMi = $res->fetchAll();

        $this->veritabanındanCik();
        return $varMi;
    }
    public function zamanAraliginiSorgulaEnd($startTotalDate, $endTotalDate, $location, $uid=NULL){ // bitis tarihiyle eslesiyor mu
        $this->veritabaninaBaglan();

        $sql = '';
        if(!empty($uid)){
            $sql = "SELECT * FROM tbl_meeting WHERE `uid`<>'$uid' AND `location`='$location' AND `dtEnd` BETWEEN '$startTotalDate' AND '$endTotalDate' ";
        }
        else{
            $sql = "SELECT * FROM tbl_meeting WHERE `location`='$location' AND `dtEnd` BETWEEN '$startTotalDate' AND '$endTotalDate' ";
        }

        $res = $this->connection->prepare($sql);
        $res->execute();

        $varMi = $res->fetchAll();

        $this->veritabanındanCik();
        return $varMi;
    }


}
?>
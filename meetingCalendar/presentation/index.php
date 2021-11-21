<?php

if(!isset($_SESSION)) {
    session_start();
}
if(!isset($_SESSION['username']) && !isset($_SESSION['realname']) && !isset($_SESSION['mail']) && !isset($_SESSION['rankName'])){ // sessionlar gelmediyse
    echo '<script>location.href = "login.php"</script>';
    exit();
}

require_once('../business/BusinessManager.php');
$businessManager = new BusinessManager('MySQLDAO');

$events = NULL;
if(isset($_GET['roomName'])){ // odalara gore toplantilar listelenecek.
    $roomName = htmlspecialchars(strip_tags(addslashes(trim($_GET['roomName']))));
    $events = $businessManager->toplantiGetir(NULL, NULL, NULL, $roomName);
}
else{
    $events = $businessManager->toplantiGetir(); // veritabanindaki butun toplanti bilgilerini $events degiskeninde tutar.
}
if(isset($_GET['ok'])){
    $getOk = htmlspecialchars(strip_tags(addslashes(trim($_GET['ok']))));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Meeting Calendar</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href='css/fullcalendar.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="css/loader.css">

    <!-- Custom CSS -->
    <style>
        body {
            padding-top: 70px;  /* .navbar-fixed-top. için gerekli.*/
        }
        #calendar {
            max-width: 900px;
        }
        .col-centered{
            float: none;
            margin: 0 auto;
        }
        a{
            border-radius: 7px; line-height: 35px; margin-right: 3px; padding: 10px; letter-spacing: 1.4px; text-decoration: none; color:#fff;
        }
        .dropdown p{
            border-radius: 7px; line-height: 35px; margin-right: 3px; padding: 5px 20px; letter-spacing: 1.4px; text-decoration: none;
            z-index: 5;
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
            z-index: 5;
        }

        /* Dropdown Content (Hidden by Default) */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 250px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            left: 0px;
            z-index: 5;
            padding: 2rem;
        }

        /* Links inside the dropdown */
        .dropdown-content a{
            color: black;
            padding: 3px 3px;
            text-decoration: none;
            display: block;
            z-index: 5;
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
            z-index: 5;

        }

        /* Show the dropdown menu on hover */
        .dropdown:hover .dropdown-content {
            display: block;
            z-index: 5;
        }

    </style>
</head>
<body">

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

<!-- Page Content -->
<div class="container">
    <div class="row">

        <div class="col-lg-12 text-center">
            <div id="calendar" class="col-centered">
            </div>
        </div>
    </div>
</div>

<!-- include modal -->
<?php require_once('addModal.php')  ?> <!-- randevu olusturmak icin acilacak olan modal -->

<?php require_once('editModal.php') ?> <!-- cift tiklama ile randevuyu duzenlemek icin acilacak olan modal -->

<?php require_once('alertModal.php') ?> <!-- uyari mesaji getirecek olan modal -->

<?php require_once('toplantiYokModal.php') ?> <!-- toplanti yoksa uyari verecek modal -->

<!-- /.container -->

<!-- jQuery Version 1.11.1 -->
<script src="js/jquery.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<!-- FullCalendar -->
<script src='js/moment.min.js'></script>
<script src='js/fullcalendar.min.js'></script>


<script>
    $(document).ready(function() {
        var date = new Date(); // bugunun bilgisi alinacak
        var yyyy = date.getFullYear().toString();
        var mm   = (date.getMonth()+1).toString().length == 1 ? "0"+(date.getMonth()+1).toString() : (date.getMonth()+1).toString();
        var dd   = (date.getDate()).toString().length == 1 ? "0"+(date.getDate()).toString() : (date.getDate()).toString();
        var userId = '<?php echo $_SESSION['username']; ?>';

        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'basicDay,agendaWeek,month'
            },
            slotEventOverlap: true, // ayni saatlerdeki eventler ust uste biner, false ile iptal edilir. Default true gelir.
            height: $(window).height()*0.83, // boyutta sorun olursa kaldirilip elle girilebilir.
            defaultDate: yyyy+"-"+mm+"-"+dd,
            //defaultView: 'agendaWeek', // baslangic olarak haftalik gozukur
            defaultView: 'month', // aylik gozukur
            //editable: true,
            //displayEventTime : false, // pm, am saatleri kaldırır
            timeFormat: 'H(:mm)', // takvimde gozuken pm, am saat bicimini degistirir.
            eventLimit: true, // allow "more" link when too many events
            selectable: true,
            selectHelper: true,
            select: function(start, end) { // yeni bir randevu olusturulurken otomatik olarak deger gozukur
                $('#ModalAdd #startDate').val(moment(start).format('YYYY-MM-DD'));
                $('#ModalAdd #endDate').val(moment(end).format('YYYY-MM-DD'));
                $('#ModalAdd #startTime').val(moment(start).format('HH:mm'));
                $('#ModalAdd #endTime').val(moment(end).format('HH:mm'));
                $('#ModalAdd #location').val('<?php if(isset($_GET["roomName"])){echo $_GET["roomName"];} ?>');

                $('.selectpicker').selectpicker('deselectAll');
                var usernameOrganizer = '<?php echo $_SESSION['username']; ?>';
                $('.selectpicker').selectpicker('val',usernameOrganizer);

                $('#ModalAdd').modal('show');
            },
            eventRender: function(event, element) {
                element.bind('dblclick', function() {
                    // dblclick metodu baslangici
                    $('#ModalEdit #modalHeader').html(event.realnameOrganizer + ' Toplantisi.');
                    $('#ModalEdit #summary').val(event.summary);
                    $('#ModalEdit #startDate').val(moment(event.start).format('YYYY-MM-DD'));
                    $('#ModalEdit #endDate').val(moment(event.end).format('YYYY-MM-DD'));
                    $('#ModalEdit #startTime').val(moment(event.start).format('HH:mm'));
                    $('#ModalEdit #endTime').val(moment(event.end).format('HH:mm'));
                    $('#ModalEdit #priority').val(event.priority);
                    $('#ModalEdit #description').val(event.description);
                    if(event.class == 'PRIVATE'){
                        $('#ModalEdit #class').prop("checked", true);
                    }
                    else{
                        $('#ModalEdit #class').prop("checked", false);
                    } // eger veritabanindaki checkbox secili ise, tik koyacak.

                    if(userId == event.usernameOrganizer || '<?php if(isset($_SESSION['rankName'])){echo $_SESSION['rankName'];}?>' == 'Admin'){ // eger organizer veya admin girisi yapildiysa
                        $('#ModalEdit #btnDigerBilgiler').show();
                        $("#aDigerBilgiler").prop("href", "proposeMeeting.php?uid="+ event.uid);
                    }
                    else{
                        $('#ModalEdit #btnDigerBilgiler').hide();
                    }

                    if(event.locationVarMi == 'var'){
                        $('#ModalEdit #location').html('<option disabled selected>' + event.location + '</option>');
                    }
                    else{
                        $('#ModalEdit #konum2Div').show();
                        $('#ModalEdit #locationForeign').val(event.location);
                    }

                    $('#ModalEdit').modal('show');
                }); // dblclick metodu bitisi
            },
            eventDrop: function(event, delta, revertFunc) { // si changement de position

                edit(event);

            },
            eventResize: function(event,dayDelta,minuteDelta,revertFunc) { // si changement de longueur

                edit(event);

            },
            events: [ // takvimi veri tabanından alır
                <?php foreach($events as $event):
                $start = explode(" ", $event['dtStart']);
                $end = explode(" ", $event['dtEnd']);
                if($start[1] == '00:00:00'){
                    $start = $start[0];
                }else{
                    $start = $event['dtStart'];
                }
                if($end[1] == '00:00:00'){
                    $end = $end[0];
                }else{
                    $end = $event['dtEnd'];
                } // baslangic ve bitis tarihleri alindi
                ?>
                {
                    uid: '<?php echo $event['uid']; ?>',
                    usernameOrganizer: '<?php echo $event['usernameOrganizer']; ?>',
                    realnameOrganizer: '<?php $realnameOrg = $businessManager->userGetir($event['usernameOrganizer']); $realname = ''; foreach ($realnameOrg as $realName){$realname =  $realName['realname'];} echo $realname; ?>',
                    locationVarMi: '<?php foreach ($rooms as $roomKontrol){if($event['location'] == $roomKontrol['roomName']){echo 'var';}} ?>',
                    usernameAttendee: '<?php echo $event['usernameAttendee']; ?>',
                    summary: '<?php echo htmlspecialchars(strip_tags(addslashes(trim($event['summary'])))); ?>',
                    start: '<?php echo $start; ?>',
                    end: '<?php echo $end; ?>',
                    location: '<?php echo htmlspecialchars(strip_tags(addslashes(trim($event['location'])))); ?>',
                    class: '<?php echo $event['class']; ?>',
                    priority: '<?php echo $event['priority']; ?>',
                    //description: '<?php echo $event['description']; ?>',
                    description: '<?php echo htmlspecialchars(strip_tags(addslashes(trim($event['description'])))); ?>',
                    title: '<?php echo htmlspecialchars(strip_tags(addslashes(trim($event['summary'])))); ?>',
                    color: '<?php foreach ($rooms as $room){
                        if($event['location'] == $room['roomName']){echo $room['roomColor'];}} ?>',
                },
                <?php endforeach; ?>

            ]
        });
        function edit(event){ // tutup surukleyince yanlislikla toplanti duzenlenip kisilere mail gidebilir.
            startDate = event.start.format('YYYY-MM-DD');
            endDate = event.end.format('YYYY-MM-DD');
            startTime = event.start.format('HH:mm');
            endTime = event.end.format('HH:mm');
            attendeeUser=event.usernameAttendee.split(';');
            usernameAttendee=[];
            for(var i=0;i<attendeeUser.length;i++){
                if(attendeeUser[i]!=""){
                    usernameAttendee.push(attendeeUser[i]);
                }
            }

            //uid =  event.uid;
            $.ajax({
                url: '', // bu islem yapilmayacak.
                type: "POST",
                data: {uid:event.uid, summary:event.summary, usernameOrganizer:event.usernameOrganizer, usernameAttendee:usernameAttendee,
                    class:event.class, location:event.location, description:event.description, priority:event.priority,
                    startDate:startDate, endDate:endDate, startTime:startTime, endTime:endTime},
            });
        }

    });

</script>

<script>
    if('<?php if(!empty($getOk)){echo $getOk;} ?>' == 'ok'){
        $('#alertModal').modal('show');
    }
    else if('<?php if(!empty($getOk)){echo $getOk;} ?>' == 'yok'){
        $('#toplantiYokModal').modal('show');
    }
</script>

</body>

</html>

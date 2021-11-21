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
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="./css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/loader.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
</head>
<body>


<!-- alertModal baslangici -->
<div id="toplantiYokModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document" style="height: 50%; padding-top: 20%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalHeader">Bilgilendirme</h4>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">Ulaşmaya çalıştığınız sayfa yok veya kaldırılmış.</h5>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tamam</button>
            </div>
        </div>
    </div>
</div>
<!-- alertModal bitisi -->
</body>
</html>


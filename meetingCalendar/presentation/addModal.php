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

<div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addForm" name="addForm" class="form-horizontal" method="POST" action="" style="">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Toplantı Oluştur</h4>
                </div>

                <div class="modal-body">
                    <!-- Başlık -->
                    <div class="form-group">
                        <img for="summary" src="https://img.icons8.com/windows/32/000000/tag-window.png" class="col-sm-1 control-label"/>
                        <div class="col-sm-10">
                            <input required="required" type="text" name="summary" class="form-control" id="summary" placeholder="Toplantı başlığı">
                        </div>
                    </div>

                    <!-- Katılımcıları Davet Et -->
                    <div class="form-group">
                        <img for="usernameAttendee[]" src="https://img.icons8.com/ios/50/000000/contact-card.png" class="col-sm-1 control-label"/>
                        <div class="col-sm-10">
                            <!-- Sayfanın en üstündeki scripler ve linkler dahil edildi -->
                            <select id="usernameAttendee[]" name="usernameAttendee[]" title="Toplantıya katılacak kişileri seçin" class="selectpicker form-control" multiple data-live-search="true" >
                                 <?php // davet gonderilecek kullanici id lerini alacaz
                                    $users = $businessManager->userGetir();
                                    foreach ($users as $user){
                                        if($user['username'] == $_SESSION['username']){
                                            echo '<option selected value="'.$user["username"].'">'. $user['realname']. ' ('.$user['mail'].')</option>';
                                        } // giris yapan kullaniciyi otomatik secili gosterecez.
                                        else{
                                            echo '<option value="'.$user["username"].'">'. $user['realname']. '('.$user['mail'].')</option>';
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
                            <input required="required" type="date" name="startDate" class="form-control" id="startDate" placeholder="Başlangıç Tarihi">
                        </div> <!-- startDate 2021-06-21 şeklinde randevu başlangıç tarihini verecek. -->
                        <div class="col-sm-2">
                            <input style="margin-top: 5px" required="required" name="startTime" id="startTime" type="time">
                        </div>
                    </div>

                    <!-- Bitiş tarihi -->
                    <div class="form-group ">
                        <span class="col-sm-1 control-label"></span>
                        <div class="col-sm-8">
                            <input required="required" type="date" name="endDate" class="form-control" id="endDate" placeholder="Bitiş Tarihi">
                        </div> <!--endDate bitiş tarihini verecek. -->
                        <div class="col-sm-2">
                            <input style="margin-top: 5px" required="required" name="endTime" id="endTime" type="time">

                        </div>
                    </div>

                    <!-- Konum  -->
                    <div class="form-group">
                        <img for="roomId" class="col-sm-1 control-label" src="https://img.icons8.com/ios/50/000000/pointer.png"/>
                        <div class="col-sm-8">
                            <select id="location" name="location" title="Toplantı odasını seçin." class="form-control">
                                <option hidden value="">Toplantı odasını seçin</option>
                                <?php // davet gonderilecek kullanici id lerini alacaz
                                $rooms = $businessManager->odaGetir();
                                foreach ($rooms as $room){ // veritabanindaki tbl_room buraya ekleniyor.
                                    echo '<option value="'.$room['roomName'].'">'.$room['roomName'].'</option>';
                                }
                                ?>
                            </select>
                        </div> <!-- Toplantinin yapilacağı konumu getirecek -->
                        <div class="col-sm-2"> <!-- buton -->
                            <input class="btn btn-default" style="width: 100%" name="btnLocation" id="btnLocation" type="button" value="Diğer">
                        </div>
                    </div>

                    <!-- Konum2  -->
                    <div class="form-group" id="konum2Div" style="display: none">
                        <span class="col-sm-1 control-label"></span>
                        <div class="col-sm-10">
                            <input disabled type="text" name="locationForeign" class="form-control" id="locationForeign" placeholder="Kendin gir">
                        </div> <!-- Toplantinin yapilacağı konumu getirecek -->
                    </div>
                    <script>
                        document.getElementById("btnLocation").onclick = function() { // digerKonum butonuna tiklandiginda
                            if(document.getElementById("konum2Div").style.display == 'none'){
                                document.getElementById("konum2Div").style.display = "block";
                                document.getElementById("location").disabled = true;
                                document.getElementById("locationForeign").disabled = false;
                                document.getElementById("location").value = "";
                                document.getElementById("btnLocation").value = "Odalar";
                            }
                            else{
                                document.getElementById("konum2Div").style.display = "none";
                                document.getElementById("location").disabled = false;
                                document.getElementById("locationForeign").disabled = true;
                                document.getElementById("locationForeign").value = "";
                                document.getElementById("btnLocation").value = "Diğer";
                            }
                        }
                    </script>

                    <!-- Önem Düzeyi -->
                    <div class="form-group">
                        <img for="priority" class="col-sm-1 control-label" src="https://img.icons8.com/ios/50/000000/high-importance.png"/>
                        <div class="col-sm-6">
                            <select name="priority" class="form-control" id="priority">
                                <option hidden value="">Önem derecesi seçin.</option>
                                <option style="color: rgb(231, 72, 86);" value="1">&#9724; Yüksek Önem Düzeyi</option>
                                <option selected style="color: #1A915D"   value="5">&#9724; Normal önem düzeyi</option>
                                <option style="color: rgb(0, 188, 242);" value="9">&#9724; Düşük Önem Düzeyi</option>
                            </select>
                        </div> <!-- Toplantının önem bilgisi seçilebilir. Varsayılan olarak Normaldir. -->

                        <img for="class" class="col-sm-1 control-label" src="https://img.icons8.com/ios/50/000000/lock--v1.png"/>
                        <span class="col-sm-2 text-muted" style="padding-top: 8px;">Özel</span>
                        <div class="col-sm-2">
                            <div class="checkbox">
                                <label>
                                    <input name="class" id="class" type="checkbox" value="PRIVATE">
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Açıklama  -->
                    <div class="form-group ">
                        <img for="description" class="col-sm-1 control-label" src="https://img.icons8.com/material-outlined/24/000000/create-new.png"/>
                        <div class="col-sm-10">
                            <textarea name="description" class="form-control" id="description" rows="3" placeholder="Açıklama ekleyin"></textarea>
                        </div>
                    </div> <!-- Kullanıcı isterse açıklama girebilir. -->

                    <input type="hidden" id="usernameOrganizer" name="usernameOrganizer" value="<?php echo $_SESSION['username']?>">
                    <input type="hidden" id="realnameOrganizer" name="realnameOrganizer" value="<?php echo $_SESSION['realname']?>">


                </div>

                <div class="modal-footer">
                    <span id="addModalMessage" class="" style="display: block; text-align: ; padding-bottom: 1.5rem; color: #c7254e; user-select: none; display: none;"></span>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Çıkış</button>
                    <span id="btnAddModal" onclick="submitToplantiOlustur()" type="button" class="btn btn-primary">Toplantıyı Oluştur</span>
                </div>

            </form>
            <!-- addEvent.php ye post edecek -->
            <script>
                function submitToplantiOlustur(){
                    $('#btnAddModal').html('<span class="loader" role="status" aria-hidden="true"></span>Loading...').addClass('disabled');
                    $.ajax({
                        url: 'addEvent.php',
                        type: "POST",
                        data: $('#addForm').serialize(),
                        success: function(rep) {
                            if(rep == 'olusturuldu'){
                                location.href = "index.php?ok=ok";
                            }
                            else{
                                document.getElementById('addModalMessage').style.display = 'block';
                                $('#addModalMessage').html(rep);
                                var delayInMilliseconds = 500; // .5 second
                                setTimeout(function() { // en az yarim saniye spinner gozuksun
                                    $('#btnAddModal').html('Toplantıyı Oluştur').removeClass('disabled');
                                }, delayInMilliseconds);
                            }
                        }
                    });
                }
            </script>

        </div>
    </div>
</div>



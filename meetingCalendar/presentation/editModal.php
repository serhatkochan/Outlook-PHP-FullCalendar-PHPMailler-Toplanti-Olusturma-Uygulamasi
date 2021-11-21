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

<!-- Edit Modal -->
<div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form class="form-horizontal" method="POST" action="">
                <div id="btnDigerBilgiler" name="btnDigerBilgiler" class="modal-header text-center">
                    <a id="aDigerBilgiler" name="aDigerBilgiler" target="_blank" href="" class="btn btn-default" style="text-decoration: none;">Diğer Bilgiler ve Değişiklik Ekranı</a>
                </div>
                <div class="modal-header">
                    <h4 class="modal-title" id="modalHeader">@ToplantiBilgileri</h4>
                </div>
                <div class="modal-body">

                    <!-- Başlık -->
                    <div class="form-group">
                        <img for="summary" src="https://img.icons8.com/windows/32/000000/tag-window.png" class="col-sm-1 control-label"/>
                        <div class="col-sm-10">
                            <input disabled type="text" name="summary" class="form-control" id="summary" placeholder="Toplantı başlığı">
                        </div>
                    </div>

                    <!-- Katılımcıları Davet Et -->
                    <div class="form-group">
                        <img for="usernameAttendee[]" src="https://img.icons8.com/ios/50/000000/contact-card.png" class="col-sm-1 control-label"/>
                        <div class="col-sm-10">
                            <!-- Sayfanın en üstündeki scripler ve linkler dahil edildi -->
                            <select required="required" id="usernameAttendee[]" name="usernameAttendee[]" title="Toplantıya katılacak kullanıcı yok." class="selectpicker form-control" multiple data-live-search="true" >
                                <option disabled selected>Katılımcımlar gizli.</option>
                            </select>
                        </div>
                    </div>

                    <!-- başlangıç tarihi -->
                    <div class="form-group ">
                        <img class="col-sm-1 control-label" for="startDate" src="https://img.icons8.com/material-outlined/24/000000/clock--v1.png" />
                        <div class="col-sm-8 ">
                            <input disabled type="date" name="startDate" class="form-control" id="startDate" placeholder="Başlangıç Tarihi">
                        </div> <!-- startDate 2021-06-21 şeklinde randevu başlangıç tarihini verecek. -->
                        <div class="col-sm-2">
                            <input disabled style="margin-top: 5px" required="required" name="startTime" id="startTime" type="time">
                        </div>
                    </div>

                    <!-- Bitiş tarihi -->
                    <div class="form-group ">
                        <span class="col-sm-1 control-label"></span>
                        <div class="col-sm-8">
                            <input disabled type="date" name="endDate" class="form-control" id="endDate" placeholder="Bitiş Tarihi">
                        </div> <!--endDate bitiş tarihini verecek. -->
                        <div class="col-sm-2">
                            <input disabled style="margin-top: 5px" required="required" name="endTime" id="endTime" type="time">

                        </div>
                    </div>

                    <!-- Konum  -->
                    <div class="form-group" >
                        <img for="roomId" class="col-sm-1 control-label" src="https://img.icons8.com/ios/50/000000/pointer.png"/>
                        <div class="col-sm-8">
                            <select disabled id="location" name="location" title="Toplantı odasını seçin." class="form-control">
                                <option hidden value="">Toplantı odasını seçin</option>
                            </select>
                        </div> <!-- Toplantinin yapilacağı konumu getirecek -->
                        <div class="col-sm-2"> <!-- buton -->
                            <input disabled class="btn btn-default" style="width: 100%" name="btnLocation" id="btnLocation" type="button" value="Diğer">
                        </div>
                    </div>

                    <!-- Konum2  -->
                    <div class="form-group" id="konum2Div" style="display: none">
                        <span class="col-sm-1 control-label"></span>
                        <div class="col-sm-10">
                            <input disabled type="text" name="locationForeign" class="form-control" id="locationForeign" placeholder="Kendin gir">
                        </div> <!-- Toplantinin yapilacağı konumu getirecek -->
                    </div>

                    <!-- Önem Düzeyi -->
                    <div class="form-group">
                        <img for="priority" class="col-sm-1 control-label" src="https://img.icons8.com/ios/50/000000/high-importance.png"/>
                        <div class="col-sm-6">
                            <select disabled name="priority" class="form-control" id="priority">
                                <option hidden value="">Önem derecesi seçin.</option>
                                <option style="color: rgb(231, 72, 86);" value="1">&#9724; Yüksek Önem Düzeyi</option>
                                <option style="color: #1A915D"   value="5">&#9724; Normal önem düzeyi</option>
                                <option style="color: rgb(0, 188, 242);" value="9">&#9724; Düşük Önem Düzeyi</option>
                            </select>
                        </div> <!-- Toplantının önem bilgisi seçilebilir. Varsayılan olarak Normaldir. -->

                        <img for="class" class="col-sm-1 control-label" src="https://img.icons8.com/ios/50/000000/lock--v1.png"/>
                        <span class="col-sm-2 text-muted" style="padding-top: 8px; user-select: none;">Özel</span>
                        <div class="col-sm-2">
                            <div class="checkbox">
                                <label>
                                    <input disabled name="class" id="class" type="checkbox" value="PRIVATE">
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Açıklama  -->
                    <div class="form-group ">
                        <img for="description" class="col-sm-1 control-label" src="https://img.icons8.com/material-outlined/24/000000/create-new.png"/>
                        <div class="col-sm-10">
                            <textarea disabled name="description" class="form-control" id="description" rows="3" placeholder="Açıklama ekleyin"></textarea>
                        </div>
                    </div> <!-- Kullanıcı isterse açıklama girebilir. -->

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Çıkış Yap</button>
                </div>
            </form>
        </div>
    </div>
</div>

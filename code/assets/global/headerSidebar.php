<?php
    //Pred includem mužete nastavit proměnné $location a $PageSpecific
    //Do $location jen prostý string s informací, kde se uživatel nacházi
    //Do $PageSpecific se může dát html kód toho, co se má zobrazit v headru mezi logem a user ikonou


?>
    <div id="backgroundOverlay" class="hidden">

        <div class="preview-container">
            <div id="Arrow"></div>
            <div id="backWrap">
                <span id="close"><span class="mdi mdi-close-thick"></span></span>
            </div>
            <div id="profile">
                <img src="" alt='pf img' id="pfPic">
                <h2 id="userEmail"></h2>
                <p id="userNames"></p>
            </div>
            <div id="links">
                <a href="<?php
                    $userd = urlencode($user);
                    $uspt = "/updateProfile.php?id={$userd}";
                    echo absolutePath($uspt);
                ?>" target="_self"><span class="mdi mdi-account-cog settingsIcon"></span></a>
                <a href="<?php echo absolutePath("/logout.php")?>" target="_self"><span class="mdi mdi-logout logout"></span></a>
            </div>
        </div>

    </div>
<div id="HeaderAndSidebar">
    <div id="header">
        <div id="HeaderLeft">
            <a id="logo" href="<?php echo absolutePath('/../../pages/homepage/homepage.php') ?>">
                <img src="<?php 
                echo absolutePath('/../../assets/img/logo/logo.svg') ?>" alt="logo"> 
            </a>
            <?php
            if(isset($location)){
                echo "<div id='LocationName'><h1>";
                echo $location;
                echo "</h1></div>";
            }
            ?>
        </div>
        <div id="PageSpecific">
            <?php
                if (isset($PageSpecific)) {
                    echo $PageSpecific;
                }
                //sem si přes $PageSpecific můžete dát co chete, pokud chcete

            ?>
        </div>
        <div id="HeaderRight">
            <div class="userIconDiv">
                <img class="UserIcon" src="" alt="">
            </div>
        </div>
    </div>
    <div id="Sidebar">
        <div class="sidebarIn">
          <a href="<?php echo absolutePath('/../../pages/admin/Dashboard/Dashboard.php') ?>" target="_self">
                <div class="SidebarIcon" id="SidebarHome"></div>
                <p class="IconText" >Home</p>
            </a>
            <a href="<?php echo absolutePath('/../../pages/admin/action/createForm.php') ?>" target="_self">
                <div class="SidebarIcon" id="SidebarNewForm"></div>
                <p class="IconText">New Form</p>
            </a>
            <span id="import" class="import">
                <div class="SidebarIcon" id="SidebarImport"></div>
                <p class="IconText">Import Form</p>

            </span>
            <a href="<?php echo absolutePath('/../../pages/user/about.html') ?>" target="_blank">

                <div class="SidebarIcon" id="SidebarAbout"></div>
                <p class="IconText">About</p>
            </a>
            <a href="<?php echo absolutePath('/../../pages/user/questionsAnswers.html') ?>" target="_blank">
                <div class="SidebarIcon" id="SidebarQA"></div>
                <p class="IconText">Q&A</p>
            </a>
        </div>
    </div>
</div>
<div id="window">
    <i class="close mdi mdi-close"></i>
    <label for="file">Choose file</label>
    <input id="file" name="file" type="file"/>
</div>
<script>
    $(document).ready(function(){

        $("#file").change(function () {

            let file = $('#file')[0].files[0];

            if (file) {
                let reader = new FileReader();

                reader.onload = function (e) {
                    let fileContent = e.target.result;

                    console.log(fileContent);

                    $.ajax({
                        url:"<?= absolutePath("/importForm.php")?>",
                        type: "POST",
                        data:{data: fileContent},
                        success:function(data){
                            $("#window").fadeOut(200)
                            console.log(data)
                            window.location.replace("<?= absolutePath("/../../pages/admin/editor.php") ?>?id="+data);
                        }
                    })
                    
                };

                reader.readAsText(file);
            }
        })

        $("#import").click(function(){
            $("#window").fadeIn(200)
            $("#window").css("display", "flex")
        })

        $(".close").click(function(){
            $("#window").fadeOut(200)
        })

        $.ajax({
            url:"<?= absolutePath("/profileMenu.php")?>",
            type: "POST",
            data:{userID: <?= $user ?>},
            success:function(userdata){
                let fetchedData =JSON.parse(userdata)
                $("#userEmail").text(fetchedData.email)
                $("#userNames").text(fetchedData.name+ "  " + fetchedData.surname)
                $(".UserIcon").attr("src", "<?= absolutePath('/../img/uploads/') ?>/" + fetchedData.image)
                $("#pfPic").attr("src", "<?= absolutePath('/../img/uploads/') ?>/" + fetchedData.image)
            }
        })
    })
</script>
<?php
    // function absolutePath($path){
    //     $absolutePath = realpath(__DIR__ . $path);
    //     $serverName = $_SERVER['HTTP_HOST'];
    //     $path = 'http://' . $serverName . str_replace($_SERVER['DOCUMENT_ROOT'], '', $absolutePath);
    //     return $path;
    // }

    function absolutePath($path) {
        $serverName = $_SERVER['HTTP_HOST'];
        $absolutePath = realpath(__DIR__ . parse_url($path, PHP_URL_PATH));
    
        $url = '//' . $serverName . str_replace($_SERVER['DOCUMENT_ROOT'], '', $absolutePath);
    
        $query = parse_url($path, PHP_URL_QUERY);
        if ($query) {
            $url .= '?' . $query;
        }
    
        return $url;
    }
?>
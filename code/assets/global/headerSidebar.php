<?php
    //Pred includem mužete nastavit proměnné $location a $PageSpecific
    //Do $location jen prostý string s informací, kde se uživatel nacházi
    //Do $PageSpecific se může dát html kód toho, co se má zobrazit v headru mezi logem a user ikonou


?>
    <div id="backgroundOverlay" class="hidden">

        <div class="preview-container">
            <div id="Arrow"></div>
            <div id="backWrap">
                <span id="close">&times;</span>
            </div>
            <div id="profile">
                <img src="placeholder" alt='pf img' id="pfPic">
                <h2></h2>
                <p></p>
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
            <div id="logo">
                <img src="<?php 
                echo absolutePath('/../../assets/img/logo/logo.svg') ?>" alt="logo"> 
            </div>
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
                $userIconPath = '/../img/icons/user.svg';
            ?>
        </div>
        <div id="HeaderRight">
            <div class="userIconDiv">
                <img class="UserIcon" src="<?php echo absolutePath($userIconPath) ?>" alt="">
            </div>
        </div>
    </div>
    <div id="Sidebar">
        <div class="sidebarIn">
          <a href="<?php echo absolutePath('/../../pages/admin/Dashboard/Dashboard.php') ?>" target="_self">
                <div class="SidebarIcon" id="SidebarHome"></div>
                <p class="IconText" >Home</p>
            </a>
            <a href="">
                <div class="SidebarIcon" id="SidebarNewForm"></div>
                <p class="IconText">New Form</p>
            </a>
            <a href="">
                <div class="SidebarIcon" id="SidebarImport"></div>
                <p class="IconText">Import Form</p>
            </a>
            <a href="">
                <div class="SidebarIcon" id="SidebarAbout"></div>
                <p class="IconText">About</p>
            </a>
            <a href="">
                <div class="SidebarIcon" id="SidebarQA"></div>
                <p class="IconText">Q&A</p>
            </a>
        </div>
    </div>
</div>

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
    
        $url = 'http://' . $serverName . str_replace($_SERVER['DOCUMENT_ROOT'], '', $absolutePath);
    
        $query = parse_url($path, PHP_URL_QUERY);
        if ($query) {
            $url .= '?' . $query;
        }
    
        return $url;
    }
?>
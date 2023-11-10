<?php
    //Pred includem mužete nastavit proměnné $location a $PageSpecific
    //Do $location jen prostý string s informací, kde se uživatel nacházi
    //Do $PageSpecific se může dát html kód toho, co se má zobrazit v headru mezi logem a user ikonou
?>

<div id="HeaderAndSidebar">
    <div id="header">
        <div id="HeaderLeft">
            <div id="logo">
                <span>Logo placeholder</span>
            </div>
            <?php
            if(isset($location)){
                echo "<div id='LocationName'><p>";
                echo $location;
                echo "</p></div>";
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
            <a href="">
                <div class="SidebarIcon" id="SidebarHome"></div>
                <p class="IconText">Home</p>
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
    function absolutePath($path){
        $absolutePath = realpath(__DIR__ . $path);
        $serverName = $_SERVER['HTTP_HOST'];
        $path = 'http://' . $serverName . str_replace($_SERVER['DOCUMENT_ROOT'], '', $absolutePath);
        return $path;
    }

?>
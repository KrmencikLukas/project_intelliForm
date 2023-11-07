<div id="HeaderAndSidebar">
    <div id="header">
        <div id="HeaderLeft">
            <div id="logo">
                <span>Logo placeholder</span>
            </div>
            <div id="LocationName">
                <p>Location name</p>
            </div>
        </div>
        <div id="PageSpecific">
            <?php
                if (!empty($PageSpecific)) {
                    echo $PageSpecific;
                }
                //sem si přes $PageSpecific můžete dát co chete, pokud chcete
            ?>
        </div>
        <div id="HeaderRight">
            <div id="notification" class="HeaderSmallBox"></div>
            <div id="HeaderSettings" class="HeaderSmallBox"></div>
            <div id="HeaderWall"></div>
            <div id="UserIcon"></div>
        </div>
    </div>
    <div id="Sidebar">
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
<div id="Content">

</div>
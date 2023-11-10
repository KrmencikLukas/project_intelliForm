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
            <img class="UserIcon" src="<?php echo __DIR__ ?>" alt="">
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
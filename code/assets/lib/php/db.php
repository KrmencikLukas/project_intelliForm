<?php
    $DBservername = "md372.wedos.net";
    $DBusername = "w321060_project";
    $DBpassword = "oSjdi~_7pn";
    $DBdatabase = "d321060_project";
    
    $db = new PDO("mysql:host=$DBservername;dbname=$DBdatabase", $DBusername, $DBpassword);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
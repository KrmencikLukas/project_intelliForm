<?php

    //funkce na css formu
    function SetFormCSS($formCSSkey, $formCSSvalue){
        $returnCSS="";
        for ($i=0; $i < count($formCSSkey); $i++) { 
            //podle názvu z db zává hodnotu css
            if ($formCSSkey[$i]["key"]=="color") {
                $returnCSS=$returnCSS.".form {background-color:".$formCSSvalue[$i]["value"].";}\n";
            }
            if ($formCSSkey[$i]["key"]=="background color") {
                $returnCSS=$returnCSS."body {background-color:".$formCSSvalue[$i]["value"].";}\n";
            }
            if ($formCSSkey[$i]["key"]=="font") {
                $returnCSS=$returnCSS.".formHeading, p, .questionHeading, label {font-family:".$formCSSvalue[$i]["value"].";}\n";
            }
        }
        return $returnCSS;
    }


?>
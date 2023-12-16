<?php
    function HashSalt($argPS, $argEM){
        $hash1 = hash("sha256", $argPS);
        $hash1 .= strrev(hash("sha384", $argEM));
        $hash2 = hash("sha512", $argPS);
        $revhash2 = strrev($hash2);
        $return = $hash1.$revhash2;
        return $return;
    }
    function generateRandomCode() {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $codeLength = 20;
        $code = '';

        $shuffledCharacters = str_shuffle($characters);

        $code = substr($shuffledCharacters, 0, $codeLength);
    
        return $code;
    }
?>
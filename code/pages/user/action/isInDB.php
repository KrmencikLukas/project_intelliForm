<?php

//funkce na kontrolu, jestli už guest form jednou neodeselal
function isInDatabase ($id,$DBlib){
    $params = [":id" => $id];
    $inDB= $DBlib->countByPDOWithCondition("guest_answer", "id","guest_id = :id", $params );
    if ($inDB==0) {
        return true;
    } else {
        return false;
    }
    
}

?>
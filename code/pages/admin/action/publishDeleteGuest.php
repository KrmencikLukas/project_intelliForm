    <?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");
    include("../../../assets/lib/php/HashLibrary.php");
    
    $pdo = new DatabaseFunctions($db);

    $form = (int)$_POST["form"] ?? null;
    $guest = (int)$_POST["guest"] ?? null;
    $email = $_POST["email"] ?? null;

    if($form && $guest){
        $arr = [
            ":id" => $guest,
            ":form" => $form
        ];
        $pdo->deleteDataWithCondition("guest","id = :id AND form_id = :form", $arr);

        echo "deleted";
    }
    if($form && $email){
        $arr2 = [
            ":email" => $email,
            ":form" => $form
        ];
        $pdo->deleteDataWithCondition("guest","email = :email AND form_id = :form", $arr2);

        echo "deleted";
    }
?>
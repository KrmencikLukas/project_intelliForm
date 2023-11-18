<?php
    /*Dokumentace pro použití classy:
        include(PATH/DBlibrary.php);

    $Objekt = new DatabaseFunctions(jmeno PDO objektu napr $PDO);
    
    Volani Funkci:

    $Objekt->funkce(parametry)
    (lze ulozit do promenne)
    */

    class DatabaseFunctions {
        private $pdo;
        public function __construct($pdo) {
            $this->pdo = $pdo;
        }
    
        // Count věci v db
        public function DBcountByPDO( $countColumn = "*",$table){
            if ($countColumn === "*") {
                $countColumn = "COUNT(*)";
            } else {
                $countColumn = "COUNT($countColumn)";
            }
            $count = "SELECT COUNT($countColumn) FROM $table";
            $sql_com =$this->pdo ->prepare($count);
            $sql_com->execute();
            $countbut = (int)$sql_com ->fetchAll(PDO::FETCH_ASSOC)[0]["COUNT(*)"];
            return $countbut;
        }
        // vzor: DBcountByPDO("Admin" <- lze i "*" , "Admin");

        public function countByPDOWithCondition($table, $countColumn = "*", $condition = "", $params = []) {
            if ($countColumn === "*") {
                $countColumn = "COUNT(*)";
            } else {
                $countColumn = "COUNT($countColumn)";
            }
        
            $sql = "SELECT $countColumn FROM $table";
            if (!empty($condition)) {
                $sql .= " WHERE $condition";
            }
            $sql_com = $this->pdo->prepare($sql);
        
            if ($sql_com === false) {
                return false;
            }
        
            $sql_com->execute($params);
            $count = (int)$sql_com->fetchColumn();
        
            return $count;
        }
        /* vzor: 
            $params = [":Email" => $email];
            countByPDOWithCondition("Admin", "id","Email = :Email", $params );
        */

        //SELECT
        public function fetchDataFromDB($table, $Something){
            $sql = "SELECT $Something FROM $table";
            $sql_com =$this->pdo->prepare($sql);
            $sql_com->execute();
            $return = $sql_com->fetchAll(PDO::FETCH_ASSOC);
            return $return;
        }
        // vzor: fetchDataFromDB("Admin" <- lze i "*" , "Admin");

        public function fetchDataWithCondition($table, $columns, $condition, $params = []) {
            $sql = "SELECT $columns FROM $table WHERE $condition";
            $sql_com = $this->pdo->prepare($sql);
            
            if ($sql_com === false) {
                return false;
            }
            $sql_com->execute($params);
            $results = $sql_com->fetchAll(PDO::FETCH_ASSOC);
        
            return $results;
        }
        /* vzor:
            $params2 = [ ":id" => $id];
            $result2 = fetchDataWithCondition("Admin", "Admin", "id = :id", $params2);
        */
        public function fetchDataWithInCondition($table, $columns, $conditionColumn, $paramArray) {
            if (!is_array($paramArray) || empty($paramArray)) {
                return false;
            }
        
            $inPlaceholder = implode(',', array_fill(0, count($paramArray), '?'));
        
            $sql = "SELECT $columns FROM $table WHERE $conditionColumn IN ($inPlaceholder)";
            $sql_com = $this->pdo->prepare($sql);
        
            if ($sql_com === false) {
                return false;
            }
        
            $sql_com->execute($paramArray);
            $results = $sql_com->fetchAll(PDO::FETCH_ASSOC);
        
            return $results;
        }
        /* 
            specialni varianta kde se WHERE parametr vyhledava v stringified array 

            vzor:
            $paramArray = [1, 2, 3];
            selectDataWithInCondition("user", "id", "id ", $paramArray);

        */
        
        //INSERT
        public function insertData($table, $data = []) {
            $columns = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            
            $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
            $sql_com = $this->pdo->prepare($sql);
            if ($sql_com === false) {
                return false;
            }
            
            $sql_com->execute($data);
            return $this->pdo->lastInsertId();
        }
        /*vzor 
            $insertArr=
                ["Title" => $title,
                    "Description" => $Desc,
                    "Price" => $Price
                ];
            insertData("Menus", $insertArr);
        */

        //UPDATE
        function updateData($table, $data = [], $condition) {

            $setClauses = [];
            
            foreach ($data as $key => $value) {
                $setClauses[] = "$key = :$key";
            }
            
            $setClause = implode(', ', $setClauses);
            
            $sql = "UPDATE $table SET $setClause WHERE $condition";
            
            $sql_com = $this->pdo->prepare($sql);
            
            if ($sql_com === false) {
                return false;
            }
            
            $sql_com->execute($data);
            return $sql_com->rowCount(); 
        }
        /*vzor 
            $insertArr=
                ["Title" => $title,
                    "Description" => $Desc,
                    "Price" => $Price,
                    "id" => $UpdateId
                ];
            updateData("Menus",$insertArr,"id = :id");
        */

        function updateDataNormal($table, $data = [], $definition, $condition) {

            $setClauses = [];
            
            foreach ($data as $key => $value) {
                $setClauses[] = "$key = :$key";
            }

            $data1 = array_merge($data, $definition);
            
            $setClause = implode(', ', $setClauses);
            
            $sql = "UPDATE $table SET $setClause WHERE $condition";
            
            $sql_com = $this->pdo->prepare($sql);
            
            if ($sql_com === false) {
                return false;
            }
            
            $sql_com->execute($data1);
            return $sql_com->rowCount(); 
        }




        //DELETE
        public function deleteDataWithCondition($table, $condition, $params = []) {
            $sql = "DELETE FROM $table WHERE $condition";
            $sql_com = $this->pdo->prepare($sql);
        
            if ($sql_com === false) {
                return false;
            }
        
            $sql_com->execute($params);

            $affectedRows = $sql_com->rowCount();
        
            return $affectedRows;
        }
        /* vzor
        !! vraci pocet smazanych radku !!  
                $insertArr=
                ["Title" => $title,
                    "Description" => $Desc,
                    "Price" => $Price,
                    "id" => $UpdateId
                ];
            deleteDataWithCondition("Menus","id = :id",$insertArr) 
        */
        public function deleteAllData($table) {
            $sql = "DELETE FROM $table";
            $sql_com = $this->pdo->prepare($sql);
        
            if ($sql_com === false) {
                return false;
            }
            $sql_com->execute();
        
            $affectedRows = $sql_com->rowCount();
        
            return $affectedRows;
        }
        //!! vraci pocet smazanych radku !!  vzor neni potreba
    }


?>
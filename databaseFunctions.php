<?php
function connect(){
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "policecases";
    $mysqli = new mysqli($host,$user,$pass,$db);
    if ($mysqli -> connect_errno) {
        exit();
    }
    return $mysqli;
}

function Query (array $queryArray){//queryarray is an associate array with the keys ["sql"] and ["values"]
    $dbConnection = null;
    $results = [];
    try{
        $dbConnection = Connect();
    }
    catch(Exception $e){
        echo "connection Error: ".$e;
    }
    $dbConnection->begin_transaction();
    try{
        foreach($queryArray as $arr){
            $sql = $arr["sql"];
            $values = $arr["values"];
            $stmt = mysqli_prepare($dbConnection, $sql);
            $stmt -> execute($values);
            $result = $stmt -> get_result();
            array_push($results, ["result"=>$result,"insertid"=>$stmt->insert_id]);
        }
        $dbConnection -> commit();
        $dbConnection->close();
        return $results;
    }
    catch(exception $e){
        echo $e;
        $dbConnection->rollback();
        $dbConnection->close();
        return false;
    }
}

function getValue(array $queryResult, string $key){
    return $queryResult[0][$key] ?? "";
}
?>
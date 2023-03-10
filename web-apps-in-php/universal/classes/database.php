<?php
class Database {
    private $mysqli;
    public function __construct($table)
    {
        global $mysql_address;
        global $mysql_port;
        global $mysql_user;
        global $mysql_pass;

        $server = $mysql_address.":".$mysql_port;
        $user = $mysql_user;
        $pass = $mysql_pass;
        $connectionErrors = "";

        try{
            $this->mysqli = new mysqli($server,$user,$pass,$table);
        } catch(mysqli_sql_exception $e){
            $connectionErrors .= "Nieudane połączenie $server\n".$e;
        }


        if($connectionErrors != ""){
            $connectionErrors = preg_replace("/$pass/","*******",$connectionErrors);
            echo nl2br($connectionErrors);
            exit();
        }

        if($this->mysqli->connect_errno){
            echo "Nie udało się połączenie z serwerem: $server\n".$this->mysqli->connect_error;
            exit();
        }

        
        if ($this->mysqli->set_charset("utf8")) {
            //udało sie zmienić kodowanie
        } else {
            printf("Failed utf8 change");
            exit();
        }
    }

    public function protect_string($str) {
        return $this->mysqli->real_escape_string($str);
    }
    
    public function protect_int($nmb) {
        return (int)$nmb;
    }

    function __destruct() {
        $this->mysqli->close();
    }

    public function getSelectArray(string $sql){
        if($queryResult = $this->mysqli->query($sql)){
            return $queryResult->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /**
     * Method for executing any sql command on database
     * 
     * @param string $sql SQL command
     * 
     * @return mysqli_result|bool Result of command or feedback
     */
    public function executeSQL(string $sql) {
        return $this->mysqli->query($sql);
    }

    public function getMysqli() {
        return $this->mysqli;
    }
}
?>
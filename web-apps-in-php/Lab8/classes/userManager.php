<?php
class UserManager {
    
    
    public function loginForm(){
        ?>
        <h2>Formularz logowania</h2>
        <form action="./loginProcess.php" method="POST">
            <label for="login">Login: </label><input type="text" id="login" name="login"><br>
            <label for="password">Hasło: </label><input type="password" id="password" name="password"><br>
            <input type="submit" name="submit" value="Zaloguj">
            <input type="reset" value="Anuluj">
        </form>
        <?php
    }

    private function getValidatedLoginInput(){
        $args = [
            "login" => FILTER_SANITIZE_SPECIAL_CHARS,
            "password" => FILTER_SANITIZE_SPECIAL_CHARS,
        ];

        $data = filter_input_array(INPUT_POST,$args);

        $errors = "";
        foreach($data as $key=>$value){
            if($value === NULL || $value === false){
                if($key === "login"){
                    $errors .= "Brakuje/niepoprawnie podany login</br>";
                } else if($key === "password"){
                    $errors .= "Brakuje/niepoprawnie podane hasło</br>";
                }
            }
        }

        if($errors ===""){
            return $data;
        } else {
            echo $errors;
            return false;
        }
    }

    public function login(Database $db){
        $data = $this->getValidatedLoginInput();
        if(!$data){
            return false;
        }

        $id = User::getUserId($data["login"],$data["password"],$db);

        if($id){
            session_start();

            //Check if someone isn't already logged in on this computer
            if(($object = $db->executeSQL("SELECT userId FROM logged_in_users WHERE sessionId = '".session_id()."'")->fetch_object()) !== NULL){
                if($object->userId !== $id){
                    echo "Wyloguj innego użytkownika zanim się zalogujesz";
                    return false;
                }
            }
            

            $db->executeSQL("DELETE from logged_in_users where userId = $id");
            $date = new DateTime('now');
            $date = date("Y-m-d H:i:s");
            $db->executeSQL("INSERT into logged_in_users values ('".session_id()."', '$id', '$date')");
            return $id;
        }

        return false;
    }

    public function logout(Database $db){
        session_start();

        $db->executeSQL("DELETE from logged_in_users where sessionId = '".session_id()."'");

        if ( isset($_COOKIE[session_name()]) ) {
            setcookie(session_name(),'', time() - 42000, '/');
        }
        session_destroy();
    }

    public function getLoggedInUser(Database $db,string $sessionId){
        $sqlResult = $db->executeSQL("SELECT userId FROM logged_in_users WHERE sessionId = '$sessionId'");

        if($id = $sqlResult->fetch_object()->userId){
            return $id;
        }
    }
}
?>
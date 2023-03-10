<?php
    function validate(){
        $args = [
            'surname' => [
                'filter' => FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => '/^[A-Z]{1}[a-ząęłńśćżźó-]{1,25}$/']
            ],
            'age' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => [
                    'min-range' => 1,
                    'max-range' => 200
                ]
            ],
            'country' => FILTER_SANITIZE_SPECIAL_CHARS,
            'email' => FILTER_VALIDATE_EMAIL,
            'languages' => [
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'flags' => FILTER_REQUIRE_ARRAY
            ],
            'payment' => FILTER_SANITIZE_SPECIAL_CHARS
        ];
        $data = filter_input_array(INPUT_POST,$args);
        var_dump($data);

        $errors = "";
        foreach ($data as $key => $val){
            if($val === false or $val === NULL or $val === ""){
                $errors .= "$key ";
            }
        }

        if($errors === ""){
            return $data;
        } else {
            echo "<br/>Nie poprawne dane: $errors";
            exit(0);
        }
    }

    function showSelectTable(array $selectArray){
        $table = "<table>";

        if($selectArray === NULL) return;
        
        $table.="<thead><tr>";
        foreach($selectArray[0] as $key=>$value){
            $table.="<th>".$key."</th>";
        }
        $table.="</tr></thead>";

        $table.="<tbody>";
        foreach($selectArray as $record){

            $table.="<tr>";
            foreach($record as $value){
                $table.="<td>".$value."</td>";
            }
            $table.="</tr>";

        }
        $table.="</tbody>";

        $table.= "</table>";
        return $table;
    }

    

    function showStatistics(){
        $d_root = $_SERVER['DOCUMENT_ROOT'];
        $arrayFromFile = file("$d_root/../ExtraFiles/Lab3/data.txt");

        $totalRecords = count($arrayFromFile);
        $notAdults = 0;
        $old = 0;
        foreach($arrayFromFile as $record){
            $temp = getAge($record);
            var_dump($temp);
            if($temp < 18){
                $notAdults++;
            } else if($temp > 49){
                $old++;
            }
        }

        $stats = "<p>Liczba wszystkich zamówień: $totalRecords</p>";
        $stats .= "<p>Liczba zamówień wiek < 18: $notAdults</p>";
        $stats .= "<p>Liczba zamówień wiek > 49: $old</p>";
    }

    function getAge($record){
        return intval(explode(" ",$record)[1]);
    }
?>
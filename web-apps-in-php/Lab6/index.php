<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formularz | Lab6</title>
</head>
<body>
    <h1>Formularz zamówienia</h1>
    <form action="./index.php" method="post">
        <div class="basic-info">
            <div class="info-row"  id="surname-cont"><label for="surname">Nazwisko:</label> <input type="text" name="surname" id="surname"/> </div>
            <div class="info-row" id="age-cont"><label for="age">Wiek:</label><input type="number" name="age" id="age"/></div>
            <div class="info-row"><label for="country" >Państwo:</label> <select name="country" id="country">
                <option value="Polska">Polska</option>
                <option value="Niemcy">Niemcy</option>
                <option value="Wielka Brytania">Wielka Brytania</option>
                <option value="Czechy">Czechy</option>
            </select></div>
            <div class="info-row" id="email-cont"><label for="email">Adres e-mail:</label> <input type="email" name="email" id="email"></div>
        </div>
        
        <h4>Zamawiam tutorial z języka:</h4>
        <div class="buttons" id="service-cont">
            <?php
                $languages = ["C","CPP","Java","C#","HTML","CSS","XML","PHP","JavaScript"];
                for ($i = 0; $i < count($languages); $i++){
                    echo "<input type='checkbox' name='languages[]' value='".$languages[$i]."' id='".$languages[$i]."'/> <label for='".$languages[$i]."'>".$languages[$i]."</label>";
                }
            ?>
        </div>
        <h4>Sposób zapłaty:</h4>
        <div class="buttons" id="payment-cont">
            <input type="radio" name="payment" value="Master Card" id="eu" /> <label for="eu">eurocard</label>
            <input type="radio" name="payment" value="Visa" id="visa" /> <label for="visa">visa</label>
            <input type="radio" name="payment" value="Przelew" id="transfer" /> <label for="transfer">przelew bankowy</label>
        </div>
        <input type="reset" value="Anuluj" />
        <input type="submit" name='submit' value="Zapisz" />
        <input type="submit" name='submit' value="Pokaż" />
        <input type="submit" name='submit' value="PHP" />
        <input type="submit" name='submit' value="CPP" />
        <input type="submit" name='submit' value="Java" />
        <input type="submit" name='submit' value="Stats" />
    </form>

    <?php
        include_once("../universal/classes/database.php");
        include_once("functions.php");
        require_once("passwords.php");

        $bd = new Database("clients");

        if(filter_input(INPUT_POST,"submit")){
            $choice = filter_input(INPUT_POST,"submit");
            switch($choice){
                case "Zapisz":
                    $data = validate();
                    $sql = "insert into clients values (NULL,'".$data['surname']."','".$data['age']."','".
                    $data['country']."','".$data['email']."','".implode(",",$data['languages'])."','".$data['payment']."');";
                    
                    if(!$bd->executeSQL($sql)){
                        echo "Wstawienie nieudane!</br>";
                        echo "Should be: INSERT INTO clients VALUES (NULL, 'Marcin', '21', 'Polska','mail@pollub.edu.pl', 'C#,CPP', 'Przelew');</br>";
                        echo $sql;
                    }
                    break;
                case "Pokaż":
                    showSelectTable($bd->getSelectArray("select Nazwisko,Zamowienie,Wiek from clients"));
                    break;
                case "PHP":
                    showSelectTable($bd->getSelectArray("select Nazwisko,Zamowienie from clients where Zamowienie regexp 'PHP'"));
                    break;
                case "CPP":
                    showSelectTable($bd->getSelectArray("select Nazwisko,Zamowienie from clients where Zamowienie regexp 'CPP'"));
                    break;
                case "Java":
                    showSelectTable($bd->getSelectArray("select Nazwisko,Zamowienie from clients where Zamowienie regexp 'Java'"));
                    break;
                case "Stats":

                    echo "<p>Liczba wszystkich zamówień: ".count($bd->getSelectArray("select Nazwisko from clients"))."</p>";
                    echo "<p>Liczba zamówień wiek < 18: ".count($bd->getSelectArray("select Nazwisko from clients where Wiek < 18"))."</p>";
                    echo "<p>Liczba zamówień wiek > 49: ".count($bd->getSelectArray("select Nazwisko from clients where Wiek > 49"))."</p>";
                    
                    break;
            }
        }

         
    ?>
</body>
</html>
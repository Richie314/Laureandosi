<?php
/**
 * Template Name: Main Page
 */
require_once dirname(__DIR__, 4) . "/utils/Configurazione.php";
require_once dirname(__DIR__, 4) . "/utils/AccessoProspetti.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>
        Genera Prospetti di Laurea
    </title>
    <style type="text/css">
        body {
            text-align: center;
            background-color: whitesmoke;
            font-size: larger;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        button {
            cursor: pointer;
            color: white;
            background-color: red;
            padding: 0.5em;
            margin: 0.5em;
            border-radius: 5px;
        }
        select, textarea, input {
            margin: 0.5em;
        }
    </style>
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script>
        $(() => {
            $('#main-form').on('submit', e => {
                e.preventDefault();
                $.ajax({
                    type: $('#main-form').attr('method'),
                    url: $('#main-form').attr('action'),
                    data: $('#main-form').serialize(),
                    success: (data, textStatus) => {
                        console.log(data, textStatus);
                    },
                    error: (JQueryXHR, textStatus, errorThrown) => {
                        console.log(JQueryXHR.responseText);
                        console.error(errorThrown);
                    }
                });
            });
        });
    </script>
</head>
<body>
<h1> 
    Genera prospetti di laurea
</h1>

<form action="./generaProspetti.php" method="post" id="main-form">

    <h1> 
        Laureandosi 2 - Gestione Lauree 
    </h1>

    <label for="cdl">
        Cdl
    </label>
    <select name="cdl" id="cdl">
        <optgroup label="Corsi di Laurea disponibili">
            <?php foreach (Configurazione::CorsiDiLaurea() as $nome => $cdl) {  ?>
                <option value="<?= htmlspecialchars($nome) ?>">
                    <?= htmlspecialchars($nome) ?>
                </option>
            <?php } ?>
        </optgroup>
    </select>

    <label for="matricole">
        Matricole
    </label>
    <textarea name="matricole" id="matricole"></textarea>

    <label for="data_laurea">
        Data Laurea
    </label>
    <input type="date" name="data_laurea" id="data_laurea">

    <button type="submit">
        Crea Prospetti
    </button>
</form>

<form action="./inviaProspetti.php" method="post" id="">
    <button type="submit"> Invia Prospetti </button>
</form>
</body>

<?php
/**
 * Template Name: Main Page
 */
require_once dirname(__DIR__, 4) . "/utils/Configurazione.php";
require_once dirname(__DIR__, 4) . "/utils/AccessoProspetti.php";
?>
<!DOCTYPE html>
<html lang="it-it">
<head>
    <title>
        Laureandosi 2 - Gestione Prospetti Laurea 
    </title>
    <script src="/lib/msg_js/msg.min.js" defer></script>
    <script src="/assets/mainpage.js" defer></script>
    <link rel="stylesheet" href="/assets/mainpage.css" type="text/css" media="all">
</head>
<body>
<div class="container">
    <h2> 
        Laureandosi 2 - Gestione Prospetti Laurea 
    </h2>
    <form action="./generaProspetti.php" method="post" id="main-form">
        <label for="cdl">
            Cdl:
        </label>
        <select name="cdl" id="cdl" required tabindex="0">
            <option value="">Seleziona un Cdl</option>
            <optgroup label="Corsi di Laurea disponibili">
                <?php foreach (Configurazione::CorsiDiLaurea() as $nome => $cdl) {  ?>
                    <option value="<?= htmlspecialchars($nome) ?>">
                        <?= htmlspecialchars($nome) ?>
                    </option>
                <?php } ?>
            </optgroup>
        </select>

        <label for="matricole">
            Matricole:
        </label>
        <textarea 
            name="matricole" id="matricole" 
            rows="7" cols="15" 
            placeholder="Incolla le matricole qui" 
            required tabindex="1"></textarea>

        <label for="data_laurea">
            Data Laurea:
        </label>
        <input type="date" name="data_laurea" id="data_laurea" min="<?= date("Y-m-d") ?>" required tabindex="2">

        <button type="submit" title="Crea i prospetti" tabindex="3">
            Crea Prospetti
        </button>
    </form>
    <form action="./inviaProspetti.php" method="post" id="second-form">
        <div class="download-link invisible">
            <a href="<?= AccessoProspetti::pathCommissioneWeb() ?>" target="_blank" tabindex="4">
                Apri Prospetti
            </a>
        </div>

        <button type="submit" disabled title="Invia i prospetti appena creati" tabindex="5">
            Invia Prospetti
        </button>
    </form>
    <div class="output">
        <output id="output">

        </output>
    </div>
    <div id="output-invio" class="hidden">
        <progress min="0" max="1" value="0" id="progress"></progress>
        <output id="progress-show"></output>
    </div>
</div>

<div class="dettagli-invio hidden">
    <h2>
        Dettagli invio
    </h2>
    <hr>
    <ul id="result-ul">
        <!-- I dettagli andranno qua -->
    </ul>
</div>        

</body>
</html>
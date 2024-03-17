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
            background-color: whitesmoke;
            font-size: larger;

            display: flex;
            flex-direction: column;
            align-items: center;

            width: 100%;
            min-width: 350px;
            height: fit-content;
            max-height: 100%;
            overflow: auto;
        }
        textarea {
            resize: none;
        }
        h1, h2 {
            text-align: center;
        }
        select {
            cursor: pointer;
        }
        .hidden {
            display: none !important;
        }
        #main-form {
            display: grid;

            grid-template-rows: min-content;
            grid-template-columns: 30% 70%;

            height: 100%;
            width: fit-content;
            max-width: 100%;
            gap: 5px;
        }
            #main-form > h2 {
                grid-column-start: 1;
                grid-column-end: 3;
                margin: 0;
            }
        
            #main-form > label {
                grid-column-start: 1;
                grid-column-end: 2;
                height: max-content;
                margin-block: auto;
                margin-inline: 5px;

                text-align: center;
                user-select: none;
            }
            #main-form > input, #main-form > textarea, #main-form > select {
                grid-column-start: 2;
                grid-column-end: 3;
                height: max-content;
                margin: 0;
            }
            #main-form > button {
                grid-column-start: 1;
                grid-column-end: 3;
                cursor: pointer;
                color: white;
                background-color: red;
                padding: 0.5em;
                margin: 0.5em;
                border-radius: 5px;
            }
    </style>
    <script src="/lib/msg_js/msg.min.js" defer></script>
</head>
<body>
<h1> 
    Genera prospetti di laurea
</h1>

<form action="./generaProspetti.php" method="post" id="main-form">

    <h2> 
        Laureandosi 2 - Gestione Lauree 
    </h2>

    <label for="cdl">
        Cdl
    </label>
    <select name="cdl" id="cdl">
        <option value="">Scegli un Corso di Laurea</option>
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
    <textarea name="matricole" id="matricole" rows="7" cols="15" placeholder="Incolla le matricole qui"></textarea>

    <label for="data_laurea">
        Data Laurea
    </label>
    <input type="date" name="data_laurea" id="data_laurea" min="<?= date("Y-m-d") ?>">

    <button type="submit">
        Crea Prospetti
    </button>
</form>

<form action="./inviaProspetti.php" method="post" id="second-form" class="hidden">
    <a href="<?= AccessoProspetti::pathCommissioneWeb() ?>" download>
        Scarica prospetti per commissione
    </a>
    <button type="submit">
        Invia Prospetti
    </button>
    <progress min="0" max="1" value="0" class="hidden"></progress>
</form>
<script>
    'use strict';
    async function post(path, params = null)
    {
        async function post_async(path, params)
        {
            if (params)
            {
                const form_data = new FormData();
                for (const [name, value] of Object.entries(params))
                {
                    form_data.append(name, value);
                }
                return await fetch(path, {
                    method: 'POST',
                    body: form_data
                });
            }
            return await fetch(path, {
                method: 'POST'
            });
        }
        try {
            const response = await post_async(path, params);
            if (!response.ok)
            {
                return null;
            }
            //console.log(await response.text());
            return await response.json();
        } catch (err) {
            console.warn(err);
            return null;
        }
    }

    function DisplayError(err)
    {
        if (typeof Msg === 'undefined')
        {
            alert(err);
            return;
        }
        const msg = Msg.factory({
            class: 'red',
            preset: 'popup',
            position: 'center',
            autoclose: false,
            closeable: true,
            close_others_on_show: true,
            replace_linebreaks: false,
            enable_titlebar: true
        });
        msg.show([
            '&Egrave; avvenuto un errore',
            err.toString()
        ]);
    }

    /**
     * @type {HTMLFormElement}
     */
    const main_form = document.getElementById('main-form');
    /**
     * @type {HTMLButtonElement} 
     */
    const main_form_submit = document.querySelector('#main-form > button[type=submit]');
    /**
     * @type {HTMLFormElement}
     */
    const second_form = document.getElementById('second-form');
    /**
     * @type {HTMLProgressElement}
     */
    const second_form_progress = document.querySelector('#second-form > progress');

    main_form.onsubmit = async (evt) => {
        // Sostituisco chiamata ajax a normale chiamata
        evt.preventDefault();

        // Prendo i parametri dalla form
        const textarea = document.getElementById('matricole');
        const matricole = [...new Set(textarea.value.split(',').map(s => s.trim()))];
        textarea.value = matricole.join(',\n'); // Riordino visivamente la textarea
        if (matricole.length === 0)
        {
            return;
        }
        
        const cdl = document.getElementById('cdl').value;
        if (!cdl || cdl.length === 0)
            return;
        const data_laurea = document.getElementById('data_laurea').value;
        if (!data_laurea || data_laurea.length === 0)
            return;

        // Disabilito click durante processazione
        main_form_submit.disabled = true;
        second_form.classList.add('hidden');
        const res = await post(
            main_form.getAttribute('action'),
            {
                'cdl': cdl,
                'matricole': matricole.join(','),
                'data_laurea': data_laurea
            });
        console.log(res);
        // Riabilito click in quanto procesazione finita
        main_form_submit.disabled = false;
        
        if (!res)
        {
            DisplayError('Il server ha risposto con un formato non accettato');
            return;
        }
        if (res.Esito !== 'Successo')
        {
            DisplayError(res.Messaggio);
            return;
        }

        second_form.classList.remove('hidden');
        second_form_progress.classList.add('hidden');
        second_form_progress.value = 0;
        second_form_progress.max = Number(res.NumeroProspetti);
    }
</script>
</body>
</html>
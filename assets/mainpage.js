'use strict';

function DisplayError(err)
{
    if (typeof Msg === 'undefined')
    {
        alert(err);
        return;
    }
    const msg = Msg.factory({
        class: 'blue',
        preset: 'popup',
        position: 'center',
        autoclose: false,
        closeable: true,
        close_others_on_show: true,
        replace_linebreaks: true,
        enable_titlebar: true
    });
    msg.show([
        '&Egrave; avvenuto un errore',
        err.toString()
    ]);
}

// Elementi del primo form
/**
 * @type {HTMLFormElement}
 */
const main_form = document.getElementById('main-form');
/**
 * @type {HTMLButtonElement} 
 */
const main_form_submit = document.querySelector('#main-form > button[type=submit]');

// Elementi del secondo form
/**
 * @type {HTMLFormElement}
 */
const second_form = document.getElementById('second-form');
/**
 * @type {HTMLDivElement}
 */
const second_form_a = document.querySelector('#second-form > .download-link');
/**
 * @type {HTMLButtonElement}
 */
const second_form_btn = document.querySelector('#second-form > button[type=submit]');

// Elementi del controllo dell'invio email
/**
 * @type {HTMLDivElement}
 */
const progress_div = document.getElementById('output-invio');
/**
 * @type {HTMLProgressElement}
 */
const progress = document.getElementById('progress');
/**
 * @type {HTMLOutputElement}
 */
const progress_show = document.getElementById('progress-show');
/**
 * @type {HTMLUListElement}
 */
const ul = document.getElementById('result-ul');

// Output generico
/**
 * @type {HTMLOutputElement}
 */
const output = document.getElementById('output');

/**
 * Disabilita i form e avverte l'utente prima che esca dalla pagine con un'operazione a metà
 */
function StartSensitiveOperation() {
    main_form_submit.disabled = true;
    second_form_btn.disabled = true;
    window.onbeforeunload = () => 'Stai abbandonando la pagina mentre un\'operazione è in corso.\nI risultati potrebbero essere parziali o imprevedibili.\nAttendi la conclusione dell\'operazione';
}
/**
 * Riabilita i form e rimuove le avvertenze per l'utente
 */
function EndSensitiveoperation() {
    main_form_submit.disabled = false;
    second_form_btn.disabled = false;
    window.onbeforeunload = null;
}

main_form.onsubmit = async (evt) => {
    // Sostituisco chiamata ajax a normale chiamata
    evt.preventDefault();

    // Prendo i parametri dalla form
    /**
     * @type {HTMLTextAreaElement}
     */
    const textarea = document.getElementById('matricole');
    const matricole = 
        [...new Set(
            textarea.value.split(/[\s,]+/)
            .map(s => s.trim())
            .filter(s => s.length > 0 && !(/[^0-9]+/.test(s)))
        )];
    textarea.value = matricole.join(',\n'); // Riordino visivamente la textarea
    second_form.matricole = [...matricole];
    if (matricole.length === 0) {
        return;
    }

    second_form.matricole.push('Commissione');
    
    const cdl = document.getElementById('cdl').value;
    if (!cdl || cdl.length === 0) {
        return;
    }
    const data_laurea = document.getElementById('data_laurea').value;
    if (!data_laurea || data_laurea.length === 0) {
        return;
    }

    // Disabilito click durante processazione
    StartSensitiveOperation();

    const res = await post(
        main_form.getAttribute('action'),
        {
            'cdl': cdl,
            'matricole': matricole.join(','),
            'data_laurea': data_laurea
        });
    
    // Riabilito click in quanto procesazione finita
    EndSensitiveoperation();
    second_form_a.classList.add('invisible');
    progress_div.classList.add('hidden');

    if (!res) {
        second_form_btn.disabled = true; // disabilita solamente form di invio
        DisplayError('Il server ha risposto con un formato non accettato');
        return;
    }
    if (res.Esito !== 'Successo') {
        second_form_btn.disabled = true; // disabilita solamente form di invio
        DisplayError(res.Messaggio);
        return;
    }

    second_form_a.classList.remove('invisible');
    progress_div.classList.remove('hidden');
    progress.value = 0;
    progress.max = Number(res.NumeroProspetti);
    output.innerHTML = res.Messaggio;
}
second_form.onsubmit = async (evt) => {
    evt.preventDefault();
    
    ul.innerHTML = '';
    second_form.matricole.forEach(mat => {
        const li = document.createElement('li');
        li.innerText = mat + ':';
        li.id = 'result-' + mat;
        li.style.userSelect = 'none';
        ul.appendChild(li);
    });

    // Disabilito click che causerebbero problemi
    StartSensitiveOperation();

    let iterate = true;
    while (iterate) {
        
        console.log('Invio in corso adesso...');
        const res = await post(second_form.getAttribute('action'), {
            'numero_max': 1
        });
        if (!res) { 
            iterate = false;
            DisplayError('Il server ha risposto con un formato sconosciuto (era previsto JSON).\nL\'operazione è stata terminata.\nApri la console del browser per maggiori dettagli');
            break;
        }
        
        iterate = res.InviiEffettuati.length > 0;
        res.InviiEffettuati.forEach(mat => {
            const li = document.getElementById('result-' + mat);
            li.innerText = mat + ': Inviato';
        });
        progress.value += res.InviiEffettuati.length;
        
        if (iterate) {
            await sleep(1000);
        }
    }
    
    // Riabilito i click
    EndSensitiveoperation();

    const a = document.createElement('a');
    a.onclick = ShowEmailDetails;
    a.innerHTML = `${progress.value} Propetti inviati`;
    output.innerHTML = '';
    output.appendChild(a);
}
function AggiornaProgressLabel() {
    const s = `${progress.value} / ${progress.max}`;
    if (progress_show.innerHTML !== s)
        progress_show.innerHTML = s;
    setTimeout(AggiornaProgressLabel, 500);
}
AggiornaProgressLabel();

function ShowEmailDetails() {
    if (typeof Msg === 'undefined') {
        return;
    }
    const msg = Msg.factory({
        class: 'blue',
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
        document.getElementById('dettagli-invio').innerHTML
    ]);
}
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
        const contentType = response.headers.get("content-type");
        if (!contentType || contentType.indexOf("application/json") === -1) {
            console.log(await response.text());
            return null;
        }
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
const second_form_a = document.querySelector('#second-form > .download-link');
const second_form_btn = document.querySelector('#second-form > button[type=submit]');

const progress_div = document.getElementById('output-invio');
/**
 * @type {HTMLProgressElement}
 */
const progress = document.getElementById('progress');
const progress_show = document.getElementById('progress-show');
const ul = document.getElementById('result-ul');

const output = document.getElementById('output');

main_form.onsubmit = async (evt) => {
    // Sostituisco chiamata ajax a normale chiamata
    evt.preventDefault();

    // Prendo i parametri dalla form
    const textarea = document.getElementById('matricole');
    const matricole = [...new Set(textarea.value.split(',').map(s => s.trim()))];
    textarea.value = matricole.join(',\n'); // Riordino visivamente la textarea
    second_form.matricole = [...matricole];
    if (matricole.length === 0)
    {
        return;
    }
    second_form.matricole.push('Commissione');
    
    const cdl = document.getElementById('cdl').value;
    if (!cdl || cdl.length === 0)
        return;
    const data_laurea = document.getElementById('data_laurea').value;
    if (!data_laurea || data_laurea.length === 0)
        return;

    // Disabilito click durante processazione
    main_form_submit.disabled = true;
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
    second_form_a.classList.add('invisible');
    progress_div.classList.add('hidden');
    second_form_btn.disabled = true;

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

    second_form_a.classList.remove('invisible');
    progress_div.classList.remove('hidden');
    progress.value = 0;
    progress.max = Number(res.NumeroProspetti);
    output.innerHTML = res.Messaggio;
    second_form_btn.disabled = false;
}
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}
second_form.onsubmit = async (evt) => {
    evt.preventDefault();
    console.log('Ajax');
    let iterate = true;
    
    ul.innerHTML = '';
    second_form.matricole.forEach(mat => {
        const li = document.createElement('li');
        li.innerText = mat + ':';
        li.id = 'result-' + mat;
        li.style.userSelect = 'none';
        ul.appendChild(li);
    });

    while (iterate)
    {
        console.log('Invio in corso adesso...');
        const res = await post(second_form.getAttribute('action'), {
            'numero_max': 1
        });
        if (!res)
        { 
            iterate = false;
            DisplayError('Il server ha risposto con un formato sconosciuto.\nL\'operazione è stata terminata');
            continue;
        }
        //console.log('Invii: ' + res.InviiEffettuati.length);
        iterate = res.InviiEffettuati.length > 0;
        res.InviiEffettuati.forEach(mat => {
            const li = document.getElementById('result-' + mat);
            li.innerText = mat + ': Inviato';
        });
        progress.value += res.InviiEffettuati.length;

        //console.log('Delay di 1s');
        await sleep(1000);
    }
}
function AggiornaProgressLabel() {
    const s = `${progress.value} / ${progress.max}`;
    if (progress_show.innerHTML !== s)
        progress_show.innerHTML = s;
    setTimeout(AggiornaProgressLabel, 500);
}
AggiornaProgressLabel();
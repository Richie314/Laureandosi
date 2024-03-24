<?php
/**
 * Template Name: Configuration Page
 */
require_once dirname(__DIR__, 4) . "/utils/Configurazione.php";
enum PageToShow:string 
{
    case ModificaCorsoDiLaurea = "modifica-cdl";
    case FiltroEsami = "filtro-esami";
    case EsamiInformatici = "esami-inf";
    case Menu = "choose";
}

if (isset($_GET["section"]) && is_string($_GET["section"]) && !empty($_GET["section"])) {
    switch ($_GET["section"]) {
        case PageToShow::ModificaCorsoDiLaurea->value: {
            $menu = PageToShow::ModificaCorsoDiLaurea;
            if (
                !isset($_GET["cdl"]) || 
                !is_string($_GET["cdl"]) || 
                empty($_GET["cdl"]) || 
                !array_key_exists($_GET["cdl"], Configurazione::CorsiDiLaurea())
            ) {
                $menu = PageToShow::Menu;
            } else {
                $cdl_short = $_GET["cdl"];
                $cdl = Configurazione::CorsiDiLaurea()[$cdl_short];
            }
        } break;

        case PageToShow::EsamiInformatici->value: {
            $menu = PageToShow::EsamiInformatici;
        } break;

        case PageToShow::FiltroEsami->value: {
            $menu = PageToShow::FiltroEsami;
        } break;

        case PageToShow::Menu->value:
        default: {
            $menu = PageToShow::Menu;
        } break;
    }
} else {
    $menu = PageToShow::Menu;
}
?>
<!DOCTYPE html>
<html lang="it-it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Riccardo Ciucci">
    <meta name="description" content="Laureandosi 2.1 - Gestione prospetti di Laurea">
    <meta name="keywords" content="Laureandosi, UniPI, Università, Pisa, Prospetti, Laurea, Email, Dipartimento d'Ingegneria dell'Informazione">
    <meta name="robots" content="noindex,nofollow">
    <title>
        Laureandosi 2 - Configurazione
    </title>
    <script src="/assets/page.js" defer></script>
    <link rel="stylesheet" href="/assets/page.css" type="text/css">
    <link rel="stylesheet" href="/assets/configuration.css" type="text/css">
</head>
<body>
    <div class="container">
        <h1>
            Modifica Configurazione
        </h1>
        <?php if (isset($_GET["esit"]) && is_string($_GET["esit"])) { ?>
            <p style="color: var(--text-color); font-weight: bold;">
                <?= htmlspecialchars($_GET["esit"]) ?>
            </p>

        <?php } ?>
        <?php switch ($menu) { 
            case PageToShow::ModificaCorsoDiLaurea: { ?>
                <form action="/modificaConfigurazione.php" method="post">
                    <input type="hidden" value="<?= htmlspecialchars($cdl_short) ?>" name="cdl">
                    
                    <label for="nome">
                        Nome
                    </label>
                    <input type="text" value="<?= htmlspecialchars($cdl->Nome) ?>" name="nome" id="nome" required>

                    <label for="formula">
                        Formula
                    </label>
                    <input type="text" value="<?= htmlspecialchars($cdl->Formula) ?>" name="formula" id="formula" required pattern="[0-9MTCFUmtcfu\/\+\-\*\%\(\)\.\s]+">
                    
                    <label for="cfu">
                        Cfu Richiesti
                    </label>
                    <input type="number" value="<?= $cdl->CFURichiesti ?>" name="cfu" id="cfu" min="1" max="1000" step="1" required>

                    <label for="lode">
                        Valore Lode
                    </label>
                    <input type="number" value="<?= $cdl->ValoreLode ?>" name="lode" id="lode" min="30" max="50" step="1" required>

                    <label for="durata">
                        Durata prevista
                    </label>
                    <input type="number" value="<?= $cdl->Durata ?>" name="durata" id="durata" min="1" max="10" step="1" required placeholder="Esprimere in anni">

                    <label for="email">
                        Email commissione
                    </label>
                    <input type="email" value="<?= htmlspecialchars($cdl->EmailCommissione) ?>" name="email" id="email" placeholder="Indirizzo email commissione" required>

                    <label for="body" class="span-2">
                        Corpo Email
                    </label>
                    <textarea name="body" id="body" placeholder="Testo dell'email qui" required class="span-2">
                        <?= $cdl->FormulaEmail ?>
                    </textarea>


                    <!-- Parametro T -->
                    <label for="tMin">
                        Tmin:
                    </label>
                    <input type="number" name="tMin" id="tMin" value="<?= $cdl->T->Min ?>" placeholder="Lasciare vuoto per 0">

                    <label for="tMax">
                        Tmax:
                    </label>
                    <input type="number" name="tMax" id="tMax" value="<?= $cdl->T->Max ?>" placeholder="Lasciare vuoto per 0">

                    <label for="tStep">
                        Tstep:
                    </label>
                    <input type="number" name="tStep" id="tStep" value="<?= $cdl->T->Step ?>" placeholder="Lasciare vuoto per 0">


                    <!-- Parametro C -->
                    <label for="cMin">
                        Cmin:
                    </label>
                    <input type="number" name="cMin" id="cMin" value="<?= $cdl->C->Min ?>" placeholder="Lasciare vuoto per 0">

                    <label for="cMax">
                        Cmax:
                    </label>
                    <input type="number" name="cMax" id="cMax" value="<?= $cdl->C->Max ?>" placeholder="Lasciare vuoto per 0">

                    <label for="cStep">
                        Cstep:
                    </label>
                    <input type="number" name="cStep" id="cStep" value="<?= $cdl->C->Step ?>" placeholder="Lasciare vuoto per 0">



                    <button type="reset">
                        Resetta
                    </button>
                    <button type="submit">
                        Salva
                    </button>
                </form>
                <script defer src="/lib/ckeditor5/build/ckeditor.js"></script>
                <script defer src="/assets/configuration.js"></script>

            <?php } break;
            case PageToShow::EsamiInformatici: { ?>
                <h2>
                    Non ancora disponibile
                </h2>
            <?php } break; 
            case PageToShow::FiltroEsami: { ?>
                <h2>
                    Non ancora disponibile
                </h2>
            <?php } break; 
            case PageToShow::Menu: { ?>
                <!-- Il target è la pagina corrente (a qualunque endpoint si trovi) -->
                <form method="get">

                    <label for="cdl"></label>
                    <select name="cdl" id="cdl">
                        <option value="">Scegli un Cdl:</option>
                        
                        <?php foreach(Configurazione::CorsiDiLaurea() as $nome => $cdl) { ?>
                            <option 
                                title="<?= htmlspecialchars($cdl) ?>"
                                value="<?= htmlspecialchars($nome) ?>">
                                <?= htmlspecialchars($cdl) ?>
                            </option>
                        <?php } ?>
                    </select>

                    <label for="section"></label>
                    <select name="section" id="section" required>
                        <option value="<?= PageToShow::Menu->value ?>">Scegli un'operazione</option>
                        <option value="<?= PageToShow::ModificaCorsoDiLaurea->value ?>">Calcolo e Reportistica</option>
                        <!--<option value="<?= PageToShow::FiltroEsami->value ?>">Filtro Esami</option>-->
                        <option value="<?= PageToShow::EsamiInformatici->value ?>">Esami informatici</option>
                    </select>

                    <button type="submit">
                        Vai
                    </button>
                </form>
            <?php } break; 
        } ?>    
        
    </div>
</body>
</html>
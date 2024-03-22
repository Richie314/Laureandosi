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
    <style type="text/css">
        body {
            width: 100%;
            min-width: 350px;
            height: auto;
            overflow: auto;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>
        Modifica Configurazione
    </h1>
    <hr>
    <?php switch ($menu) { ?>
    
        <?php case PageToShow::ModificaCorsoDiLaurea: { ?>
            <form action="/modificaConfigurazione.php" method="post">
                <input type="hidden" value="<?= htmlspecialchars($cdl_short) ?>" name="cdl">
                
                <label for="nome">
                    Nome
                </label>
                <input type="text" value="<?= htmlspecialchars($cdl->Nome) ?>" name="nome" id="nome" required>

                <label for="formula">
                    Formula
                </label>
                <input type="text" value="<?= htmlspecialchars($cdl->Formula) ?>" name="formula" id="formula" required>
                
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
                <input type="number" value="<?= $cdl->Durata ?>" name="durata" id="durata" min="1" max="10" step="1" required>

                <label for="email">
                    Indirizzo email commissione
                </label>
                <input type="email" value="<?= htmlspecialchars($cdl->EmailCommissione) ?>" name="email" id="email" placeholder="Indirizzo email commissione" required>

                <label for="body">
                    Corpo Email
                </label>
                <textarea name="body" id="body" placeholder="Testo dell'email qui" required>
                    <?= $cdl->FormulaEmail ?>
                </textarea>


                <!-- Parametro T -->
                <label for="tMin">
                    Tmin:
                </label>
                <input type="number" name="tMin" id="tMin" value="<?= $cdl->T->Min ?>">

                <label for="tMax">
                    Tmax:
                </label>
                <input type="number" name="tMax" id="tMax" value="<?= $cdl->T->Max ?>">

                <label for="tStep">
                    Tstep:
                </label>
                <input type="number" name="tStep" id="tStep" value="<?= $cdl->T->Step ?>">


                <!-- Parametro C -->
                <label for="cMin">
                    Cmin:
                </label>
                <input type="number" name="cMin" id="cMin" value="<?= $cdl->C->Min ?>">

                <label for="cMax">
                    Cmax:
                </label>
                <input type="number" name="cMax" id="cMax" value="<?= $cdl->C->Max ?>">

                <label for="tStep">
                    Cstep:
                </label>
                <input type="number" name="cStep" id="cStep" value="<?= $cdl->C->Step ?>">



                <button type="clear">
                    Resetta
                </button>
                <button type="submit">
                    Aggiorna
                </button>
            </form>
            <script defer src="/lib/ckeditor5/build/ckeditor.js"></script>
            <script defer src="/assets/configuration.js"></script>

        <?php } break; ?>
    
        <?php case PageToShow::EsamiInformatici: { ?>
            <h2>
                Non ancora disponibile
            </h2>
        <?php } break; ?>

        <?php case PageToShow::FiltroEsami: { ?>
            <h2>
                Non ancora disponibile
            </h2>
        <?php } break; ?>

        <?php case PageToShow::Menu: { ?>
            <!-- Il target è la pagina corrente (a qualunque endpoint si trovi) -->
            <form method="get">

                <label for="cdl"></label>
                <select name="cdl" id="cdl">
                    <option value="">Scegli un Cdl:</option>
                    
                    <?php foreach(Configurazione::CorsiDiLaurea() as $nome => $_cdl) { ?>
                        <option 
                            title="<?= htmlspecialchars($nome) ?>"
                            value="<?= htmlspecialchars($nome) ?>">
                            <?= htmlspecialchars($nome) ?>
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
        <?php } break; ?>

    <?php } ?>    
</body>
</html>
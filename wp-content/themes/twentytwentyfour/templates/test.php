<?php
/**
 * Template Name: Test Page
 */
include_once dirname(__DIR__, 4) . '/TEST/TestAccessoProspetti.php';
include_once dirname(__DIR__, 4) . '/TEST/TestConfigurazione.php';
include_once dirname(__DIR__, 4) . '/TEST/TestGestioneCarrieraStudente.php';
include_once dirname(__DIR__, 4) . '/TEST/TestParametroFormula.php';
include_once dirname(__DIR__, 4) . '/TEST/TestCorsoDiLaurea.php';
include_once dirname(__DIR__, 4) . '/TEST/TestCarrieraLaureando.php';
include_once dirname(__DIR__, 4) . '/TEST/TestCarrieraLaureandoInformatica.php';
$test_array = array(
    new TestAccessoProspetti_PathAusiliario,
    new TestAccessoProspetti_PathCommissioneWeb(),
    new TestAccessoProspetti_PathLaureandoServer(),

    new TestConfigurazione_CorsiDiLaurea(),
    new TestConfigurazione_IngInf(),
    new TestConfigurazione_EsamiInformatici(),

    new TestGestioneCarrieraStudente_restituisciAnagraficaStudente(),
    new TestGestioneCarrieraStudente_restituisciCarrieraStudente(),

    new TestParametroFormula(),

    new TestCorsoDiLaurea(),

    new TestCarrieraLaureando(),
    new TestCarrieraLaureandoInformatica_NoBonus(),
    new TestCarrieraLaureandoInformatica_ConBonus()
);
?>
<!DOCTYPE html>
<html lang="it-it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Test dei moduli
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
        ul {
            width: calc(100% - 6em);
            margin-inline: 3em;
            padding: 0;
        }
        .test {
            width: 100%;
            padding: 6px;
            margin: 0;
        }
        .test > span {
            user-select: none;
        }
            .test.ok > output {
                color: green;
            }
            .test.error > output {
                color: red;
            }
            .test.error pre {
                background-color: antiquewhite;
                color: darkred;
            }
    </style>
</head>
<body>
    <h1>
        Lista dei test
    </h1>
    <hr>
    <ul>
        <?php foreach ($test_array as $test) { 
            $res = $test->Test(); ?>
            <li class="test <?= $res ? 'ok' : 'error' ?>">
                <span>
                    <?= htmlspecialchars(get_class($test)) ?>:
                </span>
                &nbsp;

                <output>
                    <?= $res ? "Superato" : "Non Superato" ?>
                </output>
                <?php if (!$res) { ?>
                    <details>
                        <summary>
                            Dettagli
                        </summary>
                        <pre><?= $test->LastCallDetails() ?></pre>
                    </details>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
</body>
</html>
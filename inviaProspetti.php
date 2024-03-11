<?php
    require_once __DIR__ . '/utils/InvioPDFLaureando2.php';

    $invio = new InvioPDFLaureando2();
    $invio->invioProspetti();
    echo "i prospetti sono stati inviati";
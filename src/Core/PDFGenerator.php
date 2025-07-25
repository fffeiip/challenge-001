<?php

namespace App\Core;

use Dompdf\Dompdf;
use Dompdf\Options;

class PDFGenerator
{
    public static function generate(string $html, string $filename): void
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // in case you use any images or fonts

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Force download or open in browser
        header('Content-Type: application/pdf');
        header("Content-Disposition: inline; filename=\"$filename\"");
        echo $dompdf->output();
        exit;
    }
}

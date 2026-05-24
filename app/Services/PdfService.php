<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf as DomPDF;
use Illuminate\Support\Facades\View;

class PdfService
{
    /**
     * Génère un PDF à partir d'une vue Blade
     *
     * @param string $view Nom de la vue (ex: 'pdf.site')
     * @param array $data Données à passer à la vue
     * @param string $filename Nom du fichier de sortie
     * @return \Illuminate\Http\Response
     */
    public function generate(string $view, array $data, string $filename)
    {
        $html = View::make($view, $data)->render();

        $pdf = DomPDF::loadHTML($html);
        
        // Configuration pour supporter les caractères spéciaux et UTF-8
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download($filename . '.pdf');
    }

    /**
     * Génère un PDF et retourne l'instance pour streaming ou stockage
     */
    public function stream(string $view, array $data)
    {
        $html = View::make($view, $data)->render();
        $pdf = DomPDF::loadHTML($html);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf;
    }
}

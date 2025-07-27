<?php
namespace Core;

use TCPDF;
use App\Models\User;

class PDFService
{
    /**
     * Initialize a TCPDF instance.
     */
    protected static function initPDF(User $user, string $title): TCPDF
    {
        // Initialize TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Document metadata
        $pdf->SetCreator('Admin Tool');
        $pdf->SetAuthor($user->username);
        $pdf->SetTitle($title);

        // Header & footer fonts
        $pdf->setHeaderFont(['helvetica', '', 12]);
        $pdf->setFooterFont(['helvetica', '', 8]);

        // Set margins: left, top (including space for header), right
        $pdf->SetMargins(15, 25, 15);
        // Header margin, Footer margin
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(15);

        // Enable auto page breaks (bottom margin)
        $pdf->SetAutoPageBreak(true, 20);

        // Branded header: no logo, left title, right subtitle, custom colors
        $pdf->SetHeaderData(
            '',      // no logo
            0,
            'Admin Tool',    // header title
            '',         // header subtitle
            [0, 78, 121],   // text color (dark blue)
            [0, 78, 121]    // line color
        );
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);

        // Default font for content
        $pdf->SetFont('helvetica', '', 10);

        // Add first page
        $pdf->AddPage();

        return $pdf;
    }

    /**
     * Generate a detail report PDF for any entity.
     * @param string $filename  Name of the generated PDF file
     * @param string $title     Title displayed in the PDF
     * @param array  $fields    Associative array label=>value
     * @param User   $user      Current user
     * @param string|null $subTitle Optional subtitle for a secondary table
     * @param array  $subHeaders Optional headers for the secondary table
     * @param array  $subRows    Optional rows for the secondary table
     */
    public static function detail(string $filename, string $title, array $fields, User $user, ?string $subTitle = null, array $subHeaders = [], array $subRows = []): void
    {
        $pdf = self::initPDF($user, $title);
        $printedAt = date('Y-m-d H:i:s');
        $html = "<h1 style=\"text-align:center;color:#1F4E79;font-size:18pt;margin-bottom:10px;\">$title</h1>";
        $html .= '<table style="width:100%;border-collapse:collapse;font-size:10pt;color:#333;">';
        foreach ($fields as $label => $value) {
            $html .= '<tr>'
                   . "<td style=\"padding:6px;border:1px solid #ddd;font-weight:bold;\">$label</td>"
                   . "<td style=\"padding:6px;border:1px solid #ddd;\">$value</td>"
                   . '</tr>';
        }
        $html .= '</table>';


        // Optional secondary table
        if ($subTitle && $subHeaders && $subRows) {
            $html .= '<h2 style="color:#1F4E79;font-size:14pt;margin-top:10px;margin-bottom:8px;">'
                   . htmlspecialchars($subTitle)
                   . '</h2>';
            $html .= '<table style="width:100%;border-collapse:collapse;font-size:10pt;color:#333;margin-bottom:15px;">';
            // headers
            $html .= '<tr>';
            foreach ($subHeaders as $h) {
                $html .= '<th style="color:black;padding:6px;border:1px solid #ddd;font-weight:bold;">'
                       . htmlspecialchars($h)
                       . '</th>';
            }
            $html .= '</tr>';
            // rows
            foreach ($subRows as $row) {
                $html .= '<tr>';
                foreach ($row as $cell) {
                    $html .= '<td style="padding:6px;border:1px solid #ddd;">'
                           . htmlspecialchars((string)$cell)
                           . '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</table>';
        }

        $html .= "<div style=\"text-align:right;font-size:8pt;color:#666;border-top:1px solid #ccc;margin-top:10px;\">"
               . "Printed by {$user->username} on {$printedAt}</div>";

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output($filename, 'I');
    }

    /**
     * Generate a list report PDF for any entity.
     * @param string $filename Name of the generated PDF file
     * @param string $title    Title displayed in the PDF
     * @param array  $headers  Array of column headers
     * @param array  $rows     Array of row data (each row is indexed array)
     * @param User   $user     Current user
     */
    public static function list(string $filename, string $title, array $headers, array $rows, User $user): void
    {
        $pdf = self::initPDF($user, $title);
        $printedAt = date('Y-m-d H:i:s');
        $html = "<h1 style=\"text-align:center;color:#1F4E79;font-size:18pt;margin-bottom:10px;\">$title</h1>";
        $html .= '<table style="width:100%;border-collapse:collapse;font-size:10pt;color:#333;">';
        // header row
        $html .= '<tr>';
        foreach ($headers as $h) {
            $html .= "<th style=\"color:black;padding:6px;border:1px solid #ddd;font-weight:bold;\">$h</th>";
        }
        $html .= '</tr>';
        // data rows
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= "<td style=\"padding:6px;border:1px solid #ddd;\">$cell</td>";
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        $html .= "<div style=\"text-align:right;font-size:8pt;color:#666;border-top:1px solid #ccc;margin-top:10px;\">"
               . "Printed by {$user->username} on {$printedAt}</div>";

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output($filename,'I');
    }
}

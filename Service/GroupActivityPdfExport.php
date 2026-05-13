<?php
/**
 * Minimal PDF (PDF 1.4) for group activity reports — no external libraries.
 *
 * Object numbers and xref rows must match (1..N in order). Page references 4 (contents) and 5 (font).
 */
final class GroupActivityPdfExport
{
    private static function asciiLine(string $s): string
    {
        $s = str_replace(["\r\n", "\r", "\n"], ' ', $s);
        if (function_exists('iconv')) {
            $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
            if ($t !== false && $t !== '') {
                return $t;
            }
        }
        return preg_replace('/[^\x20-\x7E]/', '?', $s);
    }

    private static function escapePdfText(string $s): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $s);
    }

    /**
     * @param list<string> $lines
     */
    public static function streamDownload(string $downloadBaseName, array $lines): void
    {
        $downloadBaseName = preg_replace('/[^a-zA-Z0-9._-]+/', '-', $downloadBaseName);
        if ($downloadBaseName === '') {
            $downloadBaseName = 'activity-report';
        }
        if (strtolower(substr($downloadBaseName, -4)) !== '.pdf') {
            $downloadBaseName .= '.pdf';
        }

        $contentOps = [];
        $y = 742;
        $fontSize = 9;
        $lineHeight = 11;
        $left = 48;
        $bottomMargin = 52;

        foreach ($lines as $raw) {
            $line = self::asciiLine((string) $raw);
            $chunks = str_split($line, 96);
            foreach ($chunks as $chunk) {
                if ($chunk === '') {
                    continue;
                }
                if ($y < $bottomMargin) {
                    break 2;
                }
                // Tm = absolute text position (PDF user space: origin bottom-left; y up)
                $contentOps[] = sprintf(
                    'BT /F1 %d Tf 1 0 0 1 %d %d Tm (%s) Tj ET',
                    $fontSize,
                    $left,
                    $y,
                    self::escapePdfText($chunk)
                );
                $y -= $lineHeight;
            }
        }

        if (empty($contentOps)) {
            $contentOps[] = sprintf(
                'BT /F1 10 Tf 1 0 0 1 %d %d Tm (%s) Tj ET',
                $left,
                742,
                self::escapePdfText('No activity recorded for this group.')
            );
        }

        $stream = implode("\n", $contentOps);
        $streamLen = strlen($stream);

        // Objects MUST be numbered 1..5 and xref[i] = byte offset of object i.
        $obj1 = '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj';
        $obj2 = '2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj';
        $obj3 = '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] '
            . '/Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj';
        $obj4 = '4 0 obj << /Length ' . $streamLen . " >>\nstream\n" . $stream . "\nendstream\nendobj";
        $obj5 = '5 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj';

        $objects = [$obj1, $obj2, $obj3, $obj4, $obj5];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= $obj . "\n";
        }

        $count = count($objects);
        $xrefPos = strlen($pdf);
        $pdf .= "xref\n0 " . ($count + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= $count; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer << /Size " . ($count + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefPos . "\n%%EOF";

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $downloadBaseName . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        echo $pdf;
    }
}

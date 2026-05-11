<?php
// @author: C.A.D. BONDJE DOUE
// @file: MySQLCLIFormatter.php
// @date: 20260507 11:02:56
namespace IGK\System\Console\Text\Formatters;
/**
* auto generate doc.
* @package IGK\System\Console\Text\Formatters
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Console\Text\Formatters
*/
class MySQLCLIFormatter
{
    private const MIN_COLUMN_WIDTH = 10;
    private
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    const DEFAULT_COLUMN_WIDTH = 10;
    /**
    * Formate un tableau comme le CLI MySQLmysql> SELECT * FROM table;+-----+----------+-------+| id  | username | email |+-----+----------+-------+|  1  | john     | ... ||  2  | jane     | ... |+-----+----------+-------+2 rows in set
    * @param array $headers
    * @param array $rows
    */
    public static function FormatAsMySQL(array $headers, array $rows): string
    {
        if (empty($headers)) {
            return "Empty set\n";
        }

        // Calculer les largeurs optimales
        $widths = self::calculateOptimalWidths($headers, $rows);

        $output = '';
        // Ligne supérieure
        $sep = self::drawSeparator($widths) . "\n";  
        // En-têtes
        $output .= $sep.self::drawHeaderRow($headers, $widths). "\n".$sep; 
        // Lignes de données
        foreach ($rows as $row) {
            $output .= self::drawDataRow($row, $widths) . "\n";
        }
        // Ligne inférieure
        $output .= $sep;
        // Nombre de lignes
        $rowCount = count($rows);
        $rowWord = $rowCount === 1 ? 'row' : 'rows';
        $output .= "$rowCount $rowWord in set\n";
        return $output;
    }
    /**
    * Calcule les largeurs optimales des colonnes
    * @param array $headers
    * @param array $rows
    */
    private static function calculateOptimalWidths(array $headers, array $rows): array
    {
        $widths = [];
        // Initialiser avec les en-têtes
        foreach ($headers as $header) {
            $widths[] = max(strlen((string)$header), self::MIN_COLUMN_WIDTH);
        }
        // Ajuster selon les données
        foreach ($rows as $row) {
            foreach ($row as $i => $cell) {
                $cellLength = strlen((string)$cell);
                if (isset($widths[$i])) {
                    $widths[$i] = max($widths[$i], $cellLength, self::MIN_COLUMN_WIDTH);
                } else {
                    $widths[$i] = max($cellLength, self::MIN_COLUMN_WIDTH);
                }
            }
        }
        return $widths;
    }
    /**
    * Dessine la ligne de séparation: +-----+-----+
    * @param array $widths
    */
    private static function drawSeparator(array $widths): string
    {
        $separator = '+';
        foreach ($widths as $width) {
            $separator .= str_repeat('-', $width + 2) . '+';
        }
        return $separator;
    }
    /**
    * Dessine la ligne d'en-tête: | id | name |
    * @param array $headers
    * @param array $widths
    */
    private static function drawHeaderRow(array $headers, array $widths): string
    {
        $row = '|';
        foreach ($headers as $i => $header) {
            $padding = $widths[$i] - strlen((string)$header);
            $leftPad = intval(floor($padding / 2));
            $rightPad = $padding - $leftPad;
            $row .= ' ' . str_repeat(' ', $leftPad) . $header . str_repeat(' ', $rightPad) . ' |';
        }
        return $row;
    }
    /**
    * Dessine une ligne de données: | 1 | John |
    * @param array $cells
    * @param array $widths
    */
    private static function drawDataRow(array $cells, array $widths): string
    {
        $row = '|';
        foreach ($cells as $i => $cell) {
            $cellStr = (string)$cell;
            $cellStr = strlen($cellStr) > $widths[$i] ? substr($cellStr, 0, $widths[$i] - 3) . '...' : $cellStr;
            // Déterminer l'alignement selon le type
            if (is_numeric($cell) && !is_string($cell)) {
                // Nombres : alignés à droite
                $padding = $widths[$i] - strlen($cellStr);
                $row .= ' ' . str_repeat(' ', $padding) . $cellStr ;
            } else {
                // Texte : alignés à gauche
                $padding = $widths[$i] - strlen($cellStr);
                $row .= ' ' . $cellStr . str_repeat(' ', $padding);
            }
            $row.=' |';
        }
        return $row;
    }
    /**
    * print to console
    * @param array $headers
    * @param array $rows
    */
    public static function PrintMySQL(array $headers, array $rows): void
    {
        echo self::formatAsMySQL($headers, $rows);
    }
}
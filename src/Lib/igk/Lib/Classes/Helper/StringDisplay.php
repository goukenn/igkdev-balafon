<?php
// @author: C.A.D. BONDJE DOUE
// @file: StringDisplay.php
// @date: 20251226 13:43:35
namespace IGK\Helper;
use IGK\Models\ModelBase;
/**
* auto generate doc.
* @package IGK\Helper
* @author C.A.D. BONDJE DOUE
*/
abstract class StringDisplay
{
    /**
     * return core string display
     * @param string $display 
     * @param array $properties 
     * @param mixed $row 
     * @return string 
     */
    public static function Display(string $display, array $properties, $row, ?bool $exposed=null): string
    {
        $args = StringUtility::ReadArgs($display);
        $exposed = $exposed ?? (($row instanceof ModelBase) && $row->getTableInfo()->prefix);
        $out = array_map(function ($a) use ($properties, $row, $exposed) {
            if (preg_match("/^(?P<op>\^|:|\+|-)?([a-z_][a-z0-9_]+)$/i", $n = trim($a), $tab)) {
                $n = $tab[2];
                $op = $tab['op'];
                if ($exposed || in_array($n, $properties)) {
                    $v = $exposed ? $row->{$n} : igk_getv($row, $n, $a);
                    switch ($op) {
                        case '^':
                            $v = strtoupper($v);
                            break;
                        case ':':
                            $v = strtolower($v);
                            break;
                        case '+':
                            $v = ucfirst($v);
                            break;
                        case '-':
                            $v = lcfirst($v);
                            break;
                    }
                    return $v;
                }
            }
            return $a;
        }, $args);
        return implode('', $out);
    }
}
<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlNodeTagExplosionTrait.php
// @date: 20240119 11:47:11
namespace IGK\System\Html\Traits;

/**
* auto generate doc.
* @package IGK\System\Html\Traits
* @author C.A.D. BONDJE DOUE
*/
trait HtmlNodeTagExplosionTrait{

    /**
    * Property: split.
    * @var mixed
    */
    protected $split = '>';

    /**
    * Explode tag definition.
    * @param string $tagname
    * @param array & $defs
    */
    protected function explodeTagDefinition(string $tagname , array & $defs){
        $v = '';
        if (preg_match('/[\(\[\{\>]/', $tagname)) {
            //+| contains special symbol on tag name
            $ln = strlen($tagname);
            $pos = 0; 
            while ($pos < $ln) {
                $ch = $tagname[$pos];
                switch ($ch) {
                    case '"':
                    case "'":
                        $v .= igk_str_read_brank($tagname, $pos, $ch, $ch);
                        break;
                    case "[":
                        $v .= igk_str_read_brank($tagname, $pos, ']', '[');
                        $ch = '';
                        break;
                    case "(":
                        $v .= igk_str_read_brank($tagname, $pos, ')', '(');
                        $ch = '';
                        break;
                    case "{":
                        $v .= igk_str_read_brank($tagname, $pos, '}', '{');
                        $ch = '';
                        break;
                    case $this->split:
                        if (!empty($cv = trim($v))) {
                            $defs[] = $cv;
                            $v = "";
                            $ch = '';
                        }
                        break;
                    case ' ':
                        $ch = '';
                }
                $v .= $ch;
                $pos++;
            }
            if (!empty($ch = trim($v))) {
                array_push($defs, $ch);
            }
        } else {
            $defs[] = $tagname;
        }
    }
}
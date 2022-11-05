<?php
// @author: C.A.D. BONDJE DOUE
// @file: TextBracket.php
// @date: 20221023 10:16:14
namespace IGK\System\Text;


///<summary></summary>
/**
* reduce data
* @package IGK\System\Text
*/
class TextBracket{
    var $bracketStart = "(";
    var $bracketEnd = ")";

    private $m_blocks = [];

    /**
     * reduce block to string expression
     * @return string 
     */
    public function reduce():string{
        $p = null;
        $q = null;
        $tab = $this->m_blocks;
        $sb = "";
        $s = "";
        $n  = [];
        $depth_s = 0;
        $depth_e = 0;
        $blocks = [];
        while( count($tab)> 0){
            $q = array_shift($tab);
            if (is_string($q)){
                echo "write : ".$q. "\n";
                // $sb .= sprintf("%s %s %s", 
                //     str_repeat($this->bracketStart, $c),
                //     $q,
                //     str_repeat($this->bracketEnd, $c)
                //  );
                if ($depth_e){
                    // $bf = $sb;
                    $c = $depth_s - $depth_e;
                    // $bf = str_repeat($this->bracketStart, $c).$bf;
                    // $bf.= str_repeat($this->bracketEnd, $c);
                    $depth_s = $c;
                    $depth_e = 0;
                    $g = $blocks[count( $blocks) - 1];
                    $g = str_repeat($this->bracketStart, $c) .$g;
                    $g.= str_repeat($this->bracketEnd, $c);
                    $blocks[count( $blocks) - 1] = $g;
                }
                // $bf .=" ".$q." ";
                // $sb = $bf;
                $blocks[] = " ".$q." ";
                continue;
            } 
            if ($p === $q ){
                array_pop($n);
                if ($p = array_pop($n)){
                    array_push($n, $p);
                }               
                $depth_e++;
                continue;
            }
            if (count($q->blocs)>0){
                $g = $q->blocs;
                $g[] = $q;
                array_unshift($tab, ...$g);
                $p = $q;
                array_push($n, $p); 
                $depth_s++;
            }
        }

        if ($depth_e){
            $depth_s++;
            $c = $depth_s - $depth_e;
            $sb = str_repeat($this->bracketStart, $c).$sb;
            $sb.= str_repeat($this->bracketEnd, $c);

            $g = $blocks[count( $blocks) - 1];
            $g = str_repeat($this->bracketStart, $c) .$g;
            $g.= str_repeat($this->bracketEnd, $c);
            $blocks[count( $blocks) - 1] = $g;

            $depth_s = $c;
            $sb = str_repeat($this->bracketStart, $c).
                  implode ("" , $blocks).
                  str_repeat($this->bracketEnd, $c);
        }
        return $sb;

    }
    /**
     * parse string to text bracket block
     * @param string $source 
     * @return ?TextBracket 
     */
    public static function Parse(string $source, $start=null, $end=null){
        $g = new static;
        if ($start && $end){
            $g->bracketStart = $start;
            $g->bracketEnd = $end;
        }
        if (($b = self::_Load($g, $source))!==false){
            $g->m_blocks = $b;            
            return $g;
        }
        return null;
    }
    private static function _Load($def, string $source){

        $ln = strlen($source);
        $pos = 0;
        $buffer = "";
        $bloc = null;
        $blocks = [];
        $depth = 0; 
        $skipSpace = 0;
        $count = 0;
        while ($ln>$pos) {
            $ch = $source[$pos];

            if ($ch == ' '){
                if ($skipSpace){
                    continue;
                }else{ 
                    $skipSpace = 1;
                }
            }

            switch ($ch) { 
                case $def->bracketStart:
                    $depth ++ ;
                    if ($bloc){
                        if (!empty($buffer)){
                            $bloc->blocs[] = trim($buffer);
                            $buffer = "";
                        }
                    } else {
                        if (!empty($buffer)){
                            $blocks[] = $buffer;
                        }
                    }
                    $pbloc = $bloc;
                    $bloc = new TextBrackerBlockInfo;                    
                    $bloc->buffer = $buffer;
                    $bloc->parent =$pbloc;
                    $bloc->blocs =[];
                    $bloc->count=$count;
                    $count ++; 
                    if (is_null($bloc->parent)){
                        $blocks[] = $bloc;
                    }else {
                        $bloc->parent->blocs [] = $bloc;
                    }
                    $buffer = & $bloc->buffer;
                    break;
                case $def->bracketEnd:
                    $depth --;

                    if ($bloc){
                        if (!empty($buffer)){
                            $bloc->blocs[] = trim($buffer);
                            $buffer = "";
                        }
                        $bloc = $bloc->parent;
                    } else {
                        $bloc = null;
                    }
                    if($bloc){
                        $buffer = & $bloc->buffer;
                    }
                    break;
                default:
                    $skipSpace = 0;
                    $buffer .= $ch; 
                    break;
            }
            $pos++;
        }
        if ($depth == 0)
            return $blocks;
        return false;
    }
}
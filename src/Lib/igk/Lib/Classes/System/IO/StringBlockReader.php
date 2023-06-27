<?php
// @author: C.A.D. BONDJE DOUE
// @file: StringBlockReader.php
// @date: 20230105 06:43:43
namespace IGK\System\IO;


///<summary></summary>
/**
* read block expression 
* @package IGK\System\IO
* @usage(
*   $reader = new StringBlockReader;
*   $reader->start = '(';
*   $reader->end = ')';
*   $src =  $reader->read($tring);
*)
*/
class StringBlockReader{
    var $start;
    var $end;
    var $offset=0; 
    /**
     * read block in string
     * @param mixed $s 
     * @return string
     */
    public function read(string $s):string{
        $ln = strlen($s);
        $offset = & $this->offset;
        $o = "";
        $_read = false;
        $_level = 0;
        $_stop = false;
        $use_string = strpos("'\"", $this->start)===false;
        while (!$_stop && ($offset< $ln)){
            $ch = $s[$offset];
            if ($use_string){
                switch($ch){
                    case "'":
                    case '"':
                        $o.= igk_str_read_brank($s, $offset, $ch, $ch, null, 1, 1);
                        $ch = null;
                        break;
                }
            }
            switch($ch){
                case $this->start:
                    $_level++;
                    if ($_level==1){
                        $_read = true;
                        $ch = null;
                    }
                    break;
                case $this->end:
                    $_level--;
                    if($_level == 0){
                        $_read = 0;
                        $_stop = true;                        
                    }
                    break;
            }
            if ($_read){
                $o.=$ch;
            }
            $offset++;
        }
        return trim($o);
    }
}
<?php
// @author: C.A.D. BONDJE DOUE
// @file: CSVHelper.php
// @date: 20231017 23:19:24
namespace IGK\System\IO\CSV\Helper;


///<summary></summary>
/**
 * 
 * @package IGK\System\IO\CSV\Helper
 */
class CSVHelper
{

    const CSV_READ_SERIAL = 1;

    public static function IsDelimeterEscaped(string $src, int $pos ){
        if ($src[$pos-1]=='\\'){
            return true;
        }
        return false;
    }
    public static function CheckDelimiter(string $src, string $delimiter, int & $bpos){
        while($bpos && self::IsDelimeterEscaped($src, $bpos)){
            $bpos = strpos($src, $delimiter, $bpos+1);
        }
    }
    /**
     * helper
     * @param string $src source to treat
     * @param string $delimiter string delimiter
     * @param mixed $last_segment will contain last invalid segment
     * @param callable|null $callback 
     * @return array 
     */
    public static function ReadLines(string $src, $delimiter = '"', &$last_segment = null, callable $callback = null, ?int $flags = null)
    {
        $v_is_read_serialize = $flags && (($flags & self::CSV_READ_SERIAL) == self::CSV_READ_SERIAL);

        // + | --------------------------------------------------------------------
        // + | read line and update depending on line feed or not 
        // + |
        $v_tlen = strlen($src);
        $LF = "\n";
        $lines = [];
        $tpos = 0;
        $last_segment = null;
        $v_fc_add_line = function ($l) use (&$lines, $callback) {
            //igk_dev_wln("add : ".$l);
            $lines[] = $l;
            if ($callback) {
                if (!$callback($l)) {
                    return false;
                }
            }
            return true;
        };
        $count = 0;
        $ref = [0, 0];
        // TODO : read csv line helper
        while ($tpos < $v_tlen) {
            $lpos = strpos($src, $LF , $tpos);
            $bpos = strpos($src, $delimiter, $tpos);
            ($bpos > 0) && self::CheckDelimiter($src, $delimiter, $bpos);
           
            $ref[0] = &$bpos; // + <- pointer of the delimiter
            $ref[1] = &$lpos; // + <- pointer to the line feed
            if (($bpos !== false) && $v_is_read_serialize) {
                if (preg_match('/[^:]+:[^:]+:\{/', $src, $tag, 0, $bpos)) {
                    igk_str_read_serialize_data($src, $bpos);
                    $bpos++;
                    if ($bpos>$v_tlen){
                        $v_fc_add_line(substr($src, $tpos));
                        return $lines;
                    }
                    $lpos = strpos($src, $LF , $bpos);
                }
            }
            if (($lpos !== false) && ($bpos !== false)) {
                if ($lpos < $bpos) {
                    // + line detected 
                    if (!$v_fc_add_line(substr($src, $tpos, $lpos - $tpos))) {
                        return $lines;
                    }
                    $count = 0;
                } else {
                    // + | --------------------------------------------------------------------
                    // + | saute mouton: 
                    // + | tanq que le delimiteur est < ln => incrementer le counter 
                    // + |      sinon si counter est pair alors ligne detecter 
                    // + |      sinon avancer le ln sur le prochain ln
                    // + |             si ln === false alors terminer la selection
                    // + |             si ln < delimiteur 
                    $count = 0;
                    while (true) {
                        $count++;
                        $ref[0] = strpos($src, $delimiter, $ref[0] + 1);
                        if ($ref[0]===false){
                            break;
                        }
                        self::CheckDelimiter($src, $delimiter, $ref[0]);
                        if ($ref[0] > $ref[1]) {
                            if (($count % 2) == 0) {
                                break;
                            } else {
                                $next = strpos($src, $LF , $ref[1] + 1);
                                if ($next === false) {
                                    //skip to end of next delimiter
                                    $count++;
                                    $ref[1] = $ref[0]+1;
                                    break;
                                }
                                $ref[1] = $next;                               
                            }
                        }
                    }
                    if (($count % 2)==0){
                        if (!$v_fc_add_line(substr($src, $tpos, $lpos-$tpos))){
                            return $lines;
                        }
                    }else{
                        $last_segment = substr($src, $tpos);
                        return $lines;
                    }
                }
            } else if ($lpos === false) {
                if (($count % 2) === 0) {
                    $v_fc_add_line(substr($src, $tpos));
                } else {
                    $last_segment = substr($src, $tpos);
                }
                $lpos = $v_tlen;
                break;
            } else {
                // detect end line

                $v_fc_add_line(substr($src, $tpos, $lpos - $tpos));
                //$v_fc_add_line(substr($src, $tpos));//, $lpos-$tpos));
            }
            $tpos = $lpos + 1;
        }
        return $lines;
    }
}


/**
 * helper: read serialize data string
 * @param string $data 
 * @param int $pos 
 * @return string|false 
 */
function igk_str_read_serialize_data(string $str, int &$pos)
{
    $tpos = $pos;
    $brack = strpos($str, "{", $pos);
    $sub = substr($str, $tpos, $brack - $tpos);
    $s = igk_str_read_brank($str, $brack, '}', '{');
    if (($s[0] == '{') && ($s[strlen($s) - 1] == '}')) {
        $pos += ($brack - $pos);
        return $sub . $s;
    }
    $pos = $tpos;
    return false;
}

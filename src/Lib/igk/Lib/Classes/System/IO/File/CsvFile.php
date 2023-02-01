<?php
// @author: C.A.D. BONDJE DOUE
// @file: CsvFile.php
// @date: 20230120 09:12:31
namespace IGK\System\IO\File;

use IGK\Helper\MapHelper; 

///<summary></summary>
/**
 * 
 * @package IGK\System\IO\File
 */
class CsvFile
{
    const SEPARATORS = [',',':', ';',"\t", "|"];
    /**
     * limit the readed data
     * @var mixed
     */
    var $limit;
    /**
     * the separator
     * @var string
     */
    var $separator = ',';
    /**
     * ignore first line
     * @var ?bool
     */
    var $ignoreFirstline;
    /**
     * mapper
     * @var ?array
     */
    var $mapper;

    /**
     * map entry listener
     * @var ?callable
     */
    var $mapEntryListener;
    /**
     * 
     * @param string $content 
     * @return null|array 
     */
    public function parseData(string $content): ?array
    {
        $ignoreFirstline = $this->ignoreFirstline;
        $data = array_map(
            function ($line) use(& $ignoreFirstline){
                $tab = $this->_parseLine(trim($line));
                if ($this->mapper){
                    $tab = MapHelper::MapDataToObject($tab, $this->mapper);
                    if ((!$ignoreFirstline) && ($fc = $this->mapEntryListener)){
                        $fc($tab);
                    }
                    $ignoreFirstline = false;
                }
                return $tab;
            },
            explode("\n", $content)
        );
        return array_filter($data);
    }
    /**
     * use data to expor line 
     * @param array $data 
     * @return void 
     */
    public function exportLine(array $data, $length=null):string{
        
        if (!is_null($length) && ($length>0)){
            $data = array_slice($data, 0, $length);
        }
        return implode($this->separator." ", array_map(function($a){
            if (is_null($a)){
                return '';
            }
            if (!preg_match("/('|\")/", $a)){
                return trim($a);
            } 
            return $a;
        },$data));
    }
    private function _parseLine($line)
    {
        $ch = $line;
        $sep = $this->separator;
        $ln = strlen($line);
        $tab = [];
        $v = '';
        $pos = 0;
        $g = false;
        $litteral = false;
        while ($pos < $ln) {
            $g = true;
            $ch = $line[$pos];
            switch ($ch) {
                case $sep:
                    if ($litteral){
                        $v = trim($v, ' "\'');
                    }
                    $tab[] = trim($v);
                    $v = '';
                    $ch ='';
                    $g = false;
                    break;
                case '"':
                case "'":
                    $v .= igk_str_read_brank($line, $pos, $ch, $ch, null, true, false);
                    $ch ='';
                    $litteral = true;
                    break;
                default:
                    $litteral = false;
                    break;
            }
            $pos++;
            $v .= $ch;
        }
        if ($g && $v){
            if ($litteral){
                $v = trim($v, ' "\'');
            }
            $tab[] = trim($v);
        }
        return $tab;
    }

    /**
     * mapp data an return an object \
     * the callable must accept two parameter : (?string $v, int $i=null): value
     * @return null|object 
     */
    public function map(array $data, $mapper):?object{
        return MapHelper::MapDataToObject($data, $mapper); 
    }
}

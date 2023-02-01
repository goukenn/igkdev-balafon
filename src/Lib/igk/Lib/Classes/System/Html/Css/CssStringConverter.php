<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssStringConverter.php
// @date: 20230106 15:39:28
namespace IGK\System\Html\Css;

use IGK\System\IO\Configuration\ConfigurationReader;

///<summary></summary>
/**
* use to parse css litteral
* @package IGK\System\Html\Css
*/
class CssStringConverter extends ConfigurationReader{
    var $delimiter = ";";
    var $separator = ":";

    protected function _readValue(): ?string
    {
        // + | --------------------------------------------------------------------
        // + | value can contain section (), litteral string or 
        // + |        
        return trim($this->_readCssValueData($this->delimiter) ?? '');
    }
    protected function _readCssValueData(string $end){
        $d = null;
        $bracket = 0;
        while($this->_canRead()){
            $ch = $this->m_text[$this->m_offset];
            if ($ch == '(')
                $bracket++;
            if ($ch == ')')
                $bracket--;
            if ($bracket && ($ch == $end)){
                $d.=$ch;
                $this->m_offset++;
                continue;   
            }
            switch($ch){
                case '"':
                case "'":
                    // litteral consideration
                    $d.= igk_str_read_brank($this->m_text, $this->m_offset, $ch, $ch,null,1, 1);                   
                break;
                
                default:
                    if (is_null($d)){
                        $d = "";
                    }
                    $d .= $ch;
                    break;
                case $end: 
                    
                    $this->m_offset--;
                    return !is_null($d) ? trim($d) : null; 
            }
            $this->m_offset++;
        } 
        return $d;
    }
}
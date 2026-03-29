<?php
// @author: C.A.D. BONDJE DOUE
// @file: EnumDefinitionReader.php
// @date: 20231219 22:34:30
namespace IGK\System\IO;
use IGK\System\IO\Configuration\ConfigurationReader;
/** 
 * use delimiter to split value   
* @package IGK\System\IO
*/
class EnumDefinitionReader extends ConfigurationReader{
    /**
    * Read name.
    * @return ?string
    */
    protected function _readName(): ?string{
        return trim($this->_readData($this->separator) ?? '');
    }
    /**
    * Read litteral end.
    * @param string $ch
    * @param string $end
    * @return bool
    */
    protected function _readLitteralEnd(string $ch, string $end):bool{
        return ($ch==$this->delimiter) || ($ch == $end);
    }
}
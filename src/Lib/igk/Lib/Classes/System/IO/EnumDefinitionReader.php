<?php
// @author: C.A.D. BONDJE DOUE
// @file: EnumDefinitionReader.php
// @date: 20231219 22:34:30
namespace IGK\System\IO;

use IGK\System\IO\Configuration\ConfigurationReader;

///<summary></summary>
/** 
 * use delimiter to split value   
* @package IGK\System\IO
*/
class EnumDefinitionReader extends ConfigurationReader{
    protected function _readName(): ?string{
        return trim($this->_readData($this->separator) ?? '');
    }
    protected function _readLitteralEnd(string $ch, string $end):bool{
        return ($ch==$this->delimiter) || ($ch == $end);
    }
}
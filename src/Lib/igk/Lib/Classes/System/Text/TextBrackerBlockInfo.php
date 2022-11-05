<?php
// @author: C.A.D. BONDJE DOUE
// @file: TextBrackerBlockInfo.php
// @date: 20221023 10:15:46
namespace IGK\System\Text;


///<summary></summary>
/**
* 
* @package IGK\System\Text
*/
class TextBrackerBlockInfo{
    var $buffer = "";
    var $count = 0;
    var $blocs = [];
    /**
     * TextBrackerBlockInfo
     * @var TextBrackerBlockInfo
     */
    var $parent;

    public function __toString(){
        return "info: ".$this->count;
    }
}
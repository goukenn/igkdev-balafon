<?php
// @author: C.A.D. BONDJE DOUE
// @file: TextBrackerBlockInfo.php
// @date: 20221023 10:15:46
namespace IGK\System\Text;

/**
* auto generate doc.
* @package IGK\System\Text
*/
class TextBrackerBlockInfo{
    /**
    * Property: buffer.
    * @var mixed
    */
    var $buffer = "";
    /**
    * Count: count.
    * @var mixed
    */
    var $count = 0;
    /**
    * Property: blocs.
    * @var mixed
    */
    var $blocs = [];
    /**
     * TextBrackerBlockInfo
     * @var TextBrackerBlockInfo
     */
    var $parent;
    /**
    * get string presentation.
    */
    public function __toString(){
        return "info: ".$this->count;
    }
}
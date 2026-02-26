<?php
// @author: C.A.D. BONDJE DOUE
// @file: TextBrackerBlockInfo.php
// @date: 20221023 10:15:46
namespace IGK\System\Text;
/**
* 
* @package IGK\System\Text
*/
class TextBrackerBlockInfo{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $buffer = "";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $count = 0;

    /**
    * auto generate doc.
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
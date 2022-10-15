<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadStructInfo.php
// @date: 20221014 18:59:32
namespace IGK\System\Runtime\Compiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class ReadStructInfo{
    var $name = "";
    var $buffer = "";
    var $modifiers = [];
    var $depth= 0;
    var $type = "";
    /**
     * block that define the current struct
     * @var ?ReadBlockInstructionInfo
     */
    var $blockInfo;

    public function __construct(string $type)
    {
        $this->type = $type;
    }
}
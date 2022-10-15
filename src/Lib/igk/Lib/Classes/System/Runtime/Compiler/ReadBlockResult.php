<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadBlockResult.php
// @date: 20221014 21:15:46
namespace IGK\System\Runtime\Compiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class ReadBlockResult{
    var $value;
    /**
     * use internally for debug. 
     * @var mixed
     */
    var $flag;
    public function __construct(string $value, $flag=null)
    {
        $this->value = $value;
        $this->flag = $flag;
    }
    public function __toString()
    {
        return "Result:".$this->value."#".$this->flag;
    }
}
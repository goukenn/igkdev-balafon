<?php
// @author: C.A.D. BONDJE DOUE
// @file: AttribExpression.php
// @date: 20230612 14:57:04
namespace IGK\System\Html;


///<summary></summary>
/**
* 
* @package IGK\System\Html
*/
class AttribExpression{
    private $data;

    public function __construct(string $data){
        $this->data = $data;
    }

    public function getValue($options = null) { 
        return $this."";
    }
    public function __toString(){
        return "<?= ".$this->data ." ?>";
    } 
    public function useAttribName():bool{
        return true;
    }
}
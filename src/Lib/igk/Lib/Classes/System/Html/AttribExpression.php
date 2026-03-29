<?php
// @author: C.A.D. BONDJE DOUE
// @file: AttribExpression.php
// @date: 20230612 14:57:04
namespace IGK\System\Html;
/**
* auto generate doc.
* @package IGK\System\Html
*/
class AttribExpression{
    /**
    * Property: data.
    * @var mixed
    */
    private $data;
    /**
    * .ctr
    * @param string $data
    */
    public function __construct(string $data){
        $this->data = $data;
    }
    /**
    * Returns Value.
    * @param null|mixed $options
    */
    public function getValue($options = null) { 
        return $this."";
    }
    /**
    * get string presentation.
    */
    public function __toString(){
        return "<?= ".$this->data ." ?>";
    }
    /**
    * Use attrib name.
    * @return bool
    */
    public function useAttribName():bool{
        return true;
    }
}
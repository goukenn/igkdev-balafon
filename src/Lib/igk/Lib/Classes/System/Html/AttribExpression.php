<?php
// @author: C.A.D. BONDJE DOUE
// @file: AttribExpression.php
// @date: 20230612 14:57:04
namespace IGK\System\Html;
/**
* 
* @package IGK\System\Html
*/
class AttribExpression{

    /**
    * auto generate doc.
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
    * auto generate doc.
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
    * auto generate doc.
    * @return bool
    */
    public function useAttribName():bool{
        return true;
    }
}
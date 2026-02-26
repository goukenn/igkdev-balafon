<?php
// @author: C.A.D. BONDJE DOUE
// @file: ParamHostHelperActionTrait.php
// @date: 20221121 12:01:54
namespace IGK\Actions\Traits;
/**
* params action trait - store param between action access
* @package IGK\Actions\Traits
*/
trait ParamHostHelperActionTrait{

    /**
    * auto generate doc.
    * @param mixed $entry
    * @param mixed $autoreset
    */
    protected function getParams($entry, $autoreset=true){
        $g = $this->getController()->getParam($entry);
        if ($autoreset && $g){
            $this->setParams($entry , null);
        }
        return $g;
    }

    /**
    * auto generate doc.
    * @param mixed $entry
    * @param mixed $param
    */
    protected function setParams($entry, $param){
        $this->getController()->setParam($entry, $param);
    }
}
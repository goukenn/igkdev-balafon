<?php
// @author: C.A.D. BONDJE DOUE
// @file: ControllerUriReplacementTrait.php
// @date: 20230124 03:15:10
namespace IGK\System\Actions\Traits;


///<summary></summary>
/**
* use to replace uri in normal context 
* @package IGK\System\Actions\Traits
*/
trait ControllerUriReplacementTrait{
    /**
     * get replacement uri
     * @param mixed $fname 
     * @return string 
     */
    public function getReplacementUri($fname):?string{
        return $this->getController()->getAppUri($fname);
    }
}
<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKMySQLTimeManager.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Database\MySQL;
use \IGKObject;
/**
* Represent IGKMySQLTimeManager class
*/
final class IGKMySQLTimeManager extends IGKObject{

    /**
    * Property: ad.
    * @var mixed
    */
    var $ad;
    /**
    * 
    * @param mixed $ad
    */

    public function __construct($ad){
        $this->ad=$ad;
    }
    /**
    * 
    */

    public function Now(){
        return date($this->ad->getFormat("datetime"));
    }
}
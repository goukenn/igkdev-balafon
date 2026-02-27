<?php
// @file: UtilityActions.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
* Represent IGKApi namespace
*/
namespace IGK\api;

use IGK\Helper\IO;

// DIRECT RENDERINGuse IGK\Helper\IO as IGKIO;
/**
* Represent UtilityActions class
*/
class UtilityActions{

    /**
    * Property: ctrl.
    * @var mixed
    */
    var $ctrl;

    /**
    * Property: target.
    * @var mixed
    */
    var $target;

    /**
    * auto generate doc.
    * @param mixed $t
    */

    public function __construct($ctrl, $t){
        $this->ctrl=$ctrl;
        $this->target=$t;
    }

    /**
    * auto generate doc.
    */
    public function rmDir(){
        $n=igk_getr("clName");
        if(!empty($n)){
            $f=igk_dir(igk_io_basedir());
            $d=igk_io_basedir()."/__temp_dir";
            rename($f."/".$n, $d);
            IO::RmDir($d); 
        }
    }
}

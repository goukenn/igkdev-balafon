<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ActionFormOptions.php
// @date: 20221114 21:43:57
// @desc: 
namespace IGK\Actions;
use IGKObject;
/**
 * action base form option 
 * @package 
 */
class ActionFormOptions extends IGKObject{
    /**
     * uri referer
     * @var mixed
     */
    var $referer;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $good_uri;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $noRegister;
    /**
     * passing extra data 
     * @var ?array
     */
    var $data = [];    
}
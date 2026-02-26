<?php
// @file: IGKCssParserException.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Exceptions;

/**
* Css parser exception.
* @package IGK\System\Exceptions
*/
final class CssParserException extends \IGKException{

    /**
    * .ctr
    * @param mixed $msg
    */
    public function __construct($msg){
        parent::__construct($msg);
    }
}
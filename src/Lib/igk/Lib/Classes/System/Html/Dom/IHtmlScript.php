<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IHtmlScript.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;

/**
* Interface for html script.
* @package IGK\System\Html\Dom
*/
interface IHtmlScript{
    /**
     * Sets whether the script node is temporary.
     *
     * @param bool $value True to mark the script as temporary.
     * @return void
     */
    function setIsTemp(bool $value);
}
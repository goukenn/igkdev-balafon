<?php
// @author: C.A.D. BONDJE DOUE
// @file: IRegexMatchPatternStateListener.php
// @date: 20241104 11:36:34
namespace IGK\System\Text;

/**
* auto generate doc.
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
interface IRegexMatchPatternStateListener{
    /**
    * Saves State.
    */
    function saveState();
    function restoreState();
}
<?php
// @author: C.A.D. BONDJE DOUE
// @file: IRegexMatchPatternStateListener.php
// @date: 20241104 11:36:34
namespace IGK\System\Text;
/**
* 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
interface IRegexMatchPatternStateListener{

    /**
    * auto generate doc.
    */
    function saveState();
    function restoreState();
}
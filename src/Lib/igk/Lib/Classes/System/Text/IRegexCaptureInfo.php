<?php
// @author: C.A.D. BONDJE DOUE
// @file: IRegexCaptureInfo.php
// @date: 20241106 11:44:37
namespace IGK\System\Text;

/**
* auto generate doc.
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
* @property ?string $tokenID the token identification
*/
interface IRegexCaptureInfo{

    /**
    * Getis root.
    * @return bool
    */
    function getisRoot():bool;

    /**
    * Getis root captured.
    * @return bool
    */
    function getisRootCaptured():bool;
}
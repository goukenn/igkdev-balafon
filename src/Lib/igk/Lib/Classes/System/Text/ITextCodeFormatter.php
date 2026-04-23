<?php
// @author: C.A.D. BONDJE DOUE
// @file: ITextCodeFormatter.php
// @date: 20250710 08:35:18
namespace IGK\System\Text;

/**
* auto generate doc.
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
interface ITextCodeFormatter{
    /**
    * Formats.
    * @param string $source
    * @return string
    */
    function format(string $source):string;
}
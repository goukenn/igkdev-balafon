<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDocumentScriptLoader.php
// @date: 20241114 09:57:55
namespace IGK\System\IO\HtmlDocument;

use IGKHtmlDoc;

/**
* load script to manage document
* @package IGK\System\IO\HtmlDocument
* @author C.A.D. BONDJE DOUE
*/
interface IDocumentScriptLoader{
    function loadScripts(IGKHtmlDoc $doc);
}
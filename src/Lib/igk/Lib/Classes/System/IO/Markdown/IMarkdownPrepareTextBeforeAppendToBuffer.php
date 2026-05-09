<?php
// @author: C.A.D. BONDJE DOUE
// @file: IMarkdownPrepareTextBeforeAppendToBuffer.php
// @date: 20260429 16:49:45
namespace IGK\System\IO\Markdown;


/**
* 
* @package IGK\System\IO\Markdown
* @author C.A.D. BONDJE DOUE
*/
interface IMarkdownPrepareTextBeforeAppendToBuffer{
    /**
     * 
     * @param string $input 
     * @return string 
     */
    function prepareTextBeforeAppendToBuffer(string $input):string;

}
<?php
// @author: C.A.D. BONDJE DOUE
// @file: IMarkdownFilterHost.php
// @date: 20260130 18:40:12
namespace IGK\System\IO\Markdown;


/**
* 
* @package IGK\System\IO\Markdown
* @author C.A.D. BONDJE DOUE
*/
interface IMarkdownFilterHost{

    /**
    * auto generate doc.
    * @param mixed $i
    * @return ?int
    */
    function initMenuList($i);
    function getListTable();
    function getListTableNewIds():?int;

    /**
    * auto generate doc.
    * @param string $root
    * @return string
    */
    function popupBulletList(string $root);
    /**
     * 
     * @param string $text 
     * @return string 
     */
    function prepareFormat(string $text):string;
    /**
     * escape litteral
     * @param string $text 
     * @return string 
     */

    function escape(string $text):string;

    /**
    * auto generate doc.
    * @return int
    */
    function listTableRefCount():int;

    /**
    * auto generate doc.
    * @param int $level
    * @return ?string
    */
    function getTitleStyleId(int $level):?string; 
}
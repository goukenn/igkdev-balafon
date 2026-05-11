<?php
// @author: C.A.D. BONDJE DOUE
// @file: IMarkdownSubListTreatmentInfo.php
// @date: 20260426 11:11:25
namespace IGK\System\IO\Markdown;


/**
* 
* @package IGK\System\IO\Markdown
* @author C.A.D. BONDJE DOUE
* @property string $type - detected type 
* @property string $value - line value
* @property string $state - current state
* @property bool $canCreateSubContainer -  
* @property ?\stdClass $match
* @property int $depth
* @property ?HtmlNode $parent
* @property ?\stdClass & $subcontainer output subcontainer 
* @property int & $currentNode sub current
* @property ?callable(string, int):bool $canCreateSubContainerListener
* @property ?callable(string):void $handleNullParentListener
* @property ?callable(string):void $moveToQuoteDepthListener
*/
/**
* auto generate doc.
* @package IGK\System\IO\Markdown
*/
interface IMarkdownSubListTreatmentInfo{

}
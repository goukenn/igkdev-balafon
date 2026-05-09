<?php
// @author: C.A.D. BONDJE DOUE
// @file: IMarkdownConverterListener.php
// @date: 20260429 14:33:51
namespace IGK\System\IO\Markdown;

use IGK\System\Text\RegexMatcherCapture;

/**
* markdown converter listener 
* @package IGK\System\IO\Markdown
* @author C.A.D. BONDJE DOUE
* @property $appendOutputListener
* @property $lf line feed
*/
interface IMarkdownConverterListener extends IMarkdownPrepareTextBeforeAppendToBuffer{
    /**
     * title to render 
     * @param string $title 
     * @param int $level 
     * @param null|string $slug 
     * @return mixed 
     */
    function title(string $title, int $level, ?string $slug=null);
    function rtrimOutput(string $output):string;
    function filter(?string $token_id, string $value, bool $isRoot, \closure $callback, RegexMatcherCapture $capture, ?array $options = null);
    function endState():?string;
    function postTreatOutput(string $output) : string;
    function endLineFeedToBuffer():?string;
    function beforeBufferLine(RegexMatcherCapture $capture, MarkdownConverter $converter, bool $lineFeed);

    function didHandleOutput(bool & $isSingle, string & $output);
    /**
     * convert to default value
     * @param string $value 
     * @return mixed 
     */
    function default(string $value);
}
<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherEngineInfo.php
// @date: 20250714 23:52:27
namespace IGK\System\Text;


/**
 * callable token id
 * @package IGK\System\Text
 * @author C.A.D. BONDJE DOUE
 */
class RegexMatcherEngineInfo implements IRegexMatcherEngineInfo
{
    /**
     * initiator
     * @var string|'treat'
     */
    var $type;
    /**
     * name of the tokenID to match 
     * @var string|'__end__'
     */
    var $end_token_id;

    /**
    * auto generate doc.
    * @var callable
    */
    var $callable;
}

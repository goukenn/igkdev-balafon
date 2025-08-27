<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormatterPattern.php
// @date: 20250803 02:49:09
namespace IGK\System\Text\Formatters;

use IGK\System\Text\RegexMatcherPattern;

/**
 * pattern extension for formatting text code 
 * @package IGK\System\Text\Formatters
 * @author C.A.D. BONDJE DOUE
 */
class FormatterPattern extends RegexMatcherPattern
{


    /**
     * force matching pattern 
     * @var mixed
     */
    var $replacementMatch;

    /**
     * get/set splitline use replaced data
     * @var mixed
     */
    var $useReplaceData;
    /**
     * get/set the splitter definition. to split on this definition - 
     * @var ?bool
     */
    var $splitLine;

    /**
     * get/set the used class definition 
     * @var ?string
     */
    var $class;
    /**
     * 
     * @var mixed
     */
    var $lineFeed;
    /**
     * 
     * @var mixed
     */
    var $isBlock;

    /**
     * disable this formatting
     * @var ?bool
     */
    var $offScreen;

    /**
     * block that allow block level - concatenation - 
     */
    var $isContinueBlock;

    /**
     * 
     * @var ?string|string[]|closure(string $select)
     */
    var $replaceWith;
    /**
     * activate to preserve innerContent - to transformation 
     * @var ?bool|'rtrim'|'trim'|'ltrim'
     */
    var $preserveContent;


    /**
     * get/set activate or not some particular flag on transform      
     * @var array|array[string>bool]
     */
    var $flags;
    /**
     * until end of source
     * @var ?bool
     */
    var $eof;
    /**
     * match concern split on parent -  
     * @var ?bool
     */
    var $matchSplitOnParent;

    /**
     * match concern line feed
     * @var ?bool
     */
    var $matchLineFeed;

    /**
     * ignore detection on end of source
     * @var mixed
     */
    var $ignoreOnEOF; 
}

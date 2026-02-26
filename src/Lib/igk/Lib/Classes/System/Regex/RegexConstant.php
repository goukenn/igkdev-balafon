<?php
// @author: C.A.D. BONDJE DOUE
// @filename: RegexConstant.php
// @date: 20220322 15:34:54
// @desc: 
namespace IGK\System\Regex;
/**
 * regex constants
 * @package IGK\System\Regex
 */
class RegexConstant{

    /**
    * Constant: version check.
    * @var mixed
    */
    const VERSION_CHECK = '(?P<version>[0-9]+(\.[0-9]+){0,3})';

    /**
    * Constant: int regex.
    * @var mixed
    */
    const INT_REGEX = "/^[0-9]+$/i";

    /**
    * Constant: guid check.
    * @var mixed
    */
    const GUID_CHECK = "(?P<brack>\{)?(?P<guid>[0-9a-f]+(-[0-9a-f]+){4})(?(brack)\}|)";

    /**
    * Constant: guid regex.
    * @var mixed
    */
    const GUID_REGEX = "/".self::GUID_CHECK."/i";

    /**
    * Constant: template arg placeholder regex.
    * @var mixed
    */
    const TEMPLATE_ARG_PLACEHOLDER_REGEX = "/\{\{\s*\b(?P<name>[\w_]+)\b\s*\}\}/";
}
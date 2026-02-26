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
    * auto generate doc.
    * @var mixed
    */
    const VERSION_CHECK = '(?P<version>[0-9]+(\.[0-9]+){0,3})';

    /**
    * auto generate doc.
    * @var mixed
    */
    const INT_REGEX = "/^[0-9]+$/i";

    /**
    * auto generate doc.
    * @var mixed
    */
    const GUID_CHECK = "(?P<brack>\{)?(?P<guid>[0-9a-f]+(-[0-9a-f]+){4})(?(brack)\}|)";

    /**
    * auto generate doc.
    * @var mixed
    */
    const GUID_REGEX = "/".self::GUID_CHECK."/i";

    /**
    * auto generate doc.
    * @var mixed
    */
    const TEMPLATE_ARG_PLACEHOLDER_REGEX = "/\{\{\s*\b(?P<name>[\w_]+)\b\s*\}\}/";
}
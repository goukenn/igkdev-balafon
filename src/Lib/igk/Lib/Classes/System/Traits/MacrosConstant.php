<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MacrosConstant.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Traits;

/**
 * global macros constants
 * @package IGK\System\Traits
 */
abstract class MacrosConstant
{
    /**
    * Constant: closure separator.
    * @var mixed
    */
    const ClosureSeparator = "@";
    /**
    * Constant: static separator.
    * @var mixed
    */
    const StaticSeparator = "::";
    /**
    * Constant: register extension method.
    * @var mixed
    */
    const RegisterExtensionMethod = "registerExtension";
    /**
    * Constant: un register extension method.
    * @var mixed
    */
    const UnRegisterExtensionMethod = "unRegisterExtension";
    /**
    * Constant: register macro method.
    * @var mixed
    */
    const RegisterMacroMethod = 'registerMacro';
    /**
    * Constant: get macro method.
    * @var mixed
    */
    const getMacroMethod = 'getMacro';
    /**
    * Constant: get instance method.
    * @var mixed
    */
    const getInstanceMethod = 'getInstance';
    /**
    * Constant: get macro keys method.
    * @var mixed
    */
    const getMacroKeysMethod = 'getMacroKeys';
    /**
    * Constant: ref macros.
    * @var mixed
    */
    const REF_MACROS = '@ref-macros';
}
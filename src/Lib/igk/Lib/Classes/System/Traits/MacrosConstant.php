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
    const ClosureSeparator = "@";
    const StaticSeparator = ":";
    const RegisterExtensionMethod = "registerExtension";
    const UnRegisterExtensionMethod = "unRegisterExtension";
    const RegisterMacroMethod = 'registerMacro';
    const getMacroMethod = 'getMacro';
    const getInstanceMethod = 'getInstance';
    const getMacroKeysMethod = 'getMacroKeys';
}

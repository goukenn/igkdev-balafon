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
    * auto generate doc.
    * @var mixed
    */
    const ClosureSeparator = "@";

    /**
    * auto generate doc.
    * @var mixed
    */
    const StaticSeparator = "::";

    /**
    * auto generate doc.
    * @var mixed
    */
    const RegisterExtensionMethod = "registerExtension";

    /**
    * auto generate doc.
    * @var mixed
    */
    const UnRegisterExtensionMethod = "unRegisterExtension";

    /**
    * auto generate doc.
    * @var mixed
    */
    const RegisterMacroMethod = 'registerMacro';

    /**
    * auto generate doc.
    * @var mixed
    */
    const getMacroMethod = 'getMacro';

    /**
    * auto generate doc.
    * @var mixed
    */
    const getInstanceMethod = 'getInstance';

    /**
    * auto generate doc.
    * @var mixed
    */
    const getMacroKeysMethod = 'getMacroKeys';

    /**
    * auto generate doc.
    * @var mixed
    */
    const REF_MACROS = '@ref-macros';
    
}
<?php
// @author: C.A.D. BONDJE DOUE
// @file: EnumFormConstantData.php
// @date: 20260207 17:39:35
namespace IGK\System\Traits;

/**
* auto generate doc.
* @package IGK\System\Traits
* @author C.A.D. BONDJE DOUE
*/
trait EnumFormConstantData
{
    use EnumeratesConstants;
    /**
    * Form select data.
    */
    public static function FormSelectData()
    {
        $l = [];
        foreach (static::GetConstants() as $k => $v) {
            $l[] = ['i' => $v, 't' => 'enum.' . strtolower($v)];
        }
        return $l;
    }
}
<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReferenceModels.phtml
// @desc: macros for model ReferenceModels
// @date: 20251203 12:54:33
namespace IGK\Database\Macros;

use IGK\Models\ReferenceModels;

/**
 * 
 * @package IGK\Database\Macros
 * @author C.A.D. BONDJE DOUE
 */
abstract class ReferenceModelsMacros
{
    /**
     * ref condition 
     * @param int $uid 
     * @param string $modelname 
     * @return array{clModel: string} 
     */
    private static function _GetRefCondition(int $uid, string $modelname): array{
        return ["clModel" =>sprintf('%s:/%s', $uid, $modelname)];
    }
    /**
     * 
     * @param ReferenceModels $model 
     * @param int $uid 
     * @param string $modelname 
     * @return mixed|int 
     */
    public static function get_ref_nextnumber(ReferenceModels $model, int $uid, string $modelname)
    {
        $cond = self::_GetRefCondition($uid, $modelname);
        $r = $model::select_row($cond);
        if ($r) {
            return max($r->clNextValue, 1);
        } else {
            $cond["clNextValue"] = 1;
            $model::insertIfNotExists($cond);
        }
        return 1;
    }
    /**
     * 
     * @param ReferenceModels $model 
     * @param int $uid 
     * @param string $modelname 
     * @return null|ReferenceModels 
     */
    public static function update_ref_nextnumber(ReferenceModels $model,int $uid, string $modelname): ?ReferenceModels{
        $cond = self::_GetRefCondition($uid, $modelname);
        $r = $model::select_row($cond);
        if ($r){
            $r->clNextValue++;
            $r->save(true);
        }
        return $r;
    }
}

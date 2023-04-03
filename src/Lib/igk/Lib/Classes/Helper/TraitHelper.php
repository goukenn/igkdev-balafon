<?php
// @author: C.A.D. BONDJE DOUE
// @file: TraitHelper.php
// @date: 20230209 10:27:43
namespace IGK\Helper;


///<summary></summary>
/**
* 
* @package IGK\Helper
*/
abstract class TraitHelper{
    public static function SupportTrait($object_or_class, $trait):bool{
        if (!trait_exists($trait, false)){
            return false;
        }
        $trait_cl = [$object_or_class];
        while(count($trait_cl)>0){
            $q = array_shift($trait_cl);
            if ($g = class_uses($q)){
                if (in_array($trait, $g)){
                    return true;
                }
            }
            if (($s = igk_sys_reflect_class($q)->getParentClass()) && ($s = $s->getName())){
                array_push($trait_cl, $s);
            }
        }
        return false;
    }
}
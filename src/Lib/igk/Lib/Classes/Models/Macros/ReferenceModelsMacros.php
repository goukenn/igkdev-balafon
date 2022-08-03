<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ReferenceModelsMacros.php
// @date: 20220803 13:48:57
// @desc: 



namespace IGK\Models\Macros;

use IGK\System\Number;

class ReferenceModelsMacros {
    /**
	 * update object reference
	 */
	public static function updateRef($refobj){
		$refobj->clNextValue++;
		$u = $refobj->update; 
		$u();
		$u = $refobj->get_output;
		return $u();
	}
	/**
	 * get reference data
	 * @param mixed $ctrl 
	 * @param mixed|null $refmodel 
	 * @param int $base 
	 * @param int $ref 
	 * @return object 
	 * @throws Exception 
	 */
	public static function get_ref($model, $ctrl=null, $refmodel=null, $base=36, $ref=6){
		$obj=(object)array(); 
        $obj->update=function() use ($obj, & $model){		         
            if($obj->newValue){  
				$model::create((array)$obj);		 
            }
            else{ 
				$s = (array)$obj;
				unset($s["clId"]);
				$model::update($s, ["clId"=>$obj->clId]); 
            }
        };
		$raw = null;
		$v_tmodel = $refmodel == null ? (method_exists($ctrl, "getRefModel") ? $ctrl->getRefModel(): "MDL"): $refmodel;

		$obj->get_output =  function()use( $obj, $v_tmodel, $base, $ref){
			$c= $obj->clNextValue;			
			return $v_tmodel."".Number::ToBase($c, $base, $ref);
		};
        
        $r= $model::select_all(["clModel"=>$v_tmodel]);
        $raw= igk_getv($r, 0);
        $c = $raw? $raw->clNextValue: null;
        $c++;
        $out= $v_tmodel."".Number::ToBase($c, $base, $ref);
        igk_obj_append($obj, array(
            "value"=>$out,
            "newValue"=>count($r) == 0,
            "ctrl"=>$ctrl,
            "clModel"=>$v_tmodel,
            "clNextValue"=>$c,
            "clId"=>$raw ? $raw->clId: null
        ));
        return new ReferenceObj($obj);
	}
}
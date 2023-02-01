<?php
// @author: C.A.D. BONDJE DOUE
// @file: IOControllerExtensionTrait.php
// @date: 20230125 14:39:06
namespace IGK\Controllers\Traits;
 
use IGK\Controllers\BaseController;
use IGK\System\IO\Path;
use IGK\System\IO\ResIdentifierConstants;

///<summary></summary>
/**
* input management controller macros extension 
* @package IGK\System\Controllers\Traits
*/ 
trait IOControllerExtensionTrait{
    /**
     * get project asset directory
     * @param BaseController $ctrl 
     * @param string $path 
     * @param string $newPath 
     * @return string|void 
     */
    public static function resolvProductionAssetPath(BaseController $ctrl, string $path='/'):?string{
        $new_path = null;
        $path = $ctrl->asset($path, false);
        $g = Path::Combine(IGK_RES_FOLDER, ResIdentifierConstants::PROJECT)."/";
        if (strstr($path, $g)){
            $id = self::_GetResProjectIdentifier($path, $g, $pos, $a_pos);
            $ln =  strlen(IGK_RES_FOLDER); 
            $v_n = substr($path, $pos, $a_pos-$pos + $ln);
            $new_path =  substr($path,0, $pos).$id.substr($path, $a_pos+$ln);           
        }
        return $new_path;
    } 
    private static function _GetResProjectIdentifier($path, $g, & $pos =null, & $a_pos = null){
        $ln =  strlen(IGK_RES_FOLDER);
        $pos = strpos($path, $g) + strlen($g);
        $a_pos = strpos($path, IGK_RES_FOLDER, $pos);
        return sha1(substr($path, $pos, $a_pos-$pos + $ln));
    }
    /**
     * get project 
     * @param BaseController $ctrl 
     * @return string|null 
     */
    public static function getProjectAssetIdentifier(BaseController $ctrl){
        $path = $ctrl->asset('/', false);
        $g = Path::Combine(IGK_RES_FOLDER, ResIdentifierConstants::PROJECT)."/";
        if (strstr($path,  $g)){
            $ln =  strlen(IGK_RES_FOLDER);
            $pos = strpos($path, $g) + strlen($g);
            $a_pos = strpos($path, IGK_RES_FOLDER, $pos);
            $v_n = substr($path, $pos, $a_pos-$pos + $ln);
            $id = sha1($v_n);           
            return $id;
        }
        return null;
    }
}
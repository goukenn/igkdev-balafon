<?php
// @author: C.A.D. BONDJE DOUE
// @file: Requirement.php
// @date: 20231019 10:54:25
namespace IGK\System;

use ZipArchive;

///<summary></summary>
/**
* check system requirem 
* @package IGK\System
*/
class Requirement{
    private $m_requirements;

    /**
     * after check get requirement
     * @return mixed 
     */
    public function getRequirements(){
        return $this->m_requirements;
    }
    public function check() : bool{
        $is_webapp = igk_is_webapp();
        $requirement = []; 
        if (!function_exists('mb_convert_encoding')){
            $requirement[] = [
                'msg'=>'missing php mbstring extension',
                'level'=>'warn'
            ];
        }; 
        if (!function_exists('curl_exec')){
            $requirement[] = [
                'msg'=>'missing php curl extension',
                'level'=>'warn'
            ];
        }
        if (!class_exists(ZipArchive::class)){
            $requirement[] = [
                'msg'=>'missing php-zip extension',
                'level'=>'warn'
            ];
        }
        if (!empty($requirement)){
            $this->m_requirements = $requirement;
            return false;
        }
        return true; 
    }
}
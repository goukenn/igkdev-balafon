<?php
// @author: C.A.D. BONDJE DOUE
// @file: IAnnotation.php
// @date: 20230731 09:35:08
namespace IGK\System;


///<summary></summary>
/**
* 
* @package IGK\System
*/
interface IAnnotation{
    /**
     * set annotation params
     * @param array $params 
     * @return mixed 
     */
    public function setParams(array $params);
    /**
     * get annotation params
     * @return array 
     */
    public function getParams(): array;
}
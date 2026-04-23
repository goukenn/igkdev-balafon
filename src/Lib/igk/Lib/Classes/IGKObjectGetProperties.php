<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKObjectGetProperties.php
// @date: 20220803 13:48:54
// @desc: 
use IGK\Traits\GetPropertyCallbackTrait;
use IGK\Traits\SetPropertyCallbackTrait;

/**
* Abstract magic to get/set propertie
*/
abstract class IGKObjectGetProperties{
    use GetPropertyCallbackTrait;
    use SetPropertyCallbackTrait;  
}
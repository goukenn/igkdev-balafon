<?php
// @author: C.A.D. BONDJE DOUE
// @file: IUploadFileHandler.php
// @date: 20241124 23:42:25
namespace IGK\System\Html\Forms;
/**
* 
* @package IGK\System\Forms
* @author C.A.D. BONDJE DOUE
*/
interface IUploadFileHandler{

    /**
    * auto generate doc.
    * @param mixed $value
    * @param mixed $identifier
    */
    function upload($value, $identifier);
}
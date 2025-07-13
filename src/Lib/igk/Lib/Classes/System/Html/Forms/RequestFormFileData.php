<?php
// @author: C.A.D. BONDJE DOUE
// @file: RequestFormFileData.php
// @date: 20241123 11:42:23
namespace IGK\System\Html\Forms;
/**
* 
* @package IGK\System\Html\Forms
* @author C.A.D. BONDJE DOUE
*/
class RequestFormFileData{
    var $name;
    var $type;
    var $tmp_name;
    var $error;
    var $size;
    /**
     * 
     * @param string $dest 
     * @return void 
     */
    public function moveUploadTo(string $dest){
        return igk_io_move_uploaded_file($this->tmp_name, $dest);
    }
}
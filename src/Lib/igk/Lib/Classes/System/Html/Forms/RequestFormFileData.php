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
/**
* auto generate doc.
* @package IGK\System\Html\Forms
*/
class RequestFormFileData{
    /**
    * Name of name.
    * @var mixed
    */
    var $name;
    /**
    * Type of type.
    * @var mixed
    */
    var $type;
    /**
    * Name of tmp name.
    * @var mixed
    */
    var $tmp_name;
    /**
    * Property: error.
    * @var mixed
    */
    var $error;
    /**
    * Property: size.
    * @var mixed
    */
    var $size;
    /**
    * auto generate doc.
    * @param string $dest
    * @return void
    */
    public function moveUploadTo(string $dest){
        return igk_io_move_uploaded_file($this->tmp_name, $dest);
    }
}
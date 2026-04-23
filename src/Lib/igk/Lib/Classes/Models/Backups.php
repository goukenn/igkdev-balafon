<?php
// @author: C.A.D. BONDJE DOUE
// @file: Backups.php
// @date: 20260102 09:35:11
namespace IGK\Models;
use IGK\Models\ModelBase;

/**
* auto generate doc.
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property string|datetime $backup_update_at
* @method static ?self AddIfNotExists(string $backup_type
*/
class Backups extends ModelBase{
    /**
    * Constant: fd cl id.
    * @var mixed
    */
    const FD_CL_ID="clId";
    /**
    * Constant: fd backup type.
    * @var mixed
    */
    const FD_BACKUP_TYPE="backup_type";
    /**
    * Constant: fd backup class.
    * @var mixed
    */
    const FD_BACKUP_CLASS="backup_class";
    /**
    * Constant: fd backup path.
    * @var mixed
    */
    const FD_BACKUP_PATH="backup_path";
    /**
    * Constant: fd backup create at.
    * @var mixed
    */
    const FD_BACKUP_CREATE_AT="backup_create_at";
    /**
    * Constant: fd backup update at.
    * @var mixed
    */
    const FD_BACKUP_UPDATE_AT="backup_update_at";
	/**
	* table's name
	*/
	protected $table = "%prefix%backups";
}
<?php
// @author: C.A.D. BONDJE DOUE
// @file: Apps.php
// @date: 20260102 09:35:11
namespace IGK\Models;
use IGK\Models\ModelBase;
/**
* manage download time application.
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clName
* @property string $clDownloadTime
* @property string|datetime $clLast Last download time
* @method static string FN_CL_ID() - `clId` full column name 
* @method static string FN_CL_NAME() - `clName` full column name 
* @method static string FN_CL_DOWNLOAD_TIME() - `clDownloadTime` full column name 
* @method static string FN_CL_LAST() - `clLast` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clName, string $clDownloadTime, string|datetime $clLast) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clDownloadTime, string|datetime $clLast) add entry if not exists. check for unique column.
* */
class Apps extends ModelBase{
    /**
    * Constant: fd cl id.
    * @var mixed
    */
    const FD_CL_ID="clId";
    /**
    * Constant: fd cl name.
    * @var mixed
    */
    const FD_CL_NAME="clName";
    /**
    * Constant: fd cl download time.
    * @var mixed
    */
    const FD_CL_DOWNLOAD_TIME="clDownloadTime";
    /**
    * Constant: fd cl last.
    * @var mixed
    */
    const FD_CL_LAST="clLast";
	/**
	* table's name
	*/
	protected $table = "%prefix%apps";
	/**
	*override display key
	*/
	protected $display = "clName";
}
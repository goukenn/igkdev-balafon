<?php
// @author: C.A.D. BONDJE DOUE
// @file: WhoUses.php
// @date: 20260102 09:35:11
namespace IGK\Models;
use IGK\Models\ModelBase;
/**
* Track who use the framework
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clWebSite
* @property int $clState
* @property string|datetime $clDateTime
* @property string $clIP
* @method static string FN_CL_ID() - `clId` full column name 
* @method static string FN_CL_WEB_SITE() - `clWebSite` full column name 
* @method static string FN_CL_STATE() - `clState` full column name 
* @method static string FN_CL_DATE_TIME() - `clDateTime` full column name 
* @method static string FN_CL_IP() - `clIP` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clWebSite, int $clState, string|datetime $clDateTime, string $clIP) add entry helper
* @method static ?self AddIfNotExists(string $clWebSite, int $clState, string|datetime $clDateTime, string $clIP) add entry if not exists. check for unique column.
* */
class WhoUses extends ModelBase{
    /**
    * Constant: fd cl id.
    * @var mixed
    */
    const FD_CL_ID="clId";
    /**
    * Constant: fd cl web site.
    * @var mixed
    */
    const FD_CL_WEB_SITE="clWebSite";
    /**
    * Constant: fd cl state.
    * @var mixed
    */
    const FD_CL_STATE="clState";
    /**
    * Constant: fd cl date time.
    * @var mixed
    */
    const FD_CL_DATE_TIME="clDateTime";
    /**
    * Constant: fd cl ip.
    * @var mixed
    */
    const FD_CL_IP="clIP";
	/**
	* table's name
	*/
	protected $table = "%prefix%who_uses";
	/**
	*override display key
	*/
	protected $display = "clWebSite";
}
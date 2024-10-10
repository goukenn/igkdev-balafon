<?php
// @author: C.A.D. BONDJE DOUE
// @file: Mailinglists.php
// @date: 20240922 19:45:48
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store mailing lists.</summary>
/**
* store mailing lists.
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clml_email
* @property int $clml_state 0|1 (no-active, active)
* @property string $clml_source column describe.
* @property string $clml_locale ="en"
* @property string $clml_init
* @property string $clml_agent
* @property string|datetime $clml_create_at ="NOW()"
* @property string|datetime $clml_update_at ="NOW()"
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_CLML_EMAIL() - `clml_email` full column name 
* @method static string FD_CLML_STATE() - `clml_state` full column name 
* @method static string FD_CLML_SOURCE() - `clml_source` full column name 
* @method static string FD_CLML_LOCALE() - `clml_locale` full column name 
* @method static string FD_CLML_INIT() - `clml_init` full column name 
* @method static string FD_CLML_AGENT() - `clml_agent` full column name 
* @method static string FD_CLML_CREATE_AT() - `clml_create_at` full column name 
* @method static string FD_CLML_UPDATE_AT() - `clml_update_at` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clml_email, int $clml_state, string $clml_source, string $clml_init, string $clml_agent, string $clml_locale ="en", string|datetime $clml_create_at ="NOW()", string|datetime $clml_update_at ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $clml_email, int $clml_state, string $clml_source, string $clml_init, string $clml_agent, string $clml_locale ="en", string|datetime $clml_create_at ="NOW()", string|datetime $clml_update_at ="NOW()") add entry if not exists. check for unique column.
* */
class Mailinglists extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CLML_EMAIL="clml_email";
	const FD_CLML_STATE="clml_state";
	const FD_CLML_SOURCE="clml_source";
	const FD_CLML_LOCALE="clml_locale";
	const FD_CLML_INIT="clml_init";
	const FD_CLML_AGENT="clml_agent";
	const FD_CLML_CREATE_AT="clml_create_at";
	const FD_CLML_UPDATE_AT="clml_update_at";
	/**
	* table's name
	*/
	protected $table = "%prefix%mailinglists";
}
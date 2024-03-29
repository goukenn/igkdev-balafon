<?php
// @author: C.A.D. BONDJE DOUE
// @file: Mailinglists.php
// @date: 20240101 17:47:26
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
* @property string $clml_source
* @property string $clml_locale ="en"
* @property string $clml_init
* @property string $clml_agent
* @property string|datetime $clml_create_at ="NOW()"
* @property string|datetime $clml_update_at ="NOW()"
* @property string $clml_Source
* @method static ?self Add(string $clml_email, int $clml_state, string $clml_source, string $clml_init, string $clml_agent, string $clml_Source, string $clml_locale ="en", string|datetime $clml_create_at ="NOW()", string|datetime $clml_update_at ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $clml_email, int $clml_state, string $clml_source, string $clml_init, string $clml_agent, string $clml_Source, string $clml_locale ="en", string|datetime $clml_create_at ="NOW()", string|datetime $clml_update_at ="NOW()") add entry if not exists. check for unique column.
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
	const FD_CLML_SOURCE="clml_Source";
	/**
	* table's name
	*/
	protected $table = "%prefix%mailinglists";
}
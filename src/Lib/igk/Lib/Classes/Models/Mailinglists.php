<?php
// @author: C.A.D. BONDJE DOUE
// @file: Mailinglists.php
// @date: 20230131 13:55:04
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store mailing lists.</summary>
/**
* store mailing lists.
* @package IGK\Models
* @property int $clId
* @property string $clml_email
* @property int $clml_state
* @property string $clml_source
* @property string $clml_locale ="en"
* @property string $clml_init
* @property string $clml_agent
* @property string|datetime $clml_create_at ="NOW()"
* @property string|datetime $clml_update_at ="NOW()"
* @method static ?self Add(string $clml_email, int $clml_state, string $clml_source, string $clml_init, string $clml_agent, string $clml_locale ="en", string|datetime $clml_create_at ="NOW()", string|datetime $clml_update_at ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $clml_email, int $clml_state, string $clml_source, string $clml_init, string $clml_agent, string $clml_locale ="en", string|datetime $clml_create_at ="NOW()", string|datetime $clml_update_at ="NOW()") add entry if not exists. check for unique column.
* */
class Mailinglists extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CLMLEMAIL="clml_email";
	const FD_CLMLSTATE="clml_state";
	const FD_CLMLSOURCE="clml_source";
	const FD_CLMLLOCALE="clml_locale";
	const FD_CLMLINIT="clml_init";
	const FD_CLMLAGENT="clml_agent";
	const FD_CLMLCREATEAT="clml_create_at";
	const FD_CLMLUPDATEAT="clml_update_at";
	/**
	* table's name
	*/
	protected $table = "%prefix%mailinglists";
}
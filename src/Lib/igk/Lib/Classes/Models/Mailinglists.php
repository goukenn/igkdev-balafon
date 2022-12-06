<?php
// @author: C.A.D. BONDJE DOUE
// @file: Mailinglists.php
// @date: 20221203 14:34:18
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store mailing lists.</summary>
/**
* store mailing lists.
* @package IGK\Models
* @property int $clId
* @property string $clml_locale ="en"
* @property string $clml_init
* @property string $clml_agent
* @property string|datetime $clml_create_at ="NOW()"
* @property string|datetime $clml_update_at ="NOW()"
* @property string $clml_email
* @property int $clml_state
* @property string $clml_source
* @method static ?self Add(string $clml_init, string $clml_agent, string $clml_email, int $clml_state, string $clml_source, string $clml_locale ="en", string|datetime $clml_create_at ="NOW()", string|datetime $clml_update_at ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $clml_init, string $clml_agent, string $clml_email, int $clml_state, string $clml_source, string $clml_locale ="en", string|datetime $clml_create_at ="NOW()", string|datetime $clml_update_at ="NOW()") add entry if not exists. check for unique column.
* */
class Mailinglists extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%mailinglists";
}
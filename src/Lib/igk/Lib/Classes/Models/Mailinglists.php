<?php
// @author: C.A.D. BONDJE DOUE
// @file: Mailinglists.php
// @date: 20221121 16:32:44
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store mailing lists.</summary>
/**
* store mailing lists.
* @package IGK\Models
* @property int $clId
* @property string $clEmail
* @property int $clState
* @property string $clml_Source
* @property string $clml_locale ="en"
* @property string $clml_agent
* @property string|datetime $clml_create_at ="NOW()"
* @property string|datetime $clml_update_at ="NOW()"
* @method static ?self Add(string $clEmail, int $clState, string $clml_Source, string $clml_agent, string $clml_locale ="en", string|datetime $clml_create_at ="NOW()", string|datetime $clml_update_at ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $clEmail, int $clState, string $clml_Source, string $clml_agent, string $clml_locale ="en", string|datetime $clml_create_at ="NOW()", string|datetime $clml_update_at ="NOW()") add entry if not exists. check for unique column.
* */
class Mailinglists extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%mailinglists";
}
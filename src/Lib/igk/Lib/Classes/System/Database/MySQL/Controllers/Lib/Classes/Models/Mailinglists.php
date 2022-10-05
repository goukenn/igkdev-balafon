<?php
// @author: C.A.D. BONDJE DOUE
// @file: Mailinglists.php
// @date: 20220915 17:51:19
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary>store mailing lists.</summary>
/**
* store mailing lists.
* @package IGK\Models
* @property int $clId
* @property string $clEmail
* @property int $clState
* @property string $clSource
* @property string $clml_locale
* @property string $clml_agent
* @property string|datetime $clml_create_at
* @property string|datetime $clml_update_at
* @method static ?self Add(string $clEmail, int $clState, string $clSource, string $clml_agent, string|datetime $clml_create_at, string|datetime $clml_update_at, string $clml_locale) add entry helper
* @method static ?self AddIfNotExists(string $clEmail, int $clState, string $clSource, string $clml_agent, string|datetime $clml_create_at, string|datetime $clml_update_at, string $clml_locale) add entry if not exists. check for unique column.
* */
class Mailinglists extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%mailinglists";
}
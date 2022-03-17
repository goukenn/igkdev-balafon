<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IControllerConfigurationData.php
// @date: 20220308 15:48:24
// @desc: definition package


namespace IGK\Controllers;

/**
 * basic controller configuration data
 * @package IGK\Controllers
 * @property bool $no_auto_cache_view enable configuration data
 * @property string $clAppName get application name
 * @property string $clBasicUriPattern access route pattern for application controller
 * @property bool $clAppNotActive application is enabled
 * @property bool $clDataSchema get if controller support usage of data.schema.xml db file
 * @property string $clTitle controller display title
 * @property string $clDataTablePrefix controller database's table prefix
 * @method array to_array() return array definition
 * @method string to_json() return a json presentation 
 * @method mixed get() return a stored value 
 * @method void storeConfig() store config
 */
interface IControllerConfigurationData{
}
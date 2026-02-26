<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Constants.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK;
use IGK\System\EntryClassResolution;
use IGKEnvironment;
/**
* Represent Balafon's global constants
*/
final class Constants{

    /**
    * auto generate doc.
    * @var mixed
    */
    const NAMESPACE="http://schema.igkdev.com";

    /**
    * auto generate doc.
    * @var mixed
    */
    const BASE_VIEW_URI='@/';

    /**
    * auto generate doc.
    * @var mixed
    */
    const STR_PAGE_TITLE="{0} - [ {1} ]";

    /**
    * auto generate doc.
    * @var mixed
    */
    const MYSQL_DATETIME_FORMAT = "Y-m-d H:i:s";

    /**
    * auto generate doc.
    * @var mixed
    */
    const MYSQL_DATE_FORMAT = "Y-m-d";

    /**
    * auto generate doc.
    * @var mixed
    */
    const MYSQL_TIME_FORMAT = "H:i:s";

    /**
    * auto generate doc.
    * @var mixed
    */
    const DEFAULT_TIME_ZONE = 'Europe/Brussels';

    /**
    * auto generate doc.
    * @var mixed
    */
    const MODEL_TABLE_REGEX = "/%(?P<name>((sys)?prefix|year))%/i";

    /**
    * auto generate doc.
    * @var mixed
    */
    const TOKEN_EXPIRE = 60*24*3;

    /**
    * auto generate doc.
    * @var mixed
    */
    const FILE_PATH_HASH_ALGO ='crc32b';

    /**
    * auto generate doc.
    * @var mixed
    */
    const BASECLASS_COMMAND = '\System\Console\Commands';

    /**
    * auto generate doc.
    * @var mixed
    */
    const ENTRY_BASE_MODEL_CLASS = '\ModelBase';

    /**
    * auto generate doc.
    * @var mixed
    */
    const SITEMAP_VALIDATOR = "http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd";

    /**
    * auto generate doc.
    * @var mixed
    */
    const SITEMAP_INDEX_VALIDATOR = "http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd";

    /**
    * auto generate doc.
    * @var mixed
    */
    const SITEMAP_NS = "http://www.sitemaps.org/schemas/sitemap/0.9";

    /**
    * auto generate doc.
    * @var mixed
    */
    const MODULE_PACKAGE_LIST_URI = 'https://igkdev.com/balafon/get-module/';

    /**
    * auto generate doc.
    * @var mixed
    */
    const GUID_LENGTH = 38;

    /**
    * auto generate doc.
    * @var mixed
    */
    const PATH_VAR_DETECT_MODEL_REGEX = "/^%(?P<name>[^%]+)%/";

    /**
    * auto generate doc.
    * @var mixed
    */
    const RAW_VAR = 'raw';

    /**
    * auto generate doc.
    * @var mixed
    */
    const CTRL_VAR = 'ctrl';

    /**
    * auto generate doc.
    * @var mixed
    */
    const LOG_TAG = 'BLF';
    /**
     * project configuration file
     */
    const PROJECT_CONF_FILE = 'balafon.config.json';

    /**
    * auto generate doc.
    * @var mixed
    */
    const MODULE_CONF_FILE = 'balafon.module.json';
    //+ | for project entry namespace definition

    /**
    * auto generate doc.
    * @var mixed
    */
    const NS_ACTION_ENTRY = 'Actions';

    /**
    * auto generate doc.
    * @var mixed
    */
    const NS_MODEL_ENTRY = 'Models';

    /**
    * auto generate doc.
    * @var mixed
    */
    const NS_DATABASE_ENTRY = 'Database';
    // + to avoid inteliphense warning

    /**
    * auto generate doc.
    * @var mixed
    */
    const NS_MACROS_CLASS = EntryClassResolution::DbMacros;

    /**
    * auto generate doc.
    * @var mixed
    */
    const DB_INIT_MANAGER = \IGK\Database\DbInitManager::class;
    // + | default view page limit

    /**
    * auto generate doc.
    * @var mixed
    */
    const DB_VIEW_PAGE_LIMIT = 25;

    /**
    * auto generate doc.
    * @var mixed
    */
    const DEFAULT_THEME_STYLE = 'default.pcss';

    /**
    * auto generate doc.
    * @var mixed
    */
    const ANNOTATION_SUFFIX = 'Annotation';

    /**
    * auto generate doc.
    * @var mixed
    */
    const SESS_LIVING_TIME = 172800;

    /**
    * auto generate doc.
    * @var mixed
    */
    const DEFAUTL_PAGE_CONTROLLER_CLASS='IGKDefaultPageController';
    // environment

    /**
    * auto generate doc.
    * @var mixed
    */
    const COMPONENT_PACKAGE_KEY = IGKEnvironment::COMPONENT_PACKAGE_KEY;

    /**
    * auto generate doc.
    * @var mixed
    */
    const SYS_DEFAULT_HTML_PACKAGE = 'igk';

    /**
    * auto generate doc.
    * @var mixed
    */
    const BCSS_EXTENSION = '.bcss';

    /**
    * auto generate doc.
    * @var mixed
    */
    const PCSS_EXTENSION = '.pcss';

    /**
    * auto generate doc.
    * @var mixed
    */
    const DB_MODEL_FIELD_PREFIX = 'FD_';

    /**
    * auto generate doc.
    * @var mixed
    */
    const DB_MODEL_FULLNAME_FIELD_PREFIX = 'FN_';

    /**
    * auto generate doc.
    * @var mixed
    */
    const INIT_COMMAND = '--init';
    /**
     * get the core version 
     * @return string 
     */

    public static function CorePHPVersion(){
        list($major, $minor)= explode(".", PHP_VERSION);
        return $major.".".$minor;
    }
    /**
     * 
     * @return array 
     */

    public static function EnvironmentConstants(): array{
        return [
            'IGK_ENV_NO_AUTOCACHEVIEW' // to disable global constant key 
        ];
    } 
}
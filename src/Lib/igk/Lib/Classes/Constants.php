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
    const NAMESPACE="http://schema.igkdev.com";
    const BASE_VIEW_URI='@/';
    const STR_PAGE_TITLE="{0} - [ {1} ]";
    const MYSQL_DATETIME_FORMAT = "Y-m-d H:i:s";
    const MYSQL_DATE_FORMAT = "Y-m-d";
    const MYSQL_TIME_FORMAT = "H:i:s";
    const DEFAULT_TIME_ZONE = 'Europe/Brussels';
    const MODEL_TABLE_REGEX = "/%(?P<name>((sys)?prefix|year))%/i"; 
    const TOKEN_EXPIRE = 60*24*3;
    const FILE_PATH_HASH_ALGO ='crc32b';
    
    const BASECLASS_COMMAND = '\System\Console\Commands';  
    const ENTRY_BASE_MODEL_CLASS = '\ModelBase';
   
 
    const SITEMAP_VALIDATOR = "http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd";
    const SITEMAP_INDEX_VALIDATOR = "http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd";
    const SITEMAP_NS = "http://www.sitemaps.org/schemas/sitemap/0.9";    
    const MODULE_PACKAGE_LIST_URI = 'https://igkdev.com/balafon/get-module/';

    const GUID_LENGTH = 38;

    const PATH_VAR_DETECT_MODEL_REGEX = "/^%(?P<name>[^%]+)%/";

    const RAW_VAR = 'raw';
    const CTRL_VAR = 'ctrl';
    const LOG_TAG = 'BLF';

    /**
     * project configuration file
     */
    const PROJECT_CONF_FILE = 'balafon.config.json';
    const MODULE_CONF_FILE = 'balafon.module.json';

    //+ | for project entry namespace definition

    const NS_ACTION_ENTRY = 'Actions';
    const NS_MODEL_ENTRY = 'Models'; 
    const NS_DATABASE_ENTRY = 'Database';
    // + to avoid inteliphense warning
    const NS_MACROS_CLASS = EntryClassResolution::DbMacros; 
    const DB_INIT_MANAGER = \IGK\Database\DbInitManager::class;

    // + | default view page limit 
    const DB_VIEW_PAGE_LIMIT = 25;
    const DEFAULT_THEME_STYLE = 'default.pcss';
    const ANNOTATION_SUFFIX = 'Annotation';

    const SESS_LIVING_TIME = 172800;


    const DEFAUTL_PAGE_CONTROLLER_CLASS='IGKDefaultPageController';

    // environment
    const COMPONENT_PACKAGE_KEY = IGKEnvironment::COMPONENT_PACKAGE_KEY; 
    
    /**
     * get the core version 
     * @return string 
     */
    public static function CorePHPVersion(){
        list($major, $minor)= explode(".", PHP_VERSION);
        return $major.".".$minor;
    }
    public static function EnvironmentConstants(){
        return [
            'IGK_ENV_NO_AUTOCACHEVIEW' // to disable global constant key 
        ];
    }
}
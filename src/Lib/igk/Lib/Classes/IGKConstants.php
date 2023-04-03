<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKConstants.php
// @date: 20220803 13:48:54
// @desc: 


///<summary>Represente class: IGKConstants</summary>
/**
* Represente IGKConstants class
*/
final class IGKConstants{
    const NAMESPACE="http://schema.igkdev.com";
    const STR_PAGE_TITLE="{0} - [ {1} ]";
    const MYSQL_DATETIME_FORMAT = "Y-m-d H:i:s";
    const MYSQL_DATE_FORMAT = "Y-m-d";
    const MYSQL_TIME_FORMAT = "H:i:s";
    const DEFAULT_TIME_ZONE = 'Europe/Brussels';
    const MODEL_TABLE_REGEX = "/%(?P<name>((sys)?prefix|year))%/i"; 
    
    const BASECLASS_COMMAND = \System\Console\Commands::class;

 
    const SITEMAP_VALIDATOR = "http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd";
    const SITEMAP_INDEX_VALIDATOR = "http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd";
    const SITEMAP_NS = "http://www.sitemaps.org/schemas/sitemap/0.9";   

    const GUID_LENGTH = 38;

    const PATH_VAR_DETECT_MODEL_REGEX = "/^%(?P<name>[^%]+)%/";

    /**
     * get the core version 
     * @return string 
     */
    public static function CorePHPVersion(){
        list($major, $minor)= explode(".", PHP_VERSION);
        return $major.".".$minor;
    }
}
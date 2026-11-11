<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ISysConfigurationData.php
// @date: 20220813 14:17:07
// @desc: configuration data
namespace IGK\System\Configuration;

/**
* auto generate doc.
* @property bool $website_title
* @property bool $db_name
* @property bool $db_server
* @property bool $db_pwd
*/
interface ISysConfigurationData{
    /**
     * retrieve loaded file 
     * @return array 
     */
    public function loadedFiles() : array;
}
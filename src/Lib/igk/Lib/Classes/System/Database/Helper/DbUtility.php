<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbUtility.php
// @date: 20230118 11:28:43
namespace IGK\System\Database\Helper;

use IGK\Controllers\BaseController;
use IGK\Database\DbSchemas;
use IGK\System\Html\Dom\HtmlNode;
use IGKException;
use IGKSysUtil;

///<summary></summary>
/**
* 
* @package IGK\System\Database\Helper
*/
abstract class DbUtility{

    /**
     * 
     * @param BaseController $ctrl 
     * @param mixed $tables 
     * @return mixed 
     * @throws IGKException 
     */
    public static function ExportToXMLSchemaData(BaseController $ctrl, $tables){
        $xml = HtmlNode::CreateWebNode('dbdataschema');    
        $bprefix = igk_configs()->db_prefix;
        $prefix = IGKSysUtil::DBReverseTableName($bprefix, $ctrl);
        foreach($tables as $t=>$v){
            $rep = $xml->addNode(DbSchemas::DATA_DEFINITION)->setAttributes(array("TableName" => $v->defTableName));
            foreach($v->columnInfo as $info){
                $tab = $info->to_array();
                if ($lnk = $info->clLinkType){
                    if ($p = igk_getv($tables, $lnk)){                    
                        $tab['clLinkType'] = $p->defTableName;
                    } else {
                        if ($bprefix && (strpos($lnk, $bprefix)==0)){
                            $tab['clLinkType'] = $prefix.substr($lnk, strlen($bprefix));
                        }
                    }
                } 
                $rep->add(DbSchemas::COLUMN_TAG)->setAttributes(array_filter($tab)); 
            }
            // if ($defentries)
            //     $appc->datadb("get_table_definition", $rep, $v, $apt, null, $entries);
        }
        return $xml;
    }
    /**
     * 
     * @param BaseController $ctrl 
     * @param mixed $options 
     * @return void 
     * @throws IGKException 
     */
    public static function UpdateDbSchema(BaseController $ctrl, $options=null){
        $file = $ctrl::getDataSchemaFile();
        $schema = igk_db_load_data_schemas($file, $ctrl, true);
        $tables = igk_getv($schema, "tables"); 
        $n = self::ExportToXMLSchemaData($ctrl, $tables);
        if ($version = $schema->version){
            $db = \IGK\System\Version::Parse($version);
            $db->release++;
            $n['version']= $db.'';
        }
        $n['author'] = igk_getv($options, 'author') ?? IGK_AUTHOR;  
        $src = igk_ob_get_func(function($n){
            echo $n->render();
        }, $n);
        if (empty($ofile = igk_getv($options, 'outputfile'))){
            $ofile = $file;
        }
        return igk_io_w2file($ofile, $src); 
    }
    /**
     * 
     */
    public static function BackupDataSchema(BaseController $ctrl, $defentries){
        
        $tb = igk_db_get_ctrl_tables($ctrl);
        $schema = igk_html_node_dbdataschema();
        $apt = $ctrl->getDataAdapter(); 
        $appc = igk_getctrl(IGK_API_CTRL);
        if ($apt->connect()) {
            $entries = $schema->addNode(DbSchemas::ENTRIES_TAG);
            foreach ($tb as $v) {
                $rep = $schema->addNode(DbSchemas::DATA_DEFINITION)->setAttributes(array("TableName" => $v));
                if ($defentries)
                    $appc->datadb("get_table_definition", $rep, $v, $apt, null, $entries);
            }
            if (!$entries->HasChilds) {
                igk_html_rm($entries);
            }
            $apt->close();
        }
        return $schema; 
    }
}
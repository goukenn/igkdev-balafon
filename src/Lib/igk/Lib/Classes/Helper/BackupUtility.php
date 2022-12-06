<?php
// @author: C.A.D. BONDJE DOUE
// @file: BackupUtility.php
// @date: 20221122 08:46:09
namespace IGK\Helper;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Database\DbColumnInfo;
use IGK\Projects\Database\VersionClass;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\IO\Path;
use IGKCSVDataAdapter;
use IGKException;
use ReflectionException;
use Symfony\Component\Translation\Loader\CsvFileLoader;

///<summary></summary>
/**
* 
* @package IGK\Helper
*/
class BackupUtility{
    /**
     * use to backup the project 
     * @param BaseController $ctrl 
     * @param null|string $comment 
     * @param bool $overwrite 
     * @return void 
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function BackupProject(BaseController $ctrl, ?string $comment=null, bool $overwrite=true){
        $p = igk_get_class_constants($cl = get_class($ctrl));
        $name = igk_ns_name(basename(igk_uri($cl)));      
        $file = Path::getInstance()->getBackupDir().'/Projects/'.$name.'/'.$name;
        if ($gv = igk_getv($p, 'VERSION')){
            $file.='-'.$gv.'.zip';
        } else {
            $file .= date('Ymd').'.zip';
        }
        // igk_sys_zip_project($ctrl, $file);
        if ($gv &&  ($dir = dirname($file))){  
            $file = $dir."/version.db";
            $tbname = 'versions';
            $litedb = igk_get_data_adapter("SQLite3");
            if ($litedb->connect($dir."/version.db")){
                if (!$litedb->tableExists($tbname)){
                    $litedb->createTable($tbname, DbColumnInfo::CreateDefArrayFromClass(VersionClass::class));
                } 
                
                $vs = new VersionClass;
                $vs->version = $gv;
                if ($rvs = $litedb->select_row($tbname, ['version'=>$gv])){
                    $rvs = (object)$rvs;
                    $newComment = [$comment];
                    if (!empty($rvs->comment)){
                        if ($d = json_decode($rvs->comment, true )){
                            $d = (array)$d;
                        }
                            $newComment = array_merge($newComment, $d??[]);
                        $newComment[] = $rvs->comment;

                    }
                    $newComment = json_encode(array_filter($newComment));
                    $rvs->comment = ""; //empty($newComment) ? null : $newComment;
                    $vs->updateAt = date('Y-m-d H:i:s');
                    $litedb->update($tbname, $rvs, ['id'=>$rvs->id]);
               
                }else{
                $vs->name = $name;
                $vs->author = igk_environment()->author; 
                $vs->comment = $comment;
                $vs->createAt = date('Y-m-d H:i:s');
                $vs->updateAt = date('Y-m-d H:i:s');
                $litedb->insert('versions', $vs);
                }
                $litedb->close();
            }
            return true;
        }
        return false;
    }
}
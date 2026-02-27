<?php
// @author: C.A.D. BONDJE DOUE
// @file: RequestFileUtility.php
// @date: 20241123 13:34:28
namespace IGK\System\Html\Forms\Helper;
use Closure;
use IGK\Helper\IO;
use IGK\System\Forms\IUploadFileHandler;
use IGK\System\Html\Forms\RequestFormFileData;
use IGK\System\Http\HttpUtility;
use IGK\System\IO\Path;
/**
 * 
 * @package IGK\System\Html\Forms\helpers
 * @author C.A.D. BONDJE DOUE
 */

/**
* auto generate doc.
* @package IGK\System\Html\Forms\Helper
*/
class RequestFileUtility
{
    /**
     * destination
     * @param array<RequestFormFileData>|RequestFormFileData $data
     * @param string|IUploadFileHandler $destination destination folder
     * @param null|callable((RequestFormFileData):string) $identifier
     * @return void 
     */
    public static function MoveUploadTo($data, $destination, $identifier=null)
    {
        if (!is_array($data)) {
            !($data instanceof RequestFormFileData) && igk_die('invalid data type');
            $data = [$data];
        } 
        $identifier = $identifier ?? self::GuidAndExtensionCallback();
        $fc_upload = null;
        if (is_string($destination)){
            $fc_upload = static::_UploadStringFile($destination);
        } 
        else if ($destination instanceof IUploadFileHandler){
            $fc_upload =[$destination, 'upload'];
        }
        ($fc_upload === null ) && igk_die('missing upload file identifier');
        foreach ($data as $value) {
            call_user_func_array($fc_upload,[$value, $identifier]);
        }
    }
    /**
     * 
     * @param string $destination 
     * @return Closure|null 
     */
    private static function _UploadStringFile(string $destination){
        return \Closure::fromCallable(function($value, $identifier){
            extract((array)$this);
            $k = $identifier($value);
            $df = Path::Combine( $destination, $k);
            IO::CreateDir(dirname($df));  
            $value->moveUploadTo(Path::Combine( $destination, $k));
        })->bindTo((object)get_defined_vars());
    }

    /**
    * auto generate doc.
    */
    public static function GuidAndExtensionCallback(){
        return function($p){
            $ext = HttpUtility::GetExtensionFromContentType($p->type, '');
            if ($ext)
                $ext = '.'.$ext;
            return igk_create_guid_value().$ext;
        };
    }
}
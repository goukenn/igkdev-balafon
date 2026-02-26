<?php
// @author: C.A.D. BONDJE DOUE
// @filename: FileBuilderHelper.php
// @date: 20220828 14:58:07
// @desc: 
namespace IGK\Helper;
use Closure;
use IGK\System\Console\Logger;

/**
* File builder helper.
* @package IGK\Helper
*/
abstract class FileBuilderHelper
{
    /**
     * Builds files from a data map, optionally forcing regeneration and binding a context.
     *
     * @param array          $data  Map of file paths to closures or empty-content markers
     * @param bool           $force Force regeneration even when the file already exists
     * @param object|null    $bind  Optional object to bind closure context to
     * @return void
     */
    public static function Build($data, $force = false, ?object $bind = null)
    {
        foreach ($data as $n => $c) {
            if ($force || !igk_io_file_exists($n)) {
                if ($c instanceof Closure) {
                    if ($bind) {
                        $c = Closure::fromCallable($c)->bindTo($bind);
                    }
                    $c($n);
                }else{// just touch the file
                    igk_io_w2file($n, '');
                }
                Logger::info("generate : " . $n);
            }
        }
    }
}
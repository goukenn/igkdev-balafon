<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectConfiguration.php
// @date: 20230414 14:16:49
namespace IGK\System\Configuration;
use Exception;
use IGK\Helper\Activator;
use IGK\System\Console\Logger;
use IGK\System\IO\Path;
use IGKException;
/**
* represent project configuration settings
* @package IGK\System\Configuration
*/
class ProjectConfiguration extends EntityConfigurationSchema{
    /**
     * defined name
     * @var ?string
     */
    var $name;
    /**
     * list of required modules
     * @var ?array 
     */
    var $required;
    /**
     * project version
     * @var string
     */
    var $version = "1.0";
    /**
     * project author
     * @var string
     */
    var $author = IGK_AUTHOR; 
    /**
     * project description
     * @var descipriont
     */
    var $description;
    /**
     * exposed directories
     * @var ?array<string>
     */
    var $exposedDir;
    /**
     * entry namespace
     * @var ?string
     */
    var $entryNamespace;
    /**
     * keys word to used 
     * @var ?string[]|string
     */
    var $keywords;
    /**
     * configuration file workbench information 
     * @var ?ConfigurationWorkbenchInfo
     */ 
    var $workbench;
    /**
    * auto generate doc.
    * @var mixed
    */
    var $default_user_profile;
    /**
     * contribution of this project 
     * @var ?array
     */
    var $contributions;
    /**
     * exposed hook 
     * @var ?array 
     */
    var $events;
    /**
    * auto generate doc.
    * @var ?string
    */
    private static $sm_config;
    /**
    * auto generate doc.
    * @param string $file
    * @return void
    */
    public static function LoadConfig(string $file){
        $rf = realpath($file);
        if (empty($rf) ){
            if (igk_environment()->isDev() && igk_is_cmd()){
                Logger::danger('missing - file '.$file);
                igk_die('missing - file '.$file);
            }
            if (igk_is_cmd()){
                igk_io_w2file($file, '{}');
                Logger::info('create: '.$file);
            }else{
                return null;
            }
        }
        $c = hash_file('sha256', $rf );
        if (is_null(self::$sm_config)){
            self::$sm_config = [];
        }
        if (isset(self::$sm_config[$c])){
            return self::$sm_config[$c];
        }
        if ($g = json_decode(file_get_contents($file))) {
            $dec = dirname($file);
            $inf = Activator::CreateNewInstance(static::class, $g);
            if ($inf instanceof ProjectConfiguration) {
                if (is_array($inf->exposedDir)) {
                    $dirs = array_filter(array_map(function ($a) use ($dec) {
                        if ($a &&  (is_dir($a) || is_dir($a = Path::Combine($dec, $a)))) {
                            return $a;
                        }
                    }, $inf->exposedDir));
                }
            }
            self::$sm_config[$c] = $inf;
            return $inf;
        }
    }
    /**
     * clear memory cached configs
     * @return void 
     */
    public static function ClearConfigs(){
        self::$sm_config = [];
    }
}
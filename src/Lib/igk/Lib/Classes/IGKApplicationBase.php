<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKApplicationBase.php
// @date: 20220803 13:48:54
// @desc: 
use IGK\ApplicationLoader;
use IGK\System\IO\DotEnvConfiguration;
use IGK\System\IO\FileHandler;
use IGK\System\IO\Markdown\MarkdownFileHandler;
use IGK\System\IO\TextFileHandler;

/**
 * 
 * @package 
 */
abstract class IGKApplicationBase{
    /**
     * store library
     * @var array
     */
    private $lib = [];

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_library;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_appBuilder;
    /**
     * disable environment initialisation
     * @var ?bool
     */
    protected $no_init_environment;
    /**
     * retrieve entry file
     * @var mixed
     */
    protected $_entry_file;

    /**
    * .destructor
    * @param mixed $n
    */
    public function __get($n){
        if (method_exists($this, $fc="get".ucfirst($n))){
            return $this->$fc();
        }
    }
    /**
     * init not environment
     * @return mixed 
     */

    public function getNoEnvironment(){
        return $this->no_init_environment;
    }
    /**
     * check and get application option
     * @param mixed $name 
     * @return mixed
     */

    public function options($name, $default=null){
        return false;
    }
    /**
     * get library list
     * @return ?IGKObjectStorage 
     */

    public function getLibrary(){
        return $this->m_library;
    }
    /**
     * enable application library by name
     * @param mixed $libname 
     * @return mixed 
     */

    protected function library($libname)
    {
        if ($this->m_library == null){
            $tab = [];
            $this->m_library = new IGKObjStorage($tab);// new stdClass();
        }
        if (!$this->lib($libname)){
            $cl = 'IGK\\System\\Library\\' . $libname;
            if (ApplicationLoader::LoadClass($cl)){
                $c = new $cl();
                if ($c->init($this)){
                    $this->lib[$libname] = $c;
                    $this->m_library->{$libname} = $c;
                }
            }
        } else {
            $c = $this->lib[$libname];
        }
        return $c;
    }

    /**
    * auto generate doc.
    */
    public function getBuilder(){
        if ($this->m_appBuilder == null ){
            ($this->m_appBuilder = $this->createAppBuilder()) || igk_die("builder not create");
        }
        return $this->m_appBuilder;
    }
    /**
     * 
     * @return IGK\System\AppBuilder 
     */

    protected function createAppBuilder(){
        return new \IGK\System\AppBuilder();
    }
    /**
     * check if library is loaded
     * @param mixed $libname 
     * @return bool 
     */

    public function lib($libname):bool{
        return isset($this->lib[$libname]);
    }
    /**
     * initialize application environment
     * @return mixed 
     */

    abstract function bootstrap();
    /**
     * run application
     * @param string $entryfile 
     * @return mixed 
     */

    abstract function run(string $entryfile, $render=1);

    /**
    * Used by var_dump() to customize debug output.
    */
    public function __debugInfo()
    {
        return [];
    }

    /**
    * auto generate doc.
    */
    public function getEntryFile(){
        return $this->_entry_file;
    }
    /**
     * init core system component
     * @return void 
     */

    protected function initCoreSystemComponent(){
        \IGK\System\Configuration\SysConfigExpressionFactory::Register('dotenv', DotEnvConfiguration::class);

        FileHandler::Register('.md|'.FileHandler::FILE_CONTEXT_VIEW, new MarkdownFileHandler);
        FileHandler::Register('.txt|'.FileHandler::FILE_CONTEXT_VIEW, new TextFileHandler);

    }
}
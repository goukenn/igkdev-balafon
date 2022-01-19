<?php 

///<summary>represent application base type</summary>
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

    private $m_library;

    protected $no_init_environment;

    public function __get($n){
        if (method_exists($this, $fc="get".ucfirst($n))){
            return $this->$fc();
        }
    }
    public function getNoEnviroment(){
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

    public function getLibrary(){
        return $this->m_library;
    }
    /**
     * enable application libraries
     * @param mixed $libname 
     * @return void 
     */
    protected function library($libname)
    {
        if ($this->m_library == null){
            $this->m_library = new stdClass();
        }
        if (!$this->lib($libname)){
            $cl = 'IGK\\System\\Library\\' . $libname;
            if (IGKApplicationLoader::LoadClass($cl)){
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
     * check the library loading
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


    public function __debugInfo()
    {
        return [];
    }
}
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

    public function __get($n){
        if (method_exists($this, $fc="get".ucfirst($n))){
            return $this->$fc();
        }
    }

    public function getLibrary(){
        return $this->m_library;
    }
    /**
     * enable application libraries
     * @param mixed $libname 
     * @return void 
     */
    public function libary($libname)
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
    public function lib($libname):bool{
        return isset($this->lib[$libname]);
    }
    /**
     * 
     * @param string $type 
     * @return IGKApplicationBase application 
     */
    public static function Boot(string $type="web"){
        $app = IGKApplicationLoader::Boot($type);
        return $app;
    }
    /**
     * run application
     * @param string $entryfile 
     * @return mixed 
     */
    abstract function run(string $entryfile, $render=1);
}
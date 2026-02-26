<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IniFile.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\IO\File;
/**
 * represent ini file
 * @package 
 */
class IniFile{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_configs = [];

    /**
    * .destructor
    * @param mixed $n
    */
    public function __get($n){
        return igk_getv($this->m_configs, $n);
    }

    /**
    * destructor
    * @param mixed $n
    * @param mixed $v
    */
    public function __set($n, $v){
        if ($v === null){
            unset($this->m_configs[$n]);
            return;
        }
        $this->m_configs[$n] = $v;
    }

    /**
    * auto generate doc.
    */
    public function to_array(){
        return $this->m_configs; 
    }

    /**
    * auto generate doc.
    * @param mixed $d
    */
    public function comment($d){
        if (isset($this->m_configs[$d])){
            $this->m_configs["#".$d] = $this->m_configs[$d];
            unset($this->m_configs[$d]);
        }
    }

    /**
    * auto generate doc.
    * @param mixed $d
    */
    public function activate($d){
        if (isset($this->m_configs["#".$d])){
            $this->m_configs[$d] = $this->m_configs["#".$d];
            unset($this->m_configs["#".$d]);
            return true;
        }
    }
    /**
     * 
    * @param string $file 
    * @return null|self 
    */

    public static function LoadConfig(string $file){
        $conf = [];
        array_map(
            function($d)use(& $conf){
                $d = trim($d);
                // if (strpos($d, "#")===0)
                //     return null;
                if (strpos($d, "=") === false)
                    return null;
                $key = substr($d,0, $ln = strpos($d, "="));
                $v = trim(substr($d, $ln+1));
                $conf[$key] = $v;
                return [$key=>$v];
            },
            array_filter(explode("\n", file_get_contents($file)))
        );
        $c = new self();
        $c->m_configs = $conf;
        return $c;
    }
    //store to file

    /**
    * auto generate doc.
    * @param string $file
    */
    public function store(string $file){
        $m = "";
        foreach($this->m_configs as $k=>$v){
            $m .= implode("=", [$k,$v])."\n";
        }
        return igk_io_w2file($file, $m);
    }
}
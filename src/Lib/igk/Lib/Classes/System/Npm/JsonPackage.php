<?php
// @author: C.A.D. BONDJE DOUE
// @file: JsonPackage.php
// @date: 20230330 12:23:20
namespace IGK\System\Npm;

use IGK\Helper\Activator;
use IGK\Helper\JSon;
use IGK\Helper\JSonEncodeOption;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Npm\Traits\JsonPackagePropertyTrait;
use ReflectionException;

///<summary></summary>
/**
 * 
 * @package IGK\System\Npm
 */
class JsonPackage
{
    use JsonPackagePropertyTrait;
    /**
     * 
     * @param string $file 
     * @return static|false 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function Load(string $file)
    {
        $data = json_decode(file_get_contents($file)) ?? igk_die("no data in : $file");
        if ($c = JsonPackageValidator::ValidateData($data, null, $errors)) {
            return Activator::CreateNewInstance(static::class, $c);
        }
        igk_environment()->last_error = $errors;
        return false;
    }

    /**
     * mergin configuration file 
     */
    public function mergeWith(string $file)
    {
        if (!($package = self::Load($file))) {
            return false;
        }
        $fields = ['dependencies', 'devDependencies'];
        while (count($fields) > 0) {
            $f = array_shift($fields);
            if (!property_exists($this, $f)) {
                continue;
            }

            $g = (array)$this->$f;
            $t = (array)$package->$f;
            $kg = array_keys($g);
            $r = [];
            while (count($kg) > 0) {
                $k = array_shift($kg);
                if (isset($t[$k])) {
                    if (version_compare($g[$k], $t[$k]) < 0) {
                        $this->$f->$k = $t[$k];
                    }
                    unset($t[$k]);
                }
            }
            if ($t) {
                if (is_null($this->$f)) {
                    $this->$f = igk_createobj($t);
                } else {
                    foreach ($t as $m => $v) {
                        $this->$f->$m = $v;
                    }
                }
            }
            //$this->$f = (object)$r;
        }
    }
    /**
     * save the current definition
     * @param string $file 
     * @return bool 
     * @throws IGKException 
     */
    public function save(string $file){
        $e = JSonEncodeOption::IgnoreEmpty();
        $e->ignore_null = true; 
        return igk_io_w2file($file, JSon::Encode($this, $e, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

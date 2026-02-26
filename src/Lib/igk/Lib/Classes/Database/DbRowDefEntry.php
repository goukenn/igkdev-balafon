<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbRowDefEntry.php
// @date: 20251125 19:47:26
namespace IGK\Database;

use IGK\Models\ModelBase;
use Iterator;

/**
 * 
 * @package IGK\Database
 * @author C.A.D. BONDJE DOUE
 */
class DbRowDefEntry implements Iterator, IDbEntryDefinition
{
    /**
     * strict value
     * @var mixed
     */
    private $m_ref;
    /**
     * prefix value 
     * @var null|string
     */
    private $m_prefix;
    /**
     * it info value preference
     * @var object
     */
    private $m_it_info;
    /**
     * 
     * @var null|bool
     */
    private $m_strict;

    /**
     * model class 
     * @var mixed
     */
    private $m_model;

    /**
     * 
     * @param mixed $row 
     * @param null|string $prefix 
     * @param null|bool $strict 
     * @return void 
     */
    public function __construct(object $row, ?string $prefix = null, ?bool $strict = false, ?ModelBase $model=null)
    {
        $this->m_ref = $row;
        $this->m_prefix = $prefix;
        $this->m_it_info = (object)[];
        $this->m_strict = $strict;
        $this->m_model = $model ? get_class($model) : null;
    }

    /**
    * Returns Entry Values.
    * @return array
    */
    public function getEntryValues(): array
    {
        return (array)$this->m_ref;
    }

    /**
    * Initializes Def Array.
    * @return array
    */
    public function initDefArray(): array
    {
        return array_fill_keys(array_keys((array)$this->m_ref), 1);
    }
    /**
     * 
     * @return mixed 
     */

    public function current(): mixed
    {
        return $this->m_ref->{$this->key()};
    }
    /**
     * 
     * @return void 
     */

    public function next(): void
    {
        $this->m_it_info->key++;
    }

    /**
    * Key.
    * @return mixed
    */
    public function key(): mixed
    {
        return $this->m_it_info->tab[$this->m_it_info->key];
    }

    /**
    * Valid.
    * @return bool
    */
    public function valid(): bool
    {
        return $this->m_it_info->key < $this->m_it_info->count;
    }

    /**
    * Rewind.
    * @return void
    */
    public function rewind(): void
    {
        $tab = $this->reccords();
        $this->m_it_info = (object)[
            'current' => null,
            'tab' => $tab,
            'count' => count($tab),
            'key' => null
        ];
    }
    /**
     * return primary key reccord
     * @return array 
     */

    public function reccords(): array
    {
        $tab = array_keys((array)$this->m_ref);
        if ($p = $this->m_prefix) {
            $tab = array_map(function ($a) use ($p) {
                return igk_str_rm_start($a, $p, 1);
            }, $tab);
        }

        return $tab; // array_keys((array)$this->m_ref);
    }

    /**
    * .destructor
    * @param string $name
    */
    public function __get(string $name)
    {
        $g =  ['', $this->m_prefix];
        while (count($g)) {
            $q = array_shift($g);
            $p = $q . $name;
            if (property_exists($this->m_ref, $p)) {
                return $this->m_ref->$p;
            }
        }
    }

    /**
    * destructor
    * @param string $name
    * @param mixed $value
    */
    public function __set(string $name, $value)
    {
        $g =  ['', $this->m_prefix];
        while (count($g)) {
            $q = array_shift($g);
            $p = $q . $name;
            if (property_exists($this->m_ref, $p)) {
                $this->m_ref->$p = $value;
                return;
            }
        }
        if (!$this->m_strict){
            $this->m_ref->{$name} = $value;
            return;
        }
        igk_die('missing value ' . $name);
    }
    /**
     * check that the property exists
     * @param string $name 
     * @return bool 
     */

    public function keyExists(string $name): bool
    {
        return property_exists($this->m_ref, $name);
    }
    /**
     * unset definition 
     * @param string $n 
     * @return void 
     */

    function __unset(string $n)
    {
        unset($this->m_ref->$n);
    }

    /**
    * check if isset innaccessible property
    * @param string $n
    */
    function __isset(string $n)
    {
        return $this->keyExists($n);
    }

    /**
     * load from array
     * @param array $data 
     * @return static 
     */

    public function loadFromArray(array $data)
    {
        $tab = $this->reccords();
        foreach ($tab as $k) {
            if (key_exists($k, $data)) {
                $this->{$k} = igk_getv($data, $k);
            }
        }
        return $this;
    }
    /**
     * invoke model general extension function 
     * @param mixed $name 
     * @param mixed $arguments 
     * @return mixed 
     * @throws mixed 
     */

    public function __call($name, $arguments)
    {
        if ($cl = $this->m_model){
            // + | --------------------------------------------------------------------
            // + | invoke general extension helper with argument 
            // + |
            return call_user_func_array([$cl, $name], $arguments);
        }
        throw new \Exception(sprintf('%s, Not allowed in dbrow entry reccord', $name));
    }

    /**
    * Triggered when calling an inaccessible or undefined static method.
    * @param mixed $name
    * @param mixed $arguments
    */
    public static function __callStatic($name, $arguments){
        throw new \Exception('Not implemented');
    }
}

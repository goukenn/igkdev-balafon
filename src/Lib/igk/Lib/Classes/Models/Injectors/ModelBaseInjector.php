<?php
// @author: C.A.D. BONDJE DOUE
// @filenamer: ModelBaseInjector.php
// @date: 20220803 13:48:57
// @desc: model base inject 
namespace IGK\Models\Injectors;
use IGK\Models\ModelBase;
use IGK\System\IInjector;
use IGKValidator;

/**
 * model base injector 
 * @package IGK\Models\Injectors
 */
class ModelBaseInjector implements IInjector{
    /**
    * Property: model.
    * @var mixed
    */
    protected $model;
    /**
     * resolved column
     * @var ?string
     */
    private $m_column;

    public function column(){
        return $this->m_column;
    }
    /**
     * retrieve the stored model 
     * @return null|ModelBase 
     */
    public function getModel(){
        return $this->model;
    }
    /**
    * .ctr
    * @param null|ModelBase $model
    */
    public function __construct(?ModelBase $model=null)
    {
        $this->model = $model;
    }
    /**
    * resolv from request type
    * @param mixed $id
    * @param ?string $type
    * @return mixed
    */
    public function resolve($id, ?string $type=null){
        if (is_null($id)){ 
            igk_die("failed to resolve from [id] can not be null");
        } 
        $this->m_column = null;
        $fc_update_column =  function($column){
            $this->m_column = $column;
        };
        if (is_numeric($id)){
            if ($this->model->supportMacroFunction('fromId')){
                return $this->model::fromId($id, $fc_update_column);
            }
            $this->m_column = $this->model->getPrimaryKey();
            return $this->model::select_row($id);
        }
        if (IGKValidator::IsGUID($id)){            
            return $this->model::fromGuid($id, $fc_update_column);
        }
        try{
            return $this->model::resolve($id, $this->m_column);
        }
        catch(\Exception $ex) {
           if (igk_environment()->isDev()){
                throw $ex;
           }
        }
        return null;
    }
    /**
     * 
     * @param ?callable $cache_listener 
     * @param string $column 
     * @return void 
     */
    public static function ChangeColumn($cache_listener, string $column){

        if ($cache_listener){
            $cache_listener($column, 'change-column');
        }
    }
}
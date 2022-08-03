<?php
// @author: C.A.D. BONDJE DOUE
// @filename: FormStorageAction.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Forms\Actions;

use IGK\Actions\ActionBase;
use IGK\Helper\SysUtils;
use IGK\Helper\ViewHelper;
use IGK\System\Html\Forms\FormValidation;
use IGK\System\Http\Request;

/**
 * form storage action.
 * @package 
 */
class FormStorageAction extends ActionBase{
    private $m_fields;
    private $m_callback;
    
    var $formCref = 1;
    var $method = "POST";
    var $encType;

    /**
     * action requested
     * @var mixed
     */
    var $action;

    /**
     * entry file name
     * @var mixed
     */
    var $fname;
    /**
     * represent listener
     * @var mixed
     */
    public $listener;
    /**
     * get or set the binding uri
     * @var string|array
     */
    var $uri;

    /**
     * redirect uri
     * @var mixed
     */
    var $redirect_uri;
   

    /**
     * return errors 
     * @var mixed
     */
    var $errors;
    /**
     * geenerate the form
     * @param string $action 
     * @param null|callable $actions 
     * @return HtmlItemBase<mixed, mixed> 
     * @throws IGKException 
     */
    public function form(?callable $actions=null){
        $uri = $this->uri;
        $form = igk_create_node("form");//, null, [$uri, $action]);
        $_uri = is_array($uri) ? implode("/", $uri): $uri;

        if ($this->fname && $this->action){
            $_uri = implode("/",[$this->fname, $this->action]);
        }
        $form["method"] = $this->method ?? "POST";
        $form["action"] = 
            $this->listener?
            ($this->listener->getAppUri($_uri)) : $_uri;
        $form["enctype"] = $this->encType;
        
        if ($this->formCref){
            $form->cref();
        }
        $form->fields($this->m_fields);
        if ($actions){
            $form->actionbar($actions);
        }
        return $form;
    }
   
    /**
     * 
     * @param mixed $fields 
     * @param callable $callback 
     * @return void 
     */
    function __construct($fields, callable $callback, $listener=null)
    {
        $this->m_fields = $fields;
        $this->m_callback = $callback;
        $this->listener = is_null($listener) ? ViewHelper::CurrentCtrl() : $listener;
        $this->throwActionNotFound = false;
        
    }
    public function store(Request $request){      
        // validate first 
        $this->errors = null;

        if ($this->formCref){
            if (!igk_valid_cref(1, false)){
                return false;
            }
        }

        $val = new FormValidation(); 
        $obj = $val->fields($this->m_fields)->validate($_REQUEST);
        if ($obj===false){
            $this->errors  = $val->getErrors();
        }else{
            $_REQUEST = array_merge($_REQUEST,  (array)$obj);           
            if (!is_null($this->m_callback)){
                $fc = $this->m_callback;
                return $fc($request, $this);        
            }
        }
    }
   
    /**
     * handle actions
     * @param mixed $fname 
     * @param mixed $params 
     * @return never 
     */
    public function handle_actions($fname, $params){
        $action = $this->action? $this->action: $this->uri[1];
        $o = igk_view_handle_actions($fname, [
            $action=>function(Request $request){                               
                $b =  $this->store($request);
                if ($this->redirect_uri){
                    igk_navto($this->redirect_uri);
                }
                return $b;
            }
        ], $params);
        igk_view_unset_action($fname, $this->uri[1]);
        return $o;
    }
}
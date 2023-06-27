<?php

// @author: C.A.D. BONDJE DOUE
// @filename: DatabaseConfigurationController.php
// @date: 20230526 12:00:34
// @desc: configuration controller

namespace IGK\System\Configuration\Controllers;

use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Html\HtmlNodeBuilder;
use IGKException;
use ReflectionException;
use function igk_resources_gets as __;

/**
 * manage database actions 
 * @package IGK\System\Configuration\Controllers
 */
final class DatabaseConfigurationController extends ConfigControllerBase{
    public function __construct()
    {
        parent::__construct(); 
    }

    public function getName(){
        return IGK_DB_CONF_CTRL;
    }
    public function getConfigPage()
    { 
        return 'db';
    } 

    public function initDbSystem(){
        if (igk_is_conf_connected()){
            SysDbController::initDb();
        }
    }
    /**
     * view configuration page 
     * @return static 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public function View():BaseController{ 
        $t = $this->getTargetNode(); 
        $builder = new HtmlNodeBuilder($t);
        $builder([
            "panelbox.header > row"=>[
                'h2'=>'Database options'
            ],
            "panelbox.content > row"=>[
                // 'div'=>'presentation....'.igk_env_count(__METHOD__),
                // 'div.trace'=>igk_ob_get_func('igk_trace',1)
                'form.config'=>[
                    '_'=>['action'=>$this->getUri('update')],
                    'actionbar'=>[
                        '@'=>function($a){
                            $a->submit('btn_initdb', __('init system\'s database'));
                        }
                    ]
                ]
            ],
        ]); 
        return $this;
    }
    /**
     * export update configuration 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function update(){
        if (igk_getr('btn_initdb')){
            $this->initDbSystem();
        }
        igk_navto($this->getUri('showConfig'));
    }
}
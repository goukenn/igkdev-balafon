<?php
// @file: IGKSharedContentHtmlItemCtrl.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Controllers;
use IGK\System\Html\Dom\HtmlSharedContentNode;
use IGKHtmlSharedNotifyDialog;

/**
* Shared content html item controller.
* @package IGK\Controllers
*/
final class SharedContentHtmlItemController extends BaseController{

    /**
    * Constant: notifybox.
    * @var mixed
    */
    const notifybox="notifybox";
    /**
     * Constructor.
     */

    public function __construct(){
        parent::__construct();
    }
    /**
     * Get all registered shared content entities.
     *
     * @return mixed
     */

    public function getEntities(){
        return $this->m_entity;
    }
    /**
     * Get a shared content entity by name, creating a notifybox if needed.
     *
     * @param string $n The entity name.
     * @return mixed
     */

    public function getEntity($n){
        $g=igk_getv($this->m_entity, $n);
        if(($g == null) && ($n == self::notifybox)){
            $g=new HtmlSharedContentNode($this);
            $this->regEntity("notifybox", $g);
        }
        return $g;
    }
    /**
     * Get the entities environment parameter.
     *
     * @return mixed
     */

    public function getm_entity(){
        return $this->getEnvParam("entities");
    }
    /**
     * Get the controller name constant.
     *
     * @return string
     */

    public function getName(): string{
        return IGK_SHARED_CONTENT_CTRL;
    }
    /**
     * Finalise controller initialisation.
     *
     * @param mixed $context Optional initialisation context.
     * @return void
     */

    protected function initComplete($context=null){
        parent::initComplete();
    }

    /**
    * Initializes Target Node.
    * @return ?\IGK\System\Html\Dom\HtmlNode
    */

    protected function initTargetNode(): ?\IGK\System\Html\Dom\HtmlNode{
        $c=new HtmlSharedContentNode($this);
        return $c;
    }

    /**
    * Reg entity.
    * @param mixed $name
    * @param mixed $node
    */

    public function regEntity($name, $node){
        $this->m_entity[$name]=$node;
    }
}
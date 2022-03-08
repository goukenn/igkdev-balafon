<?php

namespace IGK\System\Configuration\Controllers;

use IGK\System\Http\WebResponse;

use function igk_resources_gets as __;

///<summary>Manage session </summary>
/**
* configuration manage session
*/
final class SessionManagerController extends ConfigControllerBase{
    const SESS_NOTIFY="sys://notify/sessionmanager";
    ///<summary>Represente clearall function</summary>
    /**
    * Represente clearall function
    */
    public function clearall_ajx(){
        session_destroy();
        session_write_close();
		foreach(igk_get_all_session_files() as $k=>$f){
			unlink($f);
		} 
        $sc=igk_create_node("script");
        $sc->Content="window.location = '".igk_io_baseuri()."';"; 
        return $sc;
    }
    ///<summary>Represente drop function</summary>
    /**
    * Represente drop function
    */
    public function drop(){
        $i=igk_getr("i");
        $d=ini_get("session.save_path");
        $dt=null;
        $ssid=session_id();
        $v_capp=igk_app();
        session_write_close();
        $sess_key=IGK_APP_SESSION_KEY;
        $_SESSION[$sess_key]=null;
        $file="";
        if(is_dir($d)){
			$prefix = igk_get_session_prefix();
            if(file_exists($file=$d."/".$prefix.$i)){
                unlink($file);
            }
        }
        igk_ajx_replace_ctrl_view($this, 1);
        igk_exit();
    }
    ///<summary>Represente getConfigPage function</summary>
    /**
    * Represente getConfigPage function
    */
    public function getConfigPage(){
        return "session";
    }
    public function getConfigGroup(){
        return ConfigsGroups::admin;
    }
    public function getIsConfigPageAvailable()
    {
        return igk_app()->getApplication()->getLibrary("session");
    }
    ///<summary>Represente View function</summary>
    /**
    * Represente View function
    */
    public function View(){
        $t=$this->TargetNode->clearChilds()->addPanelBox();
        $t->addSectionTitle(4)->Content=__("Session Manager");
        $bar=$t->addActionBar();
        $dv=$t->addDiv();
        $b=igk_get_all_session_file_infos();
        if($b){
            $frm=$dv->addDiv();
            $frm->addDiv()->Content=__("Total:").igk_count($b);
            $table=$frm->addDiv()->setClass("igk-table-host overflow-x-a")->add("table");
            $table["class"]="session-list";
            igk_html_db_build_table_header($table->add("tr"), ["", "Name", "Size", "Time"]);
            $maxItem=10;
            $c=0;
            $paginate=count($b) > $maxItem;
            $sess_id = session_id();
            foreach($b as $k=>$o){
				$f = $o->file;
                $tr=$table->tr();
                if($k  == $sess_id){
                    $tr["class"]="igk-active";
                }
                $tr->td()->addCheckbox("f", $k);               
                $tr->td()->Content=$k; 
                $size=filesize($f);
                $tr->td()->Content= $o->size; 
                $tr->td()->Content= $o->createtime; 
                $tr->td()->host(function($a, $k, $cond){
                    if ($cond){
                        $a->nbsp();
                    }else {
                        $a->ajxabutton($this->getUri("drop&i=".$k))->Content=igk_svg_use("drop");
                    }
                }, $k, $k==$sess_id);
                
                $c++;
                if($c>=$maxItem){
                    break;
                }
            }
            $bar=$dv->addActionBar();
            $bar->a_post($this->getUri("clearall_ajx"))
            ->setClass("igk-btn")
            ->Content=__("Clear All");
        }
        else{
            $dv->add("div")->Content=__("No sessions found");
        }
    }
}
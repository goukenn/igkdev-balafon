<?php
// @author: C.A.D. BONDJE DOUE
// @filename: .global.php
// @date: 20220803 13:48:59
// @desc:
/**
* Igk community init node callback.
* @param mixed $t
* @return mixed
*/
function igk_community_init_node_callback($t){
	$ctrl = igk_db_sys_ctrl("community");
	if ($ctrl){
		$ctrl->loadCommunityNode($t);
	}
}
/**
* Igk community init share with callback.
* @param mixed $t
* @return mixed
*/
function igk_community_init_ShareWith_callback($t){
	$ctrl = igk_db_sys_ctrl("community");
	if ($ctrl){
		$ctrl->loadCommunityNode($t);
	}
}
/**
* Igk html node community node.
* @return mixed
*/
function igk_html_node_CommunityNode(){
	$n = igk_create_node("div");
	$n["class"]="igk-community-node";
	igk_community_init_node_callback($n);
	return $n;
}
/**
* Igk html node shared with community.
* @param null|mixed $tab
* @return mixed
*/
function igk_html_node_SharedWithCommunity($tab=null){
	$n = igk_create_node("div");
	$n["class"]="igk-shared-comm";
	if ($tab!=null){
		foreach($tab as $k=>$v){
				$n->addSpan()->addA('#')->Content = $k;
		}
	}else
	igk_community_init_ShareWith_callback($n);
	return $n;
}
/**
* Igk html node follow us button.
* @param mixed $name
* @param mixed $uid
* @return mixed
*/
function igk_html_node_FollowUsButton($name, $uid){
	$srv = igk_community_get_followus_service();
	$fc = igk_getv($srv, $name);
	if ($fc){
		$n = igk_create_notagnode();
		$args = array_merge(array("view", $n, $uid), array_slice(func_get_args(),2));
		call_user_func_array($fc, $args);
		return $n;
	}
	return null;
}
/**
* Igk community get follow entries.
* @param mixed $cnf
* @return mixed
*/
function igk_community_get_follow_entries($cnf){
	$tab = igk_community_get_followus_service();
	if (!$tab){
		return null;
	}
	$otab=array();
	$cnf = igk_getv($cnf,"app.Followus");
	foreach($tab as $k=>$fc){
		$u = $fc("getlink", null, $cnf);
		if ($u)
		$otab[] = (object)array("u"=>$u, "t"=>$k);
	}
	return $otab;
}
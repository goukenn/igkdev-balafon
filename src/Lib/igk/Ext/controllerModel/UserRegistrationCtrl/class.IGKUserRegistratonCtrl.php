<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKUserRegistratonCtrl.php
// @date: 20220803 13:48:58
// @desc: 

abstract class IGKUserRegistrationCtrl extends \IGK\Controllers\ControllerTypeBase
{

	public function register()
	{

	}

	public function subscribe(){
		$obj = igk_get_robj();
		igk_val_init();
		igk_val_check(IGKValidator::IsStringNullOrEmpty($obj->clEmail) ||
			!IGKValidator::IsEmail($obj->clEmail)
		, $obj, "clEmail", "email is Null or not valid");
		igk_val_check("IsStringNullOrEmpty", $obj, "clPwd", "password not define");
		igk_val_check(($obj->clPwd != $obj->clRePwd), $obj, "clRePwd,clPwd", "password don't match");

		if (igk_val_haserror())
		{
			igk_val_regParam($this, "subscribe");
			$this->View();
		}
		else{
			igk_val_unregParam($this, "subscribe");
			$this->getView("subscribemailresponse", true);
		}
		igk_val_unregParam($this, "subscribe");
	}

	public function InitEnvironment()
	{
		igk_io_save_file_as_utf8($this->_getViewFile("registration_mail"), <<<EOF
<?php
?>
EOF
);
igk_io_save_file_as_utf8($this->_getViewFile("confirmation_mail"), <<<EOF
<?php
?>
EOF
);
		igk_io_save_file_as_utf8($this->_getViewFile("subscribeform"), <<<EOF
		\$this->TargetNode->clearChilds();
 igk_html_article(\$this , "default", \$this->TargetNode);

 \$e = \$this->getParam("subscribe:error");
 \$cb = \$this->getParam("subscribe:errorcibling");

 \$frm = \$t->addForm();
 \$frm["action"] = \$this->getUri("subscribe");
 if (\$e!=null)
 {

 \$div = \$frm->div();
 \$div["class"] = "error";
 \$div->add(\$e);
 }

 \$div = \$frm->div();
 \$li = \$div->addSLabelInput("clType","lb.particulier", "radio", "1");
 \$li["checked"]="checked";
 \$li = \$div->addSLabelInput("clType","lb.enterprise", "radio", "2");
 \$ul = \$frm->div()->add("ul");

 \$ul->add("li", array("class"=>igk_val_cbcss(\$cb, "clEmail") ))->addSLabelInput("clEmail", "lb.Email");
 \$ul->add("li", array("class"=>igk_val_cbcss(\$cb, "clPwd").""))->addSLabelInput("clPwd",  "lb.Pwd" ,"password");
 \$ul->add("li", array("class"=>igk_val_cbcss(\$cb, "clRePwd").""))->addSLabelInput("clRePwd",  "lb.RePwd" ,"password");
 \$ul->add("li", array("class"=>""))->addInput("btn_submit", "submit", R::ngets("btn.register"));
EOF
);

	}
} 
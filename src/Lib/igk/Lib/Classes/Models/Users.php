<?php 
namespace IGK\Models;

use IGK\Helper\Utility; 

/** 
 * @method static User currentUser() get current user
 * @method static 
 */
class Users extends ModelBase {
	/** 
	 */
	protected $table = "%prefix%users"; 

	protected $display = "clLogin";

	protected $fillable = ["clLogin", "clPwd", "clFirstName", "clLastName"];

	protected $form_fields = ["clLogin", "clPwd", "clFirstName", "clLastName"];


	
	public function fullname(){
		return Utility::GetFullName($this);
	}
	public function mail_display(){
		return implode(" ",array_filter([
			strtoupper($this->clLastName), ucfirst($this->clFirstName),
			"&lt;".$this->clLogin."&gt;"]));
	}
}

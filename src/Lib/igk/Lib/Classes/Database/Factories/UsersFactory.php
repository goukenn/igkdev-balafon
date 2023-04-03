<?php
// @author: C.A.D. BONDJE DOUE
// @file: UsersFactory.php
// @desc: factory UsersFactory
// @date: 20230208 17:27:59
namespace IGK\Database\Factories;

use IGK\System\Database\Factories\FactoryBase;
use IGK\System\Traits\ComposerFakerTrait;

///<summary>factory</summary>
/**
* factory
* @package IGK\Database\Factories
*/
class UsersFactory extends FactoryBase{
	use ComposerFakerTrait; 
	public function definition(): ?array{
		$faker = $this->getFaker(); 
		return array (	 
			'clLogin' => $faker->email(), 
			'clPwd' => $faker->password(8),
			'clFirstName' => $faker->firstName(),
			'clLastName' => $faker->lastName(),
			'clDisplay' => NULL,
			'clLocale' => ['fr','en'][rand(0,1)],
			'clPicture' => 'https://picsum.photos/200/300',
			'clLevel' => 0,
			'clStatus' => [-1,0,1][rand(0,2)],
			'clDate' => 'CURRENT_TIMESTAMP',
			'clLastLogin' => NULL,
			'clParent_Id' => NULL,
			'clClassName' => $this->data ? igk_getv($this->data, 'clClassName') : null
		);
	}
}
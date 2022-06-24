<?php

namespace App\V1\Controllers;
use App\Repositories\User;

class UserController {

	protected $user_repository;

	public function __construct() {
		$this->user_repository = new namespace\UserRepository();
	}

	public function details(  ) {
		
	}

}
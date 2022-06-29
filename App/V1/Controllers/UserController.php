<?php

	namespace App\V1\Controllers;

	use App\Repositories\User;
	use App\Utility\Response;

	class UserController
	{

		protected $user_repository;

		public function __construct()
		{
			$this->user_repository = new namespace\UserRepository();
		}

	public function details() {
		$user = $this->user_repository->find(1);
        Response::json( $user );
	}

	}
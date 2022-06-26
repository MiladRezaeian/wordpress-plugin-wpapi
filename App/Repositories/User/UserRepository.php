<?php

use App\Repositories\Contracts;

class UserRepository extends BaseRepository {

    public function __construct(){
        parent::__construct();
        $this->table = $this->db->users;
        $this->primary_key = 'ID';
		$this->gurded \ [ 'user_pass'];
    }

}
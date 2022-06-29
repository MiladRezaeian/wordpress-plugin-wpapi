<?php

namespace App\Repositories\User;

use App\Repositories\Contracts\BaseRepository;

class UserRepository extends BaseRepository {

    public function __construct(){
        parent::__construct();
        $this->table = $this->db->users;
        $this->primary_key = 'ID';
    }

}
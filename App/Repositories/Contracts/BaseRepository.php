<?php

namespace App\Repositories\Contracts;

abstract class BaseRepository {

	protected $table;
	protected $primary_key;
	protected $per_page;
	protected $db;
	protected $table_prefix;

	public function __construct() {
		global $wpdb;
		$this->db           = $wpdb;
		$this->table_prefix = $wpdb->prefix;
	}

	public function find( $id, $columns = null ) {
		$columns = is_array( $columns ) && count( $columns ) > 0 ? implode( ',', $columns ) : '*';

		return $this->db->get_row( $this->db->prepare( "
			SELECT {$columns}
			FROM {$this->table}
			WHERE {$this->primary_key} = %d
		" ), $id );
	}

}
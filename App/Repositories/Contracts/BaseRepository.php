<?php

namespace App\Repositories\Contracts;

abstract class BaseRepository
{

    protected $table;
    protected $primary_key;
    protected $per_page;
    protected $db;
    protected $table_prefix;
	protected $guarded;

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->table_prefix = $wpdb->prefix;
    }

    public function find( $id, $columns = null ) {
        $columns = $this->columns($columns);
		$user = $this->db->get_row($this->db->prepare("
			SELECT {$columns}
			FROM {$this->table}
			WHERE {$this->primary_key} = %d
		"), $id);
		return $user = $this->apply_guard( $user );
    }

    public function findBy( $criteria = array(), $columns = null, $single_record = false ) {
        $method = $single_record ? 'get_row' : 'get_results';
        $columns = $this->columns($columns);
        $query = "SELECT {$columns} FROM {$this->table} WHERE ";
        $query .= $this->process_where($criteria);

        return $this->db->{$method}($query);
    }

    public function delete( $id ) {
        return $this->db->delete($this->table, array($this->primary_key => $id), array('%d'));
    }

    public function update( $id, $data = array(), $format = array() ) {
        return $this->db->update($this->table, $data, array($this->primary_key => $id), $format, array('%d'));
    }

    public function columns( $columns ) {
        return is_array($columns) && count($columns) > 0 ? implode(',', $columns) : '*';
    }

    public function process_where( $criteria = array() ) {
        $query = "";
        foreach ($criteria as $key => $value) {
            $query .= " {$key} = {$value} AND";
        }
        $query = preg_replace('/AND$/', '', $query);

        return $query;
    }

	protected function apply_guard($user)	{
		$raw_data = (array)$user;
		foreach ( $raw_data as $key=>$value ){
			if( in_array( $key, $this->guarded)){
				unset($raw_data[$key]);
			}
		}

		return $raw_data;
//		return array_diff( $raw_data, $this->guarded );
	}

}
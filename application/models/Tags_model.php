<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tags_model extends CI_Model
{
    protected $table = 'tbltags';

    public function addTag($name)
    {
        $data = array(
            'name' => $name
        );
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
}

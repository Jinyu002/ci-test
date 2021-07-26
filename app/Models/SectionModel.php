<?php

namespace APP\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class SectionModel extends Model
{
    protected $db;
    public function __construct()
    {
        parent::__construct();
        //创建数据库连接
        $this->db = \Config\Database::connect()->table('section');
    }

    /**把版块列出来
     *
     * @return array
     */
    public function listSection(): array
    {
        return $this->db->select('id')->select('name')->select('information')
            ->where('status', 0)->orderBy('sequence')->get(20, 0)->getResultArray();
    }

    public function addSection($data = '')
    {
        if ($data != '') {
            return $this->db->insert($data);
        }
        return false;
    }

    public function deleteSection($update = '', $id = '')
    {
        if ($id != '' && $update != '') {
            return $this->db->set('status', 'status+1', false)
                ->set('updated_at', $update)->where('id', $id)->update();
        }
        return false;
    }

    public function editSection($update = '', $id = '', $name = '')
    {
        if ($id != '' && $name != '' && $update != '') {
            return $this->db->set('name', $name)->where('id', $id)->update();
        }
        return false;
    }

    public function top($update = '', $id = '', $sequence = '')
    {
        if ($id != '' && $sequence != '' && $update != '') {
            return $this->db->set('sequence', $sequence)
                ->set('updated_at', $update)->where('id', $id)
                ->update();
        }
        return false;
    }

    public function sequenceQuery(): array
    {
        return $this->db->selectMin('sequence')->get(1, 0)->getResultArray();
    }
}

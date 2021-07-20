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

    //把版块列出来
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

    public function deleteSection($id = '')
    {
        if ($id != '') {
            return $this->db->set('status', 1)->where('id', $id)->update();
        }
        return false;
    }

    public function editSection($id = '', $name = '')
    {
        if ($id != '' && $name != '') {
            return $this->db->set('name', $name)->where('id', $id)->update();
        }
        return false;
    }

    public function top($id = '', $sequence = '')
    {
        if ($id != '' && $sequence != '') {
            return $this->db->set('sequence', $sequence)->where('id', $id)
                ->update();
        }
        return false;
    }

    public function sequenceQuery(): array
    {
        return $this->db->selectMin('sequence')->get(1, 0)->getResultArray();
    }
}

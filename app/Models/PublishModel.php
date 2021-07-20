<?php

namespace App\Models;

use CodeIgniter\Model;

class PublishModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        //创建数据库连接
        $this->db = \Config\Database::connect()->table('post');
    }

    public function insertPost($data = '', bool $returnID = true)
    {
        if ($data != '') {
            return $this->db->insert($data);
        }
        return false;
    }

    public function idQuery(): array
    {
            return $this->db->selectMax('id')->get(1, 0)->getResultArray();
    }

    public function updateSequence($id = '', $sequence = '')
    {
        if ($id != '' && $sequence != '') {
            return $this->db->set('sequence', $sequence)
                ->where('id', $id)->update();
        }
        return false;
    }

    public function queryData(): array
    {
        return $this->db->select('id')->select('username')->select('title')
            ->select('created_at')->select('content')->select('sequence')->where('status', 0)->orderBy('sequence')
            ->get(20, 0)->getResultArray();
    }

    public function postContent($id = '')
    {
        if ($id != '') {
            return  $this->db->select('content')->where('id', $id)->get(1, 0)->getResultArray();
        }
        return false;
    }

    public function postDelete($id = '')
    {
        if ($id != '') {
            return $this->db->set('status', 1)->where('id', $id)->update();
        }
        return false;
    }

    public function editPost($content = '', $id = '')
    {
        if ($content != '') {
            return $this->db->set('content', $content)->where('id', $id)
                ->update();
        }
        return false;
    }

    public function updateReply($id = '', $num = '')
    {
        if ($id != '' && $num != '') {
            return $this->db->set('reply_number', $num)->where('id', $id)->update();
        }
        return false;
    }

    public function postNumber($id = '')
    {
        if ($id != '') {
            return $this->db->select()->where('users_id', $id)
                ->where('status', 0)->get()
                ->getResultArray();
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

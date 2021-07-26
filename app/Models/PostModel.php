<?php

namespace App\Models;

use CodeIgniter\Model;

class PostModel extends Model
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

    public function queryData($page): array
    {
        return $this->db->select('id')->select('username')->select('title')
            ->select('created_at')->select('content')->select('sequence')
            ->select('reply_number')->where('status', 0)->orderBy('sequence')
            ->get(20, $page)->getResultArray();
    }

    public function statistics(): array
    {
        return $this->db->select()->where('status', 0)->orderBy('sequence')
            ->get()->getResultArray();
    }

    public function postContent($id = '')
    {
        if ($id != '') {
            return  $this->db->select('content')->where('id', $id)->get(1, 0)->getResultArray();
        }
        return false;
    }

    public function postDelete($updated_at = '', $id = '')
    {
        if ($id != '' && $updated_at != '') {
            return $this->db->set('status', 'status+1', false)
                ->set('updated_at', $updated_at)->where('id', $id)->update();
        }
        return false;
    }

    public function editPost($updated_at = '', $content = '', $id = '')
    {
        if ($content != '' && $updated_at != '') {
            return $this->db->set('content', $content)
                ->set('updated_at', $updated_at)->where('id', $id)
                ->update();
        }
        return false;
    }

    public function updateReply($updated_at = '', $id = '')
    {
        if ($id != '' && $updated_at != '') {
            return $this->db->set('reply_number', 'reply_number+1', false)
                ->set('updated_at', $updated_at)->where('id', $id)->update();
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

    public function top($id = '', $sequence = '', $updated_at = '')
    {
        if ($id != '' && $sequence != '' && $updated_at != '') {
            return $this->db->set('sequence', $sequence)
                ->set('updated_at', $updated_at)->where('id', $id)
                ->update();
        }
        return false;
    }

    public function sequenceQuery(): array
    {
        return $this->db->selectMin('sequence')->get(1, 0)->getResultArray();
    }

    public function search($title, $page): array
    {
        return $this->db->select('id')->select('username')->select('title')
            ->select('created_at')->select('content')->select('sequence')
            ->select('reply_number')->like('title', $title)->where('status', 0)
            ->orderBy('sequence')
            ->get(20, $page)->getResultArray();
    }

    public function searchNum($title): array
    {
        return $this->db->select()->like('title', $title)->where('status', 0)
            ->orderBy('sequence')
            ->get()->getResultArray();
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;
use phpDocumentor\Reflection\Types\False_;

class ReplyModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        //创建数据库连接
        $this->db = \Config\Database::connect()->table('reply');
    }
    public function insertReply($data = '')
    {
        if ($data != '') {
            return $this->db->insert($data);
        }
        return false;
    }

    public function floor($id = '')
    {
        if ($id != '') {
            return $this->db->select()->where('post_id', $id)->get()
                ->getResultArray();
        }
        return false;
    }
    public function replyNumber($id = '')
    {
        if ($id != '') {
            return $this->db->select()->where('post_id', $id)->get()
                ->getResultArray();
        }
        return false;
    }
    public function queryData($id = '')
    {
        if ($id != '') {
            return $this->db->select('username')
                ->select('floor')
                ->select('created_at')->select('content')->where('post_id', $id)
                ->where('status', 0)
                ->get(20, 0)->getResultArray();
        }
        return false;
    }
    public function number($id = '')
    {
        if ($id != '') {
            return $this->db->select()->where('users_id', $id)->where('status', 0)
                ->get()
                ->getResultArray();
        }
        return false;
    }
}

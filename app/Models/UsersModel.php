<?php

namespace App\Models;

use CodeIgniter\Model;
use mysql_xdevapi\Exception;

class UsersModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        //创建数据库连接
        $this->db = \Config\Database::connect()->table('users');
    }

    //注册时插入数据
    public function insertData($data = '', bool $returnID = true)
    {
        if ($data != '') {
            return $this->db->insert($data);
        }
        return false;
    }

    //用户名记录查询
    public function checkUsername($username = '')
    {
        if ($username != '') {
            //$sql = "select username from users where username = ? Limit 1";
            return $this->db->select('username')->where('username', $username)
                ->get(1, 0)->getResult();
        }
        return false;
    }

    //登录查询用户名密码是否正确
    public function loginQuery($username = '', $password = '')
    {
        if ($username != '' && $password != '') {
            return $this->db->select('username')->where('username', $username)
                ->where('password', $password)->get(1, 0)->getResult();
        }
        return false;
    }

    public function updateLogin($last_login_at = '', $username = '')
    {
        if ($last_login_at != '') {
            return $this->db->set('last_login_at', $last_login_at)
                ->where('username', $username)->update();
        }
        return false;
    }

    public function idQuery($username = '')
    {
        if ($username != '') {
            return $this->db->select('id')->where('username', $username)
                ->get(1, 0)->getResultArray();
        }
        return false;
    }

    public function updatePost($id = '', $num = '')
    {
        if ($id != '' && $num != '') {
            return $this->db->set('post_number', $num)->where('id', $id)
                ->update();
        }
        return false;
    }

    public function updateReply($id = '', $num = '')
    {
        if ($id != '' && $num != '') {
            return $this->db->set('reply_number', $num)->where('id', $id)
                ->update();
        }
        return false;
    }

    public function number($username = '')
    {
        if ($username != '') {
            return $this->db->select('post_number')->select('reply_number')->where('username', $username)
                ->get(1, 0)->getResultArray();
        }
        return false;
    }

    public function listUsers(): array
    {
        return $this->db->select('id')->select('username')->select('nickname')
            ->select('post_number')->select('reply_number')->select('status')->get()
            ->getResultArray();
    }

    public function ban($id = '')
    {
        if ($id != '') {
            return $this->db->set('status', 1)->where('id', $id)->update();
        }
        return false;
    }

    public function status($username = '')
    {
        if ($username != '') {
            return $this->db->select('status')->where('username', $username)->get(1, 0)
                ->getResultArray();
        }
        return false;
    }
}

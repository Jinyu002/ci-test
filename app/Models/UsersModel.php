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
                ->get(0, 1)->getResult();
        }
        return false;
    }

    //登录查询用户名密码是否正确
    public function loginQuery($username = '', $password = '')
    {
        if ($username != '' && $password != '') {
            return $this->db->select('username')->where('username', $username)
                ->where('password', $password)->get(0, 1)->getResult();
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
}

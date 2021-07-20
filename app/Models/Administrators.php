<?php

namespace App\Models;

use CodeIgniter\Model;
use mysql_xdevapi\Exception;

class Administrators extends Model
{
    protected $db;
    public function __construct()
    {
        parent::__construct();
        //创建数据库连接
        $this->db = \Config\Database::connect()->table('administrators');
    }
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
}

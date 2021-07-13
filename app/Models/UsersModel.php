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
        $this->db = \Config\Database::connect();
    }


    function data()
    {
        $sql = "SELECT * FROM users";
        return $this->db->query($sql)->getResult();
    }

    //注册时插入数据
    function insert($data = NULL, bool $returnID = true)
    {
        if ($data != NULL) {
            $sql = "INSERT INTO users " .
                "(username,nickname, password,head,email,birthday,sex,address,last_login_at,updated_at,created_at) " .
                "VALUES " .
                "(?,?,?,?,?,?,?,?,?,?,?)";
            return $this->db->query($sql, array($data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10]));
        }
        return false;
    }

    //用户名记录查询
    function checkUsername($username = NULL)
    {
        if ($username != NULL) {
            $sql = "select username from users where username = ?";
            return $this->db->query($sql, array($username))->getResult();
        }
        return false;
    }

    //登录查询用户名密码是否正确
    function loginQuery($username = NULL, $password = NULL)
    {
        if ($username != NULL && $password != NULL) {
            $sql = "select * from users where username = ? and password = ?";
            return $this->db->query($sql, array($username, $password))->getResult();
        }
        return false;
    }

    function updateLogin($last_login_at,$username){
        if($last_login_at != NULL){
            $sql = "UPDATE `users` SET `last_login_at`= ? WHERE `username`= ?";
            return $this->db->query($sql,array($last_login_at,$username))->getResult();
        }

    }


}
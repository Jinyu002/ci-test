<?php

namespace App\Controllers;

use App\Models\UsersModel;
use CodeIgniter\Controller;

class Users extends Controller
{

    public function register()
    {

        $myUsername = $this->request->getPost('username');
        $myPassword = $this->request->getPost('password');
        $myConfirm = $this->request->getPost('confirm');
        $myNickname = $this->request->getPost('nickname');
        $myEmail = $this->request->getPost('email');
        $myBirthday = $this->request->getPost('birthday');
        $mySex = $this->request->getPost('sex');
        $myProvince = $this->request->getPost('province');
        $myCity = $this->request->getPost('city');
        $myArea = $this->request->getPost('area');
        //拼接所在地
        $address = $myProvince . $myCity . $myArea;

        //后端参数校验
        function preg_username($username)
        {
            if (preg_match("/^[a-zA-Z][a-zA-Z0-9_]{3,19}/", $username)) {
                return true;
            } else {
                return false;
            }
        }

        //验证昵称
        function checkNickname($nickname)
        {
            $length = strlen($nickname);
            if ($nickname != '' && $length >= 4 && $length <= 20) {
                return true;
            } else {
                return false;
            }
        }

        //验证密码
        function checkPassword($password)
        {
            $length = strlen($password);
            if ($length >= 6 && $length <= 27) {
                return true;
            } else {
                return false;
            }
        }

        //验证再次输入密码
        function checkConfirm($confirm, $password)
        {
            if ($confirm == $password) {
                return true;
            } else {
                return false;
            }
        }

        //验证性别
        function checkSex($sex)
        {
            if ($sex != '') {
                return true;
            } else {
                return false;
            }
        }


        //验证生日
        function checkBirth($birth)
        {
            if ($birth != '') {
                return true;
            } else {
                return false;
            }
        }

        //验证所在地
        function checkLocation($location)
        {
            if ($location != '') {
                return true;
            } else {
                return false;
            }
        }

        //验证邮箱
        function preg_email($mail)
        {
            if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/", $mail)) {
                return true;
            } else {
                return false;
            }
        }

        if (!preg_username($myUsername)) {
            $row['status'] = "0";
            $row['err'] = "fail";
            $row['msg'] = "用户名格式错误";
            exit(json_encode($row));
        }

        if (!checkNickname($myNickname)) {
            $row['status'] = "2";
            $row['err'] = "fail";
            $row['msg'] = "昵称格式错误";
            exit(json_encode($row));
        }

        if (!checkPassword($myPassword)) {
            $row['status'] = "3";
            $row['err'] = "fail";
            $row['msg'] = "密码格式错误";
            exit(json_encode($row));
        }

        if (!checkConfirm($myConfirm, $myPassword)) {
            $row['status'] = "4";
            $row['err'] = "fail";
            $row['msg'] = "两次输入密码不同";
            exit(json_encode($row));
        }

        if (!checkSex($mySex)) {
            $row['status'] = "5";
            $row['err'] = "fail";
            $row['msg'] = "性别未输入";
            exit(json_encode($row));
        }

        if (!checkBirth($myBirthday)) {
            $row['status'] = "6";
            $row['err'] = "fail";
            $row['msg'] = "生日未输入";
            exit(json_encode($row));
        }

        if (!checkLocation($address)) {
            $row['status'] = "7";
            $row['err'] = "fail";
            $row['msg'] = "所在地未输入";
            exit(json_encode($row));
        }

        if (!preg_email($myEmail)) {
            $row['status'] = "8";
            $row['err'] = "fail";
            $row['msg'] = "邮箱格式错误";
            exit(json_encode($row));
        }

        //连接数据库
        $con = db_connect();
        if (!$con) {
            $row['status'] = "9";
            $row['err'] = "fail";
            $row['msg'] = "数据库未连接";
            exit(json_encode($row));
        }

        //查询用户名是否存在，存在则无法注册
        $model = new \App\Models\UsersModel();
        $result = $model->checkUsername($myUsername);

        if (count($result) > 0) {
            $row['status'] = "10";
            $row['err'] = "fail";
            $row['msg'] = "用户名已存在，请选择另外用户名";
            exit(json_encode($row));
        }

        $head = '';
        $last_login_at = '';
        $updated_at = '';
        $myPassword = md5($myPassword);
        $created = date("Y-m-d H:i:s"); //获取本地时间，用以插入数据库创建时间

        try {
            $da = array(
                'username'      => $myUsername,
                'nickname'      => $myNickname,
                'password'      => $myPassword,
                'head'          => $head,
                'email'         => $myEmail,
                'birthday'      => $myBirthday,
                'sex'           => $mySex,
                'address'       => $address,
                'last_login_at' => $last_login_at,
                'updated_at'    => $updated_at,
                'created_at'    => $created
            );
            $result = $model->insertData($da);
        } catch (\ReflectionException $e) {
        }

        //检查是否将注册信息插入数据库
        $result = $model->checkUsername($myUsername);
        if (count($result) > 0) {
            $row['status'] = "1";
            $row['err'] = "0";
            $row['msg'] = "注册成功";
        } else {
            $row['status'] = "11";
            $row['err'] = "false";
            $row['msg'] = "数据库插入失败";
        }

        exit(json_encode($row));
    }

    public function login()
    {

        $myUsername = $this->request->getPost('username');
        $myPassword = $this->request->getPost('password');

        //校验用户名
        function preg_username($username)
        {
            if (preg_match("/^[a-zA-Z][a-zA-Z0-9_]{3,19}/", $username)) {
                return true;
            } else {
                return false;
            }
        }

        //校验密码
        function checkPassword($password)
        {
            $length = strlen($password);
            if ($length >= 6 && $length <= 27) {
                return true;
            } else {
                return false;
            }
        }

        //对用户输入的数据进行参数校验
        if (!preg_username($myUsername)) {
            $row['status'] = "0";
            $row['err'] = "fail";
            $row['msg'] = "用户名格式错误";
            exit(json_encode($row));
        }

        if (!checkpassword($myPassword)) {
            $row['status'] = "2";
            $row['err'] = "fail";
            $row['msg'] = "请填写6-27位密码";
            exit(json_encode($row));
        }

        $con = db_connect();
        if (!$con) {
            $row['status'] = "3";
            $row['err'] = "fail";
            $row['msg'] = "数据库未连接";
            exit(json_encode($row));
        }


        $myPassword = md5($myPassword); //md5加密密码

        $model = new \App\Models\UsersModel();
        $result = $model->loginQuery($myUsername, $myPassword);


        if (count($result) > 0) {
            $value = $myUsername;
            setcookie("username", $value, time() + 3600 * 48);
            $last_login = date("Y-m-d H:i:s", time() + 60 * 60);
            //获取本地时间，用以更新上次登录时间
            //操作数据库，更新上次登录时间
            $result = $model->updateLogin($last_login, $myUsername);
            $row['status'] = "1";
            $row['err'] = "0";
            $row['err'] = $last_login;
        } else {
            //用户不存在数据库中
            $row['status'] = "3";
            $row['err'] = "fail";
            $row['msg'] = "用户名或密码错误";
        }
        exit(json_encode($row));
    }

    public function logout()
    {
        setcookie("username", "", time() - 3600);
    }
}

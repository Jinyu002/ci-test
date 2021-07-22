<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\Administrators;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\Request;
use CodeIgniter\HTTP\RequestInterface;
use Config\App;
use Psr\Log\LoggerInterface;
use WebGeeker\Validation\Validation;
use WebGeeker\Validation\ValidationException;

class Users extends Controller
{
    public function __construct()
    {
        //parent::__construct();
        header("Content-type: text/html; charset=utf-8");
        header('Access-Control-Allow-Origin:*');
        date_default_timezone_set("Asia/Shanghai");
    }
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
        try {
            Validation::validate($this->request->getPost(), [
                "username" => "Regexp:/^[a-zA-Z][a-zA-Z0-9_]{3,19}/",
                "password" => "StrLenGeLe:6,27",
                "nickname" => "StrLenGeLe:4,20",
                "email"    => "Email",
                "birthday" => "Date",
                "sex"      => "IntIn:1,2",
                "province" => "StrLenGeLe:1,100",
                "city"     => "StrLenGeLe:1,100",
                "area"     => "StrLenGeLe:1,100",
            ]);
        } catch (\Exception $e) {
            $e->getMessage();
            $row['status'] = '0';
            $row['err'] = 'fail';
            $row['msg'] = $e->getMessage();
        }


        //后端参数校验
        //        function preg_username($username)
        //        {
        //            if (preg_match("/^[a-zA-Z][a-zA-Z0-9_]{3,19}/", $username)) {
        //                return true;
        //            } else {
        //                return false;
        //            }
        //        }
        //
        //        //验证昵称
        //        function checkNickname($nickname)
        //        {
        //            $length = strlen($nickname);
        //            if ($nickname != '' && $length >= 4 && $length <= 20) {
        //                return true;
        //            } else {
        //                return false;
        //            }
        //        }
        //
        //        //验证密码
        //        function checkPassword($password)
        //        {
        //            $length = strlen($password);
        //            if ($length >= 6 && $length <= 27) {
        //                return true;
        //            } else {
        //                return false;
        //            }
        //        }
        //
        //        //验证再次输入密码
        //        function checkConfirm($confirm, $password)
        //        {
        //            if ($confirm == $password) {
        //                return true;
        //            } else {
        //                return false;
        //            }
        //        }
        //
        //        //验证性别
        //        function checkSex($sex)
        //        {
        //            if ($sex != '') {
        //                return true;
        //            } else {
        //                return false;
        //            }
        //        }
        //
        //
        //        //验证生日
        //        function checkBirth($birth)
        //        {
        //            if ($birth != '') {
        //                return true;
        //            } else {
        //                return false;
        //            }
        //        }
        //
        //        //验证所在地
        //        function checkLocation($location)
        //        {
        //            if ($location != '') {
        //                return true;
        //            } else {
        //                return false;
        //            }
        //        }
        //
        //        //验证邮箱
        //        function preg_email($mail)
        //        {
        //            if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/", $mail)) {
        //                return true;
        //            } else {
        //                return false;
        //            }
        //        }
        //
        //        if (!preg_username($myUsername)) {
        //            $row['status'] = "0";
        //            $row['err'] = "fail";
        //            $row['msg'] = "用户名格式错误";
        //            exit(json_encode($row));
        //        }
        //
        //        if (!checkNickname($myNickname)) {
        //            $row['status'] = "2";
        //            $row['err'] = "fail";
        //            $row['msg'] = "昵称格式错误";
        //            exit(json_encode($row));
        //        }
        //
        //        if (!checkPassword($myPassword)) {
        //            $row['status'] = "3";
        //            $row['err'] = "fail";
        //            $row['msg'] = "密码格式错误";
        //            exit(json_encode($row));
        //        }
        //
        //        if (!checkConfirm($myConfirm, $myPassword)) {
        //            $row['status'] = "4";
        //            $row['err'] = "fail";
        //            $row['msg'] = "两次输入密码不同";
        //            exit(json_encode($row));
        //        }
        //
        //        if (!checkSex($mySex)) {
        //            $row['status'] = "5";
        //            $row['err'] = "fail";
        //            $row['msg'] = "性别未输入";
        //            exit(json_encode($row));
        //        }
        //
        //        if (!checkBirth($myBirthday)) {
        //            $row['status'] = "6";
        //            $row['err'] = "fail";
        //            $row['msg'] = "生日未输入";
        //            exit(json_encode($row));
        //        }
        //
        //        if (!checkLocation($address)) {
        //            $row['status'] = "7";
        //            $row['err'] = "fail";
        //            $row['msg'] = "所在地未输入";
        //            exit(json_encode($row));
        //        }
        //
        //        if (!preg_email($myEmail)) {
        //            $row['status'] = "8";
        //            $row['err'] = "fail";
        //            $row['msg'] = "邮箱格式错误";
        //            exit(json_encode($row));
        //        }

        //连接数据库

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
        $postNumber = 0;
        $replyNumber = 0;
        $status = 0;
        $created = date("Y-m-d H:i:s"); //获取本地时间，用以插入数据库创建时间

            $da = array(
                'username'      => $myUsername,
                'nickname'      => $myNickname,
                'password'      => $myPassword,
                'head'          => $head,
                'email'         => $myEmail,
                'birthday'      => $myBirthday,
                'sex'           => $mySex,
                'address'       => $address,
                'post_number'   => $postNumber,
                'reply_number'  => $replyNumber,
                'status'        => $status,
                'last_login_at' => $last_login_at,
                'updated_at'    => $updated_at,
                'created_at'    => $created
            );
        $validations =  [
            "username" => "Regexp:/^[a-zA-Z][a-zA-Z0-9_]{3,19}/",
            "password" => "StrLenGeLe:6,27",
            "nickname" => "StrLenGeLe:4,20",
            "head" => "StrLenGeLe:0",
            "email"    => "Email",
            "birthday" => "Date",
            "sex"      => "IntIn:1,2",
            "address" => "StrLenGeLe:1,100",
            "post_number"     => "IntIn:0",
            "reply_number"     => "IntIn:0",
            "status"=>"IntIn:0",
            "last_login_at" => "StrLenGeLe:0",
            "updated_at"    => "StrLenGeLe:0",
            "created_at"    => "Date",
        ];
        try {
            Validation::validate($da, $validations);
        } catch (ValidationException $e) {
        }
        $result = $model->insertData($da);


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

        try {
            Validation::validate($this->request->getPost(), [
                "username" => "Regexp:/^[a-zA-Z][a-zA-Z0-9_]{3,19}/",
                "password" => "StrLenGeLe:6,27",
            ]);
        } catch (\Exception $e) {
            $e->getMessage();
            $row['status'] = '0';
            $row['err'] = 'fail';
            $row['msg'] = $e->getMessage();
        }


        //管理员登录
        $model1 = new \App\Models\Administrators();
        $result1 = $model1->loginQuery($myUsername, $myPassword);
        if (count($result1) > 0) {
            $value = $myUsername;
            setcookie("username", $value, time() + 3600 * 48);
            $last_login = date("Y-m-d H:i:s", time() + 60 * 60);
            //获取本地时间，用以更新上次登录时间
            //操作数据库，更新上次登录时间
            $result = $model1->updateLogin($last_login, $myUsername);
            $row['status'] = "5";
            $row['err'] = "0";
            $row['msg'] = "管理员登录";
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
            $row['msg'] = $last_login;
        } else {
            //用户不存在数据库中
            $row['status'] = "4";
            $row['err'] = "fail";
            $row['msg'] = "用户名或密码错误";
        }
        exit(json_encode($row));
    }

    public function number()
    {
        $username = $this->request->getPost('username');
        try {
            Validation::validate($this->request->getPost(), [
                "username" => "Regexp:/^[a-zA-Z][a-zA-Z0-9_]{3,19}/",
            ]);
        } catch (\Exception $e) {
            $e->getMessage();
            $row['status'] = '0';
            $row['err'] = 'fail';
            $row['msg'] = $e->getMessage();
        }
        $model = new \App\Models\UsersModel();
        $result = $model->number($username);
        $row['status'] = "1";
        $row['err'] = "0";
        $row['data'] = $result;
        exit(json_encode($row));
    }

    public function listUsers()
    {
        $model = new \App\Models\UsersModel();
        $result = $model->listUsers();
        $row['status'] = "1";
        $row['err'] = "0";
        $row['data'] = $result;
        exit(json_encode($row));
    }

    public function ban()
    {
        $id = $this->request->getPost('userId');
        try {
            Validation::validate($this->request->getPost(), [
                "userId" => "IntGe:0",
            ]);
        } catch (\Exception $e) {
            $e->getMessage();
            $row['status'] = '0';
            $row['err'] = 'fail';
            $row['msg'] = $e->getMessage();
        }
        $model = new \App\Models\UsersModel();
        $result = $model->ban($id);
        $row['status'] = "1";
        $row['err'] = "0";
        exit(json_encode($row));
    }

    public function logout()
    {
        setcookie("username", "", time() - 3600);
    }
}

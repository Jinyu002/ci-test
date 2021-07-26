<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use WebGeeker\Validation\Validation;

class Users extends Controller
{
    public function __construct()
    {
        //parent::__construct();
        header("Content-type: text/html; charset=utf-8");
        header('Access-Control-Allow-Origin:*');
        date_default_timezone_set("Asia/Shanghai");
    }

    /**注册
     *
     * @return void
     */
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
        //查询用户名是否存在，存在则无法注册
        $user_model = new \App\Models\UsersModel();
        $username_result = $user_model->checkUsername($myUsername);

        if (count($username_result) > 0) {
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
        $result = $user_model->insertData($da);
        //检查是否将注册信息插入数据库
        $username_result = $user_model->checkUsername($myUsername);
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

    /**登录
     *
     * @return void
     */
    public function login()
    {

        $myUsername = $this->request->getPost('username');
        $myPassword = $this->request->getPost('password');

        //参数校验
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
        $administrators_model = new \App\Models\Administrators();
        $login_result = $administrators_model->loginQuery(
            $myUsername,
            $myPassword
        );
        if (count($login_result) > 0) {
            $value = $myUsername;
            setcookie("username", $value, time() + 3600 * 48);
            $last_login = date("Y-m-d H:i:s", time() + 60 * 60);
            //获取本地时间，用以更新上次登录时间
            //操作数据库，更新上次登录时间
            $result = $administrators_model->updateLogin(
                $last_login,
                $myUsername
            );
            $row['status'] = "5";
            $row['err'] = "0";
            $row['msg'] = "管理员登录";
            exit(json_encode($row));
        }


        $myPassword = md5($myPassword); //md5加密密码

        $user_model = new \App\Models\UsersModel();
        $login_result = $user_model->loginQuery($myUsername, $myPassword);


        if (count($login_result) > 0) {
            $value = $myUsername;
            setcookie("username", $value, time() + 3600 * 48);
            $last_login = date("Y-m-d H:i:s", time() + 60 * 60);
            //获取本地时间，用以更新上次登录时间
            //操作数据库，更新上次登录时间
            $result = $user_model->updateLogin($last_login, $myUsername);
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

    /**用户发帖回帖数量
     *
     * @return void
     */
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
        $user_model = new \App\Models\UsersModel();
        $user_result = $user_model->number($username);
        $row['status'] = "1";
        $row['err'] = "0";
        $row['data'] = $user_result;
        exit(json_encode($row));
    }

    /**在管理员处展示用户
     *
     * @return void
     */
    public function listUsers()
    {
        $user_model = new \App\Models\UsersModel();
        $user_result = $user_model->listUsers();
        $row['status'] = "1";
        $row['err'] = "0";
        $row['data'] = $user_result;
        exit(json_encode($row));
    }

    /**封禁用户
     *
     * @return void
     */
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
        $user_model = new \App\Models\UsersModel();
        $ban_result = $user_model->ban($id);
        $row['status'] = "1";
        $row['err'] = "0";
        exit(json_encode($row));
    }

    /**退出登录
     *
     * @return void
     */
    public function logout()
    {
        setcookie("username", "", time() - 3600);
    }
}

<?php


namespace App\Controllers;

use App\Models\UsersModel;


class Login extends BaseController
{
    public function login()
    {
        header("Content-Type: text/html;charset=utf-8");
        header('Access-Control-Allow-Origin:*');

        $myusername = $_POST['username'];
        $mypassword = $_POST['password'];

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
        function checkpassword($password)
        {
            $length = strlen($password);
            if ($length >= 6 && $length <= 27) {
                return true;
            } else {
                return false;
            }
        }

//对用户输入的数据进行参数校验
        if (!preg_username($myusername)) {
            $row['status'] = "0";
            $row['err'] = "fail";
            $row['msg'] = "用户名格式错误";
            exit(json_encode($row));
        }

        if (!checkpassword($mypassword)) {
            $row['status'] = "2";
            $row['err'] = "fail";
            $row['msg'] = "请填写6-27位密码";
            exit(json_encode($row));
        }

        $con = mysqli_connect("localhost", "root", "", "users");
        if (!$con) {
            $row['status'] = "3";
            $row['err'] = "fail";
            $row['msg'] = "数据库未连接";
            exit(json_encode($row));
        }


        $mypassword = md5($mypassword); //md5加密密码
        //$select = mysqli_select_db($con, "users");  //选择数据库表
        //操作数据库表

        $model = new \App\Models\UsersModel();
        $result = $model->loginQuery($myusername, $mypassword);


        if (count($result) > 0) {
            $value = $myusername;
            setcookie("username", $value, time() + 3600 * 48);
            $last_login = date("Y-m-d H:i:s"); //获取本地时间，用以更新上次登录时间
            //操作数据库，更新上次登录时间
            $result = $model->updateLogin($last_login, $myusername);
            $row['status'] = "1";
            $row['err'] = "0";
            $row['err'] = "登录成功";

        } else {
            //用户不存在数据库中
            $row['status'] = "3";
            $row['err'] = "fail";
            $row['msg'] = "用户名或密码错误";
        }
        exit(json_encode($row));


    }

}

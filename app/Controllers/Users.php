<?php

namespace  App\Controllers;

use App\Models\UsersModel;

class Users extends BaseController{
    public function  register(){
        $myusername = $_POST['username'];
        $mypassword = $_POST['password'];
        $myconfirm = $_POST['confirm'];
        $mynickname = $_POST['nickname'];
        $myemail = $_POST['email'];
        $mybirthday = $_POST['birthday'];
        $mysex = $_POST['sex'];
        $myprovince = $_POST['province'];
        $mycity = $_POST['city'];
        $myarea = $_POST['area'];
        //拼接所在地
        $address = $myprovince . $mycity . $myarea;

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
        function checknicknam($nickname)
        {
            $length = strlen($nickname);
            if ($nickname != '' && $length >= 4 && $length <= 20) {
                return true;
            } else {
                return false;
            }
        }

//验证密码
        function checkpassword($password)
        {
            $length = strlen($password);
            if ($length >= 6 && $length <= 27) {
                return true;
            } else {
                return false;
            }
        }

//验证再次输入密码
        function checkconfirm($confirm, $password)
        {
            if ($confirm == $password) {
                return true;
            } else {
                return false;
            }
        }

//验证性别
        function checksex($sex)
        {
            if ($sex != '') {
                return true;
            } else {
                return false;
            }
        }


//验证生日
        function checkbirth($birth)
        {
            if ($birth != '') {
                return true;
            } else {
                return false;
            }
        }

//验证所在地
        function checklocation($location)
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
            if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/",$mail)) {
                return true;
            } else {
                return false;
            }
        }

        if (!preg_username($myusername)) {
            $row['status'] = "0";
            $row['err'] = "fail";
            $row['msg'] = "用户名格式错误";
            exit(json_encode($row));

        }

        if (!checknicknam($mynickname)) {
            $row['status'] = "2";
            $row['err'] = "fail";
            $row['msg'] = "昵称格式错误";
            exit(json_encode($row));

        }

        if (!checkpassword($mypassword)) {
            $row['status'] = "3";
            $row['err'] = "fail";
            $row['msg'] = "密码格式错误";
            exit(json_encode($row));

        }

        if (!checkconfirm($myconfirm, $mypassword)) {
            $row['status'] = "4";
            $row['err'] = "fail";
            $row['msg'] = "两次输入密码不同";
            exit(json_encode($row));

        }

        if (!checksex($mysex)) {
            $row['status'] = "5";
            $row['err'] = "fail";
            $row['msg'] = "性别未输入";
            exit(json_encode($row));

        }

        if (!checkbirth($mybirthday)) {
            $row['status'] = "6";
            $row['err'] = "fail";
            $row['msg'] = "生日未输入";
            exit(json_encode($row));

        }

        if (!checklocation($address)) {
            $row['status'] = "7";
            $row['err'] = "fail";
            $row['msg'] = "所在地未输入";
            exit(json_encode($row));

        }

        if (!preg_email($myemail)) {
            $row['status'] = "8";
            $row['err'] = "fail";
            $row['msg'] = "邮箱格式错误";
            exit(json_encode($row));

        }

        //连接数据库
        $con = mysqli_connect("localhost", "root", "", "users");
        if(!$con){
            $row['status'] = "9";
            $row['err'] = "fail";
            $row['msg'] = "数据库未连接";
            exit(json_encode($row));
        }

        //查询用户名是否存在，存在则无法注册
        $model = new UsersModel();
        $result = $model->checkUsername($myusername);
        $ru = mysqli_num_rows($result);
        if($ru!=0){
            $row['status'] = "10";
            $row['err'] = "fail";
            $row['msg'] = "用户名已存在，请选择另外用户名";
            exit(json_encode($row));
        }

        $head = null;
        $last_login_at = null;
        $updated_at = null;
        $mypassword = md5($mypassword);

        $created = date("Y-m-d H:i:s"); //获取本地时间，用以插入数据库创建时间
        $result = $model->insert(array($myusername,$mynickname,$mypassword,$head,$myemail,$mybirthday,$mysex,$address,$last_login_at,$updated_at,$created));

        //检查是否将注册信息插入数据库
        $flag = mysqli_affected_rows($con);
        if ($flag>0) {
            $row['status'] = "1";
            $row['err'] = "0";
            $row['msg'] = "注册成功";
            exit(json_encode($row));
        } else {
            $row['status'] = "11";
            $row['err'] = "false";
            $row['msg'] = "数据库插入失败";
            exit(json_encode($row));
        }
    }



}

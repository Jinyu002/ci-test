<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\PublishModel;
use App\Models\ReplyModel;
use CodeIgniter\Controller;
use CodeIgniter\Model;

class Post extends Controller
{
    public function __construct()
    {
        header("Content-type: text/html; charset=utf-8");
        header('Access-Control-Allow-Origin:*');
        date_default_timezone_set("Asia/Shanghai");
    }
    //发表帖子存入数据库
    public function publishPost()
    {
        $model = new UsersModel();
        $model1 = new PublishModel();
        $username = $this->request->getPost('username');
        $title = $this->request->getPost('title');
        $content = $this->request->getPost('content');
        $created = date("Y-m-d H:i:s");
        $update = date("Y-m-d H:i:s");
        $str = trim($content); // 取得字串同时去掉头尾空格和空回车
        //$str=str_replace("<br>","",$str); // 去掉<br>标签
        //$str="<p>".trim($str); // 在文本头加入<p>
        $str = str_replace("\r\n", "<br>", $str); // 用p标签取代换行符
        //$str.="</p>\n"; // 文本尾加入</p>
        $str = str_replace("<p></p>", "", $str); // 去除空段落
        $str = str_replace("\n", "", $str); // 去掉空行并连成一行
        $str = str_replace("</p>", "</p>\n", $str); //整理html代码
        //连接数据库
        $result3 = $model->status($username);
        $status = $result3[0]['status'];
        //账号封禁中
        if ($status == "1") {
            $row['status'] = "3";
            $row['err'] = "fail";
            $row['msg'] = "账号封禁中，无法发帖或回复";
        } else {
            $result = $model->idQuery($username);
            $userid = $result[0];
            $re = $model1->idQuery();
            $sequence = $re[0];
            $reply_number = 0;
            $status = 0;
            $post = array(
                'users_id'     => $userid,
                'username'     => $username,
                'title'        => $title,
                'content'      => $str,
                'sequence'     => $sequence,
                'reply_number' => $reply_number,
                'status'       => $status,
                'created_at'   => $created,
                'updated_at'   => $update
            );
            $re = $model1->insertPost($post);
            if ($re != '') {
                $result1 = $model1->postNumber($userid);
                $post_num = count($result1);
                $result2 = $model->updatePost($userid, $post_num);
                $row['status'] = "1";
                $row['err'] = "0";
                $row['msg'] = "发表成功";
            } else {
                $row['status'] = "2";
                $row['err'] = "fail";
                $row['msg'] = "发表失败";
            }
        }
        exit(json_encode($row));
    }
    //将帖子在前端主页面显示
    public function listPost()
    {
        $con = db_connect();

        $model = new PublishModel();
        $result = $model->queryData();
        $row['status'] = "1";
        $row['err'] = "0";
        $row['data'] = $result;
        exit(json_encode($row));
    }
    //帖子详情
    public function post()
    {
        $id = $this->request->getPost('id');
        $con = db_connect();
        if (!$con) {
            $row['status'] = "0";
            $row['err'] = "数据库未连接";
            exit(json_encode($row));
        }
        $model = new PublishModel();
        $result = $model->postContent($id);
        $row['status'] = "1";
        $row['err'] = "0";
        $row['data'] = $result;
        exit(json_encode($row));
    }

    public function deletePost()
    {
        $id = $this->request->getPost('postId');
        $con = db_connect();
        if (!$con) {
            $row['status'] = "0";
            $row['err'] = "数据库未连接";
            exit(json_encode($row));
        }
        $model = new PublishModel();
        $result = $model->postDelete($id);
        $row['status'] = "1";
        $row['err'] = "0";
        exit(json_encode($row));
    }

    public function editPost()
    {
        $content = $this->request->getPost('content');
        $id = $this->request->getPost('postId');
        $con = db_connect();
        if (!$con) {
            $row['status'] = "0";
            $row['err'] = "数据库未连接";
            exit(json_encode($row));
        }
        $model = new PublishModel();
        $result = $model->editPost($content, $id);
        $row['status'] = "1";
        $row['err'] = "0";
        exit(json_encode($row));
    }
    public function reply()
    {
        $username = $this->request->getPost('username');
        $id = $this->request->getPost('postId');
        $content = $this->request->getPost('content');
        $created = date("Y-m-d H:i:s");
        $update = date("Y-m-d H:i:s");
        $str = trim($content); // 取得字串同时去掉头尾空格和空回车
        //$str=str_replace("<br>","",$str); // 去掉<br>标签
        //$str="<p>".trim($str); // 在文本头加入<p>
        $str = str_replace("\r\n", "<br>", $str); // 用p标签取代换行符
        //$str.="</p>\n"; // 文本尾加入</p>
        $str = str_replace("<p></p>", "", $str); // 去除空段落
        $str = str_replace("\n", "", $str); // 去掉空行并连成一行
        $str = str_replace("</p>", "</p>\n", $str); //整理html代码
        //连接数据库
        $model = new UsersModel();
        $model1 = new ReplyModel();
        $model2 = new PublishModel();
        $result6 = $model->status($username);
        $status = $result6[0]['status'];
        //账号封禁中
        if ($status == "1") {
            $row['status'] = "3";
            $row['err'] = "fail";
            $row['msg'] = "账号封禁中，无法发帖或回复";
        } else {
            //查询用户id
            $result = $model->idQuery($username);
            $userid = $result[0];
            //查询楼层数并设定楼层值
            $result1 = $model1->floor($id);
            $floor = count($result1) + 1;
            //查询现在的回复数
            $result2 = $model1->replyNumber($id);
            //回复数增加
            $reply_number = count($result2) + 1;
            //更新数据库中回复数
            $result3 = $model2->updateReply($id, $reply_number);
            $status = 0;
            $reply = array(
                'post_id'    => $id,
                'floor'      => $floor,
                'users_id'   => $userid,
                'username'   => $username,
                'content'    => $str,
                'status'     => $status,
                'created_at' => $created,
                'updated_at' => $update
            );
            $re = $model1->insertReply($reply);
            if ($re != '') {
                $result4 = $model1->number($userid);
                $num = count($result4);
                $result5 = $model->updateReply($userid, $num);
                $row['status'] = "1";
                $row['err'] = "0";
                $row['msg'] = "发表成功";
            } else {
                $row['status'] = "2";
                $row['err'] = "fail";
                $row['msg'] = "发表失败";
            }
        }
        exit(json_encode($row));
    }

    public function listReply()
    {
        $id = $this->request->getPost('postId');
        $con = db_connect();
        if (!$con) {
            $row['status'] = "0";
            $row['err'] = "数据库连接失败";
            exit(json_encode($row));
        }

        $model = new ReplyModel();
            $result = $model->queryData($id);
            $row['status'] = "1";
            $row['err'] = "0";
            $row['data'] = $result;
            exit(json_encode($row));
    }

    public function top()
    {
        $id = $this->request->getPost('postId');
        $con = db_connect();
        if (!$con) {
            $row['status'] = "0";
            $row['err'] = "数据库连接失败";
            exit(json_encode($row));
        }

        $model = new PublishModel();
        $result1 = $model->sequenceQuery();
        $sequence = $result1[0]['sequence'] - 1;
        $result = $model->top($id, $sequence);
        $row['status'] = "1";
        $row['err'] = "0";
        $row['data'] = $result;
        exit(json_encode($row));
    }
}

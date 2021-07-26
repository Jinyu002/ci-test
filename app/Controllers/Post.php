<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\PostModel;
use App\Models\ReplyModel;
use CodeIgniter\Controller;
use WebGeeker\Validation\Validation;
use WebGeeker\Validation\ValidationException;

class Post extends Controller
{
    public function __construct()
    {
        header("Content-type: text/html; charset=utf-8");
        header('Access-Control-Allow-Origin:*');
        date_default_timezone_set("Asia/Shanghai");
    }

    /**
     * 发表的帖子存入数据库
     *
     * @return void
     */
    public function publishPost()
    {
        $user_model = new UsersModel();
        $post_model = new PostModel();
        $username = $this->request->getPost('username');
        $title = $this->request->getPost('title');
        $content = $this->request->getPost('content');
        $created = date("Y-m-d H:i:s");
        $update = date("Y-m-d H:i:s");
        try {
            Validation::validate($this->request->getPost(), [
                "username" => "Regexp:/^[a-zA-Z][a-zA-Z0-9_]{3,19}/",
                "title"    => "StrLenGeLe:1,50",
                "content"  => "StrLenGeLe:0,65535",
            ]);
        } catch (\Exception $e) {
            $e->getMessage();
            $row['status'] = '0';
            $row['err'] = 'fail';
            $row['msg'] = $e->getMessage();
        }
        $str = trim($content); // 取得字串同时去掉头尾空格和空回车
        $str = str_replace("\r\n", "<br>", $str); // 用p标签取代换行符
        $str = str_replace("<p></p>", "", $str); // 去除空段落
        $str = str_replace("\n", "", $str); // 去掉空行并连成一行
        $str = str_replace("</p>", "</p>\n", $str); //整理html代码
        //连接数据库
        $status_result = $user_model->status($username);
        $status = $status_result[0]['status'];
        //账号封禁中
        if ($status == "1") {
            $row['status'] = "3";
            $row['err'] = "fail";
            $row['msg'] = "账号封禁中，无法发帖或回复";
        } else {
            $id_result = $user_model->idQuery($username);
            $userId = $id_result[0];
            $postId_result = $post_model->idQuery();
            $sequence = $postId_result[0];
            $reply_number = 0;
            $status = 0;
            $post = array(
                'users_id'     => $userId,
                'username'     => $username,
                'title'        => $title,
                'content'      => $str,
                'sequence'     => $sequence,
                'reply_number' => $reply_number,
                'status'       => $status,
                'created_at'   => $created,
                'updated_at'   => $update
            );
            $post_result = $post_model->insertPost($post);
            if ($post_result != '') {
                $postNumber_result = $post_model->postNumber($userId);
                $post_num = count($postNumber_result);
                $updatePost_result = $user_model->updatePost(
                    $userId,
                    $post_num
                );
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

    /**
     * 将帖子在主页显示
     *
     * @return void
     */
    public function listPost()
    {
        $page = $this->request->getPost('page');
        try {
            Validation::validate($this->request->getPost(), [
                "page" => "IntGe:1",
            ]);
        } catch (\Exception $e) {
            $e->getMessage();
            $row['status'] = '0';
            $row['err'] = 'fail';
            $row['msg'] = $e->getMessage();
        }
        $offset = 20 * ($page - 1);
        $post_model = new PostModel();
        $queryData_result = $post_model->queryData($offset);
        $statistics_result = $post_model->statistics();
        $totalPage = count($statistics_result) / 20;
        $totalPage = ceil($totalPage);
        $row['status'] = "1";
        $row['err'] = "0";
        $row['data'] = $queryData_result;
        $row['totalPage'] = $totalPage;
        exit(json_encode($row));
    }

    /**
     * 帖子详情
     *
     * @return void
     */
    public function post()
    {
        $id = $this->request->getPost('id');
        try {
            Validation::validate($this->request->getPost(), [
                "id" => "IntGe:1",
            ]);
        } catch (\Exception $e) {
            $e->getMessage();
            $row['status'] = '0';
            $row['err'] = 'fail';
            $row['msg'] = $e->getMessage();
        }
        $post_model = new PostModel();
        $postContent_result = $post_model->postContent($id);
        $row['status'] = "1";
        $row['err'] = "0";
        $row['data'] = $postContent_result;
        exit(json_encode($row));
    }

    /**
     * 删除帖子
     *
     * @return void
     */
    public function deletePost()
    {
        $id = $this->request->getPost('postId');
        try {
            Validation::validate($this->request->getPost(), [
                "postId" => "IntGe:1",
            ]);
        } catch (\Exception $e) {
            $e->getMessage();
            $row['status'] = '0';
            $row['err'] = 'fail';
            $row['msg'] = $e->getMessage();
        }
        $post_model = new PostModel();
        $update = date("Y-m-d H:i:s");
        $post_result = $post_model->postDelete($update, $id);
        $row['status'] = "1";
        $row['err'] = "0";
        exit(json_encode($row));
    }

    /**
     * 编辑帖子
     *
     * @return void
     */
    public function editPost()
    {
        $content = $this->request->getPost('content');
        $id = $this->request->getPost('postId');
        try {
            Validation::validate($this->request->getPost(), [
                "postId"  => "IntGe:1",
                "content" => "StrLenGeLe:0,65535",
            ]);
        } catch (\Exception $e) {
            $e->getMessage();
            $row['status'] = '0';
            $row['err'] = 'fail';
            $row['msg'] = $e->getMessage();
        }
        $post_model = new PostModel();
        $update = date("Y-m-d H:i:s");
//        $post_result = $post_model->editPost($update, $content, $id);
        $data = array(
            'updated_at' => $update,
            'content' => $content,
        );
        $post_result = $post_model->updateStatistics($data, $id);
        $row['status'] = "1";
        $row['err'] = "0";
        exit(json_encode($row));
    }

    /**
     * 回复帖子
     *
     * @return void
     */
    public function reply()
    {
        $username = $this->request->getPost('username');
        $id = $this->request->getPost('postId');
        $content = $this->request->getPost('content');
        try {
            Validation::validate($this->request->getPost(), [
                "username" => "Regexp:/^[a-zA-Z][a-zA-Z0-9_]{3,19}/",
                "postId"   => "IntGe:1",
                "content"  => "StrLenGeLe:0,65535",
            ]);
        } catch (\Exception $e) {
            $e->getMessage();
            $row['status'] = '0';
            $row['err'] = 'fail';
            $row['msg'] = $e->getMessage();
        }
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

        $user_model = new UsersModel();
        $reply_model = new ReplyModel();
        $post_model = new PostModel();
        $status_result = $user_model->status($username);
        $status = $status_result[0]['status'];
        //账号封禁中
        if ($status == "1") {
            $row['status'] = "3";
            $row['err'] = "fail";
            $row['msg'] = "账号封禁中，无法发帖或回复";
        } else {
            //查询用户id
            $id_result = $user_model->idQuery($username);
            $userid = $id_result[0];
            //查询楼层数并设定楼层值
            $floor_result = $reply_model->floor($id);
            $floor = count($floor_result) + 1;
            $update = date("Y-m-d H:i:s");
            //更新数据库中回复数
            $reply_result = $post_model->updateReply($update, $id);
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
            $insert_result = $reply_model->insertReply($reply);
            if ($insert_result != '') {
                $result4 = $reply_model->number($userid);
                $num = count($result4);
                $result5 = $user_model->updateReply($userid, $num);
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

    /**
     * 前端显示回复的帖子
     *
     * @return void
     */
    public function listReply()
    {
        $id = $this->request->getPost('postId');
        try {
            Validation::validate($this->request->getPost(), [
                "postId" => "IntGe:1",
            ]);
        } catch (\Exception $e) {
            $e->getMessage();
            $row['status'] = '0';
            $row['err'] = 'fail';
            $row['msg'] = $e->getMessage();
        }
        $reply_model = new ReplyModel();
        $reply_result = $reply_model->queryData($id);
        $row['status'] = "1";
        $row['err'] = "0";
        $row['data'] = $reply_result;
        exit(json_encode($row));
    }

    /**
     * 帖子置顶
     *
     * @return void
     */
    public function top()
    {
        $id = $this->request->getPost('postId');
        try {
            Validation::validate($this->request->getPost(), [
                "postId" => "IntGe:1",
            ]);
        } catch (\Exception $e) {
            $e->getMessage();
            $row['status'] = '0';
            $row['err'] = 'fail';
            $row['msg'] = $e->getMessage();
        }

        $post_model = new PostModel();
        $sequence_result = $post_model->sequenceQuery();
        $sequence = $sequence_result[0]['sequence'] - 1;
        $update = date("Y-m-d H:i:s");
        $data = array(
            'sequence'   => $sequence,
            'updated_at' => $update
        );
        $top_result = $post_model->updateStatistics($data, $id);
        $row['status'] = "1";
        $row['err'] = "0";
        $row['data'] = $top_result;
        exit(json_encode($row));
    }

    /**
     * 搜索帖子
     *
     * @return void
     */
    public function search()
    {
        $title = $this->request->getPost('title');
        $page = $this->request->getPost('page');
        try {
            Validation::validate($this->request->getPost(), [
                "title" => "StrLenGeLe:1,50",
                "page"  => "IntGe:1",
            ]);
        } catch (\Exception $e) {
            $e->getMessage();
            $row['status'] = '0';
            $row['err'] = 'fail';
            $row['msg'] = $e->getMessage();
        }
        $offset = 20 * ($page - 1);
        $post_model = new PostModel();
        $search_result = $post_model->search($title, $offset);
        $statistics_result = $post_model->searchNum($title);
        $totalPage = count($statistics_result) / 20;
        $totalPage = ceil($totalPage);
        $row['status'] = "1";
        $row['err'] = "0";
        $row['data'] = $search_result;
        $row['totalPage'] = $totalPage;
        exit(json_encode($row));
    }
}

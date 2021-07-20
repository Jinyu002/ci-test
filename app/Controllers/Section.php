<?php

namespace  App\Controllers;

use App\Models\SectionModel;
use CodeIgniter\Controller;

class Section extends Controller
{
    public function __construct()
    {
        header("Content-type: text/html; charset=utf-8");
        header('Access-Control-Allow-Origin:*');
    }

    //展示版块
    public function listSection()
    {
        $model = new SectionModel();
        $result = $model->listSection();
        $row['status'] = "1";
        $row['err'] = "0";
        $row['data'] = $result;
        exit(json_encode($row));
    }
    //增加版块
    public function addSection()
    {
        $name = $this->request->getPost('name');
        $info = $this->request->getPost('info');
        $sequence = 0;
        $status = 0;
        $model = new SectionModel();
        $data = array(
            'name'        => $name,
            'information' => $info,
            'sequence'    => $sequence,
            'status'      => $status
        );
        $result = $model->addSection($data);
        $row['status'] = "1";
        $row['err'] = "0";
        $row['data'] = $result;
        exit(json_encode($row));
    }
    //删除版块
    public function deleteSection()
    {
        $id = $this->request->getPost('sectionId');
        $model = new SectionModel();
        $result = $model->deleteSection($id);
        $row['status'] = "1";
        $row['err'] = "0";
        exit(json_encode($row));
    }
    //编辑版块
    public function editSection()
    {
        $id = $this->request->getPost('sectionId');
        $name = $this->request->getPost('sectionName');
        $model = new SectionModel();
        $result = $model->editSection($id, $name);
        $row['status'] = "1";
        $row['err'] = "0";
        exit(json_encode($row));
    }

    public function top()
    {
        $id = $this->request->getPost('sectionId');
        $model = new SectionModel();
        $result1 = $model->sequenceQuery();
        $sequence = $result1[0]['sequence'] - 1;
        $result = $model->top($id, $sequence);
        $row['status'] = "1";
        $row['err'] = "0";
        $row['data'] = $result;
        exit(json_encode($row));
    }
}

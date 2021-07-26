<?php

namespace App\Controllers;

use App\Models\SectionModel;
use CodeIgniter\Controller;
use WebGeeker\Validation\Validation;
use WebGeeker\Validation\ValidationException;

class Section extends Controller
{
    public function __construct()
    {
        header("Content-type: text/html; charset=utf-8");
        header('Access-Control-Allow-Origin:*');
    }

    /**
     * 展示版块
     *
     * @return void
     */
    public function listSection()
    {
        $section_model = new SectionModel();
        $section_result = $section_model->listSection();
        $row['status'] = "1";
        $row['err'] = "0";
        $row['data'] = $section_result;
        exit(json_encode($row));
    }

    /**
     * 增加版块
     *
     * @return void
     */
    public function addSection()
    {
        $name = $this->request->getPost('name');
        $info = $this->request->getPost('info');
        try {
            Validation::validate($this->request->getPost(), [
                "name" => "StrLenGeLe:1,50",
                "info" => "StrLenGeLe:1,100",
            ]);
        } catch (\Exception $e) {
            $e->getMessage();
            $row['status'] = '0';
            $row['err'] = 'fail';
            $row['msg'] = $e->getMessage();
        }
        $sequence = 0;
        $status = 0;
        $section_model = new SectionModel();
        $data = array(
            'name'        => $name,
            'information' => $info,
            'sequence'    => $sequence,
            'status'      => $status
        );
        $section_result = $section_model->addSection($data);
        $row['status'] = "1";
        $row['err'] = "0";
        $row['data'] = $section_result;
        exit(json_encode($row));
    }

    /**
     * 删除版块
     *
     * @return void
     */
    public function deleteSection()
    {
        $id = $this->request->getPost('sectionId');
        try {
            Validation::validate($this->request->getPost(), [
                "sectionId" => "IntGe:1",
            ]);
        } catch (\Exception $e) {
            $e->getMessage();
            $row['status'] = '0';
            $row['err'] = 'fail';
            $row['msg'] = $e->getMessage();
        }
        $section_model = new SectionModel();
        $update = date("Y-m-d H:i:s");
        $section_result = $section_model->deleteSection($update, $id);
        $row['status'] = "1";
        $row['err'] = "0";
        exit(json_encode($row));
    }

    /**
     * 编辑版块
     *
     * @return void
     */
    public function editSection()
    {
        $id = $this->request->getPost('sectionId');
        $name = $this->request->getPost('sectionName');
        try {
            Validation::validate($this->request->getPost(), [
                "sectionId"   => "IntGe:1",
                "sectionName" => "StrLenGeLe:1,50",

            ]);
        } catch (\Exception $e) {
            $e->getMessage();
            $row['status'] = '0';
            $row['err'] = 'fail';
            $row['msg'] = $e->getMessage();
        }
        $section_model = new SectionModel();
        $update = date("Y-m-d H:i:s");
        $data = array(
            'updated_at' => $update,
            'name'       => $name
        );
        $section_result = $section_model->updateStatistics($data, $id);
        $row['status'] = "1";
        $row['err'] = "0";
        exit(json_encode($row));
    }

    /**
     * 版块置顶
     *
     * @return void
     */
    public function top()
    {
        $id = $this->request->getPost('sectionId');
        try {
            Validation::validate($this->request->getPost(), [
                "sectionId" => "IntGe:1",

            ]);
        } catch (\Exception $e) {
            $e->getMessage();
            $row['status'] = '0';
            $row['err'] = 'fail';
            $row['msg'] = $e->getMessage();
        }
        $section_model = new SectionModel();
        $sequence_result = $section_model->sequenceQuery();
        $sequence = $sequence_result[0]['sequence'] - 1;
        $update = date("Y-m-d H:i:s");
        $data = array(
            'updated_at' => $update,
            'sequence'   => $sequence,
        );
        $top_result = $section_model->updateStatistics($data, $id);
        $row['status'] = "1";
        $row['err'] = "0";
        $row['data'] = $top_result;
        exit(json_encode($row));
    }
}

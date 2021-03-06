<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Feedback extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        
        $this->load->helper('public_helper');
        $this->load->database();
        
        // $this->load->helper(array('form','url')); //加载辅助函数
        // $this->load->model('feedback_model');
        // $this->load->library('form_validation'); //表单验证类
    }

    public function __destruct()
    {
        if (isset($this->response))
            echo json_encode($this->response);
    }

    function index()
    {
        header("Content-type:text/html;charset=utf-8");
        
        $data['title'] = '反馈首页';
        
        // $result = $this->feedback_model->getFeedbacks();
        // $data['feedbacks'] = $result;
        
        // $this->load->view('feedback/header',$data);
        // $this->load->view('feedback/index',$data);
        // $this->load->view('feedback/footer.html');
    }

    /* 获取所有未采纳的 */
    function list()
    {
    	$adopt = option_get('adopt');
        $page_no = require_get('page_no');
        $result = self::feedbackList($adopt, $page_no);
        
        try {
            if (empty($result)) {
                throw new Exception('没有反馈数据!!');
            }
            
            foreach ($result as $k => $v) {
                $data[$k]['feedbackId'] = intval($v['id']);
                $data[$k]['feedback'] = $v['feedback'];
                $data[$k]['contact'] = $v['contact'];
                $data[$k]['adopt'] = intval($v['adopt']);
                $data[$k]['userid'] = $v['userid'];
                $data[$k]['nickname'] = $v['nickname'];
                $data[$k]['version'] = $v['version'];
                $data[$k]['create_date'] = $v['create_date'];
            }
            
            $response['code'] = 1;
            $response['msg'] = 'success';
            $response['data'] = $data;
        } 
        catch (Exception $e) {
            
            $response['code'] = 0;
            $response['msg'] = $e->getMessage();
        }
        $this->response = & $response;
    }

    /* 添加反馈 */
    public function add()
    {
        try {
            $feedback = require_get('feedback');
            $contact = option_get('contact');
            $nickname = option_get('nickname');
            $version = require_get('version');
            $user_id = option_get('user_id');
           
            $insert_data['feedback'] = $feedback;
            $insert_data['contact'] = $contact;
            $insert_data['nickname'] = $nickname;
            $insert_data['version'] = $version;
            $insert_data['userid'] = $user_id;
            
            $insert_sql = $this->db->insert_string('feedback', $insert_data);
            $insert_sql = str_replace('INSERT', 'INSERT IGNORE', $insert_sql);
            $this->db->query($insert_sql);
            if ($this->db->affected_rows() < 1)
                throw new Exception('发送失败!请联系管理员!');
            
            // 成功
            $response['code'] = 1;
            $response['msg'] = '感谢您对我们的产品提供宝贵意见！';
        } catch (Exception $e) {
            
            $response['code'] = 0;
            $response['msg'] = $e->getMessage();
        }
        $this->response = & $response;
    }

    /* 采纳反馈 */
    function adopt()
    {
        try {
            $update_data['adopt'] = '1';
            $where['id'] = require_get('feedbackId');
            $sql = $this->db->update_string('feedback', $update_data, $where);
            $this->db->query($sql);
            if ($this->db->affected_rows() < 1)
                throw new Exception('采纳反馈失败!');
            
            $response['code'] = 1;
            $response['msg'] = '采纳反馈成功';
        } catch (Exception $e) {
            
            $response['code'] = 0;
            $response['msg'] = $e->getMessage();
        }
        $this->response = & $response;
    }

    /* 删除反馈 */
    function remove()
    {
        try {
            //不是物理删除 而是把状态置位2
            $update_data['adopt'] = '2';
            $where['id'] = require_get('feedbackId');
            $sql = $this->db->update_string('feedback', $update_data, $where);
            $this->db->query($sql);
            if ($this->db->affected_rows() < 1)
                throw new Exception('删除反馈失败!');
                
                $response['code'] = 1;
                $response['msg'] = '删除反馈成功';
        } catch (Exception $e) {
            
            $response['code'] = 0;
            $response['msg'] = $e->getMessage();
        }
        $this->response = & $response;
    }

    /* 获取所有反馈 是否采纳 */
    private function feedbackList($adopt = 0, $page_no = 1)
    {
        $page_no = $page_no - 1;		//前段页码从1开始
        $page_no = max(0, $page_no);
        $where['adopt'] = $adopt;
        $result = $this->db->where($where)
        ->get('feedback', 20, $page_no)
            ->result_array();
        return $result;
    }
}
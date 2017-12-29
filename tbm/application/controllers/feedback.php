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
    function select()
    {
        $adopt = require_post('adopt');
        $pageat = require_post('pageat');
        $result = self::feedbackList($adopt, $pageat);
        
        try {
            if (empty($result)) {
                throw new Exception('没有数据了!!');
            }
            
            foreach ($result as $k => $v) {
                $data[$k]['id'] = intval($v['id']);
                $data[$k]['feedback'] = $v['feedback'];
                $data[$k]['contact'] = $v['contact'];
                $data[$k]['adopt'] = intval($v['adopt']);
                $data[$k]['userid'] = $v['userid'];
                $data[$k]['version'] = $v['version'];
                $data[$k]['device'] = $v['device'];
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
            $feedback = require_post('feedback');
            $contact = require_post('contact');
            $insert_data['feedback'] = $feedback;
            $insert_data['contact'] = $contact;
            $insert_sql = $this->db->insert_string('feedback', $insert_data);
            $insert_sql = str_replace('INSERT', 'INSERT IGNORE', $insert_sql);
            $this->db->query($insert_sql);
            if ($this->db->affected_rows() < 1)
                throw new Exception('发送失败!请联系管理员!');
            
            // 成功
            $response['code'] = 1;
            $response['msg'] = 'success';
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
            $update_data['adopt'] = 1;
            $where['id'] = require_post('id');
            $sql = $this->db->update_string('feedback', $update_data, $where);
            $this->db->query($sql);
            if ($this->db->affected_rows() < 1)
                throw new Exception('采纳反馈失败!');
            
            $response['code'] = 1;
            $response['msg'] = 'success';
        } catch (Exception $e) {
            
            $response['code'] = 0;
            $response['msg'] = $e->getMessage();
        }
        $this->response = & $response;
    }

    /* 删除反馈 */
    function delete()
    {
        try {
            $this->db->where('id', require_post('id'));
            $this->db->delete('feedback');
            if ($this->db->affected_rows() < 1)
                throw new Exception('采纳反馈失败!');
            
            $response['code'] = 1;
            $response['msg'] = 'success';
        } catch (Exception $e) {
            
            $response['code'] = 0;
            $response['msg'] = $e->getMessage();
        }
        $this->response = & $response;
    }

    /* 获取所有反馈 是否采纳 */
    private function feedbackList($adopt = 0, $pageat = 1)
    {
        $where['adopt'] = $adopt;
        $result = $this->db->where($where)
            ->get('feedback', 20, $pageat)
            ->result_array();
        return $result;
    }
}
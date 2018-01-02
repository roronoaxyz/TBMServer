<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Assist extends CI_Controller
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
    	
    	$data['title'] = '帮助首页';
    	
    	// $result = $this->feedback_model->getFeedbacks();
    	// $data['feedbacks'] = $result;
    	
    	// $this->load->view('feedback/header',$data);
    	// $this->load->view('feedback/index',$data);
    	// $this->load->view('feedback/footer.html');
    }
    
    /* 获取所有帮助数据 */
    function list()
    {
    	$page_no = require_get('page_no');
    	$result = self::assistList($page_no);
    	
    	try {
    		if (empty($result)) {
    			throw new Exception('没有数据了!!');
    		}
    		$count = $this->db->count_all('assist');
    		
    		foreach ($result as $k => $v) {
    			$data[$k]['assistId'] = intval($v['id']);
    			$data[$k]['title'] = $v['title'];
    			$data[$k]['content'] = $v['content'];
    			$data[$k]['remark'] = $v['remark'];
    		}
    		
    		$response['code'] = 1;
    		$response['count'] = $count;
    		$response['msg'] = '操作成功';
    		$response['data'] = $data;
    	}
    	catch (Exception $e) {
    		
    		$response['code'] = 0;
    		$response['msg'] = $e->getMessage();
    	}
    	$this->response = & $response;
    }
    
    /* 添加帮助 */
    public function add()
    {
        try {
            $title = require_get('title');
            $content = require_get('content');
            $remark = option_get('remark');
            
            $insert_data['title'] = $title;
            $insert_data['content'] = $content;
            $insert_data['remark'] = $remark;
            
            $insert_sql = $this->db->insert_string('assist', $insert_data);
            $insert_sql = str_replace('INSERT', 'INSERT IGNORE', $insert_sql);
            $this->db->query($insert_sql);
            if ($this->db->affected_rows() < 1)
                throw new Exception('插入数据失败!请联系管理员!');
                
                // 成功
                $response['code'] = 1;
                $response['msg'] = '插入数据成功！';
        } catch (Exception $e) {
            
            $response['code'] = 0;
            $response['msg'] = $e->getMessage();
        }
        $this->response = & $response;
    }
    
    /* 添加帮助 */
    public function update()
    {
        try {
            $assistId = require_get('assistId');
            $title = require_get('title');
            $content = require_get('content');
            $remark = option_get('remark');
            
            $update_data['title'] = $title;
            $update_data['content'] = $content;
            $update_data['remark'] = $remark;
            
            $this->db->update('assist', $update_data, array("id" => $assistId));
            
            if ($this->db->affected_rows() < 1)
                throw new Exception('修改数据失败!请联系管理员!');
                
                // 成功
                $response['code'] = 1;
                $response['msg'] = '修改数据成功！';
        } catch (Exception $e) {
            
            $response['code'] = 0;
            $response['msg'] = $e->getMessage();
        }
        $this->response = & $response;
    }
    
    /* 删除帮助 */
    public function remove()
    {
        try {
            $assistId = require_get('assistId');
            
            $this->db->delete('assist', array("id" => $assistId));
            if ($this->db->affected_rows() < 1)
                throw new Exception('删除数据失败!请联系管理员!');
                
                // 成功
                $response['code'] = 1;
                $response['msg'] = '删除数据成功！';
        } catch (Exception $e) {
            
            $response['code'] = 0;
            $response['msg'] = $e->getMessage();
        }
        $this->response = & $response;
    }
    
    
    /* 帮助信息 */
    private function assistList($page_no = 0)
    {
    	$page_no = $page_no - 1;		//前段页码从1开始
    	$page_no = max(0, $page_no);
    	
    	$result = $this->db
    	->get('assist', 20, $page_no*20)
    	->result_array();
    	return $result;
    }
}
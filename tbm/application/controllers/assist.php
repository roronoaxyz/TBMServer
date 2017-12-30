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
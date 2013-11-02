<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 操作日志模型
 * 
 * @package		app
 * @subpackage	core
 * @category	model
 * @author		bruce.yang<kissjava@vip.qq.com>
 *
 */
class Log_model extends MY_Model{
	
	protected $table_name = 'log';

	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 记录日志
	 * @param string $msg
	 * @return int|string 成功返回1，否则返回出错信息
	 */
	public function msg($msg)
	{
	    $data = array(
	            'username' => $this->session->userdata('username'),
	            'ip' => $this->session->userdata('last_login_ip'),
	            'action' => $msg,
	            'info' => implode(';', $this->db->queries),
	            'add_time' => date("Y-m-d H:i:s")
	    );
	    
	    if ($this->create($this->table_name, $data)) {
	        return 1;
	    } else {
	        return $this->db_error;
	    }
	}
	
	/**
	 * 返回分页数据和总数
	 * 
	 * @return array 返回数组array('total'=>表记录总数,'list'=>记录列表)
	 */
	public function list_log(){
		
		$total_rows = $this->count($this->table_name);
		// 每页显示的记录条数，默认20条
		$per_page = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
		
		$data['total'] = $total_rows;
		// list_data(表,抓取记录数，偏离量,排序字段,排序方法);
		$data['list'] = $this->list_data($this->table_name, $per_page, $this->input->post('pageNum') ? ($this->input->post('pageNum') - 1) *
		        $per_page : 0, $this->input->post('orderField') ? $this->input->post('orderField') : 'id',
		        $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc');
		
		return $data;
	}

	public function __destruct(){
		parent::__destruct();
	}

}

/* End of file log_model.php */
/* Location: ./application/models/log_model.php */
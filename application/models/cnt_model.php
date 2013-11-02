<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 *@function 联系人树管理模型
 *@date     2013-10-14
 *@author   gerui
 *@email    <forgerui@gmail.com>
 */
class Cnt_model extends MY_Model{

	protected $tables = array(
			'cnt',				//0
			'cnt_dep_rel',		//1
			'dep',				//2
			'user',				//3
			'user_dep_rel'		//4
			);

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 联系人列表
	 * @return unknown
	 */
	public function list_cnt(){
		
		$per_page = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
		$orderField = $this->input->post('orderField') ? $this->input->post('orderField') : 'a.id';
		$orderDirection = $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc';
		$startOffset = $this->input->post('pageNum') ? ($this->input->post('pageNum') - 1) * $per_page : 0;
		
		$dep = $this->get_dep_array();
		$dep_id_array = array();
		foreach($dep as $line){
			array_push($dep_id_array, $line['id']);
		}
		$data['dep'] = $dep;
		$data['tree'] = false;
		
		$this->db->select('a.id, a.cnt_num, a.cnt_name, a.phone, a.desc, a.create_time, a.update_time, c.dep_name');
		$this->db->from("{$this->tables[0]} as a");
		$this->db->join("{$this->tables[1]} as b", "a.id = b.cnt_id", "left");
		$this->db->join("{$this->tables[2]} as c", "b.dep_id = c.id", "left");
		if(!empty($dep_id_array)){
			$this->db->where_in("c.id", $dep_id_array);
		}else{
			//如果为空，则用户没有任何权限
			$this->db->where('a.id', -1);
		}
		$this->db->limit($per_page, $startOffset);
		$this->db->order_by($orderField, $orderDirection);
		$result = $this->db->get()->result();
		$data['list'] = $result;

		$this->db->select('a.id');
		$this->db->from("{$this->tables[0]} as a");
		$this->db->join("{$this->tables[1]} as b", "a.id = b.cnt_id", "left");
		$this->db->join("{$this->tables[2]} as c", "b.dep_id = c.id", "left");
		if(!empty($dep_id_array)){
			$this->db->where_in("c.id", $dep_id_array);
		}else{
			//如果为空，则用户没有任何权限
			$this->db->where('a.id', -1);
		}
		$total = $this->db->get()->num_rows();
		$data['total'] = $total;
		
		return $data;
	}

	/**
	 * 根据条件查找联系人
	 */
	public function list_cnt_search(){
		$per_page = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
		$orderField = $this->input->post('orderField') ? $this->input->post('orderField') : 'a.id';
		$orderDirection = $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc';
		$startOffset = $this->input->post('pageNum') ? ($this->input->post('pageNum') - 1) * $per_page : 0;
		
		$dep = $this->get_dep_array();
		$dep_id_array = array();
		foreach($dep as $line){
			array_push($dep_id_array, $line['id']);
		}
		$data['dep'] = $dep;
		$data['tree'] = false;
		if ($this->input->post('dep'))
		{
			//有提交新的搜索
			$s_dep = $this->input->post('dep');
			$this->session->set_userdata('dep',$s_dep);
		}else{
			if ($this->input->post('page_dep'))
			{
				$s_dep = $this->session->userdata('dep');
			}
			else
			{
				//空白搜索
				$s_dep = false;
				$this->session->unset_userdata('dep');
			}
		}
		
		if ($this->input->post('cnt_num'))
		{
			$s_cnt_num = $this->input->post('cnt_num');
			$this->session->set_userdata('cnt_num',$s_cnt_num);
		}else{
			if ($this->input->post('page_cnt_num'))
			{
				$s_cnt_num = $this->session->userdata('cnt_num');
			}
			else
			{
				$s_cnt_num = false;
				$this->session->unset_userdata('cnt_num');
			}
		}
		
		if ($this->input->post('cnt_name'))
		{
			$s_cnt_name = $this->input->post('cnt_name');
			$this->session->set_userdata('cnt_name',$s_cnt_name);
		}else{
			if ($this->input->post('page_cnt_name'))
			{
				$s_cnt_name = $this->session->userdata('cnt_name');
			}
			else
			{
				//没有输入
				$s_cnt_name = false;
				$this->session->unset_userdata('cnt_name');
			}
		}
		
		if ($this->input->post('phone'))
		{
			$s_phone = $this->input->post('phone');
			$this->session->set_userdata('phone',$s_phone);
		}else{
			if ($this->input->post('page_phone')){
				$s_phone = $this->session->userdata('phone');
			}else{
				$s_phone = false;
				$this->session->unset_userdata('phone');
			}
		}
		
		if ($this->input->post('desc'))
		{
			$s_desc = $this->input->post('desc');
			$this->session->set_userdata('desc',$s_desc);
		}else{
			if($this->input->post('page_desc')){
				$s_desc = $this->session->userdata('desc');
			}else{
				$s_desc = false;
				$this->session->unset_userdata('desc');
			}
		}
		
		
		$this->db->select('a.id, a.cnt_num, a.cnt_name, a.phone, a.desc, a.create_time, a.update_time, c.dep_name');
		$this->db->from("{$this->tables[0]} as a");
		$this->db->join("{$this->tables[1]} as b", "a.id = b.cnt_id", "left");
		$this->db->join("{$this->tables[2]} as c", "b.dep_id = c.id", "left");
		if(!empty($dep_id_array)){
			$this->db->where_in("c.id", $dep_id_array);
		}else{
			//如果为空，则用户没有任何权限
			$this->db->where('a.id', -1);
		}
		
		if ($s_dep){
			//ALL时，$s_dep=0，不会进入，表示所有部门
			$this->db->where('c.id', $s_dep);
		}
		if ($s_cnt_num){
			echo $s_cnt_num;
			$this->db->like('a.cnt_num', $s_cnt_num);
		}
		if ($s_cnt_name){
			$this->db->like('a.cnt_name', $s_cnt_name);
		}
		if ($s_phone){
			$this->db->like('a.phone', $s_phone);
		}
		if ($s_desc){
			$this->db->like('a.desc', $s_desc);
		}
		
		$this->db->limit($per_page, $startOffset);
		$this->db->order_by($orderField, $orderDirection);
		$result = $this->db->get()->result();
		$data['list'] = $result;
		
		$this->db->select('a.id');
		$this->db->from("{$this->tables[0]} as a");
		$this->db->join("{$this->tables[1]} as b", "a.id = b.cnt_id", "left");
		$this->db->join("{$this->tables[2]} as c", "b.dep_id = c.id", "left");
		if(!empty($dep_id_array)){
			$this->db->where_in("c.id", $dep_id_array);
		}else{
			//如果为空，则用户没有任何权限
			$this->db->where('a.id', -1);
		}
		if ($s_dep){
			$this->db->where('c.id', $s_dep);
		}
		if ($s_cnt_num){
			$this->db->where('a.cnt_num', $s_cnt_num);
		}
		if ($s_cnt_name){
			$this->db->where('a.cnt_name', $s_cnt_name);
		}
		if ($s_phone){
			$this->db->where('a.phone', $s_phone);
		}
		if ($s_desc){
			$this->db->where('a.desc', $s_desc);
		}
		$total = $this->db->get()->num_rows();
		$data['total'] = $total;
		
		return $data;
		
	}
	
	/**
	 * 保存联系人信息，包括新增保存，修改保存
	 * @return string|number 1 => success;
	 */
	public function save_cnt(){
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		$this->form_validation->set_rules('cnt_name', '联系人姓名', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cnt_num', '联系人编号', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			return validation_errors();
		}
		else
		{
			$data = array(
					'cnt_num' => $this->input->post('cnt_num', true),
					'cnt_name' => $this->input->post('cnt_name', true),
					'phone' => $this->input->post('phone', true),
					'desc' => $this->input->post('desc', true),
					'update_time' => date('Y-m-d H:i:s')
					);
			$cnt_num = $this->is_exists($this->tables[0], 'cnt_num', $data['cnt_num']);
			$cnt_name = $this->is_exists($this->tables[0], 'cnt_name', $data['cnt_name']);
			$id = $this->input->post('id');
			if ($id)
			{
				$cnt_id = $this->is_exists($this->tables[0], 'id', $id);
				//更新保存
				if ($cnt_num && $cnt_num['id'] != $id)
					return '联系人编号已存在';
				if ($cnt_name && $cnt_name['id'] != $id)
					return '联系人姓名已存在';
				$this->db->trans_start();
				$this->update($this->tables[0], $id, $data);
				$this->db->trans_complete();
				if ($this->db->trans_status() == false)
				{
					return $this->db_error;
				}
				else
				{
					$this->applog->msg('成功修改联系人');
					return 1;
				}
			}else{
				//新增保存
				if($cnt_num)
				{
					return '联系人编号已存在';
				}
				if ($cnt_name){
					return '联系人姓名已存在';
				}
				$data['create_time'] = date('Y-m-d H:i:s');
				$this->db->trans_start();
				$this->create($this->tables[0], $data);
				$cnt_id = $this->db->insert_id();
				$data_rel = array(
						'cnt_id' => $cnt_id,
						'dep_id' => $this->input->post('dep', true)
						);
				$this->create($this->tables[1], $data_rel);
				$this->db->trans_complete();
				if ($this->db->trans_status() == false)
				{
					return $this->db_error;
				}
				else
				{
					$this->applog->msg('成功添加联系人');
					return 1;
				}
			}
		}
	}
	
	
	/**
	 * 删除指定的联系人
	 */
	public function delete_cnt($id){
		
		$this->db->trans_start();
		$this->delete($this->tables[0], $id);
		$this->db->delete($this->tables[1], array('cnt_id' => $id));
		$this->db->trans_complete();
		if ($this->db->trans_status() == false)
		{
			return $this->db_error;
		}
		else
		{
			$this->applog->msg('删除联系人');
			return 1;
		}
	}
	
	/**
	 * 获取要修改的联系人信息
	 */
	public function get_cnt($id){
		
		$this->db->select('a.id, a.cnt_num, a.cnt_name, a.phone, a.desc, 
				a.create_time, a.update_time, b.dep_id, c.dep_name');
		$this->db->from("{$this->tables[0]} as a");
		$this->db->join("{$this->tables[1]} as b", "a.id = b.cnt_id", "left");
		$this->db->join("{$this->tables[2]} as c", "b.dep_id = c.id", "left");
		$this->db->where(array('a.id' => $id));
		$result = $this->db->get()->result_array();
		return $result[0];
	}

	/**
	 * 返回对象形式的部门
	 */
	public function get_dep_object(){
		$rs = $this->get_dep();
		return $rs->result();
	}
	
	/**
	 * 返回数组形式的部门
	 */
	public function get_dep_array(){
		$rs = $this->get_dep();
		return $rs->result_array();
	}
	/**
	 * 获取被管理的所有部门
	 * @return unknown
	 */
	public function get_dep(){
		
		if ($this->session->userdata('username') == 'root'){
			$this->db->select('id, dep_name, dep_num');
			$this->db->from("{$this->tables[2]}");
		}else{
			$user_id = $this->session->userdata('id');
			$this->db->select('b.id, b.dep_name, b.dep_num');
			$this->db->from("{$this->tables[4]} as a");
			$this->db->join("{$this->tables[2]} as b", "a.dep_id = b.id", "left");
			$this->db->where(array('a.user_id' => $user_id));			
		}
		return $this->db->get();
	}



	public function __destruct(){
		parent::__destruct();
	}

}

/* End of file cnt_model.php */
/* Location: ./application/models/cnt_model.php */
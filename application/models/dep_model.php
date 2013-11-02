<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 *@function 部门管理模型
 *@date     2013-10-14
 *@author   gerui
 *@email    <forgerui@gmail.com>
 */
class Dep_model extends MY_Model{

	protected $tables = array(
			'cnt',				//0
			'cnt_dep_rel',		//1
			'dep',				//2
			'user',				//3
			'user_dep_rel'		//4
			);
	
	private $all_dep;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 列出所有部门
	 * @param  $dep_id_array 用户所有部门id的数组
	 * @return unknown
	 */
	public function list_dep($dep_id_array){
		$per_page = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
		$orderField = $this->input->post('orderField') ? $this->input->post('orderField') : 'a.id';
		$orderDirection = $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc';
		$startOffset = $this->input->post('pageNum') ? ($this->input->post('pageNum') - 1) * $per_page : 0;

		$this->db->select('a.id, a.dep_num, a.dep_name, a.lvl, a.father_dep_id, a.desc,
			 b.dep_name as father_dep_name, a.create_time, a.update_time');
		$this->db->from("{$this->tables[2]} as a");
		$this->db->join("{$this->tables[2]} as b", 'a.father_dep_id = b.id', 'left');
		if (!empty($dep_id_array)){
			$this->db->where_in('a.id', $dep_id_array);
		}
		else{
			$this->db->where('a.id', 'xxx');
		}
		$this->db->limit($per_page, $startOffset);
		$this->db->order_by($orderField, $orderDirection);
		$result = $this->db->get()->result();
		
		$data['list'] = $result;
		
		$total = 0;
		if (!empty($dep_id_array))
		{
			$this->db->select('a.id');
			$this->db->from("{$this->tables[2]} as a");
			$this->db->join("{$this->tables[2]} as b", 'a.father_dep_id = b.id', 'left');
			$this->db->where_in('a.id', $dep_id_array);
			$total = $this->db->get()->num_rows();
		}
		$data['total'] = $total;
		return $data;
		
	}

	
	/**
	 * 搜索所有部门
	 */
	public function list_dep_search($dep_id_array){
		$per_page = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
		$orderField = $this->input->post('orderField') ? $this->input->post('orderField') : 'a.id';
		$orderDirection = $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc';
		$startOffset = $this->input->post('pageNum') ? ($this->input->post('pageNum') - 1) * $per_page : 0;
		
		if ($this->input->post('dep_num'))
		{
			$s_dep_num = $this->input->post('dep_num');
			$this->session->set_userdata('dep_num', $s_dep_num);
		}else{
			if ($this->input->post('page_dep_num')){
				$s_dep_num = $this->input->post('dep_num');
			}else{
				$s_dep_num = false;
				$this->session->unset_userdata('dep_num');
			}
		}
		
		if ($this->input->post('dep_name'))
		{
			$s_dep_name = $this->input->post('dep_name');
			$this->session->set_userdata('dep_name', $s_dep_name);
		}else{
			if ($this->input->post('page_dep_name')){
				$s_dep_name = $this->input->post('dep_name');
			}else{
				$s_dep_name = false;
				$this->session->unset_userdata('dep_name');
			}
		}
		
		if ($this->input->post('dep_desc'))
		{
			$s_dep_desc = $this->input->post('dep_desc');
			$this->session->set_userdata('dep_desc', $s_dep_desc);
		}else{
			if ($this->input->post('page_dep_desc')){
				$s_dep_desc = $this->input->post('dep_desc');
			}else{
				$s_dep_desc = false;
				$this->session->unset_userdata('dep_desc');
			}
		}
		
		$this->db->select('a.id, a.dep_num, a.dep_name, a.lvl, a.father_dep_id, a.desc,
				 b.dep_name as father_dep_name, a.create_time, a.update_time');
		$this->db->from("{$this->tables[2]} as a");
		$this->db->join("{$this->tables[2]} as b", 'a.father_dep_id = b.id', 'left');
		$this->db->where_in('a.id', $dep_id_array);
		if ($s_dep_num){
			$this->db->like('a.dep_num', $s_dep_num);
		}
		if ($s_dep_name){
			$this->db->like('a.dep_name', $s_dep_name);
		}
		if ($s_dep_desc){
			$this->db->like('a.desc', $s_dep_desc);
		}
		$this->db->limit($per_page, $startOffset);
		$this->db->order_by($orderField, $orderDirection);
		$result = $this->db->get()->result();
		$data['list'] = $result;
		
		$this->db->select('a.id, a.dep_num, a.dep_name, a.lvl, a.father_dep_id, a.desc,
				 b.dep_name as father_dep_name, a.create_time, a.update_time');
		$this->db->from("{$this->tables[2]} as a");
		$this->db->join("{$this->tables[2]} as b", 'a.father_dep_id = b.id', 'left');
		$this->db->where_in('a.id', $dep_id_array);
		if ($s_dep_num){
			$this->db->like('a.dep_num', $s_dep_num);
		}
		if ($s_dep_name){
			$this->db->like('a.dep_name', $s_dep_name);
		}
		if ($s_dep_desc){
			$this->db->like('a.desc', $s_dep_desc);
		}
		$total = $this->db->get()->num_rows();
		$data['total'] = $total;
		return $data;
		
	}
	
	/**
	 * 删除部门
	 */
	public function delete_dep($id){
		$this->db->trans_start();
		$this->delete($this->tables[2], $id);
		$this->db->delete($this->tables[1], array('dep_id' => $id));
		$this->db->delete($this->tables[4], array('dep_id' => $id));
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
	 * 获取一个部门的信息
	 */
	public function get_dep($id){
		return $this->read($this->tables[2], $id);
	}
	
	/**
	 * 保存部门信息
	 */
	public function save_dep(){
		$data = array(
				"dep_num" => $this->input->post('dep_num', true),
				"dep_name" => $this->input->post('dep_name', true),
				"desc" => $this->input->post('desc', true),
				'lvl' => $this->input->post('lvl', true),
				'update_time' => date('Y-m-d H:i:s')
				);
		$data2 = array(
				'user_id' => $this->session->userdata('id')
				);
		
		if ($data['lvl'] > 0 ){
			$data['father_dep_id'] = $this->input->post('dep', true);
			$father_dep = $this->read($this->tables[2], $data['father_dep_id']);
			$data['lvl'] = $father_dep['lvl'] + 1;
		}else{
			//根部门
			$data['father_dep_id'] = -1;
		}
		$id = $this->input->post('id');
		$dep_num = $this->is_exists($this->tables[2], 'dep_num', $data['dep_num']);
		$dep_name = $this->is_exists($this->tables[2], 'dep_name', $data['dep_name']);
		if ($id)
		{
			//更新保存
			if ($dep_num && $dep_num['id'] != $id)
				return '部门编号已存在';
			if ($dep_name && $dep_name['id'] != $id)
				return '部门名称已存在';
			$old_lvl = '';
			$update_dep = $this->is_exists($this->tables[2], 'id', $id);
			$old_lvl = $update_dep['lvl'];
			$dep_array = $this->get_chil_dep_all($id, $old_lvl);
			
			$this->db->trans_start();
			$this->update($this->tables[2], $id, $data);
			//修改子部门的lvl
			if(!empty($dep_array)){
				$this->update_chil_dep($dep_array, $old_lvl, $data['lvl']);
			}
			$this->db->trans_complete();
			if ($this->db->trans_status() == false)
			{
				return $this->db_error;
			}
			else
			{
				$this->applog->msg('成功修改部门');
				return 1;
			}
			
		}
		else{
			//新增保存
			if($dep_num)
			{
				return '部门编号已存在';
			}
			if ($dep_name){
				return '部门名称已存在';
			}
			$data['create_time'] = date('Y-m-d H:i:s');
			
			$this->db->trans_start();
			$this->create($this->tables[2], $data);
			$data2['dep_id'] = $this->db->insert_id();
			$this->create($this->tables[4], $data2);
			$this->db->trans_complete();
			if ($this->db->trans_status() == false)
			{
				return $this->db_error;
			}
			else
			{
				$this->applog->msg('成功添加部门');
				return 1;
			}
		}
	}
	
	/**
	 * 修改子部门的等级
	 * @param $dep_id 父部门id
	 * @param $old_lvl 父部门未修改之前的lvl
	 * @param $new_lvl 父部门修改之后的lvl
	 */
	public function update_chil_dep($dep_array, $old_lvl, $new_lvl){
		foreach($dep_array as $line){
			$data['lvl'] = $line['lvl'] - $old_lvl + $new_lvl;
			$this->update($this->tables[2], $line['id'], $data);
		}
	}
	
	
	/**
	 * 获取子部门
	 * @param $dep_id 父部门id
	 * @param $old_lvl 父部门未修改之前的lvl
	 * @param $new_lvl 父部门修改之后的lvl
	 */
	public function get_chil_dep_all($dep_id, $old_lvl){
		$dep_array = $this->get_chil_dep_array($dep_id, $old_lvl);
		if(!empty($dep_array))
		{
			$this->all_dep = array();
			$this->get_dep_all_recur($dep_array);
			return $this->all_dep;
		}
		return null;
	}

	/**
	 * 递归出所有子部门
	 * @param unknown_type $dep_array
	 */
	public function get_dep_all_recur($dep_array)
	{
		if (!empty($dep_array)){
			foreach($dep_array as $line){
				array_push($this->all_dep, $line);
				$chil_dep_array = $this->get_chil_dep_array($line['id'], $line['lvl']);
				if (!empty($chil_dep_array)){
					//如果还有子部门，继续递归
					$this->get_dep_all_recur($chil_dep_array);
				}
			}
		}
	}
	
	/**
	 * 获取子部门的数组形式
	 * @param  $father_id
	 * @param  $father_lvl
	 */
	public function get_chil_dep_array($father_id, $father_lvl){
		$rs = $this->get_chil_dep($father_id, $father_lvl);
		return $rs->result_array();
	}
	
	/**
	 * 根据父部门从数据库中获取子部门
	 */
	public function get_chil_dep($father_id, $father_lvl){
	
		$chil_lvl = $father_lvl + 1;
		$this->db->select('id, dep_name, dep_num, father_dep_id, lvl, desc');
		$this->db->from($this->tables[2]);
	
		$this->db->where(array('father_dep_id' => $father_id));
		$this->db->where(array('lvl' => $chil_lvl));
		$chil_dep = $this->db->get();
		return $chil_dep;
	}
	
	public function __destruct(){
		parent::__destruct();
	}

}

/* End of file dep_model.php */
/* Location: ./application/models/dep_model.php */
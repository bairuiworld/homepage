<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 *@function 联系人管理模型
 *@date     2013-10-13
 *@author   gerui
 *@email    <forgerui@gmail.com>
 */
class Cnt_tree_model extends MY_Model{

	protected $tables = array(
			'cnt',				//0
			'cnt_dep_rel',		//1
			'dep',				//2
			'user',				//3
			'user_dep_rel'		//4
			);
	private  $all_dep = array();
	private  $all_chil_dep = array();
	private $dep_tree = '';

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
		
		$dep = $this->get_dep_all_uniq();
		$dep_id_array = array();
		$dep_array = array();
		foreach($dep as $line){
			if (!in_array($line['id'], $dep_id_array)){
				//没有该部门就加入，有该部门就跳过
				array_push($dep_id_array, $line['id']);
				array_push($dep_array, $line);
			}
		}
		$data['dep'] = $dep_array;
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
	 * 获取一个部门下的所有子部门
	 * @param  $dep_id 部门的id
	 * @return multitype:
	 */
	public function get_all_chil($dep_id){
		$dep_info = $this->db->get_where($this->tables[2], array('id' => $dep_id))->result_array();
		$this->all_dep = array();
		$this->get_dep_all_recur($dep_info);
		$all_chil = $this->all_dep;
		return $all_chil;
	}
	
	/**
	 * 列出所有联系人
	 * @return unknown
	 */	
	public function list_cnt_tree($dep_id){
	
		$per_page = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
		$orderField = $this->input->post('orderField') ? $this->input->post('orderField') : 'a.id';
		$orderDirection = $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc';
		$startOffset = $this->input->post('pageNum') ? ($this->input->post('pageNum') - 1) * $per_page : 0;
		
		//获取所有该部门的下属部门 (包含自己)
		$all_chil = $this->get_all_chil($dep_id);
		$all_chil_id_array = array();
		foreach($all_chil as $line){
			if (!in_array($line['id'], $all_chil_id_array)){
				//没有该部门就加入，有该部门就跳过
				array_push($all_chil_id_array, $line['id']);
			}
		}
		
		//用户的全部的部门，用于select显示
		$dep = $this->get_dep_all_uniq();
		//print_r($dep);
		//对遍历出来的部门去重
		$dep_id_array = array();
		$dep_array = array();
		foreach($dep as $line){
			if (!in_array($line['id'], $dep_id_array)){
				//没有该部门就加入，有该部门就跳过
				array_push($dep_id_array, $line['id']);
				array_push($dep_array, $line);
			}
		}
		$data['dep'] = $dep_array;
		$data['tree'] = true;
		$data['dep_id'] = $dep_id;
		$data['treeFlag'] = true;
	
		$this->db->select('a.id, a.cnt_num, a.cnt_name, a.phone, a.desc, a.create_time, a.update_time, c.dep_name');
		$this->db->from("{$this->tables[0]} as a");
		$this->db->join("{$this->tables[1]} as b", "a.id = b.cnt_id", "left");
		$this->db->join("{$this->tables[2]} as c", "b.dep_id = c.id", "left");
		$this->db->where_in('c.id', $all_chil_id_array);
		$this->db->limit($per_page, $startOffset);
		$this->db->order_by($orderField, $orderDirection);
		$result = $this->db->get()->result();
		$data['list'] = $result;
	
		$this->db->select('a.id');
		$this->db->from("{$this->tables[0]} as a");
		$this->db->join("{$this->tables[1]} as b", "a.id = b.cnt_id", "left");
		$this->db->join("{$this->tables[2]} as c", "b.dep_id = c.id", "left");
		$this->db->where_in('c.id', $all_chil_id_array);
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
		
		$dep = $this->get_dep_all_uniq();
		$dep_id_array = array();
		$dep_array = array();
		foreach($dep as $line){
			array_push($dep_id_array, $line['id']);
			array_push($dep_array, $line);
		}
		$data['dep'] = $dep_array;
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
			//ALL时，$s_dep=0，不会进入，表示所有部门
			$this->db->where('c.id', $s_dep);
		}
		if ($s_cnt_num){
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
			//提交新的部门
			$dep = $this->input->post('dep', true);
			
			$cnt_num = $this->is_exists($this->tables[0], 'cnt_num', $data['cnt_num']);
			$cnt_name = $this->is_exists($this->tables[0], 'cnt_name', $data['cnt_name']);
			$id = $this->input->post('id');
			if ($id)
			{
				$old_dep = $this->input->post('dep_id', true);
				//更新保存
				if ($cnt_num && $cnt_num['id'] != $id)
					return '联系人编号已存在';
				if ($cnt_name && $cnt_name['id'] != $id)
					return '联系人姓名已存在';
				$this->db->trans_start();
				$this->update($this->tables[0], $id, $data);
				if ($dep != $old_dep)
				{
					//修改了所属的部门
					$data2 = array(
							'cnt_id' => $id,
							'dep_id' => $dep
							);
					$this->db->where(array('cnt_id' => $id, 'dep_id' => $old_dep));
					$this->db->update($this->tables[1], $data2);
				}
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
						'dep_id' => $dep
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
	 * @param $id 联系人id
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
	 * 循环获取用户所有部门
	 */
	public function get_dep_tree(){
		//获取所有在数据库中写入的部门
		$dep = $this->get_dep_root_object();
		
		$this->whole_dep = $dep;
		$HTML=<<<HTML
		<div class="accordion" fillSpace="sidebar">
    	<div class="accordionHeader">
	    <h2><span>Folder</span>部门</h2>
	</div>
	<div class="accordionContent">
		<ul class="tree">
HTML;
		
		$this->dep_tree = $HTML;
		$lvl = 0;
		$str = $this->get_dep_recur($dep, $lvl);
		$this->dep_tree .= '</ul></div></div>';
		return $this->dep_tree;
	}
	
	/**
	 * 递归获取所有部门
	 */
	public function get_dep_recur($dep, $lvl){

		if (!empty($dep)){
			$is_root = true;
			foreach($dep as $k => $line){
				$url = site_url("cnt/list_cnt_tree/$line->id");
				$dep_name = $line->dep_name;
				
				//判断根层的部门是否是其它部门子层
				if ($lvl == 0){
					foreach($dep as $root){
						//遍历所有根层，判断是否存在父群组
						if ($line->father_dep_id == $root->id){
							$is_root = false;
							break;
						}
					}
					if(!$is_root){
						continue;
					}
				}
				
				$chil_dep = $this->get_chil_dep_object($line->id, $line->lvl);
				
				$HTML=<<<HTML
	    		<li>
	    		<a href="$url" target="navTab" rel="list_cnt" fresh="true">$dep_name</a>
	    		<ul>
HTML;
				$this->dep_tree .= $HTML;
				
				$this->get_dep_recur($chil_dep, $lvl + 1);
				$this->dep_tree .= '</ul></li>';
			}
		}
	}
	
	/**
	 * 数组形式
	 * @param unknown_type $father_id
	 * @param unknown_type $father_lvl
	 */
	public function get_chil_dep_array($father_id, $father_lvl){
		$rs = $this->get_chil_dep($father_id, $father_lvl);
		return $rs->result_array();
	}
	
	/**
	 * 对象形式
	 * @param unknown_type $father_id
	 * @param unknown_type $father_lvl
	 */
	public function get_chil_dep_object($father_id, $father_lvl){
		$rs = $this->get_chil_dep($father_id, $father_lvl);
		return $rs->result();
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
	
	/**
	 * 返回对象形式的部门
	 */
	public function get_dep_root_object(){
		$rs = $this->get_dep_root();
		return $rs->result();
	}
	
	/**
	 * 返回数组形式的部门
	 */
	public function get_dep_root_array(){
		$rs = $this->get_dep_root();
		return $rs->result_array();
	}
	/**
	 * 获取被管理的所有0级部门，即根部门
	 * @return unknown
	 */
	public function get_dep_root(){
		
		if ($this->session->userdata('username') == 'root'){
			$this->db->select('id, dep_name, dep_num, father_dep_id, lvl, desc');
			$this->db->from("{$this->tables[2]}");
			$this->db->where(array('lvl' => 0));
		}else{
			$user_id = $this->session->userdata('id');
			$this->db->select('b.id, b.dep_name, b.dep_num, b.father_dep_id, b.lvl, b.desc');
			$this->db->from("{$this->tables[4]} as a");
			$this->db->join("{$this->tables[2]} as b", "a.dep_id = b.id", "left");
			$this->db->where(array('a.user_id' => $user_id));
		}
		return $this->db->get();
	}
	
	/**
	 * 去除其中的重复部门
	 * @return multitype:
	 */
	public function get_dep_all_object_uniq(){
		$dep = $this->get_dep_all_object();
		$dep_id_array = array();
		$dep_array = array();
		foreach($dep as $line){
			if (!in_array($line->id, $dep_id_array)){
				//没有该部门就加入，有该部门就跳过
				array_push($dep_id_array, $line->id);
				array_push($dep_array, $line);
			}
		}
		return $dep_array;
	}
	
	/**
	 * 除去自己和自己的子部门剩下部门
	 */
	public function get_dep_all_uniq_excl_self($dep_id){
		$dep = $this->get_dep_all_excl_self($dep_id);
		$dep_id_array = array();
		$dep_array = array();
		foreach($dep as $line){
			if (!in_array($line['id'], $dep_id_array)){
				//没有该部门就加入，有该部门就跳过
				array_push($dep_id_array, $line['id']);
				array_push($dep_array, $line);
			}
		}
		return $dep_array;
	}
	
	/**
	 * 获取除去自己和自己的子部门剩下部门
	 */
	public function get_dep_all_excl_self($dep_id){
		//数据库中有直接关系的部门
		$dir_dep = $this->get_dep_root_array();
		//遍历直接关联的部门，获取下级部门
		$this->all_dep = array();
		if (!empty($dir_dep)){
			$this->get_dep_all_recur_excl_self($dir_dep, $dep_id);
		}else{
			//没有可以管理的部门
			return $dir_dep;
		}
		$dep = $this->all_dep;
		
		return $dep;
	}
	
	/**
	 * 递归出所有子部门，如果遇到部门则跳过
	 * @param unknown_type $dep_array
	 */
	public function get_dep_all_recur_excl_self($dep_array, $dep_id)
	{
		if (!empty($dep_array)){
			foreach($dep_array as $line){
				if($line['id'] != $dep_id){
					array_push($this->all_dep, $line);
					$chil_dep_array = $this->get_chil_dep_array($line['id'], $line['lvl']);
					if (!empty($chil_dep_array)){
						//如果还有子部门，继续递归
						$this->get_dep_all_recur_excl_self($chil_dep_array, $dep_id);
					}
				}
			}
		}
	}
	
	/**
	 * 去除其中的重复部门，
	 * @return 各个部门数组
	 */
	public function get_dep_all_uniq(){
		$dep = $this->get_dep_all();
		$dep_id_array = array();
		$dep_array = array();
		foreach($dep as $line){
			if (!in_array($line['id'], $dep_id_array)){
				//没有该部门就加入，有该部门就跳过
				array_push($dep_id_array, $line['id']);
				array_push($dep_array, $line);
			}
		}
		return $dep_array;
	}
	

	/**
	 * 去除其中的重复部门，最后的部门id
	 * @return multitype:
	 */
	public function get_dep_all_uniq_id(){
		$dep = $this->get_dep_all();
		$dep_id_array = array();
		foreach($dep as $line){
			if (!in_array($line['id'], $dep_id_array)){
				//没有该部门就加入，有该部门就跳过
				array_push($dep_id_array, $line['id']);
			}
		}
		return $dep_id_array;
	}
	
	/**
	 * 获取被管理的所有部门，递归得出
	 * @return unknown
	 */
	public function get_dep_all_object(){
	
		//数据库中有直接关系的部门
		$dir_dep = $this->get_dep_root_object();
		//遍历直接关联的部门，获取下级部门
		$this->all_dep = array();
		if (!empty($dir_dep)){
			$this->get_dep_all_recur_object($dir_dep);
		}else{
			//没有可以管理的部门
			return $dir_dep;
		}
		$dep = $this->all_dep;
	
		return $dep;
	
	}
	
	
	/**
	 * 递归出所有子部门
	 * @param unknown_type $dep_array
	 */
	public function get_dep_all_recur_object($dep_array)
	{
		foreach($dep_array as $line){
			array_push($this->all_dep, $line);
			$chil_dep_array = $this->get_chil_dep_object($line->id, $line->lvl);
			if (!empty($chil_dep_array)){
				//如果还有子部门，继续递归
				$this->get_dep_all_recur_object($chil_dep_array);
			}
		}
	}
	
	/**
	 * 获取被管理的所有部门，递归得出
	 * @return unknown
	 */
	public function get_dep_all(){
	
		//数据库中有直接关系的部门
		$dir_dep = $this->get_dep_root_array();
		//遍历直接关联的部门，获取下级部门
		$this->all_dep = array();
		if (!empty($dir_dep)){
			$this->get_dep_all_recur($dir_dep);
		}else{
			//没有可以管理的部门
			return $dir_dep;
		}
		$dep = $this->all_dep;
		
		return $dep;
		
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
	

	

	public function __destruct(){
		parent::__destruct();
	}

}

/* End of file cnt_tree_model.php */
/* Location: ./application/models/cnt_tree_model.php */
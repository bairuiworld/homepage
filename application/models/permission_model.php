<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 权限模型
 *
 * 权限及用户的处理模型
 *
 * @package		app
 * @subpackage	core
 * @category	model
 * @author		bruce.yang<kissjava@vip.qq.com>
 *
 */
class Permission_model extends MY_Model
{
    /**
     * 表名数组
     * @var array
     */
    protected $tables = array(
            'user', 			//0
            'user_dep_rel',		//1
            'dep'				//2
    );

    public function __construct ()
    {
        parent::__construct();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    /**
     * ********************************模块**********************************
     */

    /**
     * 列出模块(分页)
     *
     * @return array 返回数组array('total'=>表记录总数,'list'=>记录列表)
     */
    public function list_module ()
    {

        $total_rows = $this->count($this->tables[0]);
        // 每页显示的记录条数，默认20条
        $per_page = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;

        $data['total'] = $total_rows;
        // list_data(表,抓取记录数，偏离量,排序字段,排序方法);
        $data['list'] = $this->list_data($this->tables[0], $per_page, $this->input->post('pageNum') ? ($this->input->post('pageNum') - 1) *
                 $per_page : 0, $this->input->post('orderField') ? $this->input->post('orderField') : 'id',
                        $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc');

        return $data;
    }

    /**
     * 列出模块（全部）
     *
     * @return array 返回对象数组
     */
    public function list_module_all ()
    {
        return $this->list_all($this->tables[0]);
    }

    /**
     * 保存模块
     *
     * @return int|string 成功返回1，否则返回出错信息
     */
    public function save_module ()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<li>', '</li>');
        $this->form_validation->set_rules('title', '模块名称', 'trim|required|xss_clean');
        $this->form_validation->set_rules('code', '模块代码', 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            return validation_errors();
        } else {
            $data = array(
                    'title' => $this->input->post('title', TRUE),
                    'code' => $this->input->post('code', TRUE),
                    'add_time' => date("Y-m-d H:i:s"),
                    'update_time' => date("Y-m-d H:i:s")
            );

            // 检查重复
            $rs_title = $this->is_exists($this->tables[0], 'title', $data['title']);
            $rs_code = $this->is_exists($this->tables[0], 'code', $data['code']);

            if ($this->input->post('id')) { // 更新保存

                unset($data['add_time']);

                if ($rs_title and $rs_title['id'] != $this->input->post('id'))
                    return '模块名已经存在';

                if ($rs_code and $rs_code['id'] != $this->input->post('id'))
                    return '模块代码已经存在';

                if ($this->update($this->tables[0], $this->input->post('id'), $data)) {
                    return 1;
                } else {

                    return $this->db_error;
                }
            } else { // 新增保存

                if ($rs_title or $rs_code) {
                    return '模块名或模块代码已经存在';
                }
                if ($this->create($this->tables[0], $data)) {
                    return 1;
                } else {
                    return $this->db_error;
                }
            }
        }
    }

    /**
     * 返回一个模块信息数组
     *
     * @param int $id 模块id
     * @return array 模块数组信息
     */
    public function get_module ($id)
    {
        return $this->read($this->tables[0], $id);
    }

    /**
     * 删除模块
     *
     * @param int $id 模块id
     * @return int|string 成功返回1，否则返回出错信息
     */
    public function delete_module ($id)
    {
        // 判断是否有功能在模块下，有则停止删除
        if($this->is_exists($this->tables[1], 'perm_module_id', $id))
            return "在模块下存在功能，请先删除该模块下的功能！";
        if ($this->delete($this->tables[0], $id)) {
            return 1;
        } else {
            return $this->db_error;
        }
    }

    /**
     * ********************************模块**********************************
     */

    /**
     * ********************************功能**********************************
     */

    /**
     * 列出功能(分页)
     *
     * @return array 返回数组array('total'=>表记录总数,'list'=>记录列表)
     */
    public function list_action ()
    {
        // 每页显示的记录条数，默认20条
        $per_page = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;

        $data['total'] = $this->count($this->tables[1]);
        // list_data(表,抓取记录数，偏离量,排序字段,排序方法);
        $data['list'] = $this->list_data($this->tables[1], $per_page, $this->input->post('pageNum') ? ($this->input->post('pageNum') - 1) * $per_page : 0, $this->input->post('orderField') != '' ? $this->input->post('orderField') : 'perm_subject',
                        $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'asc');

        $modules = $this->list_module_all();

        foreach ($data['list'] as &$temp_action) {
            foreach ($modules as $module) {
                if ($module->id == $temp_action->perm_module_id) {
                    $temp_action->module_title = $module->title;
                    continue;
                }
            }
        }
        return $data;
    }

    /**
     * 取得所有功能数据
     *
     * @return array 返回对象数组
     */
    public function list_action_all ()
    {
        $this->db->order_by('perm_subject, order_no desc');
        return $this->db->get($this->tables[1])->result();
    }

    /**
     * 取得所有功能主题数据
     *
     * @return array 返回对象数组
     */
    public function list_subject ()
    {
        $sql = "SELECT distinct perm_subject, perm_module_id FROM (`plmis_perm_data`)";
        return $this->db->query($sql)->result();
    }

    /**
     * 保存功能
     *
     * @return int|string 成功返回1，否则返回出错信息
     */
    public function save_action ()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<li>', '</li>');
        $this->form_validation->set_rules('perm_module_id', '所属模块', 'trim|required|xss_clean');
        $this->form_validation->set_rules('perm_subject', '功能', 'trim|required|xss_clean');
        $this->form_validation->set_rules('perm_name', '动作', 'trim|required|xss_clean');
        $this->form_validation->set_rules('perm_key', '代码', 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            return validation_errors();
        } else {
            $data = array(
                    'perm_module_id' => $this->input->post('perm_module_id', TRUE),
                    'perm_subject' => $this->input->post('perm_subject', TRUE),
                    'order_no' => $this->input->post('order_no', TRUE),
                    'perm_name' => $this->input->post('perm_name', TRUE),
                    'perm_key' => $this->input->post('perm_key', TRUE),
                    'add_time' => date("Y-m-d H:i:s"),
                    'update_time' => date("Y-m-d H:i:s")
            );

            // 检查重复
            $array = array(
                    'perm_subject' => $data['perm_subject'],
                    'perm_name' => $data['perm_name']
            );
            $this->db->from($this->tables[1]);
            $this->db->where($array);
            $query = $this->db->get();

            $num_rows = $query->num_rows();
            $row = $query->row();

            $rs_perm_key = $this->is_exists($this->tables[1], 'perm_key', $data['perm_key']);

            if ($this->input->post('id')) { // 更新保存

                unset($data['add_time']);

                if ($num_rows > 0) { // 该功能的动作已经已经存在
                    if ($row->perm_module_id == $data['perm_module_id']) { // 同时属于同一个模块
                        if ($this->input->post('id') != $row->id) { // 并且不是本次修改的记录
                            return '该功能的动作在选择模块已经存在';
                        }
                    }
                }

                if ($rs_perm_key) { // 功能key已经存在
                    if ($rs_perm_key['perm_module_id'] == $data['perm_module_id']) { // 同时属于同一个模块
                        if ($this->input->post('id') != $rs_perm_key['id']) { // 并且不是本次修改的记录
                            return '代码在选择模块中已经存在';
                        }
                    }
                }

                if ($this->update($this->tables[1], $this->input->post('id'), $data)) {
                    return 1;
                } else {

                    return $this->db_error;
                }
            } else { // 新增保存

                if ($num_rows > 0) { // 该功能的动作已经已经存在
                    if ($row->perm_module_id == $data['perm_module_id'])
                        return '该功能的动作在选择模块中已经存在';
                }

                if ($rs_perm_key) { // 功能key已经存在
                    if ($rs_perm_key['perm_module_id'] == $data['perm_module_id'])
                        return '代码在选择模块中已经存在';
                }

                if ($this->create($this->tables[1], $data)) {
                    return 1;
                } else {
                    return $this->db_error;
                }
            }
        }
    }

    /**
     * 返回一个功能信息数组
     *
     * @param int $id 功能id
     * @return array 功能信息数组
     */
    public function get_action ($id)
    {
        return $this->read($this->tables[1], $id);
    }

    /**
     * 删除功能
     *
     * @return int|string 成功返回1，否则返回出错信息
     */
    public function delete_action ($id)
    {
        $this->db->trans_start();

        $this->delete($this->tables[1],$id);
        //同步删除已经分配给用户和角色的权限数据，以及用户禁用的数据
        $this->db->delete($this->tables[3], array('perm_id' => $id));
        $this->db->delete($this->tables[4], array('perm_id' => $id));
        $this->db->delete($this->tables[6], array('perm_id' => $id));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return 1;
        } else {
            return $this->db_error;
        }
    }

    /**
     * 取得所有权限数据，即所有的模块和功能
     *
     * @return array 返回数组array('modules'=>模块列表,'actions'=>功能列表)
     */
    public function get_all_perms ()
    {
        // 取得模块数据（控制器）
        $modules = $this->list_module_all();
        // 取得功能数据（控制器里的方法）
        $actions = $this->list_action_all();

        $data['modules'] = $modules;
        $data['actions'] = $actions;

        return $data;
    }

    /**
     * ********************************功能**********************************
     */

    /**
     * ********************************角色**********************************
     */

    /**
     * 列出角色(分页)
     *
     * @return array 返回数组array('total'=>表记录总数,'list'=>记录列表)
     */
    public function list_role ()
    {

        // 每页显示的记录条数，默认20条
        $per_page = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;

        $data['total'] = $this->count($this->tables[2]);
        // list_data(表,抓取记录数，偏离量,排序字段,排序方法);
        $data['list'] = $this->list_data($this->tables[2], $per_page, $this->input->post('pageNum') ? ($this->input->post('pageNum') - 1) *
                 $per_page : 0, $this->input->post('orderField') ? $this->input->post('orderField') : 'id',
                        $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc');

        return $data;
    }

    /**
     * 保存角色
     *
     * @return int|string 成功返回1，否则返回出错信息
     */
    public function save_role ()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<li>', '</li>');
        $this->form_validation->set_rules('role_name', '角色名称', 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            return validation_errors();
        } else {
            $data = array(
                    'role_name' => $this->input->post('role_name', TRUE),
                    'description' => $this->input->post('description', TRUE),
                    'add_time' => date("Y-m-d H:i:s"),
                    'update_time' => date("Y-m-d H:i:s")
            );

            // 检查重复
            $rs_role_name = $this->is_exists($this->tables[2], 'role_name', $data['role_name']);

            if ($this->input->post('id')) { // 更新保存

                unset($data['add_time']);

                if ($rs_role_name and $rs_role_name['id'] != $this->input->post('id'))
                    return '角色已经存在';

                if ($this->update($this->tables[2], $this->input->post('id'), $data)) {
                    $this->applog->msg('更新角色');
                    return 1;
                } else {
                    return $this->db_error;
                }
            } else { // 新增保存

                if ($rs_role_name) {
                    return '角色已经存在';
                }
                if ($this->create($this->tables[2], $data)) {
                    $this->applog->msg('新增角色');
                    return 1;
                } else {
                    return $this->db_error;
                }
            }
        }
    }

    /**
     * 返回一个角色信息数组
     *
     * @param int $id 角色id
     * @return array 角色信息数组
     */
    public function get_role ($id)
    {
        return $this->read($this->tables[2], $id);
    }

    /**
     * 删除角色
     *
     * @param int $id 角色id
     * @return int|string 成功返回1，否则返回出错信息
     */
    public function delete_role ($id)
    {
        $this->db->trans_start();

        $this->delete($this->tables[2],$id);
        //同步删除已经分配给用户角色数据，以及角色权限的数据
        $this->db->delete($this->tables[5], array('role_id' => $id));
        $this->db->delete($this->tables[3], array('role_id' => $id));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->applog->msg('删除角色');
            return 1;
        } else {
            return $this->db_error;
        }
    }

    /**
     * 取得指定角色的权限字串数组
     *
     * @param int $id 角色id
     * @return array 权限字符串数组
     */
    public function get_role_perm_string ($id)
    {
        $role_perms = $this->list_all_where($this->tables[3], 'role_id', $id);
        $perms_array = array();
        if ($role_perms) {
            foreach ($role_perms as $role_perm) {
                $this->db->select('code,perm_key');
                $this->db->from($this->tables[1]);
                $this->db->join($this->tables[0], $this->tables[0] . '.id = ' . $this->tables[1] . '.perm_module_id', 'left');
                $this->db->where($this->tables[1] . '.id', $role_perm->perm_id);
                $temp_array = $this->db->get()->result_array();
                foreach ($temp_array as $perm) {
                    $perms_array[] = $perm['code'] . '/' . $perm['perm_key'];
                }
            }
        }
        return $perms_array;
    }

    /**
     * 取得指定角色的权限
     *
     * @param int $id 角色id
     */
    public function get_role_perm ($id)
    {
        // 取得指定角色的权限
        $role_perms = $this->list_all_where($this->tables[3], 'role_id', $id);

        if ($role_perms) {
            foreach ($role_perms as $role_perm) {
                $perms[] = $role_perm->perm_id;
            }
        } else {
            $perms = false;
        }

        return $perms;
    }

    /**
     * 保存角色的权限
     *
     * @return int|string 成功返回1，否则返回出错信息
     */
    public function save_role_perm ()
    {
        $role_id = $this->input->post('role_id');
        $perms = $this->input->post('c1');

        // 删除角色的旧权限
        $this->db->delete($this->tables[3], array(
                'role_id' => $role_id
        ));

        $this->db->trans_start();

        if ($perms) {
            foreach ($perms as $k => $v) {
                $data = array(
                        'role_id' => $role_id,
                        'perm_id' => $v
                );
                $this->create($this->tables[3], $data);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return $this->db_error;
        } else {
            $this->applog->msg('更新角色权限');
            return 1;
        }
    }

    /**
     * ********************************角色**********************************
     */

    /**
     * ********************************用户**********************************
     */

    /**
     * 列出用户(分页)
     *
     * @return array 返回数组array('total'=>表记录总数,'list'=>记录列表)
     */
    public function list_user ()
    {
        // 每页显示的记录条数，默认20条
        $per_page = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;


        $data['total'] = $this->count($this->tables[0]);
        // list_data(表,抓取记录数，偏离量,排序字段,排序方法);
        $data['list'] = $this->list_data($this->tables[0], $per_page, $this->input->post('pageNum') ? ($this->input->post('pageNum') - 1) *
                 $per_page : 0, $this->input->post('orderField') ? $this->input->post('orderField') : 'id',
                        $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc');

        return $data;
        $per_page = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
        $orderField = $this->input->post('orderField') ? $this->input->post('orderField') : 'a.id';
        $orderDirection = $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc';
        $startOffset = $this->input->post('pageNum') ? ($this->input->post('pageNum') - 1) * $per_page : 0;
        
        //a: user b: user_dep_rel  c:dep
        $this->db->select('a.id, a.username, a.real_name, a.email, a.status, a.last_login_ip, 
        		a.last_login_time, a.add_time, a.update_time, c.dep_name');
        $this->db->from("{$this->tables[0]} as a");  
        $this->db->join("{$this->tables[1]} as b", "a.id = b.user_id", "left");
        $this->db->join("{$this->tables[2]} as c", "b.dep_id = c.id", "left");
        $this->db->limit($per_page, $startOffset);
        $this->db->order_by($orderField, $orderDirection);
        $result = $this->db->get()->result();
        $data['list'] = $result;
        
        $this->db->select('a.id, a.username, a.real_name, a.email, a.status, a.last_login_ip, 
        		a.last_login_time, a.add_time, a.update_time, c.dep_name');
        $this->db->from("{$this->tables[0]} as a");  
        $this->db->join("{$this->tables[1]} as b", "a.id = b.user_id", "left");
        $this->db->join("{$this->tables[2]} as c", "b.dep_id = c.id", "left");
        $total = $this->db->get()->num_rows();
        $data['total'] = $total;
        
        return $data;
    }

    /**
     * 保存用户
     *
     * @return int,string 成功返回1，否则返回出错信息
     */
    public function save_user ()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<li>', '</li>');
        $this->form_validation->set_rules('username', '用户名称', 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            return validation_errors();
        } else {
            $data = array(
                    'username' => $this->input->post('username', TRUE),
                    'password' => sha1($this->input->post('password', TRUE)),
                    'email' => $this->input->post('email', TRUE),
                    'status' => $this->input->post('status', TRUE),
                    'add_time' => date("Y-m-d H:i:s"),
                    'update_time' => date("Y-m-d H:i:s")
            );

            // 检查重复
            $rs_username = $this->is_exists($this->tables[0], 'username', $data['username']);

            if ($this->input->post('id')) { // 更新保存

                unset($data['add_time']);

                if (! $this->input->post('password'))
                    unset($data['password']);

                if ($rs_username and $rs_username['id'] != $this->input->post('id'))
                    return '用户已经存在';

                if ($this->update($this->tables[0], $this->input->post('id'), $data)) {
                    $this->applog->msg('更新用户');
                    return 1;
                } else {

                    return $this->db_error;
                }
            } else { // 新增保存

                if (! $this->input->post('password'))
                    return '密码不能为空';

                if ($rs_username) {
                    return '用户已经存在';
                }
                if ($this->create($this->tables[0], $data)) {
                    $this->applog->msg('新增用户');
                    return 1;
                } else {
                    return $this->db_error;
                }
            }
        }
    }

    /**
     * 返回一个用户信息数组
     *
     * @param int $id 用户id
     * @return array 用户信息数组
     */
    public function get_user ($id)
    {
        return $this->read($this->tables[0], $id);
    }

    /**
     * 删除用户
     *
     * @param int $id 用户id
     * @return int|string 成功返回1，否则返回出错信息
     */
    public function delete_user ($id)
    {
        if ($this->delete($this->tables[0], $id)) {
            $this->applog->msg('删除用户');
            return 1;
        } else {
            return $this->db_error;
        }
    }

    /**
     * 禁用或启用某个用户
     *
     * @param int $id 用户id
     * @param int $flag
     *            1,启用；0，禁用
     */
    public function set_status ($id, $flag)
    {
        $rs = parent::set_status($this->tables[0], $id, $flag);
        $this->applog->msg('更新用户状态');
        return $rs;
    }

    /**
     * 用户登录检查
     *
     * @return boolean
     */
    public function check_login ()
    {
        $login_id = $this->input->post('username');
        $passwd = $this->input->post('password');
        $this->db->from($this->tables[0]);
        $this->db->where('username', $login_id);
        $this->db->where('password', sha1($passwd));
        $this->db->where('status', 1);

        $rs = $this->db->get();

        if ($rs->num_rows() > 0) {
            $user_info = $rs->row_array();

            // 更新登陆信息
            $data = array(
                    'last_login_time' => date('Y-m-d H:i:s'),
                    'last_login_ip' => $this->input->ip_address()
            );
            $this->update($this->tables[0], $user_info['id'], $data);
            
            if ($login_id == "root"){
            	//root用户拥有所有权限
            	$user_info['perm'] = "ALL";
            }else{
            	//$user_info['perm'] = $manage_dep_id;
            }

            //取得权限信息

            /* $user_perm_string = array();
            // 取得自身权限
            $user_perm_string = $this->get_user_perm_string($user_info['id']);

            // 取得禁止权限
            $user_disable_string = $this->get_user_disable_string($user_info['id']);

            // 取得用户的所有角色
            $user_roles = $this->get_user_role($user_info['id']);
            // 用户已经赋予角色数据
            if ($user_roles['user_roles']) {
                foreach ($user_roles['user_roles'] as $role_id) { // 循环抓取角色的权限
                    $role_perm_string = $this->get_role_perm_string($role_id); // 取得角色权限
                    if ($role_perm_string) {
                        foreach ($role_perm_string as $k => $v) { // 循环角色权限，合并到用户权限
                            $user_perm_string[] = $v;
                        }
                    }
                }
            }
            // 去除角色和用户的重叠权限
            $user_perm_string = array_unique($user_perm_string);
            $user_info['perm'] = $user_perm_string;
            $user_info['disable'] = $user_disable_string; */

            $this->session->set_userdata($user_info);

            return true;
        } else {
            return false;
        }
    }

    /**
     * 取得指定用户的权限字串数组
     *
     * @param int $id
     *            用户id
     * @return array 权限字串数组
     */
    public function get_user_perm_string ($id)
    {
        $user_perms = $this->list_all_where($this->tables[4], 'user_id', $id);
        $perms_array = array();
        if ($user_perms) {
            foreach ($user_perms as $user_perm) {

                $this->db->select('code,perm_key');
                $this->db->from($this->tables[1]);
                $this->db->join($this->tables[0], $this->tables[0] . '.id = ' . $this->tables[1] . '.perm_module_id', 'left');
                $this->db->where($this->tables[1] . '.id', $user_perm->perm_id);
                $temp_array = $this->db->get()->result_array();
                foreach ($temp_array as $perm) {
                    $perms_array[] = $perm['code'] . '/' . $perm['perm_key'];
                }
            }
        }
        return $perms_array;
    }

    /**
     * 取得指定用户的禁用权限字串数组
     *
     * @param int $id
     *            用户id
     * @return array 权限字串数组
     */
    public function get_user_disable_string ($id)
    {
        $user_perms = $this->list_all_where($this->tables[6], 'user_id', $id);
        $perms_array = array();
        if ($user_perms) {
            foreach ($user_perms as $user_perm) {

                $this->db->select('code,perm_key');
                $this->db->from($this->tables[1]);
                $this->db->join($this->tables[0], $this->tables[0] . '.id = ' . $this->tables[1] . '.perm_module_id', 'left');
                $this->db->where($this->tables[1] . '.id', $user_perm->perm_id);
                $temp_array = $this->db->get()->result_array();
                foreach ($temp_array as $perm) {
                    $perms_array[] = $perm['code'] . '/' . $perm['perm_key'];
                }
            }
        }
        return $perms_array;
    }

    /**
     * 取得指定用户的权限
     *
     * @param int $id
     *            用户id
     */
    public function get_user_perm ($id)
    {
        // 取得指定用户的权限
        $user_perms = $this->list_all_where($this->tables[4], 'user_id', $id);

        if ($user_perms) {
            foreach ($user_perms as $user_perm) {
                $perms[] = $user_perm->perm_id;
            }
        } else {
            $perms = false;
        }

        return $perms;
    }

    /**
     * 取得指定用户被禁用的权限
     *
     * @param int $id
     *            用户id
     */
    public function get_user_disable ($id)
    {
        $user_perms = $this->list_all_where($this->tables[6], 'user_id', $id);

        if ($user_perms) {
            foreach ($user_perms as $user_perm) {
                $perms[] = $user_perm->perm_id;
            }
        } else {
            $perms = false;
        }

        return $perms;
    }

    /**
     * 保存用户的权限
     *
     * @return int|string 成功返回1，否则返回出错信息
     */
    public function save_user_perm ()
    {
        $user_id = $this->input->post('user_id');
        $perms = $this->input->post('c1');

        // 删除用户的旧权限
        $this->db->delete($this->tables[4], array(
                'user_id' => $user_id
        ));

        $this->db->trans_start();

        if ($perms) {
            foreach ($perms as $k => $v) {
                $data = array(
                        'user_id' => $user_id,
                        'perm_id' => $v
                );
                $this->create($this->tables[4], $data);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return $this->db_error;
        } else {

            $this->applog->msg('更新用户权限');
            return 1;
        }
    }

    /**
     * 保存用户禁用的权限
     *
     * @return int|string 成功返回1，否则返回出错信息
     */
    public function save_user_disable ()
    {
        $user_id = $this->input->post('user_id');
        $perms = $this->input->post('c2');

        // 删除用户的旧权限
        $this->db->delete($this->tables[6], array(
                'user_id' => $user_id
        ));

        $this->db->trans_start();

        if ($perms) {
            foreach ($perms as $k => $v) {
                $data = array(
                        'user_id' => $user_id,
                        'perm_id' => $v
                );
                $this->create($this->tables[6], $data);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return $this->db_error;
        } else {
            $this->applog->msg('更新用户禁用权限');
            return 1;
        }
    }

    /**
     * 取得指定用户的角色
     *
     * @param int $id
     *            用户id
     */
    public function get_user_role ($id)
    {
        // 取得角色数据
        $all_roles = $this->list_all($this->tables[2]);
        // 取得指定用户的角色
        $rs = $this->list_all_where($this->tables[5], 'user_id', $id);

        if ($rs) {
            foreach ($rs as $user_role) {
                $user_roles[] = $user_role->role_id;
            }
        } else {
            $user_roles = false;
        }

        $data['all_roles'] = $all_roles; // 所有角色
        $data['user_roles'] = $user_roles; // 用户的角色id数组

        return $data;
    }

    /**
     * 保存用户的角色
     *
     * @return int|string 成功返回1，否则返回出错信息
     */
    public function save_user_role ()
    {
        $user_id = $this->input->post('user_id');
        $roles = $this->input->post('c0');

        // 删除用户的旧角色
        $this->db->delete($this->tables[5], array(
                'user_id' => $user_id
        ));

        $this->db->trans_start();

        if ($roles) {
            foreach ($roles as $k => $v) {
                $data = array(
                        'user_id' => $user_id,
                        'role_id' => $v
                );
                $this->create($this->tables[5], $data);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return $this->db_error;
        } else {
            $this->applog->msg('更新用户角色');
            return 1;
        }
    }
    
    /**
     * 获取用户的权限
     * @param unknown_type $id
     */
    public function get_user_perms($id){
    	
    	$per_page = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
    	$orderField = $this->input->post('orderField') ? $this->input->post('orderField') : 'a.id';
    	$orderDirection = $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc';
    	$startOffset = $this->input->post('pageNum') ? ($this->input->post('pageNum') - 1) * $per_page : 0;
    	
    	//获取该用户的信息
    	$data['user'] = $this->db->get_where($this->tables[0], array('id' => $id))->row_array();
    	$this->db->select("a.user_id, b.id, b.dep_num, b.dep_name, b.father_dep_id, 
    			b.lvl, b.desc, c.dep_name as father_dep_name");
    	$this->db->from("{$this->tables[1]} as a");
    	$this->db->join("{$this->tables[2]} as b", 'a.dep_id = b.id', 'left');
    	$this->db->join("{$this->tables[2]} as c", 'b.father_dep_id = c.id', 'left');
    	$this->db->where("a.user_id", $id);
    	$this->db->limit($per_page, $startOffset);
    	$this->db->order_by($orderField, $orderDirection);
    	$data['list'] = $this->db->get()->result();
    	
    	$this->db->select("a.user_id, b.id, b.dep_num, b.dep_name, b.father_dep_id,
    			b.lvl, b.desc, c.dep_name as father_dep_name");
    	$this->db->from("{$this->tables[1]} as a");
    	$this->db->join("{$this->tables[2]} as b", 'a.dep_id = b.id', 'left');
    	$this->db->join("{$this->tables[2]} as c", 'b.father_dep_id = c.id', 'left');
    	$this->db->where("a.user_id", $id);
    	$data['total'] = $this->db->get()->num_rows();
		return $data;    	
    }
    
	/**
	 * 删除用户权限
	 * @param unknown_type $id
	 * @return number|string
	 */
    public function delete_user_perm($id){
    	
    	$this->db->trans_start();
    	$this->delete($this->tables[1], $id);
    	$this->db->trans_complete();
    	
    	if ($this->db->trans_status() === TRUE) {
    		return 1;
    	} else {
    		return $this->db_error;
    	}
    }
    
    public function save_user_perms(){
    	
    	$data = array(
    			'dep_id' => $this->input->post('dep', true),
    			'user_id' => $this->input->post('user_id', true)
    	);
    	$this->db->select('id');
    	$this->db->from($this->tables[1]);
    	$this->db->where('dep_id', $data['dep_id']);
    	$this->db->where('user_id', $data['user_id']);
    	$num = $this->db->get()->num_rows();
    	if($num == 0){
    	
	    	$this->db->trans_start();
	    	$this->create($this->tables[1], $data);
	    	$this->db->trans_complete();
    	}else{
    		return '已经有这个权限了';
    	}
    	 
    	if ($this->db->trans_status() === TRUE) {
    		return 1;
    	} else {
    		return $this->db_error;
    	}
    	
    }
    
    
/**
 * ********************************用户**********************************
 */
}
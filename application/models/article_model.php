<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 *@function 文章管理模型
 *@date     2013-10-14
 *@author   gerui
 *@email    <forgerui@gmail.com>
 */
class Article_model extends MY_Model{

	protected $tables = array(

			);

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 文章列表
	 * @return unknown
	 */
	public function list_article(){
		$data = '';
		return $data;
	}


	public function __destruct(){
		parent::__destruct();
	}

}

/* End of file article_model.php */
/* Location: ./application/models/article_model.php */
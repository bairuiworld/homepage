<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Article extends MY_Controller {


	public function __construct(){
		parent::__construct();
		$this->load->model('article_model');
	}

	public function index()
	{
		$data = $this->article_model->list_article();
		$this->load->view('article/index', $data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
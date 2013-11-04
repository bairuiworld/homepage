<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends MY_Controller {

	public function index()
	{
		$this->load->view('login/index');
	}
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */
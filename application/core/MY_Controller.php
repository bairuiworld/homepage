<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 扩展业务控制器
 *
 * 提供了权限认证功能
 * @package		app
 * @subpackage	Libraries
 * @category	controller
 * @author      bruce.yang<kissjava@vip.qq.com>
 *        
 */
class MY_Controller extends CI_Controller
{

    public function __construct ()
    {
        parent::__construct();
        $this->load->model('log_model', 'applog');
    }

}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */
<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 自定义异常错误处理类
 *
 * @package		app
 * @subpackage	Libraries
 * @category	Exceptions
 * @author		bruce.yang<kissjava@vip.qq.com>
 */
class MY_Exceptions extends CI_Exceptions{
    
    /**
     * 404找不到文件处理
     * @see CI_Exceptions::show_404()
     */
    function show_404($page = '', $log_error = TRUE)
    {
        $heading = "404 Page Not Found";
        $message = "The page you requested was not found.".$page;
    
        // By default we log this, but allow a dev to skip it
        if ($log_error)
        {
            log_message('error', '404 Page Not Found --> '.$page);
        }
    
        $info = array(
                "statusCode"=>"300",
                "message"=>$message
        );
        
        header('Content-type: text/json');
        echo json_encode($info);
        exit();
    }
    
    /**
     * 500服务器错误处理
     * @see CI_Exceptions::show_error()
     */
    function show_error($heading, $message, $template = 'error_general', $status_code = 500)
    {
        
        $message = '<p>'.implode('</p><p>', ( ! is_array($message)) ? array($message) : $message).'</p>';
        
        $info = array(
                "statusCode"=>"300",
                "message"=>$heading.'<br />'.$message.'<br />'
        );
        
        header('Content-type: text/json');
        echo json_encode($info);
        exit();
    }
    
    /**
     * php解析错误处理
     * @see CI_Exceptions::show_php_error()
     */
    function show_php_error($severity, $message, $filepath, $line)
    {
        $severity = ( ! isset($this->levels[$severity])) ? $severity : $this->levels[$severity];
        $filepath = str_replace("\\", "/", $filepath);
        
        // For safety reasons we do not show the full file path
        if (FALSE !== strpos($filepath, '/'))
        {
            $x = explode('/', $filepath);
            $filepath = $x[count($x)-2].'/'.end($x);
        }
    
        $info = array(
                "statusCode"=>"300",
                "message"=>$message.'<br />'.$filepath.'<br />'.$line
        );
        $this->CI =& get_instance();
        //$this->CI->db->trans_rollback();
        header('Content-type: text/json');
        echo json_encode($info);
        exit();

    }
}


/* End of file MY_Exceptions.php */
/* Location: ./application/controllers|models/MY_Exceptions.php */
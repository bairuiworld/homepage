<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 *@function 文件管理模型
 *@date     2013-10-16
 *@author   gerui
 *@email    <forgerui@gmail.com>
 */
class File_model extends MY_Model{

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
	 * 获取所有联系人信息
	 */
	public function get_all_cnt(){
		$this->db->select('a.id, a.cnt_num, a.cnt_name, a.phone, a.desc, a.create_time, a.update_time, c.dep_name');
		$this->db->from("{$this->tables[0]} as a");
		$this->db->join("{$this->tables[1]} as b", "a.id = b.cnt_id", "left");
		$this->db->join("{$this->tables[2]} as c", "b.dep_id = c.id", "left");
		$data = $this->db->get()->result();
		return $data;
	}
	
	public function export_cnt_xls(){
    	//获取所有联系人信息
		$data = $this->get_all_cnt();
		
		require_once 'application/libraries/PHPExcel.php';
    	require_once 'application/libraries/PHPExcel/Writer/Excel5.php';
    	
    	
    
    	// 创建一个处理对象实例
    	$objExcel = new PHPExcel();
    
    	// 创建文件格式写入对象实例, uncomment
    	$objWriter = new PHPExcel_Writer_Excel5($objExcel);
    
    	//设置文档基本属性
    	$objProps = $objExcel->getProperties();
    	$objProps->setCreator("苏州大学");
    	$objProps->setLastModifiedBy("苏州大学");
    	$objProps->setTitle("苏州大学联系人");
    	$objProps->setSubject("苏州大学联系人");
    	$objProps->setDescription("苏州大学联系人信息表");
    	$objProps->setKeywords("苏州大学联系人");
    	$objProps->setCategory("苏州大学联系人");
    
    	//*************************************
    	//设置当前的sheet索引，用于后续的内容操作。
    	//一般只有在使用多个sheet的时候才需要显示调用。
    	//缺省情况下，PHPExcel会自动创建第一个sheet被设置SheetIndex=0
    	$objExcel->setActiveSheetIndex(0);
    	$objActSheet = $objExcel->getActiveSheet();
    
    	//设置当前活动sheet的名称
    	$objActSheet->setTitle('联系人');
    
    	//*************************************
    	//
    	//设置宽度，这个值和EXCEL里的不同，不知道是什么单位，略小于EXCEL中的宽度
    	$objActSheet->getColumnDimension('A')->setWidth(20);
    	$objActSheet->getColumnDimension('B')->setWidth(20);
    	$objActSheet->getColumnDimension('C')->setWidth(20);
    	$objActSheet->getColumnDimension('D')->setWidth(20);
    	$objActSheet->getColumnDimension('E')->setWidth(30);
    	$objActSheet->getColumnDimension('F')->setWidth(25);
    	$objActSheet->getColumnDimension('G')->setWidth(25);

    
    	$objActSheet->getRowDimension(1)->setRowHeight(30);
    	$objActSheet->getRowDimension(2)->setRowHeight(27);
    	$objActSheet->getRowDimension(3)->setRowHeight(16);
    
    	//设置单元格的值
    	$objActSheet->setCellValue('A1', '苏州大学联系人信息');
    	//合并单元格
    	$objActSheet->mergeCells('A1:G1');
    	//设置样式
    	$objStyleA1 = $objActSheet->getStyle('A1');
    	$objStyleA1->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    	$objFontA1 = $objStyleA1->getFont();
    	$objFontA1->setName('宋体');
    	$objFontA1->setSize(18);
    	$objFontA1->setBold(true);
    
    	$column = array('A', 'B', 'C', 'D', 'E', 'F', 'G');
    
    	foreach($column as $v){
    		//设置居中对齐
    		$objActSheet->getStyle($v.'2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    	}
    
    	$objActSheet->setCellValue('A2', '联系人编号');
    	$objActSheet->setCellValue('B2', '联系人姓名');
    	$objActSheet->setCellValue('C2', '电话号码');
    	$objActSheet->setCellValue('D2', '所属部门');
    	$objActSheet->setCellValue('E2', '备注');
    	$objActSheet->setCellValue('F2', '建档时间');
    	$objActSheet->setCellValue('G2', '更新时间');
    
    	foreach($column as $v){
    		//设置边框
    		$objActSheet->getStyle($v.'2')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN );
    		$objActSheet->getStyle($v.'2')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN );
    		$objActSheet->getStyle($v.'2')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN );
    		$objActSheet->getStyle($v.'2')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN );
    	}
    
    	
    	$i=1;
    	//从数据库取值循环输出
    	foreach ($data as $result){
    		$n=$i+2;
    
    		$objActSheet->getStyle('B'.$n)->getNumberFormat()->setFormatCode('@');
    		$objActSheet->getStyle('E'.$n)->getNumberFormat()->setFormatCode('@');
    		$objActSheet->getRowDimension($n)->setRowHeight(16);
    			
    		foreach ($column as $v){
    			$objActSheet->getStyle($v.$n)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN );
    			$objActSheet->getStyle($v.$n)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN );
    			$objActSheet->getStyle($v.$n)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN );
    			$objActSheet->getStyle($v.$n)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN );
    		}
    
    		$objActSheet->setCellValue('A'.$n, $result->cnt_num);
    		$objActSheet->setCellValue('B'.$n, $result->cnt_name);
    		$objActSheet->setCellValue('C'.$n, $result->phone);
    		$objActSheet->setCellValue('D'.$n, $result->dep_name);
    		$objActSheet->setCellValue('E'.$n, $result->desc);
    		$objActSheet->setCellValue('F'.$n, $result->create_time);
    		$objActSheet->setCellValue('G'.$n, $result->update_time);

    		$i++;
    	}
    
    
    	//*************************************
    	//输出内容
    	$outputFileName = "docs/excel/cnt.xls";
    	//到文件
    	$objWriter->save($outputFileName);
    	//下面这个输出我是有个页面用Ajax接收返回的信息
    	return '<a href="docs/excel/cnt.xls" mce_href="docs/excel/cnt.xls" target="_blank">点击下载电子表</a>';
    }


    /**
     * 保存excel到mysql数据库
     */
    public function save_excel(){
    	$config['upload_path'] = ('./docs/upload');
    	$config['allowed_types'] = 'xls';
    	//允许上传10M大小
    	$config['max_size'] = '10000';
    	$config['max_width']  = '1024';
    	$config['max_height']  = '768';
    	$this->load->library('upload', $config);
    	 
    	if ( ! $this->upload->do_upload())
    	{
    		$error = $this->upload->display_errors();
    		return $error;
    	}
    	else
    	{
    		$data = $this->upload->data();
    	}
    	@require_once 'application/libraries/PHPExcel.php';
    	@require_once 'application/libraries/PHPExcel/IOFactory.php';
    	@require_once 'application/libraries/PHPExcel/Reader/Excel5.php';
    	$objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format
    	$objPHPExcel = @$objReader->load('./docs/upload/'.$data['file_name']); //$filename可以是上传的文件，或者是指定的文件
    	$sheet = $objPHPExcel->getSheet(0);
    	$highestRow = $sheet->getHighestRow(); // 取得总行数
    	$highestColumn = $sheet->getHighestColumn(); // 取得总列数
    	$k = 0;
    	
    	//循环读取excel文件,读取一条,插入一条
    	for($j=2;$j<=$highestRow;$j++)
    	{
    		$a = iconv('utf-8','utf-8',$objPHPExcel->getActiveSheet()->getCell("B".$j)->getValue());//读取单元格
    		$b = iconv('utf-8','utf-8',$objPHPExcel->getActiveSheet()->getCell("C".$j)->getValue());//读取单元格
    	
    		echo $a.' '.$b.'<br/>';
    	}
    	return 1;
    }
    
    function uploadFile($file,$filetempname)
    {
    
    	//自己设置的上传文件存放路径
    	$filePath = 'docs/upload/';
    	$str = "";
      
    	@require_once 'application/libraries/PHPExcel.php';
    	@require_once 'application/libraries/PHPExcel/IOFactory.php';
    	@require_once 'application/libraries/PHPExcel/Reader/Excel5.php';
    
    	$filename=explode(".",$file);//把上传的文件名以“.”好为准做一个数组。
    	$time=date("y-m-d-H-i-s");//去当前上传的时间
    	$filename[0]=$time;//取文件名t替换
    	$name=implode(".",$filename); //上传后的文件名
    	$uploadfile=$filePath.$name;//上传后的文件名地址
    
    
    	//move_uploaded_file() 函数将上传的文件移动到新位置。若成功，则返回 true，否则返回 false。
    	$result=move_uploaded_file($filetempname,$uploadfile);//假如上传到当前目录下
    	if($result) //如果上传文件成功，就执行导入excel操作
    	{
    		$objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format
    		$objPHPExcel = $objReader->load($uploadfile);
    		$sheet = $objPHPExcel->getSheet(0);
    		$highestRow = $sheet->getHighestRow(); // 取得总行数
    		$highestColumn = $sheet->getHighestColumn(); // 取得总列数
    
    		//循环读取excel文件,读取一条,插入一条
    		for($j=2;$j<=$highestRow;$j++)
    		{
	    		for($k='A';$k<=$highestColumn;$k++)
	    		{
	    			$str .= iconv('utf-8','gbk',$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue()).'\\';//读取单元格
	    		}
	    		//explode:函数把字符串分割为数组。
	    		$strs = explode("\\",$str);
				$sql = "INSERT INTO tab_zgjx_award (jx_name,jx_degree,jx_item_name,jx_unit,dy_originator,de_originator,xm_intro,hj_item_id) VALUES('".
				    $strs[0]."','". //奖项名称
				    $strs[1]."','". //奖项届次
				    $strs[2]."','". //获奖项目名
				    $strs[3]."','". //获奖单位
				    $strs[4]."','". //第一发明人
				    $strs[5]."','". //第二发明人
				    $strs[6]."','". //项目简介
				    		$strs[7]."')"; //获奖项目编号
	    
				mysql_query("set names GBK");//这就是指定数据库字符集，一般放在连接数据库后面就系了
				    if(!mysql_query($sql))
				    {
				    	return false;
				    }
				    $str = "";
    		}
			    
			unlink($uploadfile); //删除上传的excel文件
			$msg = "导入成功！";
		}
	    else
	    {
	    	$msg = "导入失败！";
	    }
    
    	return $msg;
    }
    
	public function __destruct(){
		parent::__destruct();
	}
	

}

/* End of file file_model.php */
/* Location: ./application/models/file_model.php */
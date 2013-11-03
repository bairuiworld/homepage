<?php include(APPPATH."views/header.php")?>

<div class="page secondary with-sidebar" id="page-index">

    <div class="page-header">
    	<div class="page-header-content">
    		<h1>近期<small>工作</small></h1>
            <a href="<?php echo base_url();?>" class="back-button big page-back"></a>
        </div>
    </div>

    <?php include(APPPATH."views/leftside.php")?>

    <div class="page-region">
    	<div class="page-region-content">
            <div class="grid span9">
	            <div class="row">
		            <div class="span9" style="background-color: #ccc; height: 200px;">
	                 <div class="page snapped bg-color-blue">
	                 	<p style="padding: 20px;">近期工作</p>
	                 </div>
	                 <div class="page fill bg-color-orange">
	                    <ol>
	                    	<li>学习Metro UI CSS，搭建个人主页</li>
	                    	<li>学习MATLAB</li>
	                    </ol>
	                 </div>
	            </div>
            </div>

            <div class="row">

            </div>




        </div>
    </div>

</div>




<?php include(APPPATH."views/footer.php")?>
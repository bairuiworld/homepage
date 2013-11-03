<?php include(APPPATH."views/header.php")?>

<div class="page secondary with-sidebar" id="page-index">

    <div class="page-header">
    	<div class="page-header-content">
    		<h1>美文<small>阅读</small></h1>
            <a href="<?php echo base_url();?>" class="back-button big page-back"></a>
        </div>
    </div>

    <?php include(APPPATH."views/leftside.php")?>

    <div class="page-region">
    	<div class="page-region-content">
            <div class="grid">
            	<?php for($i = 0; $i < 10; $i++):?>
				<div class="row">
					<div class="span9 ">
						<div class="span2" style="margin:40px 10px 0px 0px">
							<h3><strong class="bg-color-grey">货币战争</strong></h3>
						</div>

					</div>
					<div class="span8 offset1 bg-color-blueDark">
							<p class="long-text">W3CPLUS是一个前端爱好者的家园，W3CPLUS努力打造最优秀的web 前端学习的站点。W3CPLUS力求原创，以一起学习，一起进步，共同分享为原则。W3CPLUS站提供了有关于css,css3,html,html5,jQuery,手机移动端的技术文档、DEMO、资源，与前端爱好者一起共勉。

W3CPLUS是一个前端爱好者的家园，W3CPLUS努力打造最优秀的web 前端学习的站点。W3CPLUS力求原创，以一起学习，一起进步，共同分享为原则。W3CPLUS站提供了有关于css,css3,html,html5,jQuery,手机移动端的技术文档、DEMO、资源，与前端爱好者一起共勉。</p>
					</div>
				</div>
				<?php endfor;?>
	        </div>


        </div>
    </div>

</div>




<?php include(APPPATH."views/footer.php")?>
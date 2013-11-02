    <div class="page">
        <div class="nav-bar">
            <div class="nav-bar-inner padding10">
                <span class="element">
                    2013-2014, HomePage &copy; by <a class="fg-color-white" href="mailto:forgerui@gmail.com">Ge Rui</a>
                </span>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="<?php echo base_url();?>metro/js/assets/github.info.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>metro/js/assets/google-analytics.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>metro/js/google-code-prettify/prettify.js"></script>
    <script src="<?php echo base_url();?>metro/js/sharrre/jquery.sharrre-1.3.4.min.js"></script>

    <script>
        $('#shareme').sharrre({
            share: {
                googlePlus: true
                ,delicious: true
            },
            urlCurl: "<?php echo base_url();?>metro/js/sharrre/sharrre.php",
            buttons: {
                googlePlus: {size: 'tall'},
                delicious: {size: 'tall'}
            },
            hover: function(api, options){
                $(api.element).find('.buttons').show();
            }
        });
    </script>

    </body>
</html>
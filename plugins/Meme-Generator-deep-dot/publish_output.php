<?php
	function publish_meme_display()
	{
		$abspath	= $abspath	= ABSPATH.'wp-content/plugins/'.basename(dirname(__FILE__)).'/images/';
		$purl		= plugins_url('/',__FILE__);
		$imgpath	= $purl.'images/';

		$path 		= $abspath.'published/';
        $images		= glob($path.'*.*'); 
		if($images){
		foreach($images as $image)
		{ 
			$title	= basename( $image ,'.jpg');
		?>
        <div id="nyasro_memegenerator" class="ny_acenter">
			<div style="float:left;">
				<img style="padding:5px; margin:5px; border:1px solid #aaa; display:block;" src="<?php echo $imgpath.'published/'.$title.'.jpg'; ?>" />
				<h4 style="text-align:center"><?php echo $title; ?> </h4>
			</div>
		<?php }
		}else
		{
			echo 'No Images are published yet.';	
		}?>
       </div>
       <noscript>you must enable javascript to use meme generator</noscript>
       <?php             
	}
?>
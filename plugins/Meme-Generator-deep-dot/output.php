<?php
function meme_display(){ 	
  global $_ny_post_author;
	$purl		= plugins_url('/',__FILE__);
	$imgpath	= $purl.'images/';
	$abspath	= dirname(__FILE__).'/';

	$queryurl	= false;
	
	$curl		= get_permalink();
	if( strpos( $curl, '?') !== false )
		$queryurl	= true;
						
		$gpublink	= get_option('meme_publish_page');
	if($gpublink)
		$publish	= $gpublink;
	else
		if( $queryurl )
		{
			$publish	= $curl.'&publish=true';
		}
		else
			$publish	= $curl.'?publish=true';

?>
                <?php
					
				?>
 <link type="text/css" rel="stylesheet" href="<?php echo $purl; ?>css/style.css" />

<script type="text/javascript" src="<?php echo $purl; ?>js/jquery.js?ver=1.8.20"></script>

<script type="text/javascript">
	jQuery(document).ready(function(e) {
		jQuery('#nyasro_memegenerator').show();
	});
</script>
    <noscript id="ny_meme_noscript">you must enable javascript to use meme generator</noscript>

<div id="nyasro_memegenerator" class="ny_acenter">

<?php	

	if(isset($_GET['mid']))
	{
		$midg		= $_GET['mid'];
		$mtitle = $midg;
		$apath		= $abspath.'images/meme/'.$midg.'.jpg';

		if(!file_exists($apath))
		{	
			if($_POST['submit'])
			{
				include('meme-text.php');
				$mmurl		= $imgpath.'meme/'.$midg.'.jpg';
				include(ABSPATH . 'wp-admin/includes/taxonomy.php');
				$mtitle  = 'MEME '.$midg;
				if( isset($_POST['meme-title']) && $_POST['meme-title'] )
					$mtitle = $_POST['meme-title'];
				$my_post = array(
				  'post_title'    => $mtitle,
				  'post_content'  => '<img src="'.$mmurl.'" class="post-meme-image" />',
				  'post_status'   => 'draft',
				  'post_author'   => $_ny_post_author,
				  'post_category' => array(wp_create_category('Meme Images'))
				);
				$_pid = wp_insert_post( $my_post );
				$filename = $apath;
				
				$wp_upload_dir = wp_upload_dir();
				$upath = $wp_upload_dir['path'];
				$ufile = $upath .'/'. basename($filename);
				copy( $filename, $upath .'/'. basename($filename) );
				$filename	= $ufile;	
				$wp_filetype = wp_check_filetype(basename($filename), null );
				$attachment = array(
					 'guid' => $wp_upload_dir['url'] . '/' . basename( $filename ), 
					 'post_mime_type' => $wp_filetype['type'],
					 'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
					 'post_content' => '',
					 'post_status' => 'inherit'
				);
					
				$attach_id = wp_insert_attachment( $attachment, $filename, $_pid );
			
				$thumb_attach_attr = wp_get_attachment_image_src( $attach_id );
			
				$postmeta_id = add_post_meta($_pid, 'thumbnail', $thumb_attach_attr[0]);
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				
				$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
				wp_update_attachment_metadata( $attach_id, $attach_data );	
				
				set_post_thumbnail($_pid,$attach_id);
			}
			else
			{
				echo 'Meme doesn\'t exist or submitted for approval. Go to <a href="'.strtok($_SERVER['REQUEST_URI'],'?').'">meme generater</a>';
				return;
			}
				
		}
		
		
		?>
        	<h2>Your MEME <?php echo $mtitle; ?></h2>
			<img src="<?php echo $imgpath.'meme/'.$midg.'.jpg'; ?>" />
			<br/>
            <?php
				$reurl			= $_SERVER['REQUEST_URI'];
				if( strpos( $reurl , '&mid' ) )
					$caurl		= substr($reurl,0, strpos( $reurl , '&mid' ));
				else
					$caurl		= strtok($reurl,'?');
			?>
			<a href="<?php echo $purl.'download.php?file='.$midg; ?>">Download</a> | <a href="<?php echo $caurl; ?>" >Create Another</a> 
                  <!-- | <a href="#" onclick="javascript:submitMeme(<?php echo $midg; ?>);return false;" >Submit Meme</a> -->
			<?php 
	}
	else{
		
		global $upload;		
		
	$mid		= mt_rand(0,9999).time();
	
	$nymsg		= '';
	
	if(get_option('meme_message'))
	{
		$nymsg	= get_option('meme_message');
		echo $nymsg;
		update_option('meme_message','');
		if(strstr($nymsg,'error'))
			$upload		= false;
	}
	
	if($queryurl)
		$acurl	= $curl.'&mid='.$mid;
	else
		$acurl	= $curl.'?mid='.$mid;


?>
	 <script type="text/javascript">
	 

var nyi=0,nyj=2;
var ny_mainimg;

    function createMeme()
	{	
			nyi++;
			
		if(nyi===1)	jQuery('#check_text').val('true');
		var	$form		= jQuery('#meme_form');
		var $data		= $form.serialize();
				jQuery('#meme_submit_btn').removeAttr('disabled');

		var $img		= jQuery('#meme_image');
		var $text		= jQuery('#meme_text_img');
		var $font		= jQuery('#font-name').val();
			$data		+= '&width='+$img.width()+'&height='+$img.height()+'&font='+$font;
		var img			= new Image();
			img.src		= '<?php echo $purl; ?>meme-text.php?' + $data;
			$text.attr( 'src',img.src ); 
	}
	
	function changeImage(src)
	{
		jQuery('#loading_text').show();
		var $mimg		= jQuery('#meme_image');	
		var $src		= '<?php echo $imgpath.'main/'; ?>'+src;
		var $hig;
		var $tbtn		= jQuery('#meme_form').find('.textupdown.bottom');
		
		$mimg.attr('src',$src).load(function(){ 
			$hig	= this.height;
			ny_mainimg		= $src;
			var offset		= Math.floor(($hig- 30 - 60)/3) - 10;
			$tbtn.val(offset);
			jQuery('#loading_text').hide();
			});
		jQuery('#meme_text_img').removeAttr('src');
		return false;
	}
	
	function checkFormData(form)
	{
		var val = form.written.value;
		if(val.length > 1)
		{
			if(!ny_mainimg)
				ny_mainimg = jQuery('#meme_image').attr('src');
			jQuery('#ny_mainimg').val(ny_mainimg);
			//jQuery('#textimg').val(textimg);
			return true;
		}
		alert('Write some text.');
		return false;
	}
	
	function addNewLine(addf)
	{
		nyj++;
		var $div = jQuery('<div class="each_text">\
                    	<label>Text '+nyj+'</label>\
                        <input onkeyup="javascript:createMeme();return false;"  type="text" value="" placeholder="add some text" name="memetext[]"/>\
                        <a href="#" onclick="javascript:changeTextUpDown(this.parentNode,\'up\');return false;">move up</a> |\
                        <a href="#" onclick="javascript:changeTextUpDown(this.parentNode,\'down\');return false;">move down</a>\
						<br/>\
                        <a href="#" onclick="javascript:changeTextPosition(this.parentNode,1);return false;">left</a> | \
                        <a href="#"  onclick="javascript:changeTextPosition(this.parentNode,2);return false;">center</a> | \
                        <a href="#"  onclick="javascript:changeTextPosition(this.parentNode,3);return false;">right</a> | \
						(\
                        <a href="#"  onclick="javascript:changeCaps(this.parentNode,\'caps\');return false;">All Caps</a> |\
                        <a href="#"  onclick="javascript:changeCaps(this.parentNode,\'small\');return false;">All Small</a>\
						) \
                       <input class="textupdown" type="hidden" value="0" name="textupdown[]" /> \
                       <input class="textcaps" type="hidden" value="caps" name="textcaps[]" /> \
                       <input class="textpos" type="hidden" value="2" name="textpos[]" /> \
                     	</div>');
		jQuery(addf).before($div);
		
	}
	
	function changeTextPosition(pr,p)
	{
		var $pr		= jQuery(pr);
		var $ht		= $pr.find('.textpos');
			$ht.val(p);	
			createMeme();
	}
	
	function changeTextUpDown(pru,pu)
	{
		var	u		= '';
		var $prp	= jQuery(pru);
		var $hpt	= $prp.find('.textupdown');
			u		= $hpt.val();
			u		= +u;
		if(pu==='up')
			u--;
		else
			u++
		$hpt.val(u);
		createMeme();
	}
	
	function changeCaps(po,c)
	{
		var $pr		= jQuery(po);
		var $ht		= $pr.find('.textcaps');
			$ht.val(c);	
			createMeme();
	}
	
    </script>
        <div class="right_content">
        	<div class="img_gallery">
            	<h3 class="ny_aligncen">Random Images</h3>
                <?php 
					$path 		= $abspath.'images/thumbnail/';

					$thumb 	= glob($path.'*.jpg');
					$th_path = $purl.'images/thumbnail/';
					$imgm	= '';
					$range = range(0,count($thumb)-1);
					shuffle($range);
				for($i=0;$i<4;$i++){
					if($i === 1 ) $imgm	= $base; 
					$base	= basename($thumb[$range[$i]]);
				?>
                	<a href="#" onclick="javascript:changeImage(this.firstChild.title);return false;"><img src="<?php echo $th_path.$base; ?>" title="<?php echo $base; ?>" width="150"/></a>
                <?php }
					if($upload)
						$imgm	= $upload;
				?>
            </div>
        </div>
        
        <div class="leftt_content">
        	<div class="img_gallery">
            	<h3 class="ny_aligncen">Main Images</h3>
            	<ul>
            		<li>Facebook</li>
            		<li>Twitter</li>
            	</ul>
                <?php 
					$path 		= $imgpath.'thumbnail/';
					$soarr		= get_option('meme_main_images');
					if($soarr){
				foreach($soarr as $mimage){
					$base	= $path.$mimage;
					
				?>
                	<a href="#" onclick="javascript:changeImage(this.firstChild.title);return false;"><img src="<?php echo $base.'.jpg'; ?>" title="<?php echo $mimage.'.jpg'; ?>" width="150"/></a>
                <?php }
					}else
					{
						echo '<div style="width:190px; text-align:center">Please select images<br/> from admin panel.</div>';
					}
				?>
            </div>
        </div>
        
    	<div class="left_content" style="margin:0 202px;">
        
        	<h2 class="ny_aligncen" style="clear:none;">meme generator</h2>
            <div class="meme_box">
            	<div class="img_box ny_acenter">
                	<img id="meme_image" class="meme_img" src="<?php echo $imgpath.'main/'.$imgm; ?>" width="400" />
                    <img id="meme_text_img" style="position:absolute; top:6px; left:6px;" width="400" />
                    <div id="loading_text">Loading...</div>
                </div>
                <div class="form_box">
                 
                <form style="padding:10px;width:400px;border-bottom:1px solid #aaa; margin-bottom:10px;"  class="ny_acenter" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data" >
                	<div class="form-hide">
	                    <input type="file" name="image" />
	                    <input type="submit" value="Upload" name="upload" class="button-primary" />
	                </div>
                </form>
                	<form onsubmit="javascript:return checkFormData(this);" style="width:400px" class="ny_acenter" action="<?php echo $acurl; ?>" method="post" id="meme_form">
                <div style="margin:10px 0;">
                  Meme Title : 
                  <input type="text" name="meme-title" value="" />
                </div>
                <div id="select-font">
                	Select Font : 
                    <select id="font-name" name="font">
					<?php
                        $gpath	= dirname(__FILE__).'/fonts/';
                        $fonts	= glob( $gpath.'*');
						$option = '';
                        foreach( $fonts as $font )
                        {
							$f_name	= basename( $font );
							if( $f_name === 'nymeme' ) 
								$selected = ' selected="selected"';
							else
								$selected = '';
                            $option .='<option value="'.$f_name.'"'.$selected.'>'.strtok($f_name,'.').'</option>';
                        }
						echo $option;
                     ?>
                    <?php ?>
                    </select>
                </div>
                    
                    <?php 
					$isize		= @getimagesize($abspath.'images/main/'.$imgm);			
					$ihei 		= $isize[1];
					$offsetb	= floor(($ihei- 30 - 60)/3) - 10 ; 
					for($i=0;$i<2;$i++){ ?>	
                    <div class="each_text">
                    	<label>Text <?php echo $i+1; ?></label>
                        <input onkeyup="javascript:createMeme();return false;"  type="text" value="" placeholder="add some text" name="memetext[]"/>
                        <a href="#" onclick="javascript:changeTextUpDown(this.parentNode,'up');return false;">move up</a> |
                        <a href="#" onclick="javascript:changeTextUpDown(this.parentNode,'down');return false;">move down</a>     
                        <br/>
                        <a href="#" onclick="javascript:changeTextPosition(this.parentNode,1);return false;">left</a> | 
                        <a href="#"  onclick="javascript:changeTextPosition(this.parentNode,2);return false;">center</a> | 
                        <a href="#"  onclick="javascript:changeTextPosition(this.parentNode,3);return false;">right</a>
						(
                        <a href="#"  onclick="javascript:changeCaps(this.parentNode,'caps');return false;">All Caps</a> |
                        <a href="#"  onclick="javascript:changeCaps(this.parentNode,'small');return false;">All Small</a>
						) 
                       <input class="textupdown<?php if($i===1) echo ' bottom';?>" type="hidden" value="<?php if($i===1) echo $offsetb;else echo 0; ?>" name="textupdown[]" /> 
                       <input class="textcaps" type="hidden" value="caps" name="textcaps[]" /> 
                       <input class="textpos" type="hidden" value="2" name="textpos[]" /> 
                     </div>
                     <?php } ?>
                     <div class="add_another">
                     	<input type="button" name="anl" onclick="javascript:addNewLine(this.parentNode);" value="add new line" />
                     </div>
                     	<input type="hidden" value="<?php echo $mid; ?>" name="mid" />
                     	<input type="hidden" id="check_text" value="" name="written" />
                     	<input type="hidden" id="ny_mainimg" value="" name="mainimg" />
                        
                    	
                        <div style="text-align:center;"><input type="submit" value="submit" name="submit" id="meme_submit_btn" disabled="disabled" /></div>
                    </form>
                    
                
                </div>
            </div>
        
        </div>
    	<div class="ny_clear"></div>
    </div>
    <script>
    //shows the images you want to see
    	$('.img_gallery img').addClass("hide");
    	$('.img_gallery img').addClass(function () {
		    if (this.src.replace(/^.*\//, '').indexOf('facebook') > -1) {
		         return 'meme-show';
		    }
		    return false;
		});
    </script>
   
<?php }} ?>
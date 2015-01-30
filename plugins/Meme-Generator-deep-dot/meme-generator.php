<?php
/*
Plugin Name: Meme Generator
Plugin URI: http://unlimitwordpress.com/
Version: 2.0
Description: Generate a meme!
Author: Unlimit Wordpress: Jordan Banafsheha and Nyasro
Author URI: mailto:jordanbana@gmail.com
License: This plugin based on the excellent work of Jordan Banafsheha and Nyasro, under GPLv2 or later (license.txt).
*/

if(!class_exists('Nyasro_MemeGenerator'))
{
	
	class Nyasro_MemeGenerator
	{
		
		private	$menu_slug		= 'meme-generator';
		
		private	$yesimage		= 0;
		
		
		public function __construct()
		{
			$this->set_cookie();
      add_action('init',array($this,'nyasro_create_user'));
			add_action( 'admin_menu', array( $this, 'add_admin_menu'));
			//add_action( 'wp_enqueue_scripts', array( $this, 'add_js_css'));
			add_filter( 'plugin_action_links', array( $this, 'add_action_link'), 10 , 2);
			add_shortcode('nymeme', array($this,'meme_output'));
			add_shortcode('nymemepub', array($this,'meme_publish_output'));
			register_activation_hook( __FILE__, array( $this, 'add_option_meme'));
			register_deactivation_hook( __FILE__, array( $this, 'del_option_meme'));
			$this->yesimage	= get_option('meme_image_count');
		}
    
    public function nyasro_create_user()
		{
			global $_ny_post_author;
			$username							= 'memeusers';
			$post_author					= get_current_user_id();
			if(!$post_author)
			{ 
				$post_author				= get_user_by('slug',$username);
				$post_author				= $post_author->ID;
			}
				$_ny_post_author		= $post_author;
			$user_id							= username_exists($username);
			if(!$user_id)
			{
				$password						= wp_generate_password( $length=12, $include_standard_special_chars=false );
				$user_id 						= wp_create_user( $username, $password, 'memeusers@'.$_SERVER['HTTP_HOST']);
				if(is_int($user_id))
				{
					$wp_user_object = new WP_User($user_id);
					$wp_user_object->set_role('contributor'); 	
				}
			}
		}
		
		public function add_js_css()
		{
			wp_enqueue_script('jquery');
			wp_register_style( 'meme_ny_style', plugins_url('/',__FILE__).'css/style.css');
			wp_enqueue_style( 'meme_ny_style' );
		}
		
		private function set_cookie()
		{ 		
			 nocache_headers();
			 
			if(isset($_GET['mid']) || isset($_GET['cond']))
				return false;
			global $upload; 
			$upload		= false;

			if(isset($_POST['upload']))
			{ 
				if( $_POST['upload'] === 'Font Upload' )
				{
					if(move_uploaded_file($_FILES['font']['tmp_name'], dirname(__FILE__).'/fonts/'.$_FILES['font']['name']))
					{
						update_option('meme_message','<div class="updated" style="margin-left:0;"><p>Font uploaded Successfully</p></div>');
					}
					return false;
				}
				global $guploaderr;
				$guploaderr		= true;
				include('meme-image-upload.php');
				if( $guploaderr )
					return false;
					
				if(get_option('meme_message')) 
				{
					 $upload	 = $_FILES['image']['name'];
					 $upload	 = str_replace(array('.jpeg','.png','.JPG','.PNG','.JPEG','.gif','.GIF'),'.jpg',$upload);
					 if($upload)
						unset($_COOKIE['meme_uploaded_image']);
				}
			}
			if(isset($_COOKIE['meme_uploaded_image']))
				$upload	= $_COOKIE['meme_uploaded_image'];
			else
			setcookie("meme_uploaded_image",$upload,0);	
		}
		
		public function add_option_meme()
		{
			add_option('meme_message');
			add_option('meme_form_data');
			add_option('meme_main_images','');
			add_option('meme_random_images','');
			add_option('meme_publish_page','');
			$value		= 0;
			$abspath	= dirname(__FILE__).'/';
			$path 		= $abspath.'images/main/';
			$thumb 		= glob($path.'*.jpg');
			if(is_array($thumb))
				$value	= count($thumb);
			add_option('meme_image_count', $value);
		}
		public function del_option_meme()
		{
			delete_option('meme_image_count');
		}
		
		public function meme_output( $atts = array() )
		{
			if( $this->yesimage < 4 )
				return $this->err_msg();
			if(isset($_GET['publish']))
			{
				return $this->meme_publish_output();
				
			}
			include('output.php');
			return meme_display();
		}
		
		public function meme_publish_output( $atts = array() )
		{
			if( $this->yesimage < 4 )
			return $this->err_msg();

			include('publish_output.php');
			return publish_meme_display();
		}
		
		public function add_admin_menu()
		{
			$page_title			= "Meme Generator";
			$menu_title			= "Meme Generator";
			$capability			= "manage_options";
			$menu_func			= "meme_admin_page";
			add_options_page( $page_title, $menu_title, $capability, $this->menu_slug, array( $this, $menu_func));
		}
		
		
		public function add_action_link( $links, $file)
		{
			$plug				= plugin_basename( __FILE__ );
			$mpset				= get_bloginfo('wpurl').'/wp-admin/admin.php?page='.$this->menu_slug;
			
			if($file === $plug)
			{
				$setting		= '<a href="'.$mpset.'">Settings</a>';
				array_push( $links, $setting);
			}
			
			return $links;
		}
		
		private function meme_image_upload()
		{
			include('meme-image-upload.php');
		}
		
		private function err_msg()
		{
			return '[MEME GENERATOR] Please upload atleast four images form admin control panel';
		}
		
		public function meme_admin_page()
		{
			$pluginpath = '../wp-content/plugins/'.basename(dirname(__FILE__));
			$abspath	= $pluginpath.'/images/';

			
			$soarr	= array();
			$form_data = '';
			$_pid = '';
			//$this->meme_image_upload();
			$urla	= '?page='.$this->menu_slug;
			
			if(get_option('meme_main_images'))
				$soarr	= get_option('meme_main_images');
				
			if(isset($_GET['imgs']))
			{
				array_push($soarr,$_GET['imgs']);
				$soarr		= array_unique($soarr);
				update_option('meme_main_images',$soarr);
			}
			elseif(isset($_GET['imgsdel']))
			{
				$skey	= array_search($_GET['imgsdel'],$soarr);
				if($skey || $skey===0)
					unset($soarr[$skey]);
				
				update_option('meme_main_images',$soarr);

			}
			elseif(isset($_GET['imgx']))
			{
				@unlink($abspath.'main/'.$_GET['imgx'].'.jpg');
				@unlink($abspath.'thumbnail/'.$_GET['imgx'].'.jpg');
				@unlink($abspath.'uploaded/'.$_GET['imgx'].'.jpg');
				$skey	= array_search($_GET['imgx'],$soarr);
				if($skey || $skey===0)
					unset($soarr[$skey]);
				update_option('meme_main_images',$soarr);
				update_option('meme_message','<div style="margin-left:0;" class="updated"><p>Image deleted successfully</p></div>');
			}				
			elseif( isset($_POST['sub_form']) )
			{
				$form_data		= $_POST;
				update_option( 'meme_form_data', $form_data );
			}
			
			if( !$form_data )
				$form_data		= get_option( 'meme_form_data' );
			
			$chk			= array( '', '' );
			if( $form_data['watermark'] === 'yes' )
				$chk[1]		= ' checked="checked"';
			else
				$chk[0]		= ' checked="checked"';
			
			$wtr_text		= 'watermark';
				
			if($form_data['wtr_text'])
				$wtr_text	= $form_data['wtr_text'];
			
			
			if($_POST['update'])
			{
				$update	= $_POST['pub_page'];
				if(!strstr($update,'http://') && !empty($update))
							$update = 'http://'.$update; 
				update_option('meme_publish_page',$update);
			}
			
			$publup		= get_option('meme_publish_page');
			
			
			if(get_option('meme_message')) 
			{
				echo get_option('meme_message');
				update_option('meme_message','');
			}


?>
	<div id="nyasro_meme_generator">
        <div id="icon-plugins" class="icon32"></div>
        <h2 style="line-height:50px;">Meme Generator <a style="font-size:12px; font-weight:normal;" href="#">(Nyasro)</a>
</h2>
        <div class="clear"></div>
        <div>
            <div class="file_uploader" style="margin-right:300px; padding-right:20px;">
                
            
                <form action="<?php echo $urla; ?>" method="post" enctype="multipart/form-data" >
                    
                    <input type="file" name="image" />
                    <input type="submit" value="Upload" name="upload" class="button-primary" />
                    
                </form>
                <br/>
                <form action="<?php echo $urla; ?>" method="post">
                	<input type="radio"<?php echo $chk[0] ?> name="watermark" value="no" />
                    <label>Disable watermark on pictures </label>
                    <br/>
                	<input type="radio" <?php echo $chk[1] ?> name="watermark" value="yes" />
                    <label>Add watermark on pictures </label><br/>
                    <input type="text" value="<?php echo $wtr_text;?>" name="wtr_text" /><br/>
                    <input  class="button-primary" type="submit" value="Save Changes" name="sub_form" />
                </form>
                <hr />
                <!-- font uploader -->
                <h4>Upload fonts</h4>
                <form action="<?php echo $urla; ?>" method="post" enctype="multipart/form-data" >
                    <input type="file" name="font" />
                    <input type="submit" value="Font Upload" name="upload" class="button-primary" />
                </form>
                <hr />
                Uploaded fonts list : 
                <select>
                <?php
					$gpath	= $pluginpath.'/fonts/';
					$fonts	= glob( $gpath.'*');
					foreach( $fonts as $font )
					{
						echo '<option>'.strtok(basename( $font ),'.').'</option>';
					}
				 ?>
                <?php ?>
                </select>
                <!-- font uploader -->
                <h3>List of Main Images</h3>
                <div class="image_list">
                    <?php
                        $paths		= $abspath.'thumbnail/';
                        if($soarr){
                        foreach($soarr as $image)
                        { 
                            $title	= strtok(basename( $image ),'.');
                            $image	= $paths.$image;
                        ?>
                            <div style="float:left;">
                                <h4 style="text-align:center"><?php echo $title; ?> | <a href="<?php echo $urla.'&imgsdel='.$title; ?>">del</a></h4>
                                <img style="padding:5px; margin:5px; border:1px solid #aaa; display:block;" src="<?php echo $image.'.jpg'; ?>" width="150" />
                            </div>
                        <?php }
                        }else
                        {
                            echo 'Main images are empty. Click <i>stick</i> to make is sticky on meme generator page.';	
                        }
                    ?>
                </div>
                
                
                <div style="clear:left;"></div> 
                <hr  />
                <h3>List of Random Images</h3>            
                <div class="image_list">
                    <?php
                        $path 		= $abspath.'thumbnail/';
                        $images		= glob($path.'*.*'); 
    					
                        foreach($images as $image)
                        { 
                            $title	= strtok(basename( $image ),'.');
                        ?>
                            <div style="float:left; overflow:hidden; height:200px;">
                                <h4 style="text-align:center; margin-bottom:3px;"><?php echo $title; ?> | <a href="<?php echo $urla.'&imgs='.$title; ?>">stick</a> | <a href="<?php echo $urla.'&imgx='.$title; ?>">x</a></h4>
                                <img style="padding:5px; margin:5px; border:1px solid #aaa; display:block;" src="<?php echo $image; ?>" width="150" />
                            </div>
                        <?php }
						if(!$images)
						{
							echo 'Please upload atleast four images.';
							update_option('meme_image_count',0);
						}
                    ?>
                </div>
            </div>
        </div>
    </div>			
			
            
			
<?php
		}
		
		
	}
	
	new Nyasro_MemeGenerator;
	
	
}






?>
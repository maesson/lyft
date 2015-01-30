<?php

define('UPLOADED_IMAGE_DESTINATION', dirname(__FILE__).'/images/uploaded/');
define('MAIN_IMAGE_DESTINATION',dirname(__FILE__).'/images/main/');
define('THUMBNAIL_IMAGE_DESTINATION', dirname(__FILE__).'/images/thumbnail/');


function generate_image_thumbnail($uploaded_image_path,$thumbnail_image_path,$th_width,$th_height,$watermark=false)
{
	$source_image_path = $uploaded_image_path;
	
    list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
    switch ($source_image_type) {
        case IMAGETYPE_GIF:
            $source_gd_image = imagecreatefromgif($source_image_path);
            break;
        case IMAGETYPE_JPEG:
            $source_gd_image = imagecreatefromjpeg($source_image_path);
            break;
        case IMAGETYPE_PNG:
            $source_gd_image = imagecreatefrompng($source_image_path);
            break;
    }
    if ($source_gd_image === false) {
        return false;
    }
    $source_aspect_ratio = $source_image_width / $source_image_height;
    $thumbnail_aspect_ratio = $th_width / $th_height;
    if ($source_image_width <= $th_width && $source_image_height <= $th_height) {
        $thumbnail_image_width = $source_image_width;
        $thumbnail_image_height = $source_image_height;
    }else {
        $thumbnail_image_width = $th_width;
        $thumbnail_image_height = (int) ($th_width / $source_aspect_ratio);
    }
    $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
    imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
	
	$get_opt		= get_option('meme_form_data');
	$wtr_text		= false;
	if($get_opt['watermark'] === 'yes' )
		$wtr_text	= $get_opt['wtr_text'];
	if( $wtr_text && $watermark )
	{
		$tran		= imagecolorallocatealpha( $thumbnail_gd_image, 255, 255, 255, 0.85);
		imagestring($thumbnail_gd_image, 5, 5,$thumbnail_image_height-15, $wtr_text,$tran);
	}
	
    imagejpeg($thumbnail_gd_image,$thumbnail_image_path,100);
    imagedestroy($source_gd_image);
    imagedestroy($thumbnail_gd_image);
    return true;
}

function process_image_upload($field)
{
	if(!isset($_FILES[$field]['tmp_name']) || ! $_FILES[$field]['name'])
	{
		return 'empty';
	}
		
    $temp_image_path = $_FILES[$field]['tmp_name'];
    $temp_image_name = $_FILES[$field]['name'];
    list($iwidth, $iheight, $temp_image_type) = getimagesize($temp_image_path);
    if ($temp_image_type === NULL) {
        return false;
    }
	elseif( $iwidth < 400 )
		return 'size';
    switch ($temp_image_type) {
        case IMAGETYPE_GIF:
            break;
        case IMAGETYPE_JPEG:
            break;
        case IMAGETYPE_PNG:
            break;
        default:
            return false;
    }
    $uploaded_image_path = UPLOADED_IMAGE_DESTINATION . $temp_image_name;
    move_uploaded_file($temp_image_path, $uploaded_image_path);
    $thumbnail_image_path = THUMBNAIL_IMAGE_DESTINATION . preg_replace('{\\.[^\\.]+$}', '.jpg', $temp_image_name);

	$main_image_path = MAIN_IMAGE_DESTINATION . preg_replace('{\\.[^\\.]+$}', '.jpg', $temp_image_name);
	
	$result = generate_image_thumbnail($uploaded_image_path,$thumbnail_image_path,150,150);
	if( $result )
    	$result = generate_image_thumbnail($uploaded_image_path,$main_image_path,400,400, true);
    return $result ? array($uploaded_image_path, $thumbnail_image_path) : false;
}

if(isset($_POST['upload'])):
$result = process_image_upload('image');
if ($result === false) {
    update_option('meme_message','<div class="error" style="margin-left:0;"><p>An error occurred while processing upload</p></div>');
} else if( $result === 'empty')
{
			update_option('meme_message','<div class="error" style="margin-left:0;"><p>Please select a Image file</p></div>');

}
elseif( $result === 'size')
{
	update_option('meme_message','<div class="error" style="margin-left:0;"><p>Image width must be greater than 400px;</p></div>');
}
else {
		update_option('meme_message','<div style="margin-left:0;" class="updated"><p>Image uploaded successfully</p></div>');
		$guploaderr = false;
		$count		= intval(get_option('meme_image_count'));
		update_option('meme_image_count', $count+1);
}
endif;

?>
<?php
if(!isset($_REQUEST['memetext'])) return false;

global $submit,$ppath,$fontsize,$fontstrike,$fontfile,$offset,$textpos,$updown,$textcaps,$mainimg;

	$fontsize	= 20;
	$fontstrike	= 2;
	$fontfile	= 'nymeme.ttf';
	$offset		= 10;
	if( isset($_REQUEST['font']) )
		$fontfile	= $_REQUEST['font'];

	$fontfile	= 'fonts/'.$fontfile;
	
	
	$ppath		= '';
	$submit		= false;
	$textarr	= isset($_REQUEST['memetext'])?array_map('stripslashes',$_REQUEST['memetext']):false;
	$textpos	= $_REQUEST['textpos'];
	$textcaps	= $_REQUEST['textcaps'];
	$updown		= $_REQUEST['textupdown'];
	$width		= $_REQUEST['width'];
	$height		= $_REQUEST['height'];

	$text	= $textarr;

if(isset($_POST['submit']))
{
	$submit		= true;
	$ppath		= ABSPATH.'wp-content/plugins/'.basename(dirname(__FILE__)).'/';
	
	if( isset($_POST['mainimg']) && file_exists($ppath.'images/main/'.basename($_POST['mainimg'],'.jpg').'.jpg'))
		$mainimg	= $_POST['mainimg'];
	else return false;
}
else
	header('Content-type: image/png');


	
generate_image_memetext($text,$width, $height);

function generate_image_memetext($text,$width=400,$height=400)
{
	global $submit,$ppath,$textpos,$updown,$textcaps,$mainimg;
	if($submit)			
	{
		$txt_img 	= imagecreatefromjpeg($ppath.'images/main/'.basename($mainimg,'.jpg').'.jpg');
	}
	else	
		$txt_img 	= imagecreatetruecolor($width, $height);
		
	$transbak	= imagecolorallocate($txt_img, 0, 0, 0);
	$trans		= imagecolorallocatealpha($txt_img, 255, 255, 255, 255);
	
	if($submit === false)
	{
		imagecolortransparent( $txt_img , $transbak);
		imagefill( $txt_img, 0, 0, $trans);
		imagealphablending($txt_img, true);
	}
	
	$pos = 1;
	$i	= 0;
	foreach($text as $tx)
	{
		if($textcaps[$i] === 'caps')
			$tx	= strtoupper($tx);
		elseif($textcaps[$i] === 'small')
			$tx	= strtolower($tx);
		meme_text( $txt_img , $tx , $pos++, (int)$textpos[$i], $updown[$i]);
		$i++;
		
	}
		
	if($submit)
		imagejpeg($txt_img,$ppath.'images/meme/'.$_POST['mid'].'.jpg',100);
	else
   		imagepng($txt_img);
    imagedestroy($txt_img);
}



function meme_text( $img , $text, $position, $lcrpos, $updown)
{
	global $ppath,$fontsize,$fontstrike,$fontfile,$offset;
	
	$font	= $ppath.$fontfile;
	
	$bbox 	= imagettfbbox( $fontsize , 0, $font, $text);
	
	$imgw	= imagesx($img);
	$imgh	= imagesy($img);
	
	$x 		= $bbox[0] + (imagesx($img) / 2) - ($bbox[4] / 2) - 100;
	$y 		= $bbox[1] + (imagesy($img) / 2) - ($bbox[5] / 2) - 5; 
	
	$fw		= abs($bbox[2] - $bbox[0]);
	$fh		= abs($bbox[7] - $bbox[1]);
	
	$centerx		= ($imgw - $fw) /2 	;
	$centery		= ($imgh + $fh) /2 	;
	
	
	if($lcrpos === 1)
	{
		$xx	= $offset;
	}
	elseif($lcrpos === 2)
	{
		$xx	= $centerx;
		
	}
	elseif($lcrpos === 3)
	{
		$xx	= $imgw - $fw - $offset;
	}
	
		$yy = $position * ($fontsize + 2 * $fontstrike);
		$yy = $yy + (int)$updown * 3 ;
	
	/*switch( $position )
	{
		case 'top' : $yy = 20;break;
		case 'bottom' : $yy = $imgh - $fh - 20;break;
	}*/
	
	$positionx		= $xx;
	
	$positiony		= +$fh + +$yy;
	
	$col 		= imagecolorallocate( $img , 250 , 250 , 250 );
	$strokecol 	= imagecolorallocate( $img , 25 , 25 , 25 );
	
	imagettfstroketext( $img,$fontsize,0,$positionx,$positiony,$col,$strokecol, $font, $text, $fontstrike);
}

function imagettfstroketext($image, $size, $angle, $x, $y, &$textcolor, $strokecolor, $fontfile, $text, $px) {

    for($c1 = ($x-abs($px)); $c1 <= ($x+abs($px)); $c1++)
        for($c2 = ($y-abs($px)); $c2 <= ($y+abs($px)); $c2++)
            $bg = imagettftext($image, $size, $angle, $c1, $c2, $strokecolor, $fontfile, $text);

   return imagettftext($image, $size, $angle, $x, $y, $textcolor, $fontfile, $text);
}


?>
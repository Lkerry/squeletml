<?php
/*
Fonction UnsharpMask(). Voir http://vikjavev.no/computing/ump.php?id=306

New: 
- In version 2.1 (February 26 2007) Tom Bishop has done some important speed enhancements.
- From version 2 (July 17 2006) the script uses the imageconvolution function in PHP 
version >= 5.1, which improves the performance considerably.


Unsharp masking is a traditional darkroom technique that has proven very suitable for 
digital imaging. The principle of unsharp masking is to create a blurred copy of the image
and compare it to the underlying original. The difference in colour values
between the two images is greatest for the pixels near sharp edges. When this 
difference is subtracted from the original image, the edges will be
accentuated. 

The Amount parameter simply says how much of the effect you want. 100 is 'normal'.
Radius is the radius of the blurring circle of the mask. 'Threshold' is the least
difference in colour values that is allowed between the original and the mask. In practice
this means that low-contrast areas of the picture are left unrendered whereas edges
are treated normally. This is good for pictures of e.g. skin or blue skies.

Any suggenstions for improvement of the algorithm, expecially regarding the speed
and the roundoff errors in the Gaussian blur process, are welcome.

*/

function UnsharpMask($shp_img, $shp_amount, $shp_radius, $shp_threshold)	{ 

////////////////////////////////////////////////////////////////////////
////  
////				  Unsharp Mask for PHP - version 2.1.1  
////  
////	Unsharp mask algorithm by Torstein Hønsi 2003-07.  
////			 thoensi_at_netcom_dot_no.  
////			   Please leave this notice.  
////  
////////////////////////////////////////////////////////////////////////



	// $shp_img is an image that is already created within php using 
	// imgcreatetruecolor. No url! $shp_img must be a truecolor image. 

	// Attempt to calibrate the parameters to Photoshop: 
	if ($shp_amount > 500)	$shp_amount = 500; 
	$shp_amount = $shp_amount * 0.016; 
	if ($shp_radius > 50)	$shp_radius = 50; 
	$shp_radius = $shp_radius * 2; 
	if ($shp_threshold > 255)	$shp_threshold = 255; 
	 
	$shp_radius = abs(round($shp_radius));	 // Only integers make sense. 
	if ($shp_radius == 0) { 
		return $shp_img; imagedestroy($shp_img); break;		} 
	$shp_w = imagesx($shp_img); $shp_h = imagesy($shp_img); 
	$shp_imgCanvas = imagecreatetruecolor($shp_w, $shp_h); 
	$shp_imgBlur = imagecreatetruecolor($shp_w, $shp_h); 
	 

	// Gaussian blur matrix: 
	//						 
	//	1	2	1		 
	//	2	4	2		 
	//	1	2	1		 
	//						 
	////////////////////////////////////////////////// 
		 

	if (function_exists('imageconvolution')) { // PHP >= 5.1  
			$shp_matrix = array(  
			array( 1, 2, 1 ),  
			array( 2, 4, 2 ),  
			array( 1, 2, 1 )  
		);  
		imagecopy ($shp_imgBlur, $shp_img, 0, 0, 0, 0, $shp_w, $shp_h); 
		imageconvolution($shp_imgBlur, $shp_matrix, 16, 0);  
	}  
	else {  

	// Move copies of the image around one pixel at the time and merge them with weight 
	// according to the matrix. The same matrix is simply repeated for higher radii. 
		for ($shp_i = 0; $shp_i < $shp_radius; $shp_i++)	{ 
			imagecopy ($shp_imgBlur, $shp_img, 0, 0, 1, 0, $shp_w - 1, $shp_h); // left 
			imagecopymerge ($shp_imgBlur, $shp_img, 1, 0, 0, 0, $shp_w, $shp_h, 50); // right 
			imagecopymerge ($shp_imgBlur, $shp_img, 0, 0, 0, 0, $shp_w, $shp_h, 50); // center 
			imagecopy ($shp_imgCanvas, $shp_imgBlur, 0, 0, 0, 0, $shp_w, $shp_h); 

			imagecopymerge ($shp_imgBlur, $shp_imgCanvas, 0, 0, 0, 1, $shp_w, $shp_h - 1, 33.33333 ); // up 
			imagecopymerge ($shp_imgBlur, $shp_imgCanvas, 0, 1, 0, 0, $shp_w, $shp_h, 25); // down 
		} 
	} 

	if($shp_threshold>0){ 
		// Calculate the difference between the blurred pixels and the original 
		// and set the pixels 
		for ($shp_x = 0; $shp_x < $shp_w-1; $shp_x++)	{ // each row
			for ($shp_y = 0; $shp_y < $shp_h; $shp_y++)	{ // each pixel 
					 
				$shp_rgbOrig = ImageColorAt($shp_img, $shp_x, $shp_y); 
				$shp_rOrig = (($shp_rgbOrig >> 16) & 0xFF); 
				$shp_gOrig = (($shp_rgbOrig >> 8) & 0xFF); 
				$shp_bOrig = ($shp_rgbOrig & 0xFF); 
				 
				$shp_rgbBlur = ImageColorAt($shp_imgBlur, $shp_x, $shp_y); 
				 
				$shp_rBlur = (($shp_rgbBlur >> 16) & 0xFF); 
				$shp_gBlur = (($shp_rgbBlur >> 8) & 0xFF); 
				$shp_bBlur = ($shp_rgbBlur & 0xFF); 
				 
				// When the masked pixels differ less from the original 
				// than the threshold specifies, they are set to their original value. 
				$shp_rNew = (abs($shp_rOrig - $shp_rBlur) >= $shp_threshold)  
					? max(0, min(255, ($shp_amount * ($shp_rOrig - $shp_rBlur)) + $shp_rOrig))  
					: $shp_rOrig; 
				$shp_gNew = (abs($shp_gOrig - $shp_gBlur) >= $shp_threshold)  
					? max(0, min(255, ($shp_amount * ($shp_gOrig - $shp_gBlur)) + $shp_gOrig))  
					: $shp_gOrig; 
				$shp_bNew = (abs($shp_bOrig - $shp_bBlur) >= $shp_threshold)  
					? max(0, min(255, ($shp_amount * ($shp_bOrig - $shp_bBlur)) + $shp_bOrig))  
					: $shp_bOrig; 
				 
				 
							 
				if (($shp_rOrig != $shp_rNew) || ($shp_gOrig != $shp_gNew) || ($shp_bOrig != $shp_bNew)) { 
						$shp_pixCol = ImageColorAllocate($shp_img, $shp_rNew, $shp_gNew, $shp_bNew); 
						ImageSetPixel($shp_img, $shp_x, $shp_y, $shp_pixCol); 
					} 
			} 
		} 
	} 
	else{ 
		for ($shp_x = 0; $shp_x < $shp_w; $shp_x++)	{ // each row 
			for ($shp_y = 0; $shp_y < $shp_h; $shp_y++)	{ // each pixel 
				$shp_rgbOrig = ImageColorAt($shp_img, $shp_x, $shp_y); 
				$shp_rOrig = (($shp_rgbOrig >> 16) & 0xFF); 
				$shp_gOrig = (($shp_rgbOrig >> 8) & 0xFF); 
				$shp_bOrig = ($shp_rgbOrig & 0xFF); 
				 
				$shp_rgbBlur = ImageColorAt($shp_imgBlur, $shp_x, $shp_y); 
				 
				$shp_rBlur = (($shp_rgbBlur >> 16) & 0xFF); 
				$shp_gBlur = (($shp_rgbBlur >> 8) & 0xFF); 
				$shp_bBlur = ($shp_rgbBlur & 0xFF); 
				 
				$shp_rNew = ($shp_amount * ($shp_rOrig - $shp_rBlur)) + $shp_rOrig; 
					if($shp_rNew>255){$shp_rNew=255;} 
					elseif($shp_rNew<0){$shp_rNew=0;} 
				$shp_gNew = ($shp_amount * ($shp_gOrig - $shp_gBlur)) + $shp_gOrig; 
					if($shp_gNew>255){$shp_gNew=255;} 
					elseif($shp_gNew<0){$shp_gNew=0;} 
				$shp_bNew = ($shp_amount * ($shp_bOrig - $shp_bBlur)) + $shp_bOrig; 
					if($shp_bNew>255){$shp_bNew=255;} 
					elseif($shp_bNew<0){$shp_bNew=0;} 
				$shp_rgbNew = ($shp_rNew << 16) + ($shp_gNew <<8) + $shp_bNew; 
					ImageSetPixel($shp_img, $shp_x, $shp_y, $shp_rgbNew); 
			} 
		} 
	} 
	imagedestroy($shp_imgCanvas); 
	imagedestroy($shp_imgBlur); 
	 
	return $shp_img; 
}

?>

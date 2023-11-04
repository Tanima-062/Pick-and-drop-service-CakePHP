<?php
class FileUpComponent extends Component{

	function uploadimagefile($__data,$filename,$set_size,$dir){

		if (!empty($__data['name'])){
			$tmp_file = $__data['tmp_name'];
			$imginfo = getimagesize($__data['tmp_name']);
			//pr($imginfo);
			$filesize = $__data['size'];

			clearstatcache();
			if ($filesize > 1000000 || ($imginfo[2] < 1 || $imginfo[2] > 3)) {
				//$this->set('file_error',true);
				//$this->Session->setFlash('画像のアップロードに失敗しました');
				return false;
			}


			//adminからアップしているのでadminディレクトリより上の階層のimgディレクトリにアップする
			$upload_path = "../img/".$dir."/";
			$width_old  = $imginfo[0];
			$height_old = $imginfo[1];

			$width_new = $set_size;
			$height_new = $height_old * ($width_new / $width_old);

			switch ($imginfo[2]) {
				case 2: // jpeg
					$jpeg = imagecreatefromjpeg($tmp_file);
					$jpeg_new = imagecreatetruecolor($width_new, $height_new);
					imagecopyresampled($jpeg_new,$jpeg,0,0,0,0,$width_new,$height_new,$width_old,$height_old);
					imagejpeg($jpeg_new, $upload_path . $filename, 100);

					break;
				case 1: // gif
					$gif = imagecreatefromgif($tmp_file);
					$gif_new = imagecreatetruecolor($width_new, $height_new);
					imagecopyresampled($gif_new,$gif,0,0,0,0,$width_new,$height_new,$width_old,$height_old);
					imagegif($gif_new, $upload_path . $filename, 100);
					break;
				case 3: // png
					$png = imagecreatefrompng($tmp_file);
					$png_new = imagecreatetruecolor($width_new, $height_new);
					ImageAlphaBlending($png_new,false);
					ImageSaveAlpha($png_new,true);
					imagecopyresampled($png_new,$png,0,0,0,0,$width_new,$height_new,$width_old,$height_old);
					imagepng($png_new, $upload_path . $filename);
					break;
				Default:
					break;
			}
			return $filename;
		} else {
			return false;
		}
	}


	function getNewFileName($data) {

		$newTime = time();
		$ranNum = rand()*99;
		$p = pathinfo($data);
		$extensions = $p['extension'];
		return $newTime.$ranNum.".".$extensions;

	}
}
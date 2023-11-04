<?php
App::uses('Folder', 'Utility');
class ImageResizeUpLoad {

	/**
	 * 画像をアップロードする関数
	 * @param unknown $file
	 * @param string $upLoadDir
	 * @param unknown $prefix
	 * @param array $referenceSize
	 * @return boolean|unknown|string
	 */
	public function resizeUpLoad($file, $upLoadDir = 'commodity', $prefix = null, $referenceSize = array(640, 480)) {

		$result = false;
		$resizeFlg = true;

		// 画像の保存先
		$uploadPath =  ROOT.DS.WEBROOT_DIR.DS."img".DS.$upLoadDir;
		// フォルダの有無
		$folder = new Folder($upLoadDir);
		if (empty($folder->path)) {
			// フォルダ作成
			$folder = new Folder($uploadPath, true, 0777);
		}
		// 保存先
		$imagePostPath = $folder->path;

		// 画像の基準サイズ
		$referenceSizeW = $referenceSize[0];
		$referenceSizeH = $referenceSize[1];
		$imgQuality = 75;

		if (is_uploaded_file($file["tmp_name"])) {
			// 画像情報
			$imgInfo = getimagesize($file["tmp_name"]);
			// 拡張子判別
			if (file_exists($file['tmp_name']) && $type = exif_imagetype($file['tmp_name'])) {
				switch ($type) {
					case IMAGETYPE_GIF :
						$srcImage = imagecreatefromgif($file['tmp_name']);
						$imageStorage = IMAGETYPE_GIF;
						$extension = '.gif';
						break;
					case IMAGETYPE_JPEG :
						$srcImage = imagecreatefromjpeg($file['tmp_name']);
						$imageStorage = IMAGETYPE_JPEG;
						$extension = '.jpg';
						break;
					case IMAGETYPE_PNG :
						$srcImage = imagecreatefrompng($file['tmp_name']);
						$imageStorage = IMAGETYPE_PNG;
						$extension = '.png';
						break;
					default :
						$extension = false;
				}
			}

			// 画像サイズ判定
			if ($referenceSizeW == $imgInfo[0] && $referenceSizeH == $imgInfo[1]) {
				$resizeFlg = false;
			}

			// 画像リサイズ
			if (!empty($extension) && $resizeFlg) {

				$newTime = time();
				$randNum = mt_rand();
				if (!empty($prefix)) {
					$fileName = $prefix.'_'.$newTime.$randNum.$extension;
				} else {
					$fileName = $newTime.$randNum.$extension;
				}

				// サイズ計算数値（可変）
				$dstW = $referenceSizeW;
				$dstH = $referenceSizeH;
				if ($imgInfo[0] > $imgInfo[1]) {
					$dstH = ceil($referenceSizeW * $imgInfo[1] / max($imgInfo[0], 1));
				} else {
					$dstW = ceil($referenceSizeH * $imgInfo[0] / max($imgInfo[1], 1));
				}
				$dstImage = imagecreatetruecolor($dstW, $dstH);
				imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $dstW, $dstH, $imgInfo[0], $imgInfo[1]);

				// 背景画像を生成
				$baseImg = imagecreatetruecolor($referenceSizeW, $referenceSizeH);
				$bgColor = imagecolorallocate($baseImg, 255, 255, 255);
				imagefilledrectangle($baseImg, 0, 0, $referenceSizeW, $referenceSizeH, $bgColor);

				imagecopy($baseImg, $dstImage, ($referenceSizeW / 2) - ($dstW / 2), ($referenceSizeH / 2) - ($dstH / 2), 0, 0, $dstW, $dstH);

				// 画像の保存
				if ($imageStorage == IMAGETYPE_GIF) {
					// GIF
					imagegif($baseImg, $imagePostPath.DS.$fileName);
					$result = $fileName;
				} elseif ($imageStorage == IMAGETYPE_JPEG) {
					// JPG
					imagejpeg($baseImg, $imagePostPath.DS.$fileName, $imgQuality);
					$result = $fileName;
				} elseif ($imageStorage == IMAGETYPE_PNG) {
					// PNG
					imagepng($baseImg, $imagePostPath.DS.$fileName, 0);
					$result = $fileName;
				} else {
					$this->validationErrors['file'] = __('ファイルの保存に失敗しました');
				}

				// リソースを解放
				if (isset ($srcImage)&& is_resource($srcImage)) {
					imagedestroy($srcImage);
				}
				if (isset($dstImage) && is_resource($dstImage)) {
					imagedestroy($dstImage);
				}
				if (isset($bgImg) && is_resource($bgImg)) {
					imagedestroy($bgImg);
				}
				if (isset($baseImg) && is_resource($baseImg)) {
					imagedestroy($baseImg);
				}

			} else if (!empty($extension) && !$resizeFlg) {
				$newTime = time();
				$randNum = mt_rand();
				if (!empty($prefix)) {
					$fileName = $prefix.'_'.$newTime.$randNum.$extension;
				} else {
					$fileName = $newTime.$randNum.$extension;
				}
				if(move_uploaded_file($file["tmp_name"], $imagePostPath.DS.$fileName)){
					chmod($imagePostPath.$fileName, 0644);
					$result = $fileName;
				} else {
					$this->validationErrors['file'] = __('ファイルの保存に失敗しました');
				}
			}else {
				$this->validationErrors['file'] = __('不正なファイル形式です');
			}
		} else {
			$this->validationErrors['file'] = __('アップロードに失敗しました。');
		}

		return $result;
	}


}

?>
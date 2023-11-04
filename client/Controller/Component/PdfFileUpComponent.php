<?php
class PdfFileUpComponent extends Component{



	/***************************************************
	 * PDFファイルのみアップロードする
	* 引数：
	* 	１．<input type="file"/>でアップロードしたデータ
	* 	２．アップロード後のファイルにつける名前
	* 	３．アップロード先のディレクトリ名（最後に/をつける必要なし）
	* 戻り値：成功した場合はアップロード後のファイル名（保存されたファイル名）
	* 		　失敗した場合にはfalse
	***************************************************/
	public function upload_pdf($data,$name,$dir){

		 $upload_path = "../img/files/".$dir."/";

		if(!empty($data["tmp_name"]) && $data["size"]>0){
			$name_array = explode(".",$data["name"]);
			$extention = $name_array[count($name_array)-1];
			if($extention == "pdf"){

				if(!file_exists($upload_path)) {
					mkdir($upload_path,'0777');
				}

				$file_path = $upload_path.$name;

				if(move_uploaded_file($data["tmp_name"],$file_path)){
					return $name;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

}

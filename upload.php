<?php
class Upload{
	var $lokasi_simpan ="images/";
	var $batas_ukuran_file =500000;
	var $error;
	var $hasil;
	public function ukuran($file){
		return $file['size'];
	}

	public function hapusFile($fileName){
		unlink ($this->lokasi_simpan.$fileName);//unlink adalah untuk hapus
	}

	public function cekEkstensi($file){
		//pecah berdasarkan titik untuk mendapatkan ekstensi file
		//function ini akan menghasilkan ekstensi spt :jpg,png
		// var_dump(strtolower(end(explode(".", $file["name"]))));die();
		return strtolower(end(explode(".", $file["name"])));
	}
	public function unggah($file){
		/*
		1. rename file sesuai dengan tanggal dan jam saat ini_get
		2. cek apakah ukuran file lebih besar
		3. cek apakah file diupload berformat/ekstensi gambar
		4.upload . (jika gagal munculkan error)
		*/

		$ukuran = $this->ukuran($file);

		//cek ukuran
		if($ukuran == 0){//jika tak upload file
			$this->hasil = array("status" => "1", "info" => null);
		}else{
			//ambil ekstensi
			$ekstensi = $this->cekEkstensi($file);

			/*
			set nama file yang akan di upload menjadi "img_thnblntgl_jammntdtk.ekxtensi"
			*/
			$namaBaru ="img_". date("Ymd_His").".".$ekstensi;
			$targetFile = $this->lokasi_simpan . $namaBaru;

			//cek, apakah file sudah ada ?
			if(file_exists($targetFile)) $this->error = "File sudah ada";

			if($ukuran > $this->batas_ukuran_file){
				$this->error =$ukuran . "melebihi". $this->batas_ukuran_file . "byte";
			}

			//cek format file apakah berupa gambar yang diperbolehkan
			if($ekstensi != "jpg"  &&
				$ekstensi != "jpeg"  &&
				$ekstensi != "png"  &&
				$ekstensi != "gif"){
					$this->error = "File bukan berupa gambar";
			}

			//cek apakah ada error dri proses cek ukuran, file sudah ada dan file format
			if(strlen($this->error) > 0){
				//jika ada kesalahan tampilkan pesan errornya
				$this->hasil = array("status" => "0", "info" => "error: ". $this->error);
			}else{
				if(move_uploaded_file($file["tmp_name"], $targetFile)){
					$this->hasil =array("status" => "1", "info" =>$namaBaru);
				}else{
					$this->hasil =array("status" => "0", "info" => "error: file tak terupload ");
				}
			}
			return $this->hasil;
		}
	}
}
?>
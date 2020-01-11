<?php
/* Class ini berfungsi u/memudahkan transaksi dengan mysql
terdiri dari fungsi : mengubungkan database,
eksekusi data, membaca data dan menutup koneksi terhubung */

class DB{
	protected $connection;
	protected $query;
	
	
	var $dbhost = "localhost";
	var $dbname = "produk";
	var $dbuser = "root";
	var $dbpass = "";
	var $dbcharset = "utf-8";
	
	function __construct (){ //dibuild saat pertama kali class di panggil */
		$this->connection = new mysqli($this->dbhost, $this->dbuser,$this->dbpass,$this->dbname);
		
		
		//cek khawatir ada kesalahan
		if($this->connection->connect_error){
			die("Error connection: ". $this->connection->connect_error);
		}
		
		$this->connection->set_charset($this->dbcharset);
	}
	
	function query($query){
		if($this->query = $this->connection->prepare($query)){
			$this->query->execute(); //eksekusi
			
			//cek kalau error
			if($this->query->errno){
				die("Error execution: ". $this->query->error);
			}
		}else{
			die("Error execution: ". $this->query->error);
		}
	}
	
	function getList($query){
		if($this->query = $this->connection->prepare($query)){
			$this->query->execute(); //eksekusi
			
			$result = $this->query->get_result(); // ini yg ditambahkan dari function query diatas
			
			//cek kalau error
			if($this->query->errno){
				die("Error execution: ". $this->query->error);
			}else{
				//ambil data dan extract kedalam bentuk array
				$parameters = array();
				
				while($row = $result->fetch_array()){
					$parameters[] = $row;
				}
				return $parameters;
			}
		}else{
			die("Error execution: ". $this->query->error);
		}
	}
	
	function close(){
		return $this->connection->close();
	}
}
?>
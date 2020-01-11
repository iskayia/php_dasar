<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <title>Data Produk</title>
    <style>
    .css-serial {
      counter-reset: serial-number;  
    }
    .css-serial td:first-child:before {
      counter-increment: serial-number;  
      content: counter(serial-number);  /* Tampilan counter */
    }
    </style>
</head>
<body>
  <?php
  include("db.php");
  include("upload.php");
  $action = (!isset($_REQUEST["action"]))? null : $_REQUEST["action"];
  if($action == null){
  ?>

  <h3 style="text-align:center;">Data Produk</h3>
  <div class="container">
    <form action="<?php $_SERVER["PHP_SELF"] ?>" method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label for="Nama">Nama produk</label>
        <input type="text" class="form-control" name="nama">
      </div>
      <div class="form-group">
          <label for="sel1">Kategori</label>
          <select class="form-control" id="sel1" name="kategori">
            <?php
              $d = new DB(); 
              $sql = "select kategori_id, kategori_desc from kategori";
              $row = $d->getList($sql);
              for($i = 0;$i < count($row); $i++){ 
                $select = ($result[0]["kategori_id"]== $row[$i]["kategori_id"])? "seleced" : null;
            ?>
              <option value="<?= $row[$i]["kategori_id"]?>" > <?= $row[$i]["kategori_desc"]?></option>
            <?php 
              } 
            ?>
          </select>
      </div>
      <div class="form-group">
        <label for="Nama">Harga </label>
        <input type="text" class="form-control" name="harga">
      </div>
      <div class="form-group">
        <label for="Nama">Foto</label>
        <input type="file" class="form-control" name="foto">
      </div>
     <input type="submit" class="btn btn-primary" name="action" value="Simpan" style="width:150px; ">
     <input type="reset" class="btn btn-warning" name="reset" value="Ulangi" style="width:150px; margin-top: 5px;">
    </form>
      <br><br>
    <h3 style="text-align:center;">List Data Produk</h3>
      <br>
    <table id="example" class="table table-striped table-bordered css-serial" >
    <thead>
    <tr>
        <th>No</th>
        <th>Gambar</th>
        <th>Nama Produk</th>
        <th>Kategori</th>
        <th>Harga</th>
        
        <th>&nbsp; Aksi</th>

      </tr>
    </thead>
    <tbody id="myTable">
        <?php
        

    $d = new DB(); 
    $sql = "select pd.*, kt.kategori_desc from produk pd join kategori kt on kt.kategori_id = pd.kategori_id";
    $hasil=$d->getList($sql);
    
    for($i = 0; $i < count($hasil); $i++){
      ?>
      <tr>
        <td  align="center"></td>
        <td>
        
        <?php if ($hasil[$i]["foto_produk"] !="") {?>
              <img src="./images/<?= $hasil[$i]["foto_produk"]?>" height="100px"> 
              <?php } ?>
      </td>
      <td><?= $hasil[$i]["nama_produk"]?></td>
      <td><?= $hasil[$i]["kategori_desc"]?></td>
      <td><?= $hasil[$i]["harga"]?></td>
      
      
      
      <td>
      <a href="produk.php?action=Ubah&id=<?= $hasil[$i]["kode_produk"]?>" class="btn  btn-primary btn-lg">Ubah</a>
      <a href="produk.php?action=Hapus&id=<?= $hasil[$i]["kode_produk"]?>" class="btn  btn-danger btn-lg">Hapus</a>
      </td>
      </tr>
      <?php
    }
        ?>
    </tbody>
  </table>
  <script>
  $(document).ready(function() {
    $('#example').DataTable();
} );</script>
        </div>
      </div>
</div>

<script>
$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>
      
  </div>
  <?php
  }elseif($action == "Simpan"){
    $u= new Upload();
    $hasil= $u->unggah($_FILES["foto"]);
    
    if ($hasil["status"]== "0"){
      die ("Upload gagal <a href='#' onclick='window.history.back()'>coba lagi</a>");
    }else{
      $d = new DB(); 
      $sql = "insert into produk (nama_produk, kategori_id, harga, foto_produk) values ( "
        ." '".$_REQUEST['nama']."', '".$_REQUEST['kategori']."', '".$_REQUEST['harga']."', '".$hasil["info"]."')";
      $d->query($sql); 
    
    header("location: produk.php"); 
    }

    
  }elseif($action == "Update"){
    $u = new Upload();
           if($_REQUEST["temp_foto"] != "") $u->hapusFile($_REQUEST["temp_foto"]);
       $ukuran = $u->ukuran($_FILES["foto"]);
        if ($ukuran >0){
        $hasil =$u->unggah($_FILES["foto"]);

        $sql = "update produk set " 
        
        ."nama_produk = '".$_REQUEST['nama']."', "
        ."kategori_id = '".$_REQUEST['kategori']."', "
        ."harga = '".$_REQUEST['harga']."', "
        ."foto_produk = '".$hasil['info']."' "
        
        ."where kode_produk = ".$_REQUEST['kodeproduk'];
      }else{
        $sql = "update produk set "
        ."nama_produk = '".$_REQUEST['nama']."', "
        ."kategori_id = '".$_REQUEST['kategori']."', "
        ."harga = '".$_REQUEST['harga']."' "
        
        
        ."where kode_produk = ".$_REQUEST['kodeproduk'];
      }
        $d = new DB(); 
        $d->query($sql); 
        
        header("location: produk.php"); 
    
  }elseif($action == "Hapus"){
    $d = new DB(); 
    $sql = "delete from produk where kode_produk = ".$_REQUEST['id'];
    $d->query($sql); //jalankan function query untuk eksekusi sql

    header("location: produk.php"); //redirect

  }elseif($action == "HapusGambar"){
        
      $u = new Upload();
      $u->hapusFile($_REQUEST["foto"]);//hapus gambar

      $d = new DB();//mengaktifkan class DB;
        //kosongkan kolom foto
              $sql = "update produk set foto_produk = null where kode_produk = ".$_REQUEST['id'];    
              $d->query($sql);//jalankan function query u/ eksekusi sql
        //kembalikan ke proses edit
              header("location: produk.php?action=Ubah&id=".$_REQUEST["id"]); //redirect

  }elseif($action == "Ubah"){
    $d = new DB(); //Mengaktifkan class DB
    $sql = "select * from produk where kode_produk = ".$_REQUEST['id'];
    $result = $d->getList($sql); //jalankan function query untuk eksekusi sql
    ?>
    <!--silahkan copy form dari atas!!-->
    <div class="container">
    <div class="panel panel-info"><br/>
        <div class="panel-heading"style="text-align:center; font-size:20px;">Edit Data produk</div>
        <div class="panel-body">
          <form action="<?php $_SERVER["PHP_SELF"] ?>" method="POST" enctype="multipart/form-data">
      <div class="col-md-6">
        <div class="form-group">
          <label for="Nama">Nama produk</label>
          <input value="<?= $result[0]["nama_produk"]?>" type="text" class="form-control" name="nama">
        </div>

        <div class="form-group">
            <label for="sel1">Kategori</label>
            <select class="form-control" id="sel1" name="kategori">
    <?php
    $d = new DB(); //Mengaktifkan class DB
    $sql = "select kategori_id, kategori_desc from kategori";
    $row = $d->getList($sql);
    
    for($i = 0;$i < count($row); $i++){ ?>
    
    <option value="<?= $row[$i]["kategori_id"]?>" > <?= $row[$i]["kategori_desc"]?></option>
    <?php } 
    ?>
    </select>
         </div>
         </div>
        <div class="col-md-6">
           <div class="form-group">
          <label for="Nama">Harga </label>
          <input type="text" class="form-control" name="harga">
         </div>
         <div class="form-group">
          <label for="Nama">Foto</label>
          <input type="file" class="form-control" name="foto"> <br>

          <?php
      //jika gambarnya ada maka munculkan dengan tombol hapus gambarnya
      if(!$result[0]["foto_produk"] == ""){?>
        <img src="./images/<?= $result[0]["foto_produk"]?>" height="150px"> 
      <a href="produk.php?action=HapusGambar&id=<?= $_REQUEST['id'] ?>&foto=<?= $result[0]["foto_produk"]?>" class="btn btn-danger">Hapus Foto</a><br>
      <?php } ?>
        </div>
  </div> 
      <input value="<?= $result[0]["foto"] ?>" type="hidden" name="temp_foto"/>
      <input value="<?= $result[0]["kode_produk"]?>" type="hidden" name="kodeproduk"/><br/>

<div class="col-md-6">
      <input type="submit" class="btn btn-success" name="action" value="Update"/><br/>
      <input type="button" class="btn btn-success" name="reset" value="Kembali" onclick="window.history.back()" style="margin-left: 76px;
    margin-top: -52px;"><br/>
    </div>
      </form>
        </div>
      </div>
      </div>
  <?php }
  ?>
</body>
</html>

<?php if ($_GET[act]==''){ ?> 
            <div class="col-xs-12">  
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Data Administrator </h3>
                  <?php if($_SESSION[level]!='kepala'){ ?>
                    <a class='pull-right btn btn-primary btn-sm' href='index.php?view=admin&act=tambah'>Tambahkan Data Admin</a>
                  <?php } ?>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <table id="example1" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th style='width:30px'>No</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Alamat Email</th>
                        <th>No Telpon</th>
                        <th>Jabatan</th>
                        <th>Level</th>
                        <?php if($_SESSION[level]!='kepala'){ ?>
                        <th style='width:70px'>Action</th>
                        <?php } ?>
                      </tr>
                    </thead>
                    <tbody>
                  <?php 
                    $tampil = mysqli_query($koneksi,"SELECT * FROM users where level='superuser' ORDER BY id_user ASC");
                    $no = 1;
                    while($r=mysqli_fetch_array($tampil)){
                    echo "<tr><td>$no</td>
                              <td>$r[username]</td>
                              <td>$r[nama_lengkap]</td>
                              <td>$r[email]</td>
                              <td>$r[no_telpon]</td>
                              <td>$r[jabatan]</td>
                              <td>$r[level]</td>";
                              if($_SESSION[level]!='kepala'){
                                echo "<td><center>
                                <a class='btn btn-success btn-xs' title='Edit Data' href='?view=admin&act=edit&id=$r[id_user]'><span class='glyphicon glyphicon-edit'></span></a>
                                <a class='btn btn-danger btn-xs alert_notif' title='Delete Data' href='?view=admin&hapus=$r[id_user]'><span class='glyphicon glyphicon-remove'></span></a>
                              </center></td>";
                              }
                            echo "</tr>";
                      $no++;
                      }
                      if (isset($_GET[hapus])){
                          mysqli_query($koneksi,"DELETE FROM users where id_user='$_GET[hapus]'");
                          echo "<script>document.location='index.php?view=admin';</script>";
                      }
                      

                  ?>
                  <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
                  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
                    integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous">
                  </script>
                  <!-- jangan lupa menambahkan script js sweet alert di bawah ini  -->
                  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.7/dist/sweetalert2.all.min.js"></script>
    
    
                  <!-- jika ada session sukses maka tampilkan sweet alert dengan pesan yang telah di set di dalam session sukses  -->
                  <?php if(@$_SESSION['sukses']){ ?>
                    <script>
                      Swal.fire({            
                        icon: 'success',                   
                        title: 'Sukses',    
                        text: 'data berhasil dihapus',                        
                        timer: 3000,                                
                        showConfirmButton: false
                      })
                    </script>
                  <!-- jangan lupa untuk menambahkan unset agar sweet alert tidak muncul lagi saat di refresh -->
                  <?php unset($_SESSION['sukses']); } ?>
    
    
                  <!-- di bawah ini adalah script untuk konfirmasi hapus data dengan sweet alert  -->
                  <script>
                    $('.alert_notif').on('click',function(){
                      var getLink = $(this).attr('href');
                      Swal.fire({
                        title: "Apakah Anda yakin ingin menghapus data ini?",            
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Ya',
                        cancelButtonColor: '#3085d6',
                        cancelButtonText: "Batal"
                
                      }).then(result => {
                        //jika klik ya maka arahkan ke proses.php
                        if(result.isConfirmed){
                          window.location.href = getLink
                        }
                      })
                      return false;
                    });
                  </script>
                    </tbody>
                  </table>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div>
<?php 
}elseif($_GET[act]=='edit'){
    if (isset($_POST[update])){
      $data = md5($_POST[b]);
      $passs=hash("sha512",$data);
      $dir_gambar = 'foto_admin/';
      $filename = basename($_FILES['g']['name']);
      $filenamee = date("YmdHis") . '-' . basename($_FILES['g']['name']);
      $uploadfile = $dir_gambar . $filenamee;
      if ($filename != '') {
        if (move_uploaded_file($_FILES['g']['tmp_name'], $uploadfile)) {
          if (trim($_POST[b])==''){ 
            mysqli_query($koneksi,"UPDATE users SET username = '$_POST[a]',
                                             password = '$passs',
                                             nama_lengkap = '$_POST[c]',
                                             email = '$_POST[d]',
                                             no_telpon = '$_POST[e]',
                                             jabatan = '$_POST[f]',
                                             foto = '$filenamee' where id_user='$_POST[id]'");
          }
        } else{
            mysqli_query($koneksi,"UPDATE users SET username = '$_POST[a]',
                                             password = '$passs',
                                             nama_lengkap = '$_POST[c]',
                                             email = '$_POST[d]',
                                             no_telpon = '$_POST[e]',
                                             jabatan = '$_POST[f]' where id_user='$_POST[id]'");
        }
      }
        
      echo "<script>document.location='index.php?view=admin';</script>";
    }
    $edit = mysqli_query($koneksi,"SELECT * FROM users where id_user='$_GET[id]'");
    $s = mysqli_fetch_array($edit);
    echo "<div class='col-md-12'>
              <div class='box box-info'>
                <div class='box-header with-border'>
                  <h3 class='box-title'>Edit Data Administrator</h3>
                </div>
              <div class='box-body'>
              <form method='POST' class='form-horizontal' action='' enctype='multipart/form-data'>
                <div class='col-md-10'>
                  <table class='table table-condensed table-bordered'>
                  <tbody>
                    <input type='hidden' name='id' value='$s[id_user]'>
                    <tr><th style='background-color:#E7EAEC' width='160px' rowspan='25'>";
                      if (trim($s[foto]) == '') {
                        echo "<img class='img-thumbnail' style='width:155px' src='foto_siswa/no-image.jpg'>";
                      } else {
                        echo "<img class='img-thumbnail' style='width:155px' src='foto_admin/$s[foto]'>";
                      }
                    echo "</th></tr>
                    <input type='hidden' name='id' value='$s[id_user]'>
                    <tr><th width='120px' scope='row'>Username</th> <td><input type='text' class='form-control' name='a' value='$s[username]'> </td></tr>
                    <tr><th scope='row'>Password</th>               <td><input type='text' class='form-control' name='b' placeholder='Kosongkan saja jika password tidak diganti'></td></tr>
                    <tr><th scope='row'>Nama Lengkap</th>           <td><input type='text' class='form-control' name='c' value='$s[nama_lengkap]'></td></tr>
                    <tr><th scope='row'>Alamat Email</th>           <td><input type='text' class='form-control' name='d' value='$s[email]'></td></tr>
                    <tr><th scope='row'>No Telpon</th>              <td><input type='text' class='form-control' name='e' value='$s[no_telpon]'></td></tr>
                    <tr><th scope='row'>Jabatan</th>                <td><input type='text' class='form-control' name='f' value='$s[jabatan]'></td></tr>
                    <tr><th scope='row'>Ganti Foto</th>             <td><div style='position:relative;''>
                      <a class='btn btn-primary' href='javascript:;'>
                      <span class='glyphicon glyphicon-search'></span> Browse..."; ?>
                      <input type='file' class='files' name='g' onchange='$("#upload-file-info").html($(this).val());'>
                      <?php echo "</a> <span style='width:155px' class='label label-info' id='upload-file-info'></span>
                    </td></tr>
                  </tbody>
                  </table>
                </div>
              </div>
              <div class='box-footer'>
                    <button type='submit' name='update' class='btn btn-info'>Update</button>
                    <a href='javascript:window.history.back();'><button type='button' class='btn btn-default pull-right'>Batal</button></a>
                  </div>
              </form>
            </div>";
}elseif($_GET[act]=='tambah'){
    if (isset($_POST[tambah])){
      $data = md5($_POST[b]);
      $passs=hash("sha512",$data);
      $dir_gambar = 'foto_admin/';
      $filename = basename($_FILES['g']['name']);
      $filenamee = date("YmdHis") . '-' . basename($_FILES['g']['name']);
      $uploadfile = $dir_gambar . $filenamee;
      if ($filename != '') {
        if (move_uploaded_file($_FILES['g']['tmp_name'], $uploadfile)) {
          mysqli_query($koneksi,"INSERT INTO users VALUES('','$_POST[a]','$passs','$_POST[c]','$_POST[d]','$_POST[e]','$_POST[f]','$filenamee','superuser')");
        } else {
          mysqli_query($koneksi, "INSERT INTO users VALUES('','$_POST[a]','$passs','$_POST[c]','$_POST[d]','$_POST[e]','$_POST[f]','')");
        }
      }
          
      echo "<script>document.location='index.php?view=admin';</script>";
    }

    echo "<div class='col-md-12'>
              <div class='box box-info'>
                <div class='box-header with-border'>
                  <h3 class='box-title'>Tambah Data Administrator</h3>
                </div>
              <div class='box-body'>
              <form method='POST' class='form-horizontal' action='' enctype='multipart/form-data'>
                <div class='col-md-12'>
                  <table class='table table-condensed table-bordered'>
                  <tbody>
                    <input type='hidden' name='id' value='$s[id_user]'>
                    <tr><th width='120px' scope='row'>Username</th> <td><input type='text' class='form-control' name='a'value='$s[username]'> </td></tr>
                    <tr><th scope='row'>Password</th>               <td><input type='text' class='form-control' name='b'value='$s[password]'></td></tr>
                    <tr><th scope='row'>Nama Lengkap</th>           <td><input type='text' class='form-control' name='c'value='$s[nama_lengkap]'></td></tr>
                    <tr><th scope='row'>Alamat Email</th>           <td><input type='text' class='form-control' name='d' value='$s[email]'></td></tr>
                    <tr><th scope='row'>No Telpon</th>              <td><input type='text' class='form-control' name='e' value='$s[no_telpon]'></td></tr>
                    <tr><th scope='row'>Jabatan</th>                <td><input type='text' class='form-control' name='f' value='$s[jabatan]'></td></tr>
                    <tr><th scope='row'>Foto</th>                   <td><div style='position:relative;''>
                                                                            <a class='btn btn-primary' href='javascript:;'>
                                                                              <span class='glyphicon glyphicon-search'></span> Browse..."; ?>
                      <input type='file' class='files' name='g' onchange='$("#upload-file-info").html($(this).val());'>
                      <?php echo "</a> <span style='width:155px' class='label label-info' id='upload-file-info'></span>
                                                                          </div>
                    </td></tr>
                  </tbody>
                  </table>
                </div>
              </div>
              <div class='box-footer'>
                    <button type='submit' name='tambah' class='btn btn-info'>Tambahkan</button>
                    <a href='index.php?view=admin'><button type='button' class='btn btn-default pull-right'>Batal</button></a>
              </div>
              </form>
            </div>";
}
?>
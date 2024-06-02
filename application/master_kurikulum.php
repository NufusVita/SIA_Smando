<?php if ($_GET[act]==''){ ?> 
            <div class="col-xs-12">  
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Data Kurikulum </h3>
                  <?php if($_SESSION[level]!='kepala'){ ?>
                  <a class='pull-right btn btn-primary btn-sm' href='index.php?view=kurikulum&act=tambah'>Tambahkan Data</a>
                  <?php } ?>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <table id="example1" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th style='width:40px'>No</th>
                        <th>Nama Kurikulum</th>
                        <th>Status Aktif</th>
                        <?php if($_SESSION[level]!='kepala'){ ?>
                        <th style='width:70px'>Action</th>
                        <?php } ?>
                      </tr>
                    </thead>
                    <tbody>
                  <?php 
                    $tampil = mysqli_query($koneksi,"SELECT * FROM kurikulum ORDER BY kode_kurikulum ASC");
                    $no = 1;
                    while($r=mysqli_fetch_array($tampil)){
                    echo "<tr><td>$no</td>
                              <td>$r[nama_kurikulum]</td>
                              <td>$r[status_kurikulum]</td>";
                              if($_SESSION[level]!='kepala'){
                        echo "<td><center>
                                <a class='btn btn-success btn-xs' title='Edit Data' href='index.php?view=kurikulum&act=edit&id=$r[kode_kurikulum]'><span class='glyphicon glyphicon-edit'></span></a>
                                <a class='btn btn-danger btn-xs alert_notif' title='Delete Data' href='index.php?view=kurikulum&hapus=$r[kode_kurikulum]'><span class='glyphicon glyphicon-remove'></span></a>
                              </center></td>";
                              }
                            
                              echo "</tr>";
                      $no++;
                      }
                      if (isset($_GET[hapus])){
                          mysqli_query($koneksi,"DELETE FROM kurikulum where kode_kurikulum='$_GET[hapus]'");
                          echo "<script>document.location='index.php?view=kurikulum';</script>";
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
        mysqli_query($koneksi,"UPDATE kurikulum SET nama_kurikulum = '$_POST[a]',
                                         status_kurikulum = '$_POST[b]' where kode_kurikulum='$_POST[id]'");
      echo "<script>document.location='index.php?view=kurikulum';</script>";
    }
    $edit = mysqli_query($koneksi,"SELECT * FROM kurikulum where kode_kurikulum='$_GET[id]'");
    $s = mysqli_fetch_array($edit);
    echo "<div class='col-md-12'>
              <div class='box box-info'>
                <div class='box-header with-border'>
                  <h3 class='box-title'>Edit Data Kurikulum</h3>
                </div>
              <div class='box-body'>
              <form method='POST' class='form-horizontal' action='' enctype='multipart/form-data'>
                <div class='col-md-12'>
                  <table class='table table-condensed table-bordered'>
                  <tbody>
                    <input type='hidden' name='id' value='$s[kode_kurikulum]'>
                    <tr><th width='120px' scope='row'>Nama Kurikulum</th> <td><input type='text' class='form-control' name='a' value='$s[nama_kurikulum]'> </td></tr>
                    <tr><th scope='row'>Status Aktif</th>     <td>";
                                                                  if ($s[status_kurikulum]=='Ya'){
                                                                      echo "<input type='radio' name='b' value='Ya' checked> Ya
                                                                             <input type='radio' name='b' value='Tidak'> Tidak";
                                                                  }else{
                                                                      echo "<input type='radio' name='b' value='Ya'> Ya
                                                                             <input type='radio' name='b' value='Tidak' checked> Tidak";
                                                                  }
                  echo "</td></tr>
                  </tbody>
                  </table>
                </div>
              </div>
              <div class='box-footer'>
                    <button type='submit' name='update' class='btn btn-info'>Update</button>
                    <a href='index.php?view=kurikulum'><button type='button' class='btn btn-default pull-right'>Batal</button></a>
                    
                  </div>
              </form>
            </div>";
}elseif($_GET[act]=='tambah'){
    if (isset($_POST[tambah])){
        mysqli_query($koneksi,"INSERT INTO kurikulum VALUES('','$_POST[a]','$_POST[b]')");
        echo "<script>document.location='index.php?view=kurikulum';</script>";
    }

    echo "<div class='col-md-12'>
              <div class='box box-info'>
                <div class='box-header with-border'>
                  <h3 class='box-title'>Tambah Data Kurikulum</h3>
                </div>
              <div class='box-body'>
              <form method='POST' class='form-horizontal' action='' enctype='multipart/form-data'>
                <div class='col-md-12'>
                  <table class='table table-condensed table-bordered'>
                  <tbody>
                    <tr><th width='120px' scope='row'>Nama Kurikulum</th> <td><input type='text' class='form-control' name='a'> </td></tr>
                    <tr><th scope='row'>Status Aktif</th>     <td><input type='radio' name='b' value='Ya'> Ya
                                                                  <input type='radio' name='b' value='Tidak'> Tidak</td></tr>
                  </tbody>
                  </table>
                </div>
              </div>
              <div class='box-footer'>
                    <button type='submit' name='tambah' class='btn btn-info'>Tambahkan</button>
                    <a href='index.php?view=kurikulum'><button type='button' class='btn btn-default pull-right'>Batal</button></a>
                    
                  </div>
              </form>
            </div>";
}
?>
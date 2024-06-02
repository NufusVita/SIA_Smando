<?php if ($_GET[act] == '') { ?>
  <div class="col-xs-12">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Data Kelas </h3>
        <?php if ($_SESSION[level] != 'kepala') { ?>
          <a class='pull-right btn btn-primary btn-sm' href='index.php?view=kelas&act=tambah'>Tambahkan Data</a>
        <?php } ?>
      </div><!-- /.box-header -->
      <div class="box-body">
        <table id="example1" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th style='width:40px'>No</th>
              <th>Kode Kelas</th>
              <th>Nama Kelas</th>
              <th>Wali Kelas</th>
              <!-- <th>Jurusan</th> -->
              <th>Ruangan</th>
              <th>Gedung</th>
              <th>Jumlah Siswa</th>
              <?php if ($_SESSION[level] != 'kepala') { ?>
                <th style='width:70px'>Action</th>
              <?php } ?>
            </tr>
          </thead>
          <tbody>
            <?php
            $tampil = mysqli_query($koneksi, "SELECT * FROM kelas a
                                              LEFT JOIN ruangan d ON a.kode_ruangan=d.kode_ruangan 
                                                LEFT JOIN gedung e ON d.kode_gedung=e.kode_gedung 
                                              ORDER BY a.kode_kelas ASC");
            $no = 1;
            while ($r = mysqli_fetch_array($tampil)) {
              $hitung = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM siswa where kode_kelas='$r[kode_kelas]'"));
              $sql = "SELECT * FROM guru where nik='$r[nip]'";
              $wali = "Belum Memilih";
              if ($result = mysqli_query($koneksi, $sql)) {
                while ($row = mysqli_fetch_row($result)) {
                  $wali = $row[2];
                }
              }

              echo "<tr><td>$no</td>
                              <td>$r[kode_kelas]</td>
                              <td>$r[nama_kelas]</td>
                              <td>$wali</td>
                              <td>$r[nama_ruangan]</td>
                              <td>$r[nama_gedung]</td>
                              <td>$hitung Orang</td>";
              if ($_SESSION[level] != 'kepala') {
                echo "<td><center>
                                <a class='btn btn-success btn-xs' title='Edit Data' href='?view=kelas&act=edit&id=$r[kode_kelas]'><span class='glyphicon glyphicon-edit'></span></a>
                                <a class='btn btn-danger btn-xs alert_notif' title='Delete Data' href='?view=kelas&hapus=$r[kode_kelas]'><span class='glyphicon glyphicon-remove'></span></a>
                              </center></td>";
              }
              echo "</tr>";
              $no++;
            }
            if (isset($_GET[hapus])) {
              mysqli_query($koneksi, "DELETE FROM kelas where kode_kelas='$_GET[hapus]'");
              echo "<script>document.location='index.php?view=kelas';</script>";
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
} elseif ($_GET[act] == 'edit') {
  if (isset($_POST[update])) {
    mysqli_query($koneksi, "UPDATE kelas SET kode_kelas = '$_POST[a]',
                                         nip = '$_POST[b]',
                                         kode_jurusan = '$_POST[c]',
                                         kode_ruangan = '$_POST[d]',
                                         nama_kelas = '$_POST[e]', 
                                         aktif = '$_POST[f]' where kode_kelas='$_POST[id]'");
    echo "<script>document.location='index.php?view=kelas';</script>";
  }
  $edit = mysqli_query($koneksi, "SELECT * FROM kelas a
                              LEFT JOIN ruangan d ON a.kode_ruangan=d.kode_ruangan 
                                  where a.kode_kelas='$_GET[id]'");
  $s = mysqli_fetch_array($edit);
  echo "<div class='col-md-12'>
              <div class='box box-info'>
                <div class='box-header with-border'>
                  <h3 class='box-title'>Edit Data Kelas</h3>
                </div>
              <div class='box-body'>
              <form method='POST' class='form-horizontal' action='' enctype='multipart/form-data'>
                <div class='col-md-12'>
                  <table class='table table-condensed table-bordered'>
                  <tbody>
                    <input type='hidden' name='id' value='$s[kode_kelas]'>
                    <tr><th width='120px' scope='row'>Kode Kelas</th> <td><input type='text' class='form-control' name='a' value='$s[kode_kelas]'> </td></tr>
                    <tr><th scope='row'>Wali Kelas</th>               <td><select class='form-control' name='b'> 
                                                                          <option value='0' selected>- Pilih Wali Kelas -</option>";
  $wali = mysqli_query($koneksi, "SELECT * FROM guru");
  while ($a = mysqli_fetch_array($wali)) {
    if ($a['nik'] == $s['nip']) {
      echo "<option value='$a[nik]' selected>$a[nama_guru]</option>";
    } else {
      echo "<option value='$a[nik]'>$a[nama_guru]</option>";
    }
  }
  echo "</select></td></tr>
                    <tr><th scope='row'>Jurusan</th>               <td><select class='form-control' name='c'> 
                                                                          <option value='0' selected>- Pilih Jurusan -</option>";
  $jur = mysqli_query($koneksi, "SELECT * FROM jurusan");
  while ($a = mysqli_fetch_array($jur)) {
    if ($a[kode_jurusan] == $s[kode_jurusan]) {
      echo "<option value='$a[kode_jurusan]' selected>$a[nama_jurusan]</option>";
    } else {
      echo "<option value='$a[kode_jurusan]'>$a[nama_jurusan]</option>";
    }
  }
  echo "</select></td></tr>
                    <tr><th scope='row'>Ruangan</th>               <td><select class='form-control' name='d'> 
                                                                          <option value='0' selected>- Pilih Ruangan -</option>";
  $rua = mysqli_query($koneksi, "SELECT * FROM ruangan a JOIN gedung b ON a.kode_gedung=b.kode_gedung ");
  while ($a = mysqli_fetch_array($rua)) {
    if ($a[kode_ruangan] == $s[kode_ruangan]) {
      echo "<option value='$a[kode_ruangan]' selected>$a[nama_gedung] - $a[nama_ruangan]</option>";
    } else {
      echo "<option value='$a[kode_ruangan]'>$a[nama_gedung] - $a[nama_ruangan]</option>";
    }
  }
  echo "</select></td></tr>
                    <tr><th scope='row'>Nama Kelas</th>           <td><input type='text' class='form-control' name='e' value='$s[nama_kelas]'></td></tr>
                    <tr><th scope='row'>Aktif</th>                <td>";
  if ($s[aktif] == 'Ya') {
    echo "<input type='radio' name='f' value='Ya' checked> Ya
                                                                             <input type='radio' name='f' value='Tidak'> Tidak";
  } else {
    echo "<input type='radio' name='f' value='Ya'> Ya
                                                                             <input type='radio' name='f' value='Tidak' checked> Tidak";
  }
  echo "</td></tr>
                  </tbody>
                  </table>
                </div>
              </div>
              <div class='box-footer'>
                    <button type='submit' name='update' class='btn btn-info'>Update</button>
                    <a href='index.php?view=kelas'><button type='button' class='btn btn-default pull-right'>Batal</button></a>
                    
                  </div>
              </form>
            </div>";
} elseif ($_GET[act] == 'tambah') {
  if (isset($_POST[tambah])) {
    mysqli_query($koneksi, "INSERT INTO kelas VALUES('$_POST[a]','$_POST[b]','$_POST[c]','$_POST[d]','$_POST[e]','$_POST[f]')");
    echo "<script>document.location='index.php?view=kelas';</script>";
  }

  echo "<div class='col-md-12'>
              <div class='box box-info'>
                <div class='box-header with-border'>
                  <h3 class='box-title'>Tambah Data Kelas</h3>
                </div>
              <div class='box-body'>
              <form method='POST' class='form-horizontal' action='' enctype='multipart/form-data'>
                <div class='col-md-12'>
                  <table class='table table-condensed table-bordered'>
                  <tbody>
                    <tr><th width='120px' scope='row'>Kode Kelas</th> <td><input type='text' class='form-control' name='a'> </td></tr>
                    <tr><th scope='row'>Wali Kelas</th>               <td><select class='form-control' name='b'> 
                                                                          <option value='0' selected>- Pilih Wali Kelas -</option>";
  $wali = mysqli_query($koneksi, "SELECT * FROM guru");
  while ($a = mysqli_fetch_array($wali)) {
    echo "<option value='$a[nip]'>$a[nama_guru]</option>";
  }
  echo "</select></td></tr>
                    <tr><th scope='row'>Jurusan</th>               <td><select class='form-control' name='c'> 
                                                                          <option value='0' selected>- Pilih Jurusan -</option>";
  $jur = mysqli_query($koneksi, "SELECT * FROM jurusan");
  while ($a = mysqli_fetch_array($jur)) {
    echo "<option value='$a[kode_jurusan]'>$a[nama_jurusan]</option>";
  }
  echo "</select></td></tr>
                    <tr><th scope='row'>Ruangan</th>               <td><select class='form-control' name='d'> 
                                                                          <option value='0' selected>- Pilih Ruangan -</option>";
  $rua = mysqli_query($koneksi, "SELECT * FROM ruangan a JOIN gedung b ON a.kode_gedung=b.kode_gedung ");
  while ($a = mysqli_fetch_array($rua)) {
    echo "<option value='$a[kode_ruangan]'>$a[nama_gedung] - $a[nama_ruangan]</option>";
  }
  echo "</select></td></tr>
                    <tr><th scope='row'>Nama Kelas</th>           <td><input type='text' class='form-control' name='e' value='$s[nama_kelas]'></td></tr>
                    <tr><th scope='row'>Aktif</th>                <td><input type='radio' name='f' value='Ya' checked> Ya
                                                                             <input type='radio' name='f' value='Tidak'> Tidak </td></tr>
                  </tbody>
                  </table>
                </div>
              </div>
              <div class='box-footer'>
                    <button type='submit' name='tambah' class='btn btn-info'>Tambahkan</button>
                    <a href='index.php?view=kelas'><button type='button' class='btn btn-default pull-right'>Batal</button></a>
                    
                  </div>
              </form>
            </div>";
}
?>
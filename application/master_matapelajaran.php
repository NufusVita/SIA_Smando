<?php if ($_GET[act] == '') { ?>
  <div class="col-xs-12">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Data Mata Pelajaran </h3>
        <?php if ($_SESSION[level] != 'kepala') { ?>
          <a class='pull-right btn btn-primary btn-sm' href='index.php?view=matapelajaran&act=tambah'>Tambahkan Data</a>
        <?php } ?>
      </div><!-- /.box-header -->
      <div class="box-body">
        <table id="example1" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th style='width:30px'>No</th>
              <th>Kode Mapel</th>
              <th>Nama Mapel</th>
              <th>Kelompok Mapel</th>
              <?php if ($_SESSION[level] != 'kepala') { ?>
                <th style='width:70px'>Action</th>
              <?php } ?>
            </tr>
          </thead>
          <tbody>
            <?php
            $tampil = mysqli_query($koneksi, "SELECT * FROM mata_pelajaran a 
                                              LEFT JOIN kelompok_mata_pelajaran b ON a.id_kelompok_mata_pelajaran=b.id_kelompok_mata_pelajaran
                                                    where a.kode_kurikulum='$kurikulum[kode_kurikulum]'
                                                      ORDER BY a.kode_pelajaran ASC");
            $no = 1;
            while ($r = mysqli_fetch_array($tampil)) {
              $sql = "SELECT * FROM guru where nik='$r[nip]'";
              $nama_guru = "Belum Memilih";
              if ($result = mysqli_query($koneksi2, $sql)) {
                while ($row = mysqli_fetch_row($result)) {
                  $nama_guru = $row[1];
                }
              }
              echo "<tr><td>$no</td>
                              <td>$r[kode_pelajaran]</td>
                              <td>$r[namamatapelajaran]</td>
                              <td>$r[nama_kelompok_mata_pelajaran]</td>";
              if ($_SESSION[level] != 'kepala') {
                echo "<td><center>
                                <a class='btn btn-primary btn-xs' title='Detail Data' href='?view=matapelajaran&act=detail&id=$r[kode_pelajaran]'><span class='glyphicon glyphicon-search'></span></a>
                                <a class='btn btn-success btn-xs' title='Edit Data' href='?view=matapelajaran&act=edit&id=$r[kode_pelajaran]'><span class='glyphicon glyphicon-edit'></span></a>
                                <a class='btn btn-danger btn-xs alert_notif' title='Delete Data' href='?view=matapelajaran&hapus=$r[kode_pelajaran]'><span class='glyphicon glyphicon-remove'></span></a>
                              </center></td>";
              }
              echo "</tr>";
              $no++;
            }
            if (isset($_GET[hapus])) {
              mysqli_query($koneksi, "DELETE FROM mata_pelajaran where kode_pelajaran='$_GET[hapus]'");
              echo "<script>document.location='index.php?view=matapelajaran';</script>";
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
    mysqli_query($koneksi, "UPDATE mata_pelajaran SET kode_pelajaran = '$_POST[a]',
                                         id_kelompok_mata_pelajaran = '$_POST[b]',
                                         kode_kurikulum = '$_POST[c]',
                                         namamatapelajaran = '$_POST[d]',
                                         jumlah_jam = '$_POST[e]',
                                         aktif = '$_POST[f]' kode_pelajaran='$_POST[id]'");
    echo "<script>document.location='index.php?view=matapelajaran';</script>";
  }
  $edit = mysqli_query($koneksi, "SELECT * FROM mata_pelajaran where kode_pelajaran='$_GET[id]'");
  $s = mysqli_fetch_array($edit);
  echo "<div class='col-md-12'>
              <div class='box box-info'>
                <div class='box-header with-border'>
                  <h3 class='box-title'>Edit Data Mata Pelajaran</h3>
                </div>
              <div class='box-body'>
              <form method='POST' class='form-horizontal' action='' enctype='multipart/form-data'>
                <div class='col-md-12'>
                  <table class='table table-condensed table-bordered'>
                  <tbody>
                    <input type='hidden' name='id' value='$s[kode_pelajaran]'>
                    <tr><th width='140px' scope='row'>Kurikulum</th> <td><select class='form-control' name='c'> 
                             <option value='0' selected>- Pilih Kurikulum -</option>";
  $kurikulum = mysqli_query($koneksi, "SELECT * FROM kurikulum");
  while ($a = mysqli_fetch_array($kurikulum)) {
    if ($s[kode_kurikulum] == $a[kode_kurikulum]) {
      echo "<option value='$a[kode_kurikulum]' selected>$a[nama_kurikulum]</option>";
    } else {
      echo "<option value='$a[kode_kurikulum]'>$a[nama_kurikulum]</option>";
    }
  }
  echo "</select>
                    </td></tr>
                    <tr><th scope='row'>Kode Pelajaran</th>       <td><input type='text' class='form-control' name='a' value='$s[kode_pelajaran]'> </td></tr>
                    <tr><th scope='row'>Nama Mapel</th>           <td><input type='text' class='form-control' name='d' value='$s[namamatapelajaran]'></td></tr>
                    <tr><th scope='row'>Jumlah Jam</th>           <td><input type='text' class='form-control' name='e' value='$s[jumlah_jam]'></td></tr>
                    <tr><th scope='row'>Kelompok</th> <td><select class='form-control' name='b'> 
                             <option value='0' selected>- Pilih Kelompok Mata Pelajaran -</option>";
  $kelompok = mysqli_query($koneksi, "SELECT * FROM kelompok_mata_pelajaran");
  while ($a = mysqli_fetch_array($kelompok)) {
    if ($s[id_kelompok_mata_pelajaran] == $a[id_kelompok_mata_pelajaran]) {
      echo "<option value='$a[id_kelompok_mata_pelajaran]' selected>$a[nama_kelompok_mata_pelajaran]</option>";
    } else {
      echo "<option value='$a[id_kelompok_mata_pelajaran]'>$a[nama_kelompok_mata_pelajaran]</option>";
    }
  }
  echo "</select>
                    </td></tr>
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
                    <a href='index.php?view=matapelajaran'><button type='button' class='btn btn-default pull-right'>Batal</button></a>
                    
                  </div>
              </form>
            </div>";
} elseif ($_GET[act] == 'tambah') {
  if (isset($_POST[tambah])) {
    mysqli_query($koneksi, "INSERT INTO mata_pelajaran VALUES('$_POST[a]','$_POST[b]','$_POST[c]','$_POST[d]','$_POST[e]','$_POST[f]')");
    echo "<script>document.location='index.php?view=matapelajaran';</script>";
  }

  echo "<div class='col-md-12'>
              <div class='box box-info'>
                <div class='box-header with-border'>
                  <h3 class='box-title'>Tambah Data Mata Pelajaran</h3>
                </div>
              <div class='box-body'>
              <form method='POST' class='form-horizontal' action='' enctype='multipart/form-data'>
                <div class='col-md-12'>
                  <table class='table table-condensed table-bordered'>
                  <tbody>
                    <tr><th width='140px' scope='row'>Kurikulum</th> <td><select class='form-control' name='c'> 
                             <option value='0' selected>- Pilih Kurikulum -</option>";
  $kurikulum = mysqli_query($koneksi, "SELECT * FROM kurikulum");
  while ($a = mysqli_fetch_array($kurikulum)) {
    echo "<option value='$a[kode_kurikulum]'>$a[nama_kurikulum]</option>";
  }
  echo "</select>
                    </td></tr>
                    <tr><th scope='row'>Kode Pelajaran</th>       <td><input type='text' class='form-control' name='a' value='$s[kode_pelajaran]'> </td></tr>
                    <tr><th scope='row'>Nama Mapel</th>           <td><input type='text' class='form-control' name='d' value='$s[namamatapelajaran]'></td></tr>
                    <tr><th scope='row'>Jumlah Jam</th>           <td><input type='text' class='form-control' name='e' value='$s[jumlah_jam]'></td></tr>
                    <tr><th scope='row'>Kelompok</th> <td><select class='form-control' name='b'> 
                             <option value='0' selected>- Pilih Kelompok Mata Pelajaran -</option>";
  $kelompok = mysqli_query($koneksi, "SELECT * FROM kelompok_mata_pelajaran");
  while ($a = mysqli_fetch_array($kelompok)) {
    echo "<option value='$a[id_kelompok_mata_pelajaran]'>$a[nama_kelompok_mata_pelajaran]</option>";
  }
  echo "</select>
                    </td></tr>
                    <tr><th scope='row'>Aktif</th>                <td><input type='radio' name='f' value='Ya' checked> Ya
                                                                             <input type='radio' name='f' value='Tidak'> Tidak</td></tr>
                  </tbody>
                  </table>
                </div>
              </div>
              <div class='box-footer'>
                    <button type='submit' name='tambah' class='btn btn-info'>Tambahkan</button>
                    <a href='index.php?view=matapelajaran'><button type='button' class='btn btn-default pull-right'>Batal</button></a>
                    
                  </div>
              </form>
            </div>";
} elseif ($_GET[act] == 'detail') {
  $edit = mysqli_query($koneksi, "SELECT a.*, b.nama_kelompok_mata_pelajaran, d.nama_kurikulum FROM mata_pelajaran a 
                                              JOIN kelompok_mata_pelajaran b ON a.id_kelompok_mata_pelajaran=b.id_kelompok_mata_pelajaran
                                                  JOIN kurikulum d ON a.kode_kurikulum=d.kode_kurikulum
                                                      where a.kode_pelajaran='$_GET[id]'");
  $s = mysqli_fetch_array($edit);
  echo "<div class='col-md-12'>
              <div class='box box-info'>
                <div class='box-header with-border'>
                  <h3 class='box-title'>Detail Data Mata Pelajaran</h3>
                </div>
              <div class='box-body'>
              <form method='POST' class='form-horizontal' action='' enctype='multipart/form-data'>
                <div class='col-md-12'>
                  <table class='table table-condensed table-bordered'>
                  <tbody>
                    <tr><th width='140px' scope='row'>Kurikulum</th> <td>$s[nama_kurikulum]</td></tr>
                    <tr><th scope='row'>Kode Pelajaran</th>       <td>$s[kode_pelajaran] </td></tr>
                    <tr><th scope='row'>Nama Mapel</th>           <td>$s[namamatapelajaran]</td></tr>
                    <tr><th scope='row'>Jumlah Jam</th>           <td>$s[jumlah_jam]</td></tr>
                    <tr><th scope='row'>Kelompok</th>             <td>$s[nama_kelompok_mata_pelajaran]</td></tr>
                    <tr><th scope='row'>Aktif</th>                <td>$s[aktif]</td></tr>
                  </tbody>
                  </table>
                </div>
              </div>
              <div class='box-footer'>
                    <a href='index.php?view=matapelajaran'><button type='button' class='btn btn-default pull-right'>Kembali</button></a>
                    
                  </div>
              </form>
            </div>";
}
?>
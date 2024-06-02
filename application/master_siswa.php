<?php
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

if ($_GET['act'] == '') {
  cek_session_admin();
  if (isset($_POST['pindahkelas'])) {
    if ($_POST['angkatan'] != '' and $_POST['kelas'] != '') {
      $jml = mysqli_fetch_array(mysqli_query($koneksi, "SELECT count(*) as jmlp FROM siswa where kode_kelas='$_POST[kelas]' AND angkatan='$_POST[angkatan]'"));
    } elseif ($_POST['angkatan'] == '' and $_POST['kelas'] != '') {
      $jml = mysqli_fetch_array(mysqli_query($koneksi, "SELECT count(*) as jmlp FROM siswa where kode_kelas='$_POST[kelas]'"));
    } elseif ($_POST['angkatan'] != '' and $_POST['kelas'] == '') {
      $jml = mysqli_fetch_array(mysqli_query($koneksi, "SELECT count(*) as jmlp FROM siswa where angkatan='$_POST[angkatan]'"));
    }

    $n = $jml['jmlp'];
    $kelas = $_POST['kelaspindah'];
    $angkatan = $_POST['angkatanpindah'];
    for ($i = 0; $i <= $n; $i++) {
      if (isset($_POST['pilih' . $i])) {
        $nisn = $_POST['pilih' . $i];
        if ($angkatan != '' and $kelas != '') {
          mysqli_query($koneksi, "UPDATE siswa SET angkatan='$angkatan', kode_kelas='$kelas' where nisn='$nisn'");
        } elseif ($angkatan == '' and $kelas != '') {
          mysqli_query($koneksi, "UPDATE siswa SET kode_kelas='$kelas' where nisn='$nisn'");
        } elseif ($angkatan != '' and $kelas == '') {
          mysqli_query($koneksi, "UPDATE siswa SET angkatan='$angkatan' where nisn='$nisn'");
        }
      }
    }
    echo "<script>document.location='index.php?view=siswa&angkatan=" . $angkatan . "&kelas=" . $kelas . "';</script>";
  }
?>
  <div class="col-xs-12">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Semua Data Siswa </h3>
        <?php if ($_SESSION['level'] != 'kepala') { ?>
          <a class='pull-right btn btn-success btn-sm' target='_BLANK' href='print-siswa.php?kelas=<?php echo $_GET['kelas']; ?>&angkatan=<?php echo $_GET['angkatan']; ?>'>Print</a>
          <a style='margin-right:5px; background-color:orange; border-color:orange;' class='pull-right btn btn-primary btn-sm' href='index.php?view=siswa&act=import_siswa'>Import</a>
          <a style='margin-right:5px' class='pull-right btn btn-primary btn-sm' href='index.php?view=siswa&act=tambahsiswa'>Tambahkan Data Siswa</a>
        <?php } ?>

        <form style='margin-right:5px; margin-top:0px' class='pull-right' action='' method='GET'>
          <input type="hidden" name='view' value='siswa'>
          <input type="number" name='angkatan' style='padding:3px' placeholder='Angkatan' value='<?php echo $_GET['angkatan']; ?>'>
          <select name='kelas' style='padding:4px'>
            <?php
            echo "<option value=''>- Filter Kelas -</option>";
            $kelas = mysqli_query($koneksi, "SELECT * FROM kelas");
            while ($k = mysqli_fetch_array($kelas)) {
              if ($_GET['kelas'] == $k['kode_kelas']) {
                echo "<option value='$k[kode_kelas]' selected>$k[kode_kelas] - $k[nama_kelas]</option>";
              } else {
                echo "<option value='$k[kode_kelas]'>$k[kode_kelas] - $k[nama_kelas]</option>";
              }
            }
            ?>
          </select>
          <input type="submit" style='margin-top:-4px' class='btn btn-info btn-sm' value='Lihat'>
        </form>
      </div><!-- /.box-header -->
      <div class="box-body">
        <form action='' method='POST'>
          <input type="hidden" name='angkatan' value='<?php echo $_GET['angkatan']; ?>'>
          <input type="hidden" name='kelas' value='<?php echo $_GET['kelas']; ?>'>
          <?php
          if (isset($_GET['kelas'])) {
            echo "<table id='myTable' class='table table-bordered table-striped'>
                            <tr><th></th>";
          } else {
            echo "<table id='example1' class='table table-bordered table-striped'>
                            <thead>
                              <tr>";
          }
          echo "<th>No</th>
                        <th>NIPD</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>";

          $getKelas = isset($_GET['kelas'])?$_GET['kelas']:'';
          $getAngkatan = isset($_GET['angkatan'])?$_GET['angkatan']:'';
          $tampil = [];

          if ($getKelas != '' and $getAngkatan != '') {
            $tampil = mysqli_query($koneksi, "SELECT * FROM siswa a LEFT JOIN kelas b ON a.kode_kelas=b.kode_kelas
                                                  where a.kode_kelas='$_GET[kelas]' AND a.angkatan='$_GET[angkatan]' ORDER BY a.nama");
          } elseif ($getKelas != '' and $getAngkatan == '') {
            $tampil = mysqli_query($koneksi, "SELECT * FROM siswa a LEFT JOIN kelas b ON a.kode_kelas=b.kode_kelas
                                                  where a.kode_kelas='$_GET[kelas]' ORDER BY a.nama");
          } elseif ($getKelas == '' and $getAngkatan != '') {
            $tampil = mysqli_query($koneksi, "SELECT * FROM siswa a LEFT JOIN kelas b ON a.kode_kelas=b.kode_kelas
                                                  where a.angkatan='$_GET[angkatan]' ORDER BY a.nama");
          } else {
            $tampil = mysqli_query($koneksi, "SELECT * FROM siswa a LEFT JOIN kelas b ON a.kode_kelas=b.kode_kelas ORDER BY a.nama");
          }

          $no = 1;
          while ($r = mysqli_fetch_array($tampil)) {
            echo "<tr>";
            if (isset($_GET['kelas'])) {
              echo "<td><input type='checkbox' name='pilih" . $no . "' value='$r[nisn]'/></td>";
            }
            echo "<td>$no</td>
                              <td>$r[nipd]</td>
                              <td>$r[nisn]</td>
                              <td>$r[nama]</td>
                              <td>$r[nama_kelas]</td>";
            if ($_SESSION['level'] != 'kepala') {
              echo "<td><center>
                                  <a class='btn btn-default btn-xs' title='Lihat Detail' href='?view=siswa&act=detailsiswa&id=$r[nisn]'><span class='glyphicon glyphicon-search'></span></a>
                                  <a class='btn btn-info btn-xs' title='Edit Siswa' href='?view=siswa&act=editsiswa&id=$r[nisn]'><span class='glyphicon glyphicon-edit'></span></a>
                                  <a class='btn btn-danger btn-xs alert_notif' title='Delete Siswa' href='?view=siswa&hapus=$r[nisn]'><span class='glyphicon glyphicon-remove'></span></a>
                                </center></td>";
            } else {
              echo "<td><center>
                                  <a class='btn btn-default btn-xs' title='Lihat Detail' href='?view=siswa&act=detailsiswa&id=$r[nisn]'><span class='glyphicon glyphicon-search'></span></a>
                                  
                                </center></td>";
            }
            echo "</tr>";
            $no++;
          }
          if (isset($_GET['hapus'])) {
            mysqli_query($koneksi, "DELETE FROM siswa where nisn='$_GET[hapus]'");
            echo "<script>document.location='index.php?view=siswa';</script>";
          }
          ?>
          <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.js"
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
    <?php if ($_SESSION['level'] != 'kepala') {
      if (isset($_GET['kelas'])) { ?>
        <div class='box-footer'>
          Pindah Ke :
          <input type="number" name='angkatanpindah' style='padding:3px' placeholder='Angkatan' value='<?php echo $_GET['angkatan']; ?>'>
          <select name='kelaspindah' style='padding:4px' required>
            <?php
            echo "<option value=''>- Pilih Kelas -</option>";
            $kelas = mysqli_query($koneksi, "SELECT * FROM kelas");
            while ($k = mysqli_fetch_array($kelas)) {
              echo "<option value='$k[kode_kelas]'>$k[kode_kelas] - $k[nama_kelas]</option>";
            }
            ?>
          </select>
          <button style='margin-top:-5px' type='submit' name='pindahkelas' class='btn btn-sm btn-info'>Proses</button>
          <a href='index.php?view=siswa'><button type='button' class='btn btn-sm  btn-default pull-right'>Cancel</button></a>
        </div>
    <?php }
    } ?>

    </form>
  </div>
<?php
} elseif ($_GET['act'] == 'tambahsiswa') {
  cek_session_admin();
  if (isset($_POST['tambah'])) {
    $rtrw = explode('/', $_POST['ai']);
    $rt = $rtrw[0];
    $rw = $rtrw[1];
    $dir_gambar = 'foto_siswa/';
    $filename = basename($_FILES['ao']['name']);
    $filenamee = date("YmdHis") . '-' . basename($_FILES['ao']['name']);
    $uploadfile = $dir_gambar . $filenamee;
    if ($filename != '') {
      if (move_uploaded_file($_FILES['ao']['tmp_name'], $uploadfile)) {
        mysqli_query($koneksi, "INSERT INTO siswa VALUES('','$_POST[aa]','$_POST[ac]','$_POST[ad]','$_POST[bd]','$_POST[ab]',
                               '$_POST[bb]','$_POST[bc]','$_POST[ba]','$_POST[be]','$_POST[bf]','$_POST[ah]','$rt','$rw',
                               '$_POST[aj]','$_POST[ak]','$_POST[al]','$_POST[am]','$_POST[bg]','$_POST[bh]','$_POST[bi]',
                               '$_POST[bj]','$_POST[bk]','$_POST[bl]','$_POST[bm]','$_POST[bn]','$filenamee','$_POST[ca]',
                               '$_POST[cb]','$_POST[cc]','$_POST[cd]','$_POST[ce]','$_POST[cf]','$_POST[cg]','$_POST[ch]',
                               '$_POST[ci]','$_POST[cj]','$_POST[ck]','$_POST[cl]','$_POST[cm]','$_POST[cn]','$_POST[co]',
                               '$_POST[cp]','$_POST[cq]','$_POST[cr]','$_POST[cs]','$_POST[af]','$_POST[an]','$_POST[bo]',
                               '','$_POST[ae]','$_POST[ag]','0')");
      }
    } else {
      mysqli_query($koneksi, "INSERT INTO siswa VALUES('','$_POST[aa]','$_POST[ac]','$_POST[ad]','$_POST[bd]','$_POST[ab]',
                               '$_POST[bb]','$_POST[bc]','$_POST[ba]','$_POST[be]','$_POST[bf]','$_POST[ah]','$rt','$rw',
                               '$_POST[aj]','$_POST[ak]','$_POST[al]','$_POST[am]','$_POST[bg]','$_POST[bh]','$_POST[bi]',
                               '$_POST[bj]','$_POST[bk]','$_POST[bl]','$_POST[bm]','$_POST[bn]','','$_POST[ca]',
                               '$_POST[cb]','$_POST[cc]','$_POST[cd]','$_POST[ce]','$_POST[cf]','$_POST[cg]','$_POST[ch]',
                               '$_POST[ci]','$_POST[cj]','$_POST[ck]','$_POST[cl]','$_POST[cm]','$_POST[cn]','$_POST[co]',
                               '$_POST[cp]','$_POST[cq]','$_POST[cr]','$_POST[cs]','$_POST[af]','$_POST[an]','$_POST[bo]',
                               '','$_POST[ae]','$_POST[ag]','0')");
    }
    echo "<script>document.location='index.php?view=siswa&act=detailsiswa&id=" . $_POST['ab'] . "';</script>";
  }

  echo "<div class='col-md-12'>
              <div class='box box-info'>
                <div class='box-header with-border'>
                  <h3 class='box-title'>Tambah Data Siswa</h3>
                </div>
                <div class='box-body'>

                  <div class='panel-body'>
                    <ul id='myTabs' class='nav nav-tabs' role='tablist'>
                      <li role='presentation' class='active'><a href='#siswa' id='siswa-tab' role='tab' data-toggle='tab' aria-controls='siswa' aria-expanded='true'>Data Siswa </a></li>
                      <li role='presentation' class=''><a href='#ortu' role='tab' id='ortu-tab' data-toggle='tab' aria-controls='ortu' aria-expanded='false'>Data Orang Tua / Wali</a></li>
                    </ul><br>

                    <div id='myTabContent' class='tab-content'>
                      <div role='tabpanel' class='tab-pane fade active in' id='siswa' aria-labelledby='siswa-tab'>
                          <form action='' method='POST' enctype='multipart/form-data' class='form-horizontal'>
                          <div class='col-md-6'>
                            <table class='table table-condensed table-bordered'>
                            <tbody>
                                
                              <tr><th width='130px' scope='row'>NIPD</th> <td><input type='text' class='form-control' name='aa'></td></tr>
                              <tr><th scope='row'>NISN</th> <td><input type='text' class='form-control' name='ab'></td></tr>
                              <tr><th scope='row'>Password</th> <td><input type='text' class='form-control' name='ac'></td></tr>
                              <tr><th scope='row'>Nama Siswa</th> <td><input type='text' class='form-control' name='ad'></td></tr>
                              <tr><th scope='row'>Kelas</th> <td><select class='form-control' name='ae'> 
                                                                            <option value='0' selected>- Pilih Kelas -</option>";
  $kelas = mysqli_query($koneksi, "SELECT * FROM kelas");
  while ($a = mysqli_fetch_array($kelas)) {
    echo "<option value='$a[kode_kelas]'>$a[nama_kelas]</option>";
  }
  echo "</select></td></tr>
                              <tr><th scope='row'>Angkatan</th> <td><input type='text' class='form-control' name='af'></td></tr>
                              <tr><th scope='row'>Jurusan</th> <td><select class='form-control' name='ag'> 
                                                                            <option value='0' selected>- Pilih Jurusan -</option>";
  $jurusan = mysqli_query($koneksi, "SELECT * FROM jurusan");
  while ($a = mysqli_fetch_array($jurusan)) {
    echo "<option value='$a[kode_jurusan]'>$a[nama_jurusan]</option>";
  }
  echo "</select></td></tr>
                              <tr><th scope='row'>Alamat Siswa</th> <td><input type='text' class='form-control' name='ah'></td></tr>
                              <tr><th scope='row'>RT/RW</th> <td><input type='text' class='form-control' value='00/00' name='ai'></td></tr>
                              <tr><th scope='row'>Dusun</th> <td><input type='text' class='form-control' name='aj'></td></tr>
                              <tr><th scope='row'>Kelurahan</th> <td><input type='text' class='form-control' name='ak'></td></tr>
                              <tr><th scope='row'>Kecamatan</th> <td><input type='text' class='form-control' name='al'></td></tr>
                              <tr><th scope='row'>Kode Pos</th> <td><input type='text' class='form-control' name='am'></td></tr>
                              <tr><th scope='row'>Status Awal</th> <td><input type='text' class='form-control' name='an'></td></tr>
                              <tr><th scope='row'>Foto</th>             <td><div style='position:relative;''>
                                                                            <a class='btn btn-primary' href='javascript:;'>
                                                                              <span class='glyphicon glyphicon-search'></span> Browse..."; ?>
  <input type='file' class='files' name='ao' onchange='$("#upload-file-info").html($(this).val());'>
  <?php echo "</a> <span style='width:155px' class='label label-info' id='upload-file-info'></span>
                                                                          </div>
                              </td></tr>
                            </tbody>
                            </table>
                          </div>
                          <div class='col-md-6'>
                            <table class='table table-condensed table-bordered'>
                            <tbody>
                              <tr><th width='130px' scope='row'>NIK</th> <td><input type='text' class='form-control' name='ba'></td></tr>
                              <tr><th scope='row'>Tempat Lahir</th> <td><input type='text' class='form-control' name='bb'></td></tr>
                              <tr><th scope='row'>Tanggal Lahir</th> <td><input type='text' class='form-control' name='bc'></td></tr>
                              <tr><th scope='row'>Jenis Kelamin</th> <td><select class='form-control' name='bd'> 
                                                                            <option value='0' selected>- Pilih Jenis Kelamin -</option>";
  $jk = mysqli_query($koneksi, "SELECT * FROM jenis_kelamin");
  while ($a = mysqli_fetch_array($jk)) {
    echo "<option value='$a[id_jenis_kelamin]'>$a[jenis_kelamin]</option>";
  }
  echo "</select></td></tr>
                              <tr><th scope='row'>Agama</th> <td><select class='form-control' name='be'> 
                                                                            <option value='0' selected>- Pilih Agama -</option>";
  $agama = mysqli_query($koneksi, "SELECT * FROM agama");
  while ($a = mysqli_fetch_array($agama)) {
    echo "<option value='$a[id_agama]'>$a[nama_agama]</option>";
  }
  echo "</select></td></tr>
                              <tr><th scope='row'>Keb. Khusus</th> <td><input type='text' class='form-control' name='bf'></td></tr>
                              <tr><th scope='row'>Jenis Tinggal</th> <td><input type='text' class='form-control' name='bg'></td></tr>
                              <tr><th scope='row'>Transportasi</th> <td><input type='text' class='form-control' name='bh'></td></tr>
                              <tr><th scope='row'>No Telpon</th> <td><input type='text' class='form-control' name='bi'></td></tr>
                              <tr><th scope='row'>No Handpone</th> <td><input type='text' class='form-control' name='bj'></td></tr>
                              <tr><th scope='row'>Alamat Email</th> <td><input type='text' class='form-control' name='bk'></td></tr>
                              <tr><th scope='row'>SKHUN</th> <td><input type='text' class='form-control' name='bl'></td></tr>
                              <tr><th scope='row'>Penerima KPS</th> <td><input type='text' class='form-control' name='bm'></td></tr>
                              <tr><th scope='row'>No KPS</th> <td><input type='text' class='form-control' name='bn'></td></tr>
                              <tr><th scope='row'>Status Siswa</th> <td><input type='radio' name='bo' value='Aktif' checked> Aktif
                                                                        <input type='radio' name='bo' value='Tidak Aktif'> Tidak Aktif </td></tr>
                            </tbody>
                            </table>
                          </div>  
                          <div style='clear:both'></div>
                          <div class='box-footer'>
                            <button type='submit' name='tambah' class='btn btn-info'>Tambahkan</button>
                            <a href='index.php?view=siswa'><button type='button' class='btn btn-default pull-right'>Batal</button></a>
                          </div> 
                      </div>

                      <div role='tabpanel' class='tab-pane fade' id='ortu' aria-labelledby='ortu-tab'>
                          <div class='col-md-12'>
                            <table class='table table-condensed table-bordered'>
                            <tbody>
                              <tr bgcolor=#e3e3e3><th width='130px' scope='row'>Nama Ayah</th> <td><input type='text' class='form-control' name='ca'></td></tr>
                              <tr><th scope='row'>Tahun Lahir</th> <td><input type='text' class='form-control' name='cb'></td></tr>
                              <tr><th scope='row'>Pendidikan</th> <td><input type='text' class='form-control' name='cc'></td></tr>
                              <tr><th scope='row'>Pekerjaan</th> <td><input type='text' class='form-control' name='cd'></td></tr>
                              <tr><th scope='row'>Penghasilan</th> <td><input type='text' class='form-control' name='ce'></td></tr>
                              <tr><th scope='row'>Kebutuhan Khusus</th> <td><input type='text' class='form-control' name='cf'></td></tr>
                              <tr><th scope='row'>No Telpon</th> <td><input type='text' class='form-control' name='cg'></td></tr>
                              <tr><th scope='row' coslpan='2'><br></th></tr>
                              <tr bgcolor=#e3e3e3><th scope='row'>Nama Ibu</th> <td><input type='text' class='form-control' name='ch'></td></tr>
                              <tr><th scope='row'>Tahun Lahir</th> <td><input type='text' class='form-control' name='ci'></td></tr>
                              <tr><th scope='row'>Pendidikan</th> <td><input type='text' class='form-control' name='cj'></td></tr>
                              <tr><th scope='row'>Pekerjaan</th> <td><input type='text' class='form-control' name='ck'></td></tr>
                              <tr><th scope='row'>Penghasilan</th> <td><input type='text' class='form-control' name='cl'></td></tr>
                              <tr><th scope='row'>Kebutuhan Khusus</th> <td><input type='text' class='form-control' name='cm'></td></tr>
                              <tr><th scope='row'>No Telpon</th> <td><input type='text' class='form-control' name='cn'></td></tr>
                              <tr><th scope='row' coslpan='2'><br></th></tr>
                              <tr bgcolor=#e3e3e3><th scope='row'>Nama Wali</th> <td><input type='text' class='form-control' name='co'></td></tr>
                              <tr><th scope='row'>Tahun Lahir</th> <td><input type='text' class='form-control' name='cp'></td></tr>
                              <tr><th scope='row'>Pendidikan</th> <td><input type='text' class='form-control' name='cq'></td></tr>
                              <tr><th scope='row'>Pekerjaan</th> <td><input type='text' class='form-control' name='cr'></td></tr>
                              <tr><th scope='row'>Penghasilan</th> <td><input type='text' class='form-control' name='cs'></td></tr>
                            </tbody>
                            </table>
                          </div>
                          <div class='box-footer'>
                            <button type='submit' name='tambah' class='btn btn-info'>Tambahkan</button>
                            <a href='index.php?view=siswa'><button type='button' class='btn btn-default pull-right'>Batal</button></a>
                          </div>
                          </form>
                      </div>
                    </div>
                  </div>

                </div>
            </div>
        </div>";
} elseif ($_GET['act'] == 'editsiswa') {
  cek_session_siswa();
  if (isset($_POST['update1'])) {
    $rtrw = explode('/', $_POST['ai']);
    $rt = $rtrw[0];
    $rw = $rtrw[1];
    $dir_gambar = 'foto_siswa/';
    $filename = basename($_FILES['ao']['name']);
    $filenamee = date("YmdHis") . '-' . basename($_FILES['ao']['name']);
    $uploadfile = $dir_gambar . $filenamee;
    if ($filename != '') {
      if (move_uploaded_file($_FILES['ao']['tmp_name'], $uploadfile)) {
        mysqli_query($koneksi, "UPDATE siswa SET 
                               nipd        = '$_POST[aa]',
                               nisn   = '$_POST[ab]',
                               password         = '$_POST[ac]',
                               nama       = '$_POST[ad]',
                               kode_kelas    = '$_POST[ae]',
                               angkatan   = '$_POST[af]',
                               kode_jurusan   = '$_POST[ag]',
                               alamat        = '$_POST[ah]',
                               rt         = '$rt',
                               rw   = '$rw',
                               dusun    = '$_POST[aj]',
                               kelurahan       = '$_POST[ak]',
                               kecamatan     = '$_POST[al]',
                               kode_pos      = '$_POST[am]',
                               status_awal   = '$_POST[an]',
                               foto = '$filenamee',

                               nik = '$_POST[ba]',
                               tempat_lahir = '$_POST[bb]',
                               tanggal_lahir = '$_POST[bc]',
                               id_jenis_kelamin = '$_POST[bd]',
                               id_agama = '$_POST[be]',
                               kebutuhan_khusus = '$_POST[bf]',
                               jenis_tinggal = '$_POST[bg]',
                               alat_transportasi = '$_POST[bh]',
                               telepon = '$_POST[bi]',
                               hp = '$_POST[bj]',
                               email = '$_POST[bk]',
                               skhun = '$_POST[bl]',
                               penerima_kps = '$_POST[bm]',
                               no_kps = '$_POST[bn]',
                               status_siswa = '$_POST[bo]' where nipd='$_POST[id]'");
      }
    } else {
      mysqli_query($koneksi, "UPDATE siswa SET 
                               nipd        = '$_POST[aa]',
                               nisn   = '$_POST[ab]',
                               password         = '$_POST[ac]',
                               nama       = '$_POST[ad]',
                               kode_kelas    = '$_POST[ae]',
                               angkatan   = '$_POST[af]',
                               kode_jurusan   = '$_POST[ag]',
                               alamat        = '$_POST[ah]',
                               rt         = '$rt',
                               rw   = '$rw',
                               dusun    = '$_POST[aj]',
                               kelurahan       = '$_POST[ak]',
                               kecamatan     = '$_POST[al]',
                               kode_pos      = '$_POST[am]',
                               status_awal   = '$_POST[an]',

                               nik = '$_POST[ba]',
                               tempat_lahir = '$_POST[bb]',
                               tanggal_lahir = '$_POST[bc]',
                               id_jenis_kelamin = '$_POST[bd]',
                               id_agama = '$_POST[be]',
                               kebutuhan_khusus = '$_POST[bf]',
                               jenis_tinggal = '$_POST[bg]',
                               alat_transportasi = '$_POST[bh]',
                               telepon = '$_POST[bi]',
                               hp = '$_POST[bj]',
                               email = '$_POST[bk]',
                               skhun = '$_POST[bl]',
                               penerima_kps = '$_POST[bm]',
                               no_kps = '$_POST[bn]',
                               status_siswa = '$_POST[bo]' where nisn='$_POST[id]'");
    }
    echo "<script>document.location='index.php?view=siswa';</script>";
  }

  if (isset($_POST['update2'])) {
    mysqli_query($koneksi, "UPDATE siswa SET 
                               nama_ayah        = '$_POST[ca]',
                               tahun_lahir_ayah   = '$_POST[cb]',
                               pendidikan_ayah         = '$_POST[cc]',
                               pekerjaan_ayah       = '$_POST[cd]',
                               penghasilan_ayah    = '$_POST[ce]',
                               kebutuhan_khusus_ayah   = '$_POST[cf]',
                               no_telpon_ayah   = '$_POST[cg]',

                               nama_ibu        = '$_POST[ch]',
                               tahun_lahir_ibu   = '$_POST[ci]',
                               pendidikan_ibu         = '$_POST[cj]',
                               pekerjaan_ibu       = '$_POST[ck]',
                               penghasilan_ibu    = '$_POST[cl]',
                               kebutuhan_khusus_ibu   = '$_POST[cm]',
                               no_telpon_ibu   = '$_POST[cn]',

                               nama_wali        = '$_POST[co]',
                               tahun_lahir_wali   = '$_POST[cp]',
                               pendidikan_wali         = '$_POST[cq]',
                               pekerjaan_wali       = '$_POST[cr]',
                               penghasilan_wali    = '$_POST[cs]' where nisn='$_POST[id]'");

    echo "<script>document.location='index.php?view=siswa';</script>";
  }
  if ($_SESSION['level'] == 'siswa') {
    $nisn = $_SESSION['id'];
    $close = 'readonly=on';
  } else {
    $nisn = $_GET['id'];
    $close = '';
  }
  $edit = mysqli_query($koneksi, "SELECT * FROM siswa a LEFT JOIN kelas b ON a.kode_kelas=b.kode_kelas 
                               
                                 
                                    LEFT JOIN agama e ON a.id_agama=e.id_agama 
                                      where a.nisn='$nisn'");
  $s = mysqli_fetch_array($edit);
  echo "<div class='col-md-12'>
              <div class='box box-info'>
                <div class='box-header with-border'>
                  <h3 class='box-title'>Edit Data Siswa</h3>
                </div>
                <div class='box-body'>";

  if ($_SESSION['level'] == 'siswa') {
    echo "<div class='alert alert-warning alert-dismissible fade in' role='alert'> 
                          <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                          <span aria-hidden='true'>×</span></button> <strong>Perhatian!</strong> - Semua Data-data yang ada dibawah ini akan digunakan untuk keperluan pihak sekolah, jadi tolong di isi dengan data sebenarnya dan jika kedapatan data yang diisikan tidak seuai dengan yang sebenarnya, maka pihak sekolah akan memberikan sanksi tegas !!!
                    </div>";
  }

  echo "<div class='panel-body'>
                    <ul id='myTabs' class='nav nav-tabs' role='tablist'>
                      <li role='presentation' class='active'><a href='#siswa' id='siswa-tab' role='tab' data-toggle='tab' aria-controls='siswa' aria-expanded='true'>Data Siswa </a></li>
                      <li role='presentation' class=''><a href='#ortu' role='tab' id='ortu-tab' data-toggle='tab' aria-controls='ortu' aria-expanded='false'>Data Orang Tua / Wali</a></li>
                    </ul><br>

                    <div id='myTabContent' class='tab-content'>
                    <div role='tabpanel' class='tab-pane fade active in' id='siswa' aria-labelledby='siswa-tab'>
                        <form action='' method='POST' enctype='multipart/form-data' class='form-horizontal'>
                        <div class='col-md-7'>
                          <table class='table table-condensed table-bordered'>
                          <tbody>
                            <tr><th style='background-color:#E7EAEC' width='160px' rowspan='17'>";
  if (trim($s['foto']) == '') {
    echo "<img class='img-thumbnail' style='width:155px' src='foto_siswa/no-image.jpg'>";
  } else {
    echo "<img class='img-thumbnail' style='width:155px' src='foto_siswa/$s[foto]'>";
  }
  echo "</th></tr>
                            <input type='hidden' value='$s[nipd]' name='id'>
                            <tr><th width='120px' scope='row'>NIPD</th> <td><input type='text' class='form-control' value='$s[nipd]' name='aa' $close></td></tr>
                            <tr><th scope='row'>NISN</th> <td><input type='text' class='form-control' value='$s[nisn]' name='ab' $close></td></tr>
                            <tr><th scope='row'>Password</th> <td><input type='text' class='form-control' value='$s[password]' name='ac'></td></tr>
                            <tr><th scope='row'>Nama Siswa</th> <td><input type='text' class='form-control' value='$s[nama]' name='ad'></td></tr>
                            <tr><th scope='row'>Kelas</th> <td><select class='form-control' name='ae' $close> 
                                                                          <option value='0' selected>- Pilih Kelas -</option>";
  $kelas = mysqli_query($koneksi, "SELECT * FROM kelas");
  while ($a = mysqli_fetch_array($kelas)) {
    if ($_SESSION['level'] == 'siswa') {
      if ($a['kode_kelas'] == $s['kode_kelas']) {
        echo "<option value='$a[kode_kelas]' selected>$a[nama_kelas]</option>";
      }
    } else {
      if ($a['kode_kelas'] == $s['kode_kelas']) {
        echo "<option value='$a[kode_kelas]' selected>$a[nama_kelas]</option>";
      } else {
        echo "<option value='$a[kode_kelas]'>$a[nama_kelas]</option>";
      }
    }
  }
  echo "</select></td></tr>
                            <tr><th scope='row'>Angkatan</th> <td><input type='text' class='form-control' value='$s[angkatan]' name='af' $close></td></tr>
                            <tr><th scope='row'>Jurusan</th> <td><select class='form-control' name='ag' $close> 
                                                                          <option value='0' selected>- Pilih Jurusan -</option>";
  $jurusan = mysqli_query($koneksi, "SELECT * FROM jurusan");
  while ($a = mysqli_fetch_array($jurusan)) {
    if ($_SESSION['level'] == 'siswa') {
      if ($a['kode_jurusan'] == $s['kode_jurusan']) {
        echo "<option value='$a[kode_jurusan]' selected>$a[nama_jurusan]</option>";
      }
    } else {
      if ($a['kode_jurusan'] == $s['kode_jurusan']) {
        echo "<option value='$a[kode_jurusan]' selected>$a[nama_jurusan]</option>";
      } else {
        echo "<option value='$a[kode_jurusan]'>$a[nama_jurusan]</option>";
      }
    }
  }
  echo "</select></td></tr>
                            <tr><th scope='row'>Alamat Siswa</th> <td><input type='text' class='form-control' value='$s[alamat]' name='ah'></td></tr>
                            <tr><th scope='row'>RT/RW</th> <td><input type='text' class='form-control' value='$s[rt]/$s[rw]' name='ai'></td></tr>
                            <tr><th scope='row'>Dusun</th> <td><input type='text' class='form-control' value='$s[dusun]' name='aj'></td></tr>
                            <tr><th scope='row'>Kelurahan</th> <td><input type='text' class='form-control' value='$s[kelurahan]' name='ak'></td></tr>
                            <tr><th scope='row'>Kecamatan</th> <td><input type='text' class='form-control' value='$s[kecamatan]' name='al'></td></tr>
                            <tr><th scope='row'>Kode Pos</th> <td><input type='text' class='form-control' value='$s[kode_pos]' name='am'></td></tr>
                            <tr><th scope='row'>Status Awal</th> <td><input type='text' class='form-control' value='$s[status_awal]' name='an' $close></td></tr>
                            <tr><th scope='row'>Ganti Foto</th>             <td><div style='position:relative;''>
                                                                          <a class='btn btn-primary' href='javascript:;'>
                                                                            <span class='glyphicon glyphicon-search'></span> Browse..."; ?>
  <input type='file' class='files' name='ao' onchange='$("#upload-file-info").html($(this).val());'>
  <?php echo "</a> <span style='width:155px' class='label label-info' id='upload-file-info'></span>
                                                                        </div>
                            </td></tr>
                          </tbody>
                          </table>
                        </div>
                        <div class='col-md-5'>
                          <table class='table table-condensed table-bordered'>
                          <tbody>
                            <tr><th width='120px' scope='row'>NIK</th> <td><input type='text' class='form-control' value='$s[nik]' name='ba'></td></tr>
                            <tr><th scope='row'>Tempat Lahir</th> <td><input type='text' class='form-control' value='$s[tempat_lahir]' name='bb'></td></tr>
                            <tr><th scope='row'>Tanggal Lahir</th> <td><input type='text' class='form-control' value='$s[tanggal_lahir]' name='bc'></td></tr>
                            <tr><th scope='row'>Jenis Kelamin</th> <td><select class='form-control' name='bd'> 
                                                                          <option value='0' selected>- Pilih Jenis Kelamin -</option>";
  $jk = mysqli_query($koneksi, "SELECT * FROM jenis_kelamin");
  while ($a = mysqli_fetch_array($jk)) {
    if ($a['id_jenis_kelamin'] == $s['id_jenis_kelamin']) {
      echo "<option value='$a[id_jenis_kelamin]' selected>$a[jenis_kelamin]</option>";
    } else {
      echo "<option value='$a[id_jenis_kelamin]'>$a[jenis_kelamin]</option>";
    }
  }
  echo "</select></td></tr>
                            <tr><th scope='row'>Agama</th> <td><select class='form-control' name='be'> 
                                                                          <option value='0' selected>- Pilih Agama -</option>";
  $agama = mysqli_query($koneksi, "SELECT * FROM agama");
  while ($a = mysqli_fetch_array($agama)) {
    if ($a['id_agama'] == $s['id_agama']) {
      echo "<option value='$a[id_agama]' selected>$a[nama_agama]</option>";
    } else {
      echo "<option value='$a[id_agama]'>$a[nama_agama]</option>";
    }
  }
  echo "</select></td></tr>
                            <tr><th scope='row'>Keb. Khusus</th> <td><input type='text' class='form-control' value='$s[kebutuhan_khusus]' name='bf'></td></tr>
                            <tr><th scope='row'>Jenis Tinggal</th> <td><input type='text' class='form-control' value='$s[jenis_tinggal]' name='bg'></td></tr>
                            <tr><th scope='row'>Transportasi</th> <td><input type='text' class='form-control' value='$s[alat_transportasi]' name='bh'></td></tr>
                            <tr><th scope='row'>No Telpon</th> <td><input type='text' class='form-control' value='$s[telepon]' name='bi'></td></tr>
                            <tr><th scope='row'>No Handpone</th> <td><input type='text' class='form-control' value='$s[hp]' name='bj'></td></tr>
                            <tr><th scope='row'>Alamat Email</th> <td><input type='text' class='form-control' value='$s[email]' name='bk'></td></tr>
                            <tr><th scope='row'>SKHUN</th> <td><input type='text' class='form-control' value='$s[skhun]' name='bl'></td></tr>
                            <tr><th scope='row'>Penerima KPS</th> <td><input type='text' class='form-control' value='$s[penerima_kps]' name='bm'></td></tr>
                            <tr><th scope='row'>No KPS</th> <td><input type='text' class='form-control' value='$s[no_kps]' name='bn'></td></tr>
                            <tr><th scope='row'>Status Siswa</th> <td>";
  if ($s['status_siswa'] == 'Aktif') {
    echo "<input type='radio' name='bo' value='Aktif' checked> Aktif
                                                                              <input type='radio' name='bo' value='Tidak Aktif'> Tidak Aktif";
  } else {
    echo "<input type='radio' name='bo' value='Aktif'> Aktif
                                                                              <input type='radio' name='bo' value='Tidak Aktif' checked> Tidak Aktif";
  }
  echo "</td></tr>
                          </tbody>
                          </table>
                        </div>  
                        <div style='clear:both'></div>
                        <div class='box-footer'>
                          <button type='submit' name='update1' class='btn btn-info'>Update</button>
                          <a href='javascript:window.history.back();'><button type='button' class='btn btn-default pull-right'>Batal</button></a>
                        </div> 
                        </form>
                    </div>


                    <div role='tabpanel' class='tab-pane fade' id='ortu' aria-labelledby='ortu-tab'>
                        <form action='' method='POST' class='form-horizontal'>
                        <div class='col-md-12'>
                          <table class='table table-condensed table-bordered'>
                          <tbody>
                            <tr><th style='background-color:#E7EAEC' width='160px' rowspan='22'>";
  if (trim($s['foto']) == '') {
    echo "<img class='img-thumbnail' style='width:155px' src='foto_siswa/no-image.jpg'>";
  } else {
    echo "<img class='img-thumbnail' style='width:155px' src='foto_siswa/$s[foto]'>";
  }
  echo "</th></tr>
                            <input type='hidden' value='$s[nipd]' name='id'>
                            <tr bgcolor=#e3e3e3><th width='130px' scope='row'>Nama Ayah</th> <td><input type='text' class='form-control' value='$s[nama_ayah]' name='ca'></td></tr>
                            <tr><th scope='row'>Tahun Lahir</th> <td><input type='text' class='form-control' value='$s[tahun_lahir_ayah]' name='cb'></td></tr>
                            <tr><th scope='row'>Pendidikan</th> <td><input type='text' class='form-control' value='$s[pendidikan_ayah]' name='cc'></td></tr>
                            <tr><th scope='row'>Pekerjaan</th> <td><input type='text' class='form-control' value='$s[pekerjaan_ayah]' name='cd'></td></tr>
                            <tr><th scope='row'>Penghasilan</th> <td><input type='text' class='form-control' value='$s[penghasilan_ayah]' name='ce'></td></tr>
                            <tr><th scope='row'>Kebutuhan Khusus</th> <td><input type='text' class='form-control' value='$s[kebutuhan_khusus_ayah]' name='cf'></td></tr>
                            <tr><th scope='row'>No Telpon</th> <td><input type='text' class='form-control' value='$s[no_telpon_ayah]' name='cg'></td></tr>
                            <tr><th scope='row' coslpan='2'><br></th></tr>
                            <tr bgcolor=#e3e3e3><th scope='row'>Nama Ibu</th> <td><input type='text' class='form-control' value='$s[nama_ibu]' name='ch'></td></tr>
                            <tr><th scope='row'>Tahun Lahir</th> <td><input type='text' class='form-control' value='$s[tahun_lahir_ibu]' name='ci'></td></tr>
                            <tr><th scope='row'>Pendidikan</th> <td><input type='text' class='form-control' value='$s[pendidikan_ibu]' name='cj'></td></tr>
                            <tr><th scope='row'>Pekerjaan</th> <td><input type='text' class='form-control' value='$s[pekerjaan_ibu]' name='ck'></td></tr>
                            <tr><th scope='row'>Penghasilan</th> <td><input type='text' class='form-control' value='$s[penghasilan_ibu]' name='cl'></td></tr>
                            <tr><th scope='row'>Kebutuhan Khusus</th> <td><input type='text' class='form-control' value='$s[kebutuhan_khusus_ibu]' name='cm'></td></tr>
                            <tr><th scope='row'>No Telpon</th> <td><input type='text' class='form-control' value='$s[no_telpon_ibu]' name='cn'></td></tr>
                            <tr><th scope='row' coslpan='2'><br></th></tr>
                            <tr bgcolor=#e3e3e3><th scope='row'>Nama Wali</th> <td><input type='text' class='form-control' value='$s[nama_wali]' name='co'></td></tr>
                            <tr><th scope='row'>Tahun Lahir</th> <td><input type='text' class='form-control' value='$s[tahun_lahir_wali]' name='cp'></td></tr>
                            <tr><th scope='row'>Pendidikan</th> <td><input type='text' class='form-control' value='$s[pendidikan_wali]' name='cq'></td></tr>
                            <tr><th scope='row'>Pekerjaan</th> <td><input type='text' class='form-control' value='$s[pekerjaan_wali]' name='cr'></td></tr>
                            <tr><th scope='row'>Penghasilan</th> <td><input type='text' class='form-control' value='$s[penghasilan_wali]' name='cs'></td></tr>
                          </tbody>
                          </table>
                        </div>
                        <div class='box-footer'>
                          <button type='submit' name='update2' class='btn btn-info'>Update</button>
                          <a href='javascript:window.history.back();'><button type='button' class='btn btn-default pull-right'>Batal</button></a>
                        </div>
                        </form>
                    </div>

                </div>
            </div>";
} elseif ($_GET['act'] == 'detailsiswapage') {
            cek_session_siswa();
            if ($_SESSION['level'] == 'siswa') {
              $nisn = $_SESSION['id'];
            } else {
              $nisn = $_GET['id'];
            }
            $detailsiswa = mysqli_query($koneksi, "SELECT * FROM siswa a LEFT JOIN kelas b ON a.kode_kelas=b.kode_kelas 
                                         
                                           
                                              LEFT JOIN agama e ON a.id_agama=e.id_agama 
                                                where a.nisn='$nisn'");
            $s = mysqli_fetch_array($detailsiswa);
            echo "<div class='col-md-12'>
                        <div class='box box-info'>
                          <div class='box-header with-border'>
                            <h3 class='box-title'>Detail Data Siswa</h3>
                          </div>
                          <div class='box-body'>
          
                            <div class='panel-body'>
                              <ul id='myTabs' class='nav nav-tabs' role='tablist'>
                                <li role='presentation' class='active'><a href='#siswa' id='siswa-tab' role='tab' data-toggle='tab' aria-controls='siswa' aria-expanded='true'>Data Siswa </a></li>
                                <li role='presentation' class=''><a href='#ortu' role='tab' id='ortu-tab' data-toggle='tab' aria-controls='ortu' aria-expanded='false'>Data Orang Tua / Wali</a></li>
                              </ul><br>
          
                              <div id='myTabContent' class='tab-content'>
                              <div role='tabpanel' class='tab-pane fade active in' id='siswa' aria-labelledby='siswa-tab'>
                                  <form class='form-horizontal'>
                                  <div class='col-md-7'>
                                    <table class='table table-condensed table-bordered'>
                                    <tbody>
                                      <tr><th style='background-color:#E7EAEC' width='160px' rowspan='17'>";
            if (trim($s['foto']) == '') {
              echo "<img class='img-thumbnail' style='width:155px' src='foto_siswa/no-image.jpg'>";
            } else {
              echo "<img class='img-thumbnail' style='width:155px' src='foto_siswa/$s[foto]'>";
            }
            /* if ($_SESSION['level'] != 'kepala') {
              echo "<a href='index.php?view=siswa&act=editsiswa&id=$_GET[id]' class='btn btn-success btn-block'>Edit Profile</a>";
            } */
            echo "</th>
                                      </tr>
                                      <tr><th width='120px' scope='row'>NIPD</th> <td>$s[nipd]</td></tr>
                                      <tr><th scope='row'>NISN</th> <td>$s[nisn]</td></tr>
                                      <tr><th scope='row'>Password</th> <td>$s[password]</td></tr>
                                      <tr><th scope='row'>Nama Siswa</th> <td>$s[nama]</td></tr>
                                      <tr><th scope='row'>Kelas</th> <td>$s[nama_kelas]</td></tr>
                                      <tr><th scope='row'>Angkatan</th> <td>$s[angkatan]</td></tr>
                                      <tr><th scope='row'>Jurusan</th> <td>$s[nama_jurusan]</td></tr>
                                      <tr><th scope='row'>Alamat Siswa</th> <td>$s[alamat]</td></tr>
                                      <tr><th scope='row'>RT/RW</th> <td>$s[rt]/$s[rw]</td></tr>
                                      <tr><th scope='row'>Dusun</th> <td>$s[dusun]</td></tr>
                                      <tr><th scope='row'>Kelurahan</th> <td>$s[kelurahan]</td></tr>
                                      <tr><th scope='row'>Kecamatan</th> <td>$s[kecamatan]</td></tr>
                                      <tr><th scope='row'>Kode Pos</th> <td>$s[kode_pos]</td></tr>
                                      <tr><th scope='row'>Status Awal</th> <td>$s[status_awal]</td></tr>
                                      <tr><th scope='row'>Status Siswa</th> <td>$s[status_siswa]</td></tr>
                                    </tbody>
                                    </table>
                                  </div>
                                  <div class='col-md-5'>
                                    <table class='table table-condensed table-bordered'>
                                    <tbody>
                                      <tr><th width='120px' scope='row'>NIK</th> <td>$s[nik]</td></tr>
                                      <tr><th scope='row'>Tempat Lahir</th> <td>$s[tempat_lahir]</td></tr>
                                      <tr><th scope='row'>Tanggal Lahir</th> <td>" . tgl_indo($s['tanggal_lahir']) . "</td></tr>
                                      <tr><th scope='row'>Jenis Kelamin</th> <td>$s[jenis_kelamin]</td></tr>
                                      <tr><th scope='row'>Agama</th> <td>$s[nama_agama]</td></tr>
                                      <tr><th scope='row'>Keb. Khusus</th> <td>$s[kebutuhan_khusus]</td></tr>
                                      <tr><th scope='row'>Jenis Tinggal</th> <td>$s[jenis_tinggal]</td></tr>
                                      <tr><th scope='row'>Transportasi</th> <td>$s[alat_transportasi]</td></tr>
                                      <tr><th scope='row'>No Telpon</th> <td>$s[telepon]</td></tr>
                                      <tr><th scope='row'>No Handpone</th> <td>$s[hp]</td></tr>
                                      <tr><th scope='row'>Alamat Email</th> <td>$s[email]</td></tr>
                                      <tr><th scope='row'>SKHUN</th> <td>$s[skhun]</td></tr>
                                      <tr><th scope='row'>Penerima KPS</th> <td>$s[penerima_kps]</td></tr>
                                      <tr><th scope='row'>No KPS</th> <td>$s[no_kps]</td></tr>
                                    </tbody>
                                    </table>
                                  </div>
                                  <div class='box-footer'>
                                    <a href='javascript:window.history.back();'><button type='button' class='btn btn-default pull-right'>Kembali</button></a>
                                  </div>
                                  </form>
                              </div>
          
                              <div role='tabpanel' class='tab-pane fade' id='ortu' aria-labelledby='ortu-tab'>
                                  <form class='form-horizontal'>
                                  <div class='col-md-12'>
                                    <table class='table table-condensed table-bordered'>
                                    <tbody>
                                      <tr><th style='background-color:#E7EAEC' width='160px' rowspan='20'>";
            if (trim($s['foto']) == '') {
              echo "<img class='img-thumbnail' style='width:155px' src='foto_siswa/no-image.jpg'>";
            } else {
              echo "<img class='img-thumbnail' style='width:155px' src='foto_siswa/$s[foto]'>";
            }
            if ($_SESSION['level'] != 'kepala') {
              echo "<a href='index.php?view=siswa&act=editsiswa&id=$_GET[id]' class='btn btn-success btn-block'>Edit Profile</a>";
            }
            echo "</th>
                                      </tr>
                                      <tr bgcolor=#e3e3e3><th width='120px' scope='row'>Nama Ayah</th> <td>$s[nama_ayah]</td></tr>
                                      <tr><th scope='row'>Tahun Lahir</th> <td>$s[tahun_lahir_ayah]</td></tr>
                                      <tr><th scope='row'>Pendidikan</th> <td>$s[pendidikan_ayah]</td></tr>
                                      <tr><th scope='row'>Pekerjaan</th> <td>$s[pekerjaan_ayah]</td></tr>
                                      <tr><th scope='row'>Penghasilan</th> <td>$s[penghasilan_ayah]</td></tr>
                                      <tr><th scope='row'>No Telpon</th> <td>$s[no_telpon_ayah]</td></tr>
                                      <tr><th scope='row' coslpan='2'><br></th></tr>
                                      <tr bgcolor=#e3e3e3><th scope='row'>Nama Ibu</th> <td>$s[nama_ibu]</td></tr>
                                      <tr><th scope='row'>Tahun Lahir</th> <td>$s[tahun_lahir_ibu]</td></tr>
                                      <tr><th scope='row'>Pendidikan</th> <td>$s[pendidikan_ibu]</td></tr>
                                      <tr><th scope='row'>Pekerjaan</th> <td>$s[pekerjaan_ibu]</td></tr>
                                      <tr><th scope='row'>Penghasilan</th> <td>$s[penghasilan_ibu]</td></tr>
                                      <tr><th scope='row'>No Telpon</th> <td>$s[no_telpon_ibu]</td></tr>
                                      <tr><th scope='row' coslpan='2'><br></th></tr>
                                      <tr bgcolor=#e3e3e3><th scope='row'>Nama Wali</th> <td>$s[nama_wali]</td></tr>
                                      <tr><th scope='row'>Tahun Lahir</th> <td>$s[tahun_lahir_wali]</td></tr>
                                      <tr><th scope='row'>Pendidikan</th> <td>$s[pendidikan_wali]</td></tr>
                                      <tr><th scope='row'>Pekerjaan</th> <td>$s[pekerjaan_wali]</td></tr>
                                      <tr><th scope='row'>Penghasilan</th> <td>$s[penghasilan_wali]</td></tr>
                                    </tbody>
                                    </table>
                                  </div>
                                  <div class='box-footer'>
                                    <a href='javascript:window.history.back();'><button type='button' class='btn btn-default pull-right'>Kembali</button></a>
                                  </div>
                                  </form>
                              </div>
          
                          </div>
                      </div>";
} elseif ($_GET['act'] == 'detailsiswa') {
  cek_session_siswa();
  if ($_SESSION['level'] == 'siswa') {
    $nisn = $_SESSION['id'];
  } else {
    $nisn = $_GET['id'];
  }
  $detailsiswa = mysqli_query($koneksi, "SELECT * FROM siswa a LEFT JOIN kelas b ON a.kode_kelas=b.kode_kelas 
                               
                                 
                                    LEFT JOIN agama e ON a.id_agama=e.id_agama 
                                      where a.nisn='$nisn'");
  $s = mysqli_fetch_array($detailsiswa);
  echo "<div class='col-md-12'>
              <div class='box box-info'>
                <div class='box-header with-border'>
                  <h3 class='box-title'>Detail Data Siswa</h3>
                </div>
                <div class='box-body'>

                  <div class='panel-body'>
                    <ul id='myTabs' class='nav nav-tabs' role='tablist'>
                      <li role='presentation' class='active'><a href='#siswa' id='siswa-tab' role='tab' data-toggle='tab' aria-controls='siswa' aria-expanded='true'>Data Siswa </a></li>
                      <li role='presentation' class=''><a href='#ortu' role='tab' id='ortu-tab' data-toggle='tab' aria-controls='ortu' aria-expanded='false'>Data Orang Tua / Wali</a></li>
                    </ul><br>

                    <div id='myTabContent' class='tab-content'>
                    <div role='tabpanel' class='tab-pane fade active in' id='siswa' aria-labelledby='siswa-tab'>
                        <form class='form-horizontal'>
                        <div class='col-md-7'>
                          <table class='table table-condensed table-bordered'>
                          <tbody>
                            <tr><th style='background-color:#E7EAEC' width='160px' rowspan='17'>";
  if (trim($s['foto']) == '') {
    echo "<img class='img-thumbnail' style='width:155px' src='foto_siswa/no-image.jpg'>";
  } else {
    echo "<img class='img-thumbnail' style='width:155px' src='foto_siswa/$s[foto]'>";
  }
  if ($_SESSION['level'] != 'kepala') {
    echo "<a href='index.php?view=siswa&act=editsiswa&id=$_GET[id]' class='btn btn-success btn-block'>Edit Profile</a>";
  }
  echo "</th>
                            </tr>
                            <tr><th width='120px' scope='row'>NIPD</th> <td>$s[nipd]</td></tr>
                            <tr><th scope='row'>NISN</th> <td>$s[nisn]</td></tr>
                            <tr><th scope='row'>Password</th> <td>$s[password]</td></tr>
                            <tr><th scope='row'>Nama Siswa</th> <td>$s[nama]</td></tr>
                            <tr><th scope='row'>Kelas</th> <td>$s[nama_kelas]</td></tr>
                            <tr><th scope='row'>Angkatan</th> <td>$s[angkatan]</td></tr>
                            <tr><th scope='row'>Jurusan</th> <td>$s[nama_jurusan]</td></tr>
                            <tr><th scope='row'>Alamat Siswa</th> <td>$s[alamat]</td></tr>
                            <tr><th scope='row'>RT/RW</th> <td>$s[rt]/$s[rw]</td></tr>
                            <tr><th scope='row'>Dusun</th> <td>$s[dusun]</td></tr>
                            <tr><th scope='row'>Kelurahan</th> <td>$s[kelurahan]</td></tr>
                            <tr><th scope='row'>Kecamatan</th> <td>$s[kecamatan]</td></tr>
                            <tr><th scope='row'>Kode Pos</th> <td>$s[kode_pos]</td></tr>
                            <tr><th scope='row'>Status Awal</th> <td>$s[status_awal]</td></tr>
                            <tr><th scope='row'>Status Siswa</th> <td>$s[status_siswa]</td></tr>
                          </tbody>
                          </table>
                        </div>
                        <div class='col-md-5'>
                          <table class='table table-condensed table-bordered'>
                          <tbody>
                            <tr><th width='120px' scope='row'>NIK</th> <td>$s[nik]</td></tr>
                            <tr><th scope='row'>Tempat Lahir</th> <td>$s[tempat_lahir]</td></tr>
                            <tr><th scope='row'>Tanggal Lahir</th> <td>" . tgl_indo($s['tanggal_lahir']) . "</td></tr>
                            <tr><th scope='row'>Jenis Kelamin</th> <td>$s[jenis_kelamin]</td></tr>
                            <tr><th scope='row'>Agama</th> <td>$s[nama_agama]</td></tr>
                            <tr><th scope='row'>Keb. Khusus</th> <td>$s[kebutuhan_khusus]</td></tr>
                            <tr><th scope='row'>Jenis Tinggal</th> <td>$s[jenis_tinggal]</td></tr>
                            <tr><th scope='row'>Transportasi</th> <td>$s[alat_transportasi]</td></tr>
                            <tr><th scope='row'>No Telpon</th> <td>$s[telepon]</td></tr>
                            <tr><th scope='row'>No Handpone</th> <td>$s[hp]</td></tr>
                            <tr><th scope='row'>Alamat Email</th> <td>$s[email]</td></tr>
                            <tr><th scope='row'>SKHUN</th> <td>$s[skhun]</td></tr>
                            <tr><th scope='row'>Penerima KPS</th> <td>$s[penerima_kps]</td></tr>
                            <tr><th scope='row'>No KPS</th> <td>$s[no_kps]</td></tr>
                          </tbody>
                          </table>
                        </div>
                        <div class='box-footer'>
                          <a href='javascript:window.history.back();'><button type='button' class='btn btn-default pull-right'>Kembali</button></a>
                        </div>
                        </form>
                    </div>

                    <div role='tabpanel' class='tab-pane fade' id='ortu' aria-labelledby='ortu-tab'>
                        <form class='form-horizontal'>
                        <div class='col-md-12'>
                          <table class='table table-condensed table-bordered'>
                          <tbody>
                            <tr><th style='background-color:#E7EAEC' width='160px' rowspan='20'>";
  if (trim($s['foto']) == '') {
    echo "<img class='img-thumbnail' style='width:155px' src='foto_siswa/no-image.jpg'>";
  } else {
    echo "<img class='img-thumbnail' style='width:155px' src='foto_siswa/$s[foto]'>";
  }
  if ($_SESSION['level'] != 'kepala') {
    echo "<a href='index.php?view=siswa&act=editsiswa&id=$_GET[id]' class='btn btn-success btn-block'>Edit Profile</a>";
  }
  echo "</th>
                            </tr>
                            <tr bgcolor=#e3e3e3><th width='120px' scope='row'>Nama Ayah</th> <td>$s[nama_ayah]</td></tr>
                            <tr><th scope='row'>Tahun Lahir</th> <td>$s[tahun_lahir_ayah]</td></tr>
                            <tr><th scope='row'>Pendidikan</th> <td>$s[pendidikan_ayah]</td></tr>
                            <tr><th scope='row'>Pekerjaan</th> <td>$s[pekerjaan_ayah]</td></tr>
                            <tr><th scope='row'>Penghasilan</th> <td>$s[penghasilan_ayah]</td></tr>
                            <tr><th scope='row'>No Telpon</th> <td>$s[no_telpon_ayah]</td></tr>
                            <tr><th scope='row' coslpan='2'><br></th></tr>
                            <tr bgcolor=#e3e3e3><th scope='row'>Nama Ibu</th> <td>$s[nama_ibu]</td></tr>
                            <tr><th scope='row'>Tahun Lahir</th> <td>$s[tahun_lahir_ibu]</td></tr>
                            <tr><th scope='row'>Pendidikan</th> <td>$s[pendidikan_ibu]</td></tr>
                            <tr><th scope='row'>Pekerjaan</th> <td>$s[pekerjaan_ibu]</td></tr>
                            <tr><th scope='row'>Penghasilan</th> <td>$s[penghasilan_ibu]</td></tr>
                            <tr><th scope='row'>No Telpon</th> <td>$s[no_telpon_ibu]</td></tr>
                            <tr><th scope='row' coslpan='2'><br></th></tr>
                            <tr bgcolor=#e3e3e3><th scope='row'>Nama Wali</th> <td>$s[nama_wali]</td></tr>
                            <tr><th scope='row'>Tahun Lahir</th> <td>$s[tahun_lahir_wali]</td></tr>
                            <tr><th scope='row'>Pendidikan</th> <td>$s[pendidikan_wali]</td></tr>
                            <tr><th scope='row'>Pekerjaan</th> <td>$s[pekerjaan_wali]</td></tr>
                            <tr><th scope='row'>Penghasilan</th> <td>$s[penghasilan_wali]</td></tr>
                          </tbody>
                          </table>
                        </div>
                        <div class='box-footer'>
                          <a href='javascript:window.history.back();'><button type='button' class='btn btn-default pull-right'>Kembali</button></a>
                        </div>
                        </form>
                    </div>

                </div>
            </div>";
} elseif ($_GET['act'] == 'penilaiandiri') {
  $t = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM siswa a JOIN kelas b ON a.kode_kelas=b.kode_kelas where a.nisn='$_GET[id]'"));
  echo "<div class='col-xs-12'>  
              <div class='box'>
                <div class='box-header'>
                  <h3 class='box-title'>Data Pertanyaan dan Jawaban Penilaian Diri </h3>
                </div>
                <div class='box-body'>

                        <div class='col-md-12'>
                            <table class='table table-condensed table-hover'>
                                <tbody>
                                  <tr><th width='120px' scope='row'>NISN</th> <td>$t[nisn]</td></tr>
                                  <tr><th scope='row'>Nama Siswa</th>           <td>$t[nama]</td></tr>
                                  <tr><th scope='row'>Kelas</th>           <td>$t[nama_kelas]</td></tr>
                                </tbody>
                            </table>
                        </div>

                  <table id='example2' class='table table-bordered table-striped'>
                    <thead>
                      <tr>
                        <th style='width:20px'>No</th>
                        <th>Pertanyaan</th>
                      </tr>
                    </thead>
                    <tbody>";

  $tampil = mysqli_query($koneksi, "SELECT * FROM pertanyaan_penilaian where status='diri' ORDER BY id_pertanyaan_penilaian DESC");
  $no = 1;
  while ($r = mysqli_fetch_array($tampil)) {
    $jwb = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM pertanyaan_penilaian_jawab where nisn='$_GET[id]' AND id_pertanyaan_penilaian='$r[id_pertanyaan_penilaian]' AND status='diri' AND kode_kelas='$t[kode_kelas]'"));
    if (trim($jwb['jawaban']) == '') {
      $jawab = "<i style='color:red'>Belum Ada Jawaban...</i>";
    } else {
      $jawab = "<i>$jwb[jawaban]</i>";
    }
    echo "<tr><td>$no</td>
                              <td>$r[pertanyaan] <br> <strong>Jawaban :</strong> <br>$jawab</td>
                          </tr>";
    $no++;
  }
  echo "</tbody>
                  </table>
                </div>
              </div>
            </div>";
} elseif ($_GET['act'] == 'penilaianteman') {
  echo "<div class='col-xs-12'>  
              <div class='box'>
              <form action='' method='POST'>
                <div class='box-header'>
                  <h3 class='box-title'>Semua Data Teman Kelas anda </h3>
                </div>
                <div class='box-body'>
                  <table class='table table-bordered table-striped'>
                    <thead>
                      <tr>
                        <th style='width:40px'>No</th>
                        <th>NIPD</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Angkatan</th>
                        <th>Jurusan</th>
                        <th>Kelas</th>
                        <th width='135px'></th>
                      </tr>
                    </thead>
                    <tbody>";

  $cs = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM siswa where nisn='$_GET[id]'"));
  $tampil = mysqli_query($koneksi, "SELECT * FROM siswa a LEFT JOIN kelas b ON a.kode_kelas=b.kode_kelas 
                                               
                                                
                                                  where a.kode_kelas='$cs[kode_kelas]' AND a.angkatan='$cs[angkatan]' AND nisn!='$_GET[id]' ORDER BY a.id_siswa");
  $no = 1;
  while ($r = mysqli_fetch_array($tampil)) {
    echo "<tr><td>$no</td>
                              <td>$r[nipd]</td>
                              <td>$r[nisn]</td>
                              <td>$r[nama]</td>
                              <td>$r[angkatan]</td>
                              <td>$r[nama_jurusan]</td>
                              <td>$r[nama_kelas]</td>
                              <td align=center><a class='btn btn-success btn-xs' title='Lihat Penilaian' href='index.php?view=siswa&act=pertanyaan&nisn=$r[nisn]&id=$_GET[id]'><span class='glyphicon glyphicon-search'></span> Lihat Penilaian</a></td>
                          </tr>";
    $no++;
  }
  echo "</tbody>
                  </table>
                </div>
              </form>
              </div>
            </div>";
} elseif ($_GET['act'] == 'pertanyaan') { ?>
  <div class="col-xs-12">
    <div class="box">
      <form action='' method='POST'>
        <div class="box-header">
          <h3 class="box-title">Data Pertanyan dan Jawaban Penilaian Teman </h3>
        </div><!-- /.box-header -->
        <div class="box-body">
          <?php
          echo "<input type='hidden' value='$_GET[nisn]' name='nisnteman'>";
          $t = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM siswa where nisn='$_GET[nisn]'"));
          $tt = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM siswa where nisn='$_GET[id]'"));
          echo "<div class='col-md-12'>
                            <table class='table table-condensed table-hover'>
                                <tbody>
                                  <tr><th scope='row'>NISN Penilai</th>           <td>$tt[nisn]</td></tr>
                                  <tr><th scope='row'>Nama Penilai</th>           <td>$tt[nama]</td></tr>

                                  <tr bgcolor=#f4f4f4><th width='120px' scope='row'>NISN Teman</th> <td style='color:blue'>$t[nisn]</td></tr>
                                  <tr bgcolor=#f4f4f4><th scope='row'>Nama Teman</th>           <td style='color:blue'>$t[nama]</td></tr>
                                </tbody>
                            </table>
                            </div>";
          ?>
          <table id="example3" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style='width:20px'>No</th>
                <th>Pertanyaan</th>
              </tr>
            </thead>
            <tbody>

              <?php
              $tampil = mysqli_query($koneksi, "SELECT * FROM pertanyaan_penilaian where status='teman' ORDER BY id_pertanyaan_penilaian DESC");
              $no = 1;
              while ($r = mysqli_fetch_array($tampil)) {
                $jwb = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM pertanyaan_penilaian_jawab where nisn='$_GET[id]' AND nisn_teman='$_GET[nisn]' AND id_pertanyaan_penilaian='$r[id_pertanyaan_penilaian]' AND status='teman' AND kode_kelas='$tt[kode_kelas]'"));
                if (trim($jwb['jawaban']) == '') {
                  $jawab = "<i style='color:red'>Belum Ada Jawaban...</i>";
                } else {
                  $jawab = "<i>$jwb[jawaban]</i>";
                }
                echo "<tr><td>$no</td>
                              <td>$r[pertanyaan] <br> <strong>Jawaban :</strong> <br>$jawab</td>
                          </tr>";
                $no++;
              }
              ?>
            </tbody>
          </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
  </div>
<?php
} elseif ($_GET['act'] == 'import_siswa') { 
  
  if (isset($_POST['import_siswa'])) {
    // var_dump($_FILES['excel_file']['type']);
    // die();

    $excelMimes = array('text/xls', 'text/xlsx', 'application/excel', 'application/vnd.msexcel', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); 
     
    // Validasi file excel
    if(!empty($_FILES['excel_file']['name']) && in_array($_FILES['excel_file']['type'], $excelMimes)){ 
         
        if(is_uploaded_file($_FILES['excel_file']['tmp_name'])){ 
            $reader = new Xlsx(); 
            $spreadsheet = $reader->load($_FILES['excel_file']['tmp_name']); 
            $worksheet = $spreadsheet->getActiveSheet();  
            $worksheet_arr = $worksheet->toArray(); 
 
            // Menghapus header dari row excel
            unset($worksheet_arr[0]); 
            unset($worksheet_arr[1]); 
            unset($worksheet_arr[2]); 
            unset($worksheet_arr[3]); 
            unset($worksheet_arr[4]); 
            unset($worksheet_arr[5]); 
            // var_dump($worksheet_arr);
            // die();
            $agama = 1;
 
            foreach($worksheet_arr as $row){ 
              // Replace karakter ' pada nama atau dihapus karakter ' untuk insert ke db
              $nama = str_replace("'","",$row[1]); 
              $nipd = $row[2]; 
              $jk = '0';
              if ($row[3] = 'L') {
                $jk = '0';
              } else {
                $jk = '1';
              }
              $nisn = $row[4];
              $tmpLahir = $row[5]; 
              $tglLahir = $row[6];
              $nik = $row[7];
              $agama = $row[8];
              $alamat = str_replace("'",'',$row[9]);
              $rt = $row[10];
              $rw = $row[11];
              $dusun = str_replace("'",'',$row[12]);
              $kelurahan = str_replace("'",'',$row[13]);
              $kecamatan = str_replace("'",'',$row[14]);
              $kodePos = $row[15];
              $jenisTinggal = $row[16];
              $alatTransportasi = $row[17];
              $telepon = $row[18];
              $noHp = $row[19];
              $email = $row[20];
              $skhun = $row[21];
              $penerimaKps = $row[22];
              $noKps = $row[23];
              $namaAyah = str_replace("'",'',$row[24]);
              $tahunAyah = $row[25];
              $jenjangPendAyah = $row[26];
              $pekerjaanAyah = str_replace("'",'',$row[27]);
              $penghasilanAyah = $row[28];
              $nikAyah = $row[29];
              $namaIbu = str_replace("'",'',$row[30]);
              $tahunIbu = $row[31];
              $jenjangPendIbu = $row[32];
              $pekerjaanIbu = str_replace("'",'',$row[33]);
              $penghasilanIbu = $row[34];
              $nikIbu = $row[35];
              $namaWali = str_replace("'",'',$row[36]);
              $tahunWali = $row[37];
              $jenjangPendWali = str_replace("'",'',$row[38]);
              $pekerjaanWali = str_replace("'",'',$row[39]);
              $penghasilanWali = $row[40];
              $nikWali = $row[41];
              $rombel = $row[42];
              $noPesertaUN = $row[43];
              $noIjazah = $row[44];
              $penerimaKip = $row[45];
              $nomorKip = $row[46];
              $namaDiKip = $row[47];
              $nomorKks = $row[48];
              $noRegAktaLahir = $row[49];
              $bank = $row[50];
              $noRek = $row[51];
              $atasNamaRek = str_replace("'",'',$row[52]);
              $layakPip = $row[53];
              $layakPip = $row[54];
              $kebutuhanKhusus = $row[55];
              $sekolahAsal = str_replace("'",'',$row[56]);
              $anakKe = $row[57];
              $lintang = str_replace("'",'',$row[58]);
              $bujur = str_replace("'",'',$row[59]);
              $noKk = $row[60];
              $bb = $row[61];
              $tb = $row[62];
              $lingkarKepala = $row[63];
              $jmlSaudaraKandung = $row[64];
              $jarakRumahSekolah = $row[65];

              $getAgama = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM agama WHERE nama_agama = '".$agama."'"));
              $agama = $getAgama['id_agama'];

                // Cek user 
                $prevResult =  mysqli_query($koneksi, "SELECT nipd FROM siswa WHERE nipd = '".$nipd."'"); 
                 
                if($prevResult->num_rows > 0){ 
                  mysqli_query($koneksi, "UPDATE siswa SET 
                              nipd = '$nipd',
                              nisn = '$nisn',
                              nama = '$nama',
                              alamat = '$alamat',
                              rt = '$rt',
                              rw = '$rw',
                              dusun = '$dusun',
                              kelurahan = '$kelurahan',
                              kecamatan = '$kecamatan',
                              kode_pos = '$kodePos',
                              nik = '$nik',
                              tempat_lahir = '$tmpLahir',
                              tanggal_lahir = '$tglLahir',
                              id_jenis_kelamin = '$jk',
                              id_agama = '$agama',
                              kebutuhan_khusus = '$kebutuhanKhusus',
                              jenis_tinggal = '$jenisTinggal',
                              alat_transportasi = '$alatTransportasi',
                              telepon = '$telepon',
                              hp = '$noHp',
                              email = '$email',
                              skhun = '$skhun',
                              penerima_kps = '$penerimaKps',
                              no_kps = '$noKps',
                              nama_ayah        = '$namaAyah',
                              tahun_lahir_ayah   = '$tahunAyah',
                              pendidikan_ayah         = '$jenjangPendAyah',
                              pekerjaan_ayah       = '$pekerjaanAyah',
                              penghasilan_ayah    = '$penghasilanAyah',
                              nama_ibu        = '$namaIbu',
                              tahun_lahir_ibu   = '$tahunIbu',
                              pendidikan_ibu         = '$jenjangPendIbu',
                              pekerjaan_ibu       = '$pekerjaanIbu',
                              penghasilan_ibu    = '$penghasilanIbu',
                              nama_wali        = '$namaWali',
                              tahun_lahir_wali   = '$tahunWali',
                              pendidikan_wali         = '$jenjangPendWali',
                              pekerjaan_wali       = '$pekerjaanWali',
                              penghasilan_wali    = '$penghasilanWali',
                              kode_kelas = '$rombel'
                              where nipd='$nipd'"); 
                }else{ 
                  mysqli_query($koneksi, "INSERT INTO siswa VALUES('','$nipd','','$nama','$jk','$nisn',
                             '$tmpLahir','$tglLahir','$nik','$agama','$kebutuhanKhusus','$alamat','$rt','$rw',
                             '$dusun','$kelurahan','$kecamatan','$kodePos','$jenisTinggal','$alatTransportasi','$telepon',
                             '$noHp','$email','$skhun','$penerimaKps','$noKps','','$namaAyah',
                              '$tahunAyah','$jenjangPendAyah','$pekerjaanAyah','$penghasilanAyah','','','$namaIbu',
                              '$tahunIbu','$jenjangPendIbu','$pekerjaanIbu','$penghasilanIbu','','','$namaWali',
                              '$tahunWali','$jenjangPendWali','$pekerjaanWali','$penghasilanWali','','Baru','Aktif',
                              '(SLTA)','','','0')");
                } 
            } 
             
            echo "<script>document.location='index.php?view=siswa';</script>";
        }else{ 
          echo "<script>document.location='index.php?view=siswa&act=tambah';</script>";
        } 
    }else{ 
      echo "<script>document.location='index.php?view=siswa&act=tambah';</script>";
    } 

  }
  
  ?>


  <div class="col-xs-12">
    <div class="box">
      <form action='' method='POST' enctype="multipart/form-data">
        <div class="box-header">
          <h3 class="box-title">Import Excel File Data Siswa</h3>
          <div style="margin-top: 10px;">
            <div>
              <span>Silahkan input file excel yang sesuai dengan contoh file berikut ini</span><br>
              <span>Proses ini membutuhkan waktu beberapa saat tergantung jumlah data excel, tunggu dan jangan tutup tab ini</span><br>
              <span><strong></strong>Template file excel dapat diunduh pada <a href="doc/example_file.xlsx">file ini.</a></span>
            </div>
            <div style="margin-top: 30px;">
              <label for="excel_file">File Excel / CSV</label>
              <input type="file" class="form-control" id="excel_file" name="excel_file">
            </div>
            <input type="submit" class="form-control btn btn-primary" style="margin-top: 20px; width: 200px;" name="import_siswa" value="Import Data Siswa">
          </div>
        </div><!-- /.box-header -->
      </from>
    </div><!-- /.box -->
  </div>
<?php
}
?>
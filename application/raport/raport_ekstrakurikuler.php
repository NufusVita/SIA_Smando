
<?php 
if ($_GET[act]==''){ 
    if (isset($_POST[simpan])){
            if ($_POST[status]=='Update'){
              mysqli_query($koneksi,"UPDATE nilai_ekstrakurikuler SET kegiatan='$_POST[a]', nilai='$_POST[b]', deskripsi='$_POST[c]' where id_nilai_ekstrakurikuler='$_POST[id]'");
            }else{
              mysqli_query($koneksi,"INSERT INTO nilai_ekstrakurikuler VALUES('','$_GET[tahun]','$_POST[nisn]','$_GET[kelas]','$_POST[a]','$_POST[b]','$_POST[c]','$_SESSION[id]','".date('Y-m-d H:i:s')."')");
            }
        echo "<script>document.location='index.php?view=ekstrakurikuler&tahun=$_GET[tahun]&kelas=$_GET[kelas]#$_POST[nisn]';</script>";
    }

    if (isset($_GET[delete])){
        mysqli_query($koneksi,"DELETE FROM nilai_ekstrakurikuler where id_nilai_ekstrakurikuler='$_GET[delete]'");
        echo "<script>document.location='index.php?view=ekstrakurikuler&tahun=$_GET[tahun]&kelas=$_GET[kelas]#$_POST[nisn]';</script>";
    }
?> 
            <div class="col-xs-12">  
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Input Ekstrakurikuler Siswa</h3>
                  <form style='margin-right:5px; margin-top:0px' class='pull-right' action='' method='GET'>
                    <input type="hidden" name='view' value='ekstrakurikuler'>
                    <select name='tahun' style='padding:4px'>
                        <?php 
                            echo "<option value=''>- Pilih Tahun Akademik -</option>";
                            $tahun = mysqli_query($koneksi,"SELECT * FROM tahun_akademik");
                            while ($k = mysqli_fetch_array($tahun)){
                              if ($_GET[tahun]==$k[id_tahun_akademik]){
                                echo "<option value='$k[id_tahun_akademik]' selected>$k[nama_tahun]</option>";
                              }else{
                                echo "<option value='$k[id_tahun_akademik]'>$k[nama_tahun]</option>";
                              }
                            }
                        ?>
                    </select>
                    <select name='kelas' style='padding:4px'>
                        <?php 
                            echo "<option value=''>- Filter Kelas -</option>";
                            $kelas = mysqli_query($koneksi,"SELECT * FROM kelas");
                            while ($k = mysqli_fetch_array($kelas)){
                              if ($_GET[kelas]==$k[kode_kelas]){
                                echo "<option value='$k[kode_kelas]' selected>$k[kode_kelas] - $k[nama_kelas]</option>";
                              }else{
                                echo "<option value='$k[kode_kelas]'>$k[kode_kelas] - $k[nama_kelas]</option>";
                              }
                            }
                        ?>
                    </select>
                    <input type="submit" style='margin-top:-4px' class='btn btn-info btn-sm' value='Lihat'>
                  </form>
                </div><!-- /.box-header -->
                <div class="box-body">
                <?php 
                  echo "<table id='example' class='table table-bordered table-striped'>
                    <thead>
                      <tr><th rowspan='2'>No</th>
                        <th>NISN</th>
                        <th width='170px'>Nama Siswa</th>
                        <th width='240px'><center>Kegiatan Ekstrakurikuler</center></th>
                        <th><center>Nilai</center></th>
                        <th><center>Deskripsi</center></th>
                        <th><center>Action</center></th>
                      </tr>
                    </thead>
                    <tbody>";

                  if ($_GET[kelas] != '' AND $_GET[tahun] != ''){
                    $tampil = mysqli_query($koneksi,"SELECT * FROM siswa a LEFT JOIN kelas b ON a.kode_kelas=b.kode_kelas 
                                              LEFT JOIN jenis_kelamin c ON a.id_jenis_kelamin=c.id_jenis_kelamin 
                                                LEFT JOIN jurusan d ON b.kode_jurusan=d.kode_jurusan 
                                                  where a.kode_kelas='$_GET[kelas]' ORDER BY a.id_siswa");
                  }
                    $no = 1;
                    while($r=mysqli_fetch_array($tampil)){
                      if (isset($_GET[edit])){
                          $e = mysqli_fetch_array(mysqli_query($koneksi,"SELECT * FROM nilai_ekstrakurikuler where id_nilai_ekstrakurikuler='$_GET[edit]'"));
                          $name = 'Update';
                      }else{
                          $name = 'Simpan';
                      }

                  if ($_GET[nisn]==$r[nisn]){   
                    echo "<form action='index.php?view=ekstrakurikuler&tahun=$_GET[tahun]&kelas=$_GET[kelas]' method='POST'>
                            <tr><td>$no</td>
                              <td>$r[nisn]</td>
                              <td style='font-size:12px' id='$r[nisn]'>$r[nama]</td>
                              <input type='hidden' name='nisn' value='$r[nisn]'>
                              <input type='hidden' name='id' value='$e[id_nilai_ekstrakurikuler]'>
                              <input type='hidden' name='status' value='$name'>
                              <td><input type='text' name='a' class='form-control' style='width:100%; color:blue' placeholder='Tuliskan Kegiatan...' value='$e[kegiatan]'></td>
                              <td><center><input type='text' class='form-control'  name='b' value='$e[nilai]' style='width:50px; text-align:center; padding:0px; color:blue'></center></td>
                              <td><input type='text' name='c' class='form-control' style='width:100%; color:blue' placeholder='Tuliskan Deskripsi...' value='$e[deskripsi]'></td>
                              <td align=center><input type='submit' name='simpan' class='btn btn-xs btn-primary' style='width:65px' value='$name'></td>
                            </tr>
                          </form>";
                  }else{
                    echo "<form action='index.php?view=ekstrakurikuler&tahun=$_GET[tahun]&kelas=$_GET[kelas]' method='POST'>
                            <tr><td>$no</td>
                              <td>$r[nisn]</td>
                              <td style='font-size:12px' id='$r[nisn]'>$r[nama]</td>
                              <input type='hidden' name='nisn' value='$r[nisn]'>
                              <input type='hidden' name='nisn' value='$r[nisn]'>
                              <td><input type='text' name='a' class='form-control' style='width:100%; color:blue' placeholder='Tuliskan Kegiatan...'></td>
                              <td><center><input type='text' class='form-control'  name='b' style='width:50px; text-align:center; padding:0px; color:blue'></center></td>
                              <td><input type='text' name='c' class='form-control' style='width:100%; color:blue' placeholder='Tuliskan Deskripsi...'></td>
                              <td align=center><input type='submit' name='simpan' class='btn btn-xs btn-primary' style='width:65px' value='$name'></td>
                            </tr>
                          </form>";
                  }

                            $pe = mysqli_query($koneksi,"SELECT * FROM nilai_ekstrakurikuler where id_tahun_akademik='$_GET[tahun]' AND nisn='$r[nisn]' AND kode_kelas='$_GET[kelas]'");
                            while ($n = mysqli_fetch_array($pe)){
                                echo "<tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>$n[kegiatan]</td>
                                        <td align=center>$n[nilai]</td>
                                        <td>$n[deskripsi]</td>
                                        <td align=center><a href='index.php?view=ekstrakurikuler&tahun=".$_GET[tahun]."&kelas=".$_GET[kelas]."&edit=".$n[id_nilai_ekstrakurikuler]."&nisn=".$r[nisn]."#$r[nisn]' class='btn btn-xs btn-success'><span class='glyphicon glyphicon-edit'></span></a>
                                                        <a href='index.php?view=ekstrakurikuler&tahun=".$_GET[tahun]."&kelas=".$_GET[kelas]."&delete=".$n[id_nilai_ekstrakurikuler]."&nisn=".$r[nisn]."' class='btn btn-xs btn-danger alert_notif'><span class='glyphicon glyphicon-remove'></span></a></td>
                                      </tr>";
                            }
                      $no++;
                      }
                  ?>
                    </tbody>
                  </table>
                </div><!-- /.box-body -->
                <?php 
                    if ($_GET[kelas] == '' AND $_GET[tahun] == ''){
                        echo "<center style='padding:60px; color:red'>Silahkan Memilih Tahun akademik dan Kelas Terlebih dahulu...</center>";
                    }
                ?>
              </div><!-- /.box -->
              
            </div>
<?php }  ?>
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
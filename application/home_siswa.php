<?php if ($_GET['act']==''){ ?>
            <div class="col-xs-12">  
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title"><?php "Jadwal Pelajaran Pada Tahun ".date('Y'); ?></h3>
                  

                </div><!-- /.box-header -->
                <div class="box-body">
                  <table id="example1" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th style='width:20px'>No</th>
                        <th>Kode Pelajaran</th>
                        <th>Jadwal Pelajaran</th>
                        <th>Kelas</th>
                        <th>Guru</th>
                        <th>Hari</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Ruang</th>
                        <!-- <th>Semester</th> -->
                        <!-- <th>Action</th> -->
                      </tr>
                    </thead>
                    <tbody>
                  <?php
                    if (isset($_SESSION['kode_kelas'])){
                      $tampil = mysqli_query($koneksi,"SELECT a.*, e.nama_kelas, b.namamatapelajaran, b.kode_pelajaran, c.nama_guru, d.nama_ruangan FROM jadwal_pelajaran a 
                                            JOIN mata_pelajaran b ON a.kode_pelajaran=b.kode_pelajaran
                                              JOIN guru c ON a.nip=c.nik 
                                                JOIN ruangan d ON a.kode_ruangan=d.kode_ruangan
                                                  JOIN kelas e ON a.kode_kelas=e.kode_kelas 
                                                  where a.kode_kelas='$_SESSION[kode_kelas]' ORDER BY a.hari DESC");
                    
                    }else{
                      $tampil = mysqli_query($koneksi,"SELECT a.*, e.nama_kelas, b.namamatapelajaran, b.kode_pelajaran, c.nama_guru, d.nama_ruangan FROM jadwal_pelajaran a 
                                            JOIN mata_pelajaran b ON a.kode_pelajaran=b.kode_pelajaran
                                              JOIN guru c ON a.nip=c.nik 
                                                JOIN ruangan d ON a.kode_ruangan=d.kode_ruangan
                                                JOIN kelas e ON a.kode_kelas=e.kode_kelas 
                                                  where a.kode_kelas='$_SESSION[kode_kelas]' ORDER BY a.hari DESC");
                    }
                    $no = 1;
                    while($r=mysqli_fetch_array($tampil)){
                      $s = mysqli_fetch_array(mysqli_query($koneksi,"SELECT * FROM jadwal_pelajaran INNER JOIN siswa on siswa.kode_kelas = jadwal_pelajaran.kode_kelas where kodejdwl='$r[kodejdwl]' AND nisn='$iden[nisn]'"));
                    echo "<tr><td>$no</td>
                              <td>$r[kode_pelajaran]</td>
                              <td>$r[namamatapelajaran]</td>
                              <td>$r[nama_kelas]</td>
                              <td>$r[nama_guru]</td>
                              <td>$r[hari]</td>
                              <td>$r[jam_mulai]</td>
                              <td>$r[jam_selesai]</td>
                              <td>$r[nama_ruangan]</td>
                              
                              
                          </tr>";
                      $no++;
                      }
                  ?>
                    </tbody>
                  </table>
                </div><!-- /.box-body -->
                </div>
            </div>

<?php 
}elseif ($_GET['act']=='kompetensidasar'){
    $d = mysqli_fetch_array(mysqli_query($koneksi,"SELECT * FROM jadwal_pelajaran a JOIN mata_pelajaran b ON a.kode_pelajaran=b.kode_pelajaran JOIN kelas c ON a.kode_kelas=c.kode_kelas where a.kodejdwl='$_GET[kodejdwl]'"));
            echo "<div class='col-xs-12'>  
              <div class='box'>
                <div class='box-header'>
                  <h3 class='box-title'>Kompetensi Dasar</h3>
                </div>
                <div class='box-body'>
                  <div class='col-md-12'>
                  <table class='table table-condensed table-hover'>
                      <tbody>
                        <input type='hidden' name='id' value='$d[kode_kelas]'>
                        <tr><th width='120px' scope='row'>Kode Kelas</th> <td>$d[kode_kelas]</td></tr>
                        <tr><th scope='row'>Nama Kelas</th>               <td>$d[nama_kelas]</td></tr>
                        <tr><th scope='row'>Mata Pelajaran</th>           <td>$d[namamatapelajaran]</td></tr>
                      </tbody>
                  </table>
                  </div>

                  <table class='table table-bordered table-striped'>
                    <thead>
                      <tr>
                        <th style='width:20px'>No</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Ranah</th>
                        <th>Indikator</th>
                      </tr>
                    </thead>
                    <tbody>";
                      $tampil = mysqli_query($koneksi,"SELECT * FROM kompetensi_dasar z JOIN jadwal_pelajaran a ON z.kodejdwl=a.kodejdwl JOIN kelas b ON a.kode_kelas=b.kode_kelas JOIN mata_pelajaran c ON a.kode_pelajaran=c.kode_pelajaran where a.kodejdwl='$_GET[kodejdwl]' ORDER BY z.id_kompetensi_dasar DESC");
                    $no = 1;
                    while($r=mysqli_fetch_array($tampil)){
                      $s = mysqli_fetch_array(mysqli_query($koneksi,"SELECT * FROM jadwal_pelajaran INNER JOIN siswa on siswa.kode_kelas = jadwal_pelajaran.kode_kelas where kodejdwl='$r[kodejdwl]' AND nisn='$iden[nisn]'"));
                    echo "<tr><td>$no</td>
                              <td>$r[nama_kelas]</td>
                              <td>$r[namamatapelajaran]</td>
                              <td>$r[ranah]</td>
                              <td>$r[kompetensi_dasar]</td>
                          </tr>";
                      $no++;
                      }
                    echo "<tbody>
                  </table>
                </div>
                </div>
            </div>";
} 
?>
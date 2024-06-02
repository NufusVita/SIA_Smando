            <a style='color:#000' href='index.php?view=siswa'>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                <?php $siswa = mysqli_fetch_array(mysqli_query($koneksi,"SELECT count(*) as total FROM siswa")); ?>
                  <span class="info-box-text">Siswa</span>
                  <span class="info-box-number"><?php echo $siswa[total]; ?></span>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- /.col -->
            </a>

            <a style='color:#000' href='index.php?view=guru'>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-user"></i></span>
                <div class="info-box-content">
                <?php $guru = mysqli_fetch_array(mysqli_query($koneksi,"SELECT count(*) as total FROM guru")); ?>
                  <span class="info-box-text">Guru</span>
                  <span class="info-box-number"><?php echo $guru[total]; ?></span>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- /.col -->
            </a>

            <a style='color:#000' href='index.php?view=kelas'>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-cubes"></i></span>
                <div class="info-box-content">
                <?php $kelas = mysqli_fetch_array(mysqli_query($koneksi,"SELECT count(*) as total FROM kelas")); ?>
                  <span class="info-box-text">Kelas</span>
                  <span class="info-box-number"><?php echo $kelas[total]; ?></span>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- /.col -->
            </a>

            <a style='color:#000' href='index.php?view=ruangan'>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-building"></i></span>
                <div class="info-box-content">
                <?php $ruangan = mysqli_fetch_array(mysqli_query($koneksi,"SELECT count(*) as total FROM ruangan")); ?>
                  <span class="info-box-text">Ruangan</span>
                  <span class="info-box-number"><?php echo $ruangan[total]; ?></span>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- /.col -->
            </a>
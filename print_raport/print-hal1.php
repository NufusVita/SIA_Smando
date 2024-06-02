<?php 
session_start();
error_reporting(0);
include "../config/koneksi.php"; 
include "../config/fungsi_indotgl.php"; 
$s = mysqli_fetch_array(mysqli_query($koneksi,"SELECT * FROM identitas_sekolah ORDER BY id_identitas_sekolah DESC LIMIT 1"));
?>
<html>
<head>
<title>Hal 1 - Raport Siswa</title>
<link rel="stylesheet" href="../bootstrap/css/printer.css">
<style type="text/css">
  td { padding:9px; }
</style>
</head>
<body onload="window.print()">
    <h1 align=center>RAPOR<br>SEKOLAH MENENGAH ATAS <br> (SMA)</h1><br><br><br>

    <table style='font-size:23px' width='100%'>
        <tr><td width='180px'><b>Nama Sekolah</td>   <td width='10px'> : </td><td> <?php echo "<b>$s[nama_sekolah]"; ?></td></tr>
        <tr><td width='180px'><b>NPSN/NSS</td>       <td width='10px'> : </td><td> <?php echo "<b>$s[npsn]"; ?></td></tr>
        <tr><td width='180px'><b>NSS</td>            <td width='10px'> : </td><td> <?php echo "<b>$s[nss]"; ?></td></tr>
        <tr><td width='180px'><b>Alamat Sekolah</td> <td width='10px'> : </td><td> <?php echo "<b>$s[alamat_sekolah]"; ?></td></tr>
        <tr><td width='180px'></td>               <td width='10px'>   </td><td> <?php echo "<b>Kode Pos $s[kode_pos], Telp. $s[no_telpon]"; ?></td></tr>
        <tr><td width='180px'><b>Kelurahan</td>      <td width='10px'> : </td><td> <?php echo "<b>$s[kelurahan]"; ?></td></tr>
        <tr><td width='180px'><b>Kecamatan</td>      <td width='10px'> : </td><td> <?php echo "<b>$s[kecamatan]"; ?></td></tr>
        <tr><td width='180px'><b>Kabupaten/Kota</td> <td width='10px'> : </td><td> <?php echo "<b>$s[kabupaten_kota]"; ?></td></tr>
        <tr><td width='180px'><b>Provinsi</td>       <td width='10px'> : </td><td> <?php echo "<b>$s[provinsi]"; ?></td></tr>
        <tr><td width='180px'><b>Website</td>        <td width='10px'> : </td><td> <?php echo "<b>$s[website]"; ?></td></tr>
        <tr><td width='180px'><b>E-Mail</td>         <td width='10px'> : </td><td> <?php echo "<b>$s[email]"; ?></td></tr>
    </table>
</body>
</html>
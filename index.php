<?php

function getdata($nip, $date1, $date2)
{
    $con1 = new mysqli('202.6.227.246', 'root', 'surabaya', 'db_tatsumi_attendance', '8089');
    $get = mysqli_query($con1, "SELECT DATE_FORMAT(fd_date,'%d %M %Y') tanggal, IF(a.fc_daytype='H','H',IF(a.fc_shift1='S','S',IF(a.fc_shift1='Z','S',IF(a.fc_shift1='X','S',fc_daytype)))) fc_daytype, CONCAT((IF(s1.fc_shiftnamealias = '1',s1.fc_shiftnamealias,IF(s2.fc_shiftnamealias = '1',s2.fc_shiftnamealias,IF(s3.fc_shiftnamealias='1',s3.fc_shiftnamealias,'')))),(IF(s1.fc_shiftnamealias = '2',s1.fc_shiftnamealias,IF(s2.fc_shiftnamealias = '2',s2.fc_shiftnamealias,IF(s3.fc_shiftnamealias='2',s3.fc_shiftnamealias,'')))),(IF(s1.fc_shiftnamealias = '3',s1.fc_shiftnamealias,IF(s2.fc_shiftnamealias = '3',s2.fc_shiftnamealias,IF(s3.fc_shiftnamealias='3',s3.fc_shiftnamealias,''))))) shift, (CASE WHEN IFNULL(NULLIF(s1.fc_shiftnamealias,''),501) < IFNULL(NULLIF(s2.fc_shiftnamealias,''),502) THEN (CASE WHEN IFNULL(NULLIF(s1.fc_shiftnamealias,''),501) < IFNULL(NULLIF(s3.fc_shiftnamealias,'') ,503) THEN ifnull(fc_timeinshift1,'') ELSE ifnull(fc_timeinshift3,'') END) ELSE (CASE WHEN IFNULL(NULLIF(s2.fc_shiftnamealias,''),502) < IFNULL(NULLIF(s3.fc_shiftnamealias,''),503) THEN ifnull(fc_timeinshift2,'') ELSE ifnull(fc_timeinshift3,'') END) END) work_in, (CASE WHEN IFNULL(s1.fc_shiftnamealias,0) > IFNULL(s2.fc_shiftnamealias,0) THEN (CASE WHEN IFNULL(s1.fc_shiftnamealias,0) > IFNULL(s3.fc_shiftnamealias,0) THEN ifnull(fc_timeoutshift1,'') ELSE ifnull(fc_timeoutshift3,'') END) ELSE (CASE WHEN IFNULL(s2.fc_shiftnamealias,0) > IFNULL(s3.fc_shiftnamealias,0) THEN ifnull(fc_timeoutshift2,'') ELSE ifnull(fc_timeoutshift3,'') END) END) work_out, a.fv_description  FROM t_attendance a LEFT JOIN t_dailytimerange s1 on a.fc_shift1 = s1.fc_timerangecode LEFT JOIN t_dailytimerange s2 on a.fc_shift2 = s2.fc_timerangecode LEFT JOIN t_dailytimerange s3 on a.fc_shift3 = s3.fc_timerangecode WHERE a.fc_membercode = '{$nip}' and a.fd_date>= '{$date1}' and a.fd_date <= '{$date2}' ORDER BY a.fd_date ASC");
    $con1->close();

    $res1 = mysqli_fetch_all($get);

    $con2 = new mysqli('localhost', 'hans', '!Red270896', 'fin_pro');
    $return = [];
    
    $np = substr($nip, -6);
    foreach ($res1 as $r) {
        $get2 = mysqli_query($con2, "SELECT a.scan_date FROM att_log a LEFT JOIN pegawai b on a.pin = b.pegawai_pin WHERE b.pegawai_nip = '$np' AND scan_date like '".date('Y-m-d', strtotime($r[0]))."%'");
        $res2 = mysqli_fetch_all($get2);
        foreach ($res2 as $k) {
            array_push($r, $k[0]);
        }
        $return[] = $r;
        
    }
    $con2->close();

    return $return;
}

function getWH($name) {

    $con = new mysqli('202.6.227.246', 'root', 'surabaya', 'db_tatsumi_attendance', '8089');
    
    $getwh = mysqli_query($con, "SELECT fc_membercode, fc_membername1 FROM t_member WHERE fc_memberposition LIKE '%OPT_WH%' AND fc_membername1 LIKE '%".$name."%'");
    $wh = mysqli_fetch_all($getwh);

    return $wh;
}

function getAddWH($name) {

    $con = new mysqli('202.6.227.246', 'root', 'surabaya', 'db_tatsumi_attendance', '8089');
    
    $getaddwh = mysqli_query($con, "SELECT fc_membercode, fc_membername1 FROM t_member WHERE fc_memberposition LIKE '%FORK%' AND fc_membername1 LIKE '%".$name."%'");
    $addwh = mysqli_fetch_all($getaddwh);

    return $addwh;
}

function getLog($name) {

    $con = new mysqli('202.6.227.246', 'root', 'surabaya', 'db_tatsumi_attendance', '8089');
    
    $getlog = mysqli_query($con, "SELECT fc_membercode, fc_membername1 FROM t_member WHERE fc_memberposition LIKE '%OPR_CLERK' AND fc_membername1 LIKE '%".$name."%'");
    $log = mysqli_fetch_all($getlog);

    return $log;
}

?>

<html>
    <head>
        <title>WH-LOGISTIC ATTENDANCE</title>
        <style>
            table {
                border-collapse: collapse;
            }
            table, th, td {
            border: 1px solid black;
            }
            .warehouse {
                width: 20%;
                float: left;
            }
            .logistic {
                width: 20%;
                float: left;
            }
            .result {
                width: 60%;
                float: right;
            }
        </style>
    </head>
    <body style="font-size: 12px;">
        <?php $con = new mysqli('202.6.227.246', 'root', 'surabaya', 'db_tatsumi_attendance', '8089');


$getaddwh = mysqli_query($con, "SELECT fc_membercode, fc_membername1 FROM t_member WHERE fc_membername1 LIKE '%IMAM MUAD%' ");
$addwh = mysqli_fetch_all($getaddwh);


if (date('d')>15) {
    $date1 = date('Y-m-01');
    $date2 = date('Y-m-15');
} else {
    $date1 = date('Y-m-16',strtotime('-1 month'));
    $date2 = date('Y-m-t',strtotime('-1 month'));
}
?>
        <b>TANGGAL</b> 
        Date 1 : <input type="text" id="date1" value="<?php echo $date1; ?>"> 
        Date 2 : <input type="text" id="date2" value="<?php echo $date2; ?>"><br>

        <br>
        <div class="row">
            <div class="warehouse">
                <table width="80%" style="font-size: 12px;">
                    <thead>
                        <tr>
                            <th colspan="3">WAREHOUSE</th>
                        </tr>
                        <tr>
                            <th width="10%">NIP</th>
                            <th width="30%">NAMA</th>
                            <th width="10%">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $wharr = ['TRI MUL', 'BADRUL', 'BIMA R', 'KEVIN IBRAHIM', 'FIRDAUS', 'SUNARDI', 'FAYAKHUN'];
                        foreach ($wharr as $name) {
                        $wh = getWH($name);
                        foreach ($wh as $row) {
                        ?>
                        <tr>    
                            <td style="text-align:center"><?php echo $row[0]; ?></td>
                            <td>&nbsp;<?php echo $row[1]; ?></td>
                            <td style="text-align:center">
                                <form action="index.php" method="get">
                                    <input type="hidden" name="nip" value="<?php echo $row[0]; ?>">
                                    <input type="hidden" name="name" value="<?php echo $row[1]; ?>">
                                    <input type="hidden" name="pos" value="wh">
                                    <input type="hidden" class="date1" name="date1" value="<?php echo $date1; ?>">
                                    <input type="hidden" class="date2" name="date2" value="<?php echo $date2; ?>">
                                    <button type="submit">SHOW</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="logistic">
                <table width="80%" style="font-size: 12px;">
                    <thead>
                        <tr>
                            <th colspan="3">LOGISTIC</th>
                        </tr>
                        <tr>
                            <th width="10%">NIP</th>
                            <th width="30%">NAMA</th>
                            <th width="10%">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $logarr = ['NUR HUDA','DERYL', 'DWI YUS', 'KELVIN', 'FAUZI', 'ARIF', 'AFIF', 'DEDI APRI', 'NIZAR', 'RIZKI DIAN', 'ARDIAN K', 'FATRA', 'AINUR R'];
                        foreach ($logarr as $name) {
                        $log = getLog($name);
                        foreach ($log as $row) {
                        ?>
                        <tr>    
                            <td style="text-align:center"><?php echo $row[0]; ?></td>
                            <td>&nbsp;<?php echo $row[1]; ?></td>
                            <td style="text-align:center">
                                <form action="index.php" method="get">
                                    <input type="hidden" name="nip" value="<?php echo $row[0]; ?>">
                                    <input type="hidden" name="name" value="<?php echo $row[1]; ?>">
                                    <input type="hidden" name="pos" value="log">
                                    <input type="hidden" class="date1" name="date1" value="<?php echo $date1; ?>">
                                    <input type="hidden" class="date2" name="date2" value="<?php echo $date2; ?>">
                                    <button type="submit">SHOW</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="result">
                <?php if (isset($_GET['date1'])) {
                    $data = getdata($_GET['nip'], $_GET['date1'], $_GET['date2']); ?>
                    <a href="index.php">CLEAR</a> 
                    <b><?php echo $_GET['nip']; ?></b> - <b><?php echo $_GET['name']; ?></b>
                    <br>
                    <div class="row">
                        <table style="width: 15%;float:left;font-size: 14px;">
                            <thead>
                                <tr>
                                    <th>tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $r) { ?>
                                    <tr>
                                        <td><?php echo '' == $r[0] ? ' ' : $r[0]; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>   
                        <table style="width: 75%;float:left;font-size: 14px;">
                            <thead>
                                <tr>
                                    <th width="10%">tipe</th>
                                    <th width="10%">shift</th>
                                    <th width="10%">in</th>
                                    <th width="10%">out</th>
                                    <?php if ($_GET['pos']=='wh') { ?>
                                        <th width="10%">scan1</th>
                                        <th width="10%">scan2</th>
                                        <th width="10%">scan3</th>
                                        <th width="10%">scan4</th>
                                    <?php }?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $r) { ?>
                                    <tr style="text-align: center;">
                                        <td><?=$r[1]=='' ? '<br>' : $r[1];?></td>
                                        <td><?=array_key_exists(2, $r) ? $r[2] : ' ';?></td>
                                        <td><?=array_key_exists(3, $r) ? $r[3] : ' ';?></td>
                                        <td><?=array_key_exists(4, $r) ? $r [4] : ' ';?></td>
                                        <?php if ($_GET['pos']=='wh') { ?>
                                            <td><?=array_key_exists(6, $r) ? date('H:i', strtotime($r[6])) : ' ';?></td>
                                            <td><?=array_key_exists(7, $r) ? date('H:i', strtotime($r[7])) : ' ';?></td>
                                            <td><?=array_key_exists(8, $r) ? date('H:i', strtotime($r[8])) : ' ';?></td>
                                            <td><?=array_key_exists(9, $r) ? date('H:i', strtotime($r[9])) : ' ';?></td>
                                        <?php }?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <table style="width: 10%;float:left;font-size: 14px;">
                            <thead>
                                <tr>
                                    <th>desc</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $r) { ?>
                                    <tr style="text-align: center;">
                                        <td><?=$r[5]=='' ? '<br>' : $r[5];?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>            
                    </div>
                <?php } ?>
            </div>
        </div>
    </body>
</html>
    


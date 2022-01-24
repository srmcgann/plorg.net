<?
  require('db.php');
  $ipfs_dir = explode("\n",shell_exec('which ipfs'))[0];;
  chdir('/var/www/html/plorg.net/dist_public/');
  function sortfunc($a, $b){ return $a[1] < $b[1];}

  function purgeOldSyncFiles(){
		global $ipfs_dir;
    $syncfiles=explode("\n", shell_exec("sudo -u cantelope $ipfs_dir files ls /sync 2>&1"));
    $ar=[];
    for($i=0; $i < sizeof($syncfiles); ++$i){
      if($syncfiles[$i]){
        $name=$syncfiles[$i];
        $parts = explode('_', explode('.', $syncfiles[$i])[0]);
        $Y=$parts[0];
        $m=$parts[1];
        $d=$parts[2];
        $H=$parts[3];
        $I=$parts[4];
        $s=$parts[5];
        $ar[]=[$name, strtotime("$Y-$m-$d $H:$I:$s")];
      }
    }
    usort($ar, "sortfunc");
    //echo json_encode($ar);
    for($i=0;$i<sizeof($ar);++$i){
      if($i){
        $file=$ar[$i][0];
        $output = shell_exec("sudo -u cantelope $ipfs_dir files rm /sync/$file 2>&1");
      } else {
        $newestFile = $ar[$i][0];
      }
    }
    return $newestFile;
  }     

  $file = purgeOldSyncFiles();
  $output = shell_exec("sudo -u cantelope $ipfs_dir files read /sync/$file | mysql -uuser -p$db_pass");
  echo $output . "\n";
  

  shell_exec("mysql -uuser -p$db_pass < generative.sql");
  shell_exec("mysqldump -h localhost -uuser -p$db_pass --no-data --single-transaction generative > temp.sql");
  $input=file_get_contents('temp.sql');
  $lines=explode("\n",$input);
  @unlink('sync.sql');
  file_put_contents('sync.sql', 'USE `' . $db . "`;\n", FILE_APPEND);
  for($i=0;$i<sizeof($lines);++$i){
    if(strpos($lines[$i], 'DROP TABLE IF EXISTS')===false){
      if(($pos=strpos($lines[$i], 'CREATE TABLE '))!==false){
        $lines[$i] = 'CREATE TABLE IF NOT EXISTS ' . substr($lines[$i],13);
      }
      file_put_contents('sync.sql', $lines[$i] . "\n", FILE_APPEND);
    }
  }
  unlink('temp.sql');
  $sql = "SHOW TABLES";
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    for($i=0;$i<mysqli_num_rows($res); ++$i){
      $table = mysqli_fetch_array($res);
      if($table[0] && $table[0] !== 'views'){
        $table = $table[0];
        $sql='SELECT * FROM ' . $table;
        if($res2 = mysqli_query($link, $sql)){
          for($k=0;$k<mysqli_num_rows($res2); ++$k){
            $cols = array_keys($row=mysqli_fetch_assoc($res2));
            $temp=[];
            for($j=0;$j<sizeof($cols);++$j){
              if($cols[$j] !== 'passhash') $temp[] = $cols[$j];
            }
            $cols = $temp;
            $updates = [];
            $vals=[];
            for($j=0;$j<sizeof($cols);++$j){
              if($cols[$j]!='updated'){
                $updates[] = $cols[$j] .'=IF('.$table.'.updated < DATE(newVals.updated),VALUES('.$cols[$j].'),'.$table.'.'.$cols[$j].')';
              }else{
                $updates[] = $table.'.updated=IF('.$table.'.updated < DATE(newVals.updated),"'. date('Y-m-d H:i:s') .'", '.$table.'.updated)';
              }
              $vals[] = '"'.$row[$cols[$j]].'"';
            }
            $sql = "INSERT INTO $table (" . implode(',',  $cols) . ") VALUES(".implode(',',$vals).") AS newVals ON DUPLICATE KEY UPDATE " . implode(',', $updates).';';
            file_put_contents('sync.sql', $sql . "\n", FILE_APPEND);
          }
          file_put_contents('sync.sql', "\n", FILE_APPEND);
        }
      }
    }
  }
  $file=date('Y_m_d_H_i_s').'.sql';
  copy('sync.sql', $filepath = "./sync/$file");
  $output = shell_exec("sudo -u cantelope $ipfs_dir add $filepath -q 2>&1");
  $ipfs_hash=($s=explode("\n", $output))[sizeof($s)-2];
  $output = shell_exec($command = "sudo -u cantelope $ipfs_dir files cp /ipfs/$ipfs_hash /sync/$file 2>&1");
  echo $output . "\n";
  purgeOldSyncFiles();
  shell_exec('rm ./sync/*');
?>

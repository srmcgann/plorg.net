<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $pkh = mysqli_real_escape_string($link, $data->{'pkh'});
  $sql = "SELECT * FROM users WHERE pkh = \"$pkh\" AND enabled = 1";
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    $url = mysqli_real_escape_string($link, $data->{'url'});
    $delay = intval(mysqli_real_escape_string($link, $data->{'delay'}));
    $width = intval(mysqli_real_escape_string($link, $data->{'width'}));
    $height = intval(mysqli_real_escape_string($link, $data->{'height'}));
    $out='/var/www/html/plorg.net/dist_public/scratchfolder/'.md5($url.(rand())).'.png';
    //header ('Content-Type: image/png');
    if($url){
      if($delay < 0 || $delay > 30000) $delay = 0;
      if(!$width || $width < 0 || $width > 1920) $width = 800;
      if(!$height || $height < 0 || $height > 1080) $height = 800;
      shell_exec("webshot $url $delay $width $height $out");
      //echo file_get_contents($out);
      $output = shell_exec($command = "sudo -u cantelope /usr/local/bin/ipfs add $out -q 2>&1");
      $t = 2;
      $ipfs_hash=($s=explode("\n", $output))[sizeof($s)-$t];
      unlink($out);
      echo json_encode([true, $ipfsURL . $ipfs_hash]);
    } else {
      echo json_encode([false, $url]);
    }
  } else {
    echo json_encode([false, $sql]);
  }
?>
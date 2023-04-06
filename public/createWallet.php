<?
  require('db.php');
  function decToAlpha($val){
    $alphabet="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $ret="";
    while($val){
      $r=floor($val/62);
      $frac=$val/62-$r;
      $ind=(int)round($frac*62);
      $ret=$alphabet[$ind].$ret;
      $val=$r;
    }
    return $ret==""?"0":$ret;
  }

  function escapeUserName($userName, $id){
    return str_replace('\\', '', str_replace(';', '', str_replace("'", '', escapeshellarg(str_replace(' ', '', str_replace("\t", '', str_replace(';', '', str_replace("\n", '', str_replace('&', '', str_replace('|', '', str_replace('@', '', str_replace('#', '', str_replace('$', '', str_replace('%', '', str_replace('^', '', str_replace('(', '', str_replace(')', '', str_replace('?','', str_replace('!', '', str_replace('_', '', str_replace('`', '', str_replace("'", '', str_replace( '~', '', str_replace('"', '', $userName))))))))))))))))))))))).decToAlpha($id));
  }


	$data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $userName = str_replace(';', '', $userName);
  $password = mysqli_real_escape_string($link, $data->{'password'});

  $available=str_replace(chr(10),'',file_get_contents('https://' . $baseURL . "/checkUserNameAvailability.php?userName=".urlencode($userName)))==="true";
  if($available && $password){
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $pkh='tz1NmVb4Ym2mrMVpFmBc2T6fWQ956XMSj6bS';
    //todo add actual address generation here
    $sql = 'INSERT INTO users (name, originalName, passhash, avatar, pkh, prefs, connected) VALUES("'.$userName.'", "'.$userName.'", "'.$hash.'", "", "'.$pkh.'", "", 1);';
    mysqli_query($link, $sql);
		$id = mysqli_insert_id($link);
    echo json_encode([true, $userName, $id, "", $pkh, 0, true, $hash]);
  } else {
    echo json_encode([$available,'https://' . $baseURL . "/checkUserNameAvailability.php?userName=".$userName,'username unavailable or password not provided!']);
  }
?>

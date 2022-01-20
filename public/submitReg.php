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
  $email = mysqli_real_escape_string($link, $data->{'email'});

  $available=str_replace(chr(10),'',file_get_contents('https://' . $baseURL . "/checkUserNameAvailability.php?userName=".urlencode($userName)))==="true";
  if($available && $password){
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $mnemonic = shell_exec('node /home/cantelope/tezos-nodejs/Main.js mnemonic');
    $passphrase = str_replace(' ', '_', str_replace("\n", '', $mnemonic));
    $alias = md5($passphrase);
    $filename = $alias;
    file_put_contents('./temp/'.$filename, $mnemonic . "\n");
    file_put_contents('./temp/'.$filename, $passphrase . "\n", FILE_APPEND);
    file_put_contents('./temp/'.$filename, $passphrase . "\n", FILE_APPEND);

    $output = shell_exec("sudo /home/cantelope/.local/bin/tezos-client import keys from mnemonic $alias -f --encrypt < /var/www/html/plorg.net/dist_public/temp/$filename 2>&1");
    @unlink('./temp/'.$filename);
    $pkh = str_replace("\n", '', explode('Tezos address added: ', $output)[1]);
    $wallet = [true, $pkh, str_replace("\n", '', $mnemonic)];

    $sql = 'INSERT INTO users (name, email, originalName, passhash, avatar, pkh, prefs, connected, walletAlias) VALUES("'.$userName.'", "'.$email.'", "'.$userName.'", "'.$hash.'", "", "'.$pkh.'", "", 1, "'.$alias.'");';
    mysqli_query($link, $sql);
		$id = mysqli_insert_id($link);
    echo json_encode([true, $userName, $id, "", $pkh, 0, true, $hash, $wallet]);
  } else {
    echo json_encode([$available,'https://' . $baseURL . "/checkUserNameAvailability.php?userName=".$userName,'username unavailable or password not provided!']);
  }
?>

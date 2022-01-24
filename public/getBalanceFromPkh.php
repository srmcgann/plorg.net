<?php
  require('db.php');
	$data = json_decode(file_get_contents('php://input'));
  $pkh = mysqli_real_escape_string($link, $data->{'pkh'});
  if($pkh){
    $sql = 'SELECT pkh FROM users WHERE pkh = "' . $pkh . '" AND enabled = 1';
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      if($pkh = mysqli_fetch_assoc($res)['pkh']){
        $bal=explode(' ', $output=shell_exec($command='sudo tezos-client get balance for ' . $pkh . ' 2>&1'))[0];
        $xtz_to_usd = json_decode(file_get_contents('https://'.$baseURL.'/XTZ_to_USD.php'));
        echo json_encode([true, $bal, $xtz_to_usd[0]?$xtz_to_usd[1]:'']);
      } else {
        echo json_encode([false, $output, $sql]);
      }
    } else {
      echo json_encode([false]);
    }
  } else {
    echo json_encode([false]);
  }
?>

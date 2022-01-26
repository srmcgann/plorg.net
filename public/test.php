<?
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

  //echo decToAlpha(2121777265) . "\n";

  //die();
  //populate blank hashes

  require('db.php');
  /*
  $sql="SELECT * FROM users";
  $res = mysqli_query($link, $sql);
  for($i=0; $i<mysqli_num_rows($res); ++$i){
    $row = mysqli_fetch_assoc($res);
    if($row['hash']==''){
      $hash = md5($row['pkh'].strtotime('now').rand());
      $sql = "UPDATE users SET hash = \"$hash\" where id = " . $row['id'];
      mysqli_query($link, $sql);
    }
  }
  */

  $sql="SELECT * FROM items";
  $res = mysqli_query($link, $sql);
  for($i=0; $i<mysqli_num_rows($res); ++$i){
    $row = mysqli_fetch_assoc($res);
    if($row['creatorHash']==''){
      //$hash = md5(hash_file('md5', $ipfsURL.$row['ipfs']).strtotime('now').rand());
      $sql = 'SELECT hash FROM users WHERE id = ' . $row['creatorID'];
      $res2 = mysqli_query($link, $sql);
      $row2 = mysqli_fetch_assoc($res2);
      $sql = "UPDATE items SET creatorHash = \"{$row2['hash']}\" where id = " . $row['id'];
      mysqli_query($link, $sql);
    }
  }

  die();

  $sql="SELECT * FROM comments";
  $res = mysqli_query($link, $sql);
  for($i=0; $i<mysqli_num_rows($res); ++$i){
    $row = mysqli_fetch_assoc($res);
    if($row['userHash']==''){
      $sql="SELECT * FROM users WHERE id = " . $row['userID'];
      $res2=mysqli_query($link, $sql);
      $row2=mysqli_fetch_assoc($res2);
      $userHash = $row2['hash'];
      $sql = "UPDATE comments SET userHash = \"$userHash\" where id = " . $row['id'];
      mysqli_query($link, $sql);
    }
    if($row['itemHash']==''){
      $sql="SELECT * FROM items WHERE id = " . $row['itemID'];
      $res2=mysqli_query($link, $sql);
      $row2=mysqli_fetch_assoc($res2);
      $itemHash = $row2['hash'];
      $sql = "UPDATE comments SET itemHash = \"$itemHash\" where id = " . $row['id'];
      mysqli_query($link, $sql);
    }
  }

  $sql="SELECT * FROM views";
  $res = mysqli_query($link, $sql);
  for($i=0; $i<mysqli_num_rows($res); ++$i){
    $row = mysqli_fetch_assoc($res);
    if($row['tokenHash']==''){
      //$hash = md5(hash_file('md5', $ipfsURL.$row['ipfs']).strtotime('now').rand());
      $sql = 'SELECT hash FROM items WHERE id = ' . $row['tokenID'];
      $res2 = mysqli_query($link, $sql);
      $row2 = mysqli_fetch_assoc($res2);
      $sql = "UPDATE views SET tokenHash = \"{$row2['hash']}\" where tokenid = " . $row['tokenID'];
      mysqli_query($link, $sql);
    }
  }
?>

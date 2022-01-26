<?
  require('db.php');
  $sql = 'SELECT * FROM items';
  $res = mysqli_query($link, $sql);
  for($i=0; $i<mysqli_num_rows($res); ++$i){
    if($i == mysqli_num_rows($res)-1){
      $row = mysqli_fetch_assoc($res);
      $ak = array_keys($row);
      for($j=0; $j< 20; ++$j){
        $sql = 'INSERT INTO items ('.implode(',',$ak).') VALUES('.
        (rand()).',"'.
        ($row['title']).'","'.
        ($row['address']).'","'.
        ($row['date']).'","'.
        ($row['userID']).'","'.
        ($row['description']).'","'.
        ($row['image']).'","'.
        ($row['private']).'","'.
        ($row['metaData']).'","'.
        ($row['views']).'","'.
        ($row['price']).'","'.
        ($row['royalties']).'","'.
        ($row['edition']).'","'.
        ($row['history']).'","'.
        ($row['created']).'","'.
        ($row['ipfs']).'","'.
        ($row['creatorID']).'","'.
        ($row['editions']).'","'.
        ($row['mints']).'",'.
        ($row['enabled']).')';
        mysqli_query($link, $sql);
      }
    }
  }
  echo 'done' . "\n";
?>

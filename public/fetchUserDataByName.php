<?
  require_once('db.php');
  //require_once('getBackups.php');
  $data = json_decode(file_get_contents('php://input'));
  $name = mysqli_real_escape_string($link, $data->{'name'});
  $page = mysqli_real_escape_string($link, $data->{'page'});
  $loggedinUserName = mysqli_real_escape_string($link, $data->{'loggedinUserName'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $overrideMaxResults = mysqli_real_escape_string($link, $data->{'maxResultsPerPage'});
  if($overrideMaxResults) $maxResultsPerPage = $overrideMaxResults;

  $admin = false;
  $totalPages=0;
  $start = $maxResultsPerPage * $page;

  if($name){
    if($loggedinUserName){
      $sql = 'SELECT * FROM users WHERE LOWER(name) = LOWER("' . $loggedinUserName . '") AND BINARY passhash = "' .  $passhash . '" AND enabled = 1';
      if($res = mysqli_query($link, $sql)){
        $row = mysqli_fetch_assoc($res);
        $pkh = $row['pkh'];
        $loggedinUserData = $row;
        if($row['isAdmin']) $admin = true;
        $loggedinPasshash = $row['passhash'];
      }
    }
    $sql='SELECT * FROM users WHERE LOWER(name) = LOWER("' . $name . '") AND enabled = 1';
    $res = mysqli_query($link, $sql);
    $row=mysqli_fetch_assoc($res);
    //$userID = $row['id'];
    if(strtoupper($row['name']) === strtoupper($loggedinUserName) && $passhash == $loggedinPasshash  || $admin){
      $includePrivate = true;
    }
    unset($row['passhash']);
    $ret = $row;
    if($includePrivate){

      $sql="SELECT id FROM items WHERE userHash = " . $row['hash'];
      $res = mysqli_query($link, $sql);
      $totalRecords = mysqli_num_rows($res);
      $totalPages = (($totalRecords-1) / $maxResultsPerPage | 0) + 1;
  
      $sql1=$sql = "SELECT * FROM items WHERE userHash = \"" . $row['hash'] . '" ORDER BY date DESC LIMIT ' . $start . ', ' . $maxResultsPerPage;
    } else {

      $sql="SELECT id FROM items WHERE private = 0 AND userHash = \"" . $row['hash'] . '"';

      $res = mysqli_query($link, $sql);
      $totalRecords = mysqli_num_rows($res);
      $morePages = false;
      $totalPages = (($totalRecords-1) / $maxResultsPerPage | 0) + 1;
      if($start + $maxResultsPerPage < $totalRecords + 1) $morePages = true;

      $sql1=$sql = "SELECT * FROM items WHERE userHash = \"" . $row['hash'] . '" AND private = 0 ORDER BY id DESC LIMIT ' . $start . ', ' . $maxResultsPerPage;
    }
    $res = mysqli_query($link, $sql);
    $items = [];
    for($i=0;$i<mysqli_num_rows($res);++$i){
      $row = mysqli_fetch_assoc($res);
      $items[]=$row;
    }
    forEach($items as &$item){
      $itemHash = $item['hash'];
      $sql = 'SELECT * FROM comments WHERE itemHash = "' . $itemHash . '" ORDER BY date DESC';
      $res2 = mysqli_query($link, $sql);
      $item['comments'] = [];
      for($j=0;$j<mysqli_num_rows($res2);++$j){
        $item['comments'][] = mysqli_fetch_assoc($res2);
      }
    }
    
    $ret['items'] = $items;
  } else {
    $ret = false;
  }
  echo json_encode([$ret, $totalPages, $sql1]);
?>

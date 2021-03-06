<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $maxResultsPerPage = mysqli_real_escape_string($link, $data->{'maxResultsPerPage'});
  $mode = mysqli_real_escape_string($link, $data->{'mode'});
  $curPage = mysqli_real_escape_string($link, $data->{'curPage'});


  $start = $maxResultsPerPage * $curPage;
  $sql = "SELECT hash FROM items WHERE listed = 1 and enabled = 1 AND private = 0 AND edition = 1";
	$res = mysqli_query($link, $sql);
  $totalRecords = mysqli_num_rows($res);
  $totalPages = (($totalRecords-1) / $maxResultsPerPage | 0) + 1;
	$sql = 'SELECT * FROM items WHERE listed = 1 AND enabled = 1 AND private = 0 AND edition = 1 ORDER BY date DESC LIMIT ' . $start . ', ' . $maxResultsPerPage;
	$res = mysqli_query($link, $sql);
	$items = [];

  for($i = 0; $i < mysqli_num_rows($res); ++$i){
    $item = mysqli_fetch_assoc($res);
    $sql2 = 'SELECT enabled FROM users WHERE hash = "' . $item['userHash'] . '"';
    $res2 = mysqli_query($link, $sql2);
    if(intval(mysqli_fetch_assoc($res2)['enabled'])){
      $items[] = $item;
    }  
  }
  forEach($items as &$item){
		$itemHash = $item['hash'];
    $sql2 = $sql = 'SELECT * FROM comments WHERE itemHash = "' . $itemHash . '" ORDER BY date DESC';
    $res2 = mysqli_query($link, $sql);
    $item['comments'] = [];
    $scanned=[];
    for($j=0;$j<mysqli_num_rows($res2);++$j){
      $row = mysqli_fetch_assoc($res2);
      $en = true;
      if(!isset($scanned[$row['id']])){
        $scanned[$row['userID']]=1;
        $sql3='SELECT enabled FROM users WHERE hash = "' . $row['userHash'] . '"';
        $res3 = mysqli_query($link, $sql3);
        $en = !!(mysqli_fetch_assoc($res3)['enabled']);
      }
      if($en){
        $item['comments'][] = $row;
      }
    }
  }
  echo json_encode([true, $items, $totalPages, $mode, $curPage]);

?>

<?
  require_once('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $string = mysqli_real_escape_string($link, $data->{'string'});
  $loggedinUserName = mysqli_real_escape_string($link, $data->{'loggedinUserName'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $page = mysqli_real_escape_string($link, $data->{'page'});
  $exact = mysqli_real_escape_string($link, $data->{'exact'});
  $allWords = mysqli_real_escape_string($link, $data->{'allWords'});
  $overrideMaxResults = mysqli_real_escape_string($link, $data->{'maxResultsPerPage'});
  if($overrideMaxResults) $maxResultsPerPage = $overrideMaxResults;

  if($exact){
    $tokens = [ $string ];
    $temp = explode(' ', $string);
    $ids = [];
    forEach($temp as $token){
      $sql = 'SELECT name, id FROM users WHERE enabled = 1 AND LOWER(name) = LOWER("'.$token.'")';
      $res = mysqli_query($link, $sql);
      for($i=0; $i<mysqli_num_rows($res);++$i){
        $ids[] = mysqli_fetch_assoc($res)['id'];
      }
    }
    $string2 = implode(' ', $ids);
  }else{
    $tokens = explode(' ', $string);
    $temp = explode(' ', $string);
    $ids = [];
    forEach($temp as $token){
      $sql = 'SELECT name, id FROM users WHERE enabled = 1 AND LOWER(name) = LOWER("'.$token.'"))';
      $res = mysqli_query($link, $sql);
      for($i=0; $i<mysqli_num_rows($res);++$i){
        $ids[] = mysqli_fetch_assoc($res)['id'];
      }
    }
    $string2 = implode(' ', $ids);
  }
  
  if($allWords){
    $clause = 'AND';
  }else{
    $clause = 'OR';
  }
  
  $admin = false;
  $start = $maxResultsPerPage * $page;

  if(sizeof($tokens)){
    
    $confirmed = false;
		if($loggedinUserName){
  		$sql = 'SELECT * FROM users WHERE LOWER(name) = LOWER("' . $loggedinUserName . '") AND enabled = 1 AND BINARY passhash = "' .  $passhash . '"';
		  if($res = mysqli_query($link, $sql)){
  		  $row = mysqli_fetch_assoc($res);
	  	  $loggedinUserData = $row;
        $confirmed = true;
				if($row['isAdmin']) $admin = true;
	  	}
		}

    if($loggedinUserName && $confirmed){
      $sql = 'SELECT * FROM items WHERE (listed = 1 || userHash = "'.$loggedinUserData['userHash'].'") AND ((title LIKE "%' . $tokens[0] . '%"';
    }else{
      $sql = 'SELECT * FROM items WHERE listed = 1 AND ((title LIKE "%' . $tokens[0] . '%"';
    }
    if(sizeof($tokens)>1){
      array_shift($tokens);
      forEach($tokens as $token){
        $sql .= ' ' . $clause . ' title LIKE "%'.$token.'%"';
      }
    }
    $sql .= ')';
    if($exact){
      $tokens = [ $string ];
    }else{
      $tokens = explode(' ', $string);
    }
    $sql .= ' OR (description LIKE "%' . $tokens[0] . '%"';
    if(sizeof($tokens)>1){
      array_shift($tokens);
      forEach($tokens as $token){
        $sql .= ' ' . $clause . ' description LIKE "%'.$token.'%"';
      }
    }
    $sql .= ')';
    if($exact){
      $tokens = [ $string ];
    }else{
      $tokens = explode(' ', $string);
    }
    $sql .= ' OR (address LIKE "%' . $tokens[0] . '%"';
    if(sizeof($tokens)>1){
      array_shift($tokens);
      forEach($tokens as $token){
        $sql .= ' ' . $clause . ' address LIKE "%'.$token.'%"';
      }
    }
    $sql .= ')';
    if($exact){
      $tokens = [ $string ];
    }else{
      $tokens = explode(' ', $string);
    }
    $sql .= ' OR (metaData LIKE "%' . $tokens[0] . '%"';
    if(sizeof($tokens)>1){
      array_shift($tokens);
      forEach($tokens as $token){
        $sql .= ' ' . $clause . ' metaData LIKE "%'.$token.'%"';
      }
    }
    if(strlen($string2)){
      $sql .= ')';
      if($exact){
        $tokens = [ $string2 ];
      }else{
        $tokens = explode(' ', $string2);
      }
      $sql .= ' OR (creatorID = ' . $tokens[0] ;
      $sql .= ')';
      if($exact){
        $tokens = [ $string2 ];
      }else{
        $tokens = explode(' ', $string2);
      }
      $sql .= ' OR (userID = ' . $tokens[0] ;
    }
    $sql .= '))';
  }
  
  $sql;
	$res = mysqli_query($link, $sql);
  $totalRecords = mysqli_num_rows($res);
  $totalPages = (($totalRecords-1) / $maxResultsPerPage | 0) + 1;


  $sql1 = $sql .= ' ORDER BY created DESC LIMIT ' . $start . ', ' . $maxResultsPerPage;
	$res = mysqli_query($link, $sql);
  
	$items = [];
	for($i = 0; $i < mysqli_num_rows($res); ++$i){
		$items[] = mysqli_fetch_assoc($res);
  }

  forEach($items as &$item){
		$itemID = $item['id'];
    $sql = 'SELECT * FROM comments WHERE itemID = ' . $itemID . ' ORDER BY date DESC';
    $res = mysqli_query($link, $sql);
    $item['comments'] = [];
    for($j=0;$j<mysqli_num_rows($res);++$j){
      $item['comments'][] = mysqli_fetch_assoc($res);
    }
  }
  
	echo json_encode([$items, $totalPages, $page, $totalRecords, $sql1]);
?>


<?
  function alphaToDec($val){
    $pow=0;
    $res=0;
    while($val!=""){
      $cur=$val[strlen($val)-1];
      $val=substr($val,0,strlen($val)-1);
      $mul=ord($cur)<58?$cur:ord($cur)-(ord($cur)>96?87:29);
      $res+=$mul*pow(62,$pow);
      $pow++;
    }
    return $res;
  }

  require('db.php');
  $query = explode('/',$_GET['i']);
  $title = 'plorg.net';
  $image = 'https://jsbot.cantelope.org/uploads/1jft1e.png';
  if($query[0] === 't'){
    $id = alphaToDec(mysqli_real_escape_string($link, $query[1]));
    $sql = 'SELECT * FROM items WHERE id = ' . $id;
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      $image = $row['image'];
      $imageData = getimagesize($image);
      $imageWidth = $imageData[0];
      $imageHeight = $imageData[1];
      $sql='SELECT name, avatar FROM users WHERE id = '. $row['userID'];
      $res2=mysqli_query($link, $sql);
      $row2=mysqli_fetch_assoc($res2);
      $title = $row['title'];
      $res = mysqli_query($link, $sql);
    }
  } elseif($query[0] === 'u') {
    $sql = 'SELECT name, avatar FROM users WHERE name LIKE "' . mysqli_real_escape_string($link, $query[1]) . '";';
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      if($row['name']) $title = 'collection - ' . $row['name'];
      if($row['avatar']) $image = $row['avatar'];
    }
  } else {
    $image = 'https://jsbot.cantelope.org/uploads/1jft1e.png';
  }
  $url =  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https:" : "http:") . "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
  $url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
  $type = 'website';
  $description = 'plorg.net - digital assets on blockchain';
?> <!DOCTYPE html><html lang=""><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width,initial-scale=0.55"><meta name="description" content="<?=$description?>"><title><?=$title?></title> <? if($image){?> <link rel="icon" href="<?='https://plorg.net/imgProxy.php?url='.$image?>"><?}else{?> <link rel="icon" href="https://jsbot.cantelope.org/uploads/VCNU1.png"> <?}?> <? if($image){?><meta property="og:url" content="<?=$url?>"><?}?> <? if($image){?><meta property="og:type" content="<?=$type?>"><?}?> <? if($image){?><meta property="og:title" content="<?=$title?>"><?}?> <? if($image){?><meta property="og:description" content="<?=$description?>"><?}?> <? if($image){?><meta property="og:image" content="<?=$image?>"><?}?> <link href="/css/app.fd3ec2a2.css" rel="preload" as="style"><link href="/js/app.76bcecdc.js" rel="preload" as="script"><link href="/js/chunk-vendors.3a09a439.js" rel="preload" as="script"><link href="/css/app.fd3ec2a2.css" rel="stylesheet"></head><body><noscript><strong>We're sorry but plorg.net doesn't work properly without JavaScript enabled. Please enable it to continue.</strong></noscript><div id="app"></div><script src="/js/chunk-vendors.3a09a439.js"></script><script src="/js/app.76bcecdc.js"></script></body></html>
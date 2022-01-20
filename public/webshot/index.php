<?
  if($_SERVER['SERVER_NAME'] !== 'plorg.net') die();
  $url = escapeshellarg($_GET['url']);
  $delay = intval($_GET['delay']);
  $width = intval($_GET['width']);
  $height = intval($_GET['height']);
  $out=md5($url.(rand()));
  header ('Content-Type: image/png'); 
  if($url){
    if($delay < 0 || $delay > 30000) $delay = 0;
    if(!$width || $width < 0 || $width > 1920) $width = 800;
    if(!$height || $height < 0 || $height > 1080) $height = 800;
    shell_exec("webshot $url $delay $width $height $out");
    echo file_get_contents($out);
    unlink($out);
  }
?>

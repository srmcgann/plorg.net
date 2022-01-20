<?
  header ('Content-Type: audio/mpeg');
  $url = $_GET['url'];
  echo file_get_contents($url);
?>

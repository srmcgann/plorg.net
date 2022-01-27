<?
  $db_user="user";
  $db_pass=explode("\n", file_get_contents('/home/cantelope/plorgpw'))[0];
  $db_host="localhost";
  $db="generative";
  $maxResultsPerPage = 4;
  $peer = explode("\n", file_get_contents('/home/cantelope/plorgPeer'))[0];
  $baseURL = explode("\n", file_get_contents('/home/cantelope/plorgBaseURL'))[0];
  $ipfsURL = 'https://ipfs.dweet.net/ipfs/';  
  $link = mysqli_connect($db_host, $db_user, $db_pass, $db);
?>

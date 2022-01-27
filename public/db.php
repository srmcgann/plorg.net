<?
  $db_user="user";
  $db_pass=file_get_contents('/home/cantelope/plorgpw');
  $db_host="localhost";
  $db="generative";
  $maxResultsPerPage = 4;
  $peer = 'https://plorgmirror.appliedlearning.academy';
  $baseURL = 'plorg.net';
  $ipfsURL = 'https://ipfs.dweet.net/ipfs/';  
  $link = mysqli_connect($db_host, $db_user, $db_pass, $db);
?>

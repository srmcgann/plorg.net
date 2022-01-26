<?
  $db_user="user";
  $db_pass="";
  $db_host="localhost";
  $db="generative";
  $maxResultsPerPage = 4;
	$peer = 'https://plorgmirror.appliedlearning.academy';
  $masterDBSyncURL = file_get_contents($peer . '/links/curlink.php');
  $baseURL = 'plorgmirror.appliedlearning.academy';
  $ipfsURL = 'https://ipfs.appliedlearning.academy/ipfs/';
  
  $link = mysqli_connect($db_host, $db_user, $db_pass, $db);
?>



<?php
require_once('../includes/config.php');
if(!$user->is_logged_in()){ header('Location: login.php'); }
if(isset($_GET['delpost'])){ 
	$folder = $_GET['folder'];
	$path="../images/$folder";
	delete_directory($path);
	$stmt = $db->prepare('DELETE FROM suggestions WHERE postID = :postID') ;
	$stmt->execute(array(':postID' => $_GET['delpost']));
	header('Location: suggestions.php?action=Discarded');
	exit;
} 
?>
<?php function delete_directory($dir){
	if(is_dir($dir)){
		$dir_handle = opendir($dir);
	}
	if(!$dir_handle){
		return false;
	}
	while($file = readdir($dir_handle)){
		if($file != "." && $file != ".."){
			if(!is_dir($dir."/".$file)) unlink($dir."/".$file);
			else delete_directory($dir."/".$file);
		}
	}
	closedir($dir_handle);
	rmdir($dir);
	return true;
}?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin</title>
  <link rel="stylesheet" href="../css/main.css">
  <?php include "../includes/linkStyle.html"?>
  <script language="JavaScript" type="text/javascript">
  function delpost(id, title,folder)
  {
	  if (confirm("Are you sure you want to discard '" + title + "'"))
	  {
		  window.location.href = 'suggestions.php?delpost=' + id+'&folder='+folder;
	  }
  }
  </script>
</head>
<body>
	<?php include('navbar.html.php');?>
	<div id="wrapper" >
	<?php 
	if(isset($_GET['action'])){ 
		echo '<h3>Post '.$_GET['action'].'.</h3>'; 
	} 
	?>
	<h1>Posts</h1>
	<hr/>
	<table  style="font-size:15px;">
	<tr>
		<th>Title</th>
		<th>Author</th>
		<th style="min-width:100px;">Action</th>
	</tr>
	<?php
		try {
			$stmt = $db->query('SELECT postID, postTitle,authorName,postDescImage FROM suggestions ORDER BY postID DESC');
			while($row = $stmt->fetch()){
				
				echo '<tr>';
				echo '<td>'.$row['postTitle'].'</td>';
				echo '<td>'.$row['authorName'].'</td>';
				?>

				<td>
					<a href="add-suggest-post.php?suggest=1&id=<?php echo $row['postID'];?>" style="color:red;">Look</a> | 
					<a href="javascript:delpost('<?php echo $row['postID'];?>','<?php echo $row['postTitle'];?>','<?php echo $row['postDescImage'];?>')" style="color:red;">Discard</a>
				</td>

				<?php 
				echo '</tr>';
			}
		} catch(PDOException $e) {
		    echo $e->getMessage();
		}
	?>
	</table>
	<br><br>
</div>
<?php include "../includes/linkScript.html"?>
<?php include "footer.html.php"?>
</body>
</html>
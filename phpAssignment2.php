<?php
ini_set('display_errors', 'On');
require 'db/connect.php';

$records = array();
$cats = array();

$forcat = $db->query("SELECT * FROM VideoStoreInventory");
if($forcat->num_rows) {
	while($row = $forcat->fetch_object()) {
		$dupeCheck = strtolower($row->category);
		if(!(in_array($dupeCheck, $cats))) {
			$cats[] = $dupeCheck;
		}
	}
	$forcat->free();
}

//displaying current database
if(empty($_POST)) {
	if($result = $db->query("SELECT * FROM VideoStoreInventory")) {
		if($result->num_rows) {
			while($row = $result->fetch_object()) {
				$records[] = $row;
			}
			$result->free();
		}
	}
}
//adding a new movie
if(!empty($_POST)) {
	if(isset($_POST['title'], $_POST['category'], $_POST['length'])) {
		$title = trim($_POST['title']);
		$category = trim(strtolower($_POST['category']));
		$length = trim($_POST['length']);
		
		if(!empty($title) && !empty($category) && !empty($length)) {
			$insert = $db->prepare("INSERT INTO VideoStoreInventory (name, category, length) VALUES (?, ?, ?)");
			$insert->bind_param('ssi', $title, $category, $length);
			
			if($insert->execute()) {
				header('Location: phpAssignment2.php');
				die();
			}
			$insert->close();
		}
	}
	else if(isset($_POST['genre']) && !isset($_POST['nuke'])) {
		if($_POST['genre'] == "all") {
			if($result = $db->query("SELECT * FROM VideoStoreInventory")) {
				if($result->num_rows) {
					while($row = $result->fetch_object()) {
						if(!(in_array($row->name, $records))) {
						$records[] = $row;
						}
					}
					$result->free();
				}
			}
		}
		else if($_POST['genre'] != "all") {
			$records = null;
			$result = $db->prepare("SELECT * FROM VideoStoreInventory WHERE category = ?");
			$result->bind_param('i', $_POST['genre']);
			if($result->execute()) {
				echo "should be display all movies with category: ", $_POST['genre'];
				if($result->num_rows) {
					while($row = $result->fetch_object()) {
						$records[] = $row;
					}
					$result->free();
				}
			}
		}
	}
	else if(isset($_POST['nuke'])) {
		$deleteAll = $db->prepare("DELETE FROM VideoStoreInventory");
		$deleteAll->execute();
		header('Location: phpAssignment2.php');
	}
}

//deleting a movie
if(isset($_GET['movieId'])) {
	$deleteMovie = $db->prepare("DELETE FROM VideoStoreInventory WHERE id = ?");
	$deleteMovie->bind_param('i', $_GET['movieId']);
	
	if($deleteMovie->execute()) {
		header('Location: phpAssignment2.php');	
		die();
	}
	$deleteMovie->free();
}

//checking out/return
if(isset($_GET['movieSId'])) {
	$updateMovie = $db->prepare("UPDATE VideoStoreInventory SET rented = !rented WHERE id = ?");
	$updateMovie->bind_param('i', $_GET['movieSId']);
	if($updateMovie->execute()) {
		header('Location: phpAssignment2.php');	
		die();
	}
	$updateMovie->free();
}

?>

<DOCTYPE! html>
<html>
	<head>
		<title>Video Store</title>
	</head>
	<body>
		<h2>Video Store Inventory</h2>
		
		<?php
		if(!count($records)) {
			echo 'We got nothing in the shop right now';
		}
		else {
		?>
			<table>
				<thead>
					<tr>
						<th>Title</th>
						<th>Category</th>
						<th>Length</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($records as $r) {
					?>
						<tr>
							<td><?php echo $r->name; ?></td>
							<td><?php echo $r->category; ?></td>
							<td><?php echo $r->length, 'min(s)'; ?></td>
							<td><?php 
									if($r->rented == 1) {
										echo 'Checked Out';
									}
									else {
										echo 'Available'; 
									} 
								?></td>
							<td><a href="phpAssignment2.php?movieSId=<?php echo $r->id; ?>">Check Out/Return<a/></td>
							<td><a href="phpAssignment2.php?movieId=<?php echo $r->id; ?>">Delete<a/></td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		<?php
		}
		?>
		
		<hr>
		
		<form action="" method="POST">
			<label for="title">Title</label>
			<input type="text" name="title" id="title" required>
			<label for="title">Category</label>
			<input type="text" name="category" id="category" required>
			<label for="title">Length(min)</label>
			<input type="number" name="length" id="length" required>
			<input type="submit" value="Add Movie">
		</form>
		<div>
		CATEGORY FILTER
			<form action="" method="POST">
				<select name="genre">
					<option value="all">All Movies</option>
					<?php 
					foreach ($cats as $ca) { 
					?>
						<option value="<?php echo $ca; ?>"><?php echo $ca; ?></option>
					<?php 
					}
					?>
				</select>
				<input type="submit">
			</form>
		</div>
		<div>
			<form action="" method="POST">
				<input name="nuke" type="submit" value="DELETE ALL RECORDS">
			</form>
		</div>
		
	</body>
</html>
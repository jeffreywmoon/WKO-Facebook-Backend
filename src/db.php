
<?php
/*
 * database manager for fbookposts.php
 *
 * @author: Jeffrey Moon
 *
 */

// connection info
$USERNAME = "root";
$PASSWORD = "";
$SERVERNAME = "localhost";
$DBNAME = "test";

/*
 * returns a connection to the mysql db
 */
function connect(){
	global $SERVERNAME, $USERNAME, $PASSWORD, $DBNAME;

	// connect...
	$conn = new mysqli($SERVERNAME, $USERNAME, $PASSWORD, $DBNAME);
	
	//if connection failed
	if($conn->connect_error)
		die("Connection to database failed: ".$conn->connect_error);
	else
		// connection was successful	
		return $conn;
}

/*
 * Executes a mysql INSERT with the given sql
 */
function insert($sql){
	$conn = connect();
	
	// if insert was successfull
	if(mysqli_query($conn, $sql)){
		// close db connection
		mysqli_close($conn);
		return true;
	}else{
		// insert unsuccessful
		mysqli_close($conn);
		return false;
	}
}

/*
 * Queries the mysql db with a SELECT statement
 */
function query($sql){
	$conn = connect();
	// query db 
	$result = $conn->query($sql);
	// close connection after query
	mysqli_close($conn);

	return $result;	
}

function countRows(){
	$sql = "SELECT count(*) as 'count' FROM facebook";
	$result = query($sql);

	$row = $result->fetch_assoc();
	return $row['count'];
}

?>

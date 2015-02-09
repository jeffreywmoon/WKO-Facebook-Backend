
<?php
/*
 * Manages a table of the most recent facebook posts on a public page.
 * The update() function must be called in order to sync the db with the 
 * facebook page.
 *
 * @Author Jeffrey Moon
 *
 */
include 'db.php';
$NUMBER_OF_POSTS=5;
$MIN_POST_LENGTH=3;
$PAGE_ID="781575691936316";

/**
 * syncs the db with the facebook page
 */
function update(){
	global $NUMBER_OF_POSTS;
	$posts = getPosts();

	printPosts($posts);

	$prev_post = getNewestFromDb();

	$added_posts=0;

	echo $prev_post['idpost'];
	// check each of the posts we received, starting with the newest-created
	foreach($posts['data'] as $post){
		//if given post has same id as newest in db, we know we can stop
		//looking through posts
		if($prev_post != NULL && $post['id'] == $prev_post['idpost']){
			break;
		// break if we have added max # of posts
		}elseif($added_posts == $NUMBER_OF_POSTS){	
			break;
		// post is still a potential 'new post'
		}else{
			echo "checking post content...<br>";
			// filter out non-status updates (pictures, changing info, friend-accepts)
			if(checkPost($post)){
				$time = str_replace("T", " ", substr($post['created_time'], 0, 19));

				// sql statement for insert
				$sql = "INSERT INTO facebook VALUES ('".$post['id']."','".$post['message'].
					"','".$time."');";

				// execute insert
				insert($sql);
				$added_posts++;
			}
		}	
	}
	clearOldPosts();
}

/**
 * retrieves the posts from a page's wall
 */
function getPosts(){
	global $PAGE_ID;

	// access token is required to read a feed
	// this was generated using getAccessToken(), and will remain
	// the same until either the client_id or the app_secret changes
	$access_token = "404036536424538|Y7z0ESUNF2tgog3P5ljFJ9ooW2U";

	// http get url, we are requesting the edge /posts of node $user_id,
	// we are requesting just the fields id, status_type, created_time, and link
	$url = "https://graph.facebook.com/".$PAGE_ID."/posts?fields=id,status_type".
		",message,created_time,link&access_token=".$access_token;

	$reply = file_get_contents($url);
	return json_decode($reply, true);
}

/**
 * checks to see if a post is a potential 'newest post'
 */
function checkPost($post){
	global $MIN_POST_LENGTH;

	// first, check status_type (what type of post)
	if($post['status_type'] != "mobile_status_update" && $post['status_type'] != "wall_post")
		return false;
	elseif(strlen($post['message']) < $MIN_POST_LENGTH)
		return false;
	else
		return true;

}

/** 
 * clears old posts from db
 * keeps NUMBER_OF_POSTS in db
 */
function clearOldPosts(){
	global $NUMBER_OF_POSTS;
	// if we have more posts than we should
	if(countrows() > $NUMBER_OF_POSTS){
		// sql query that will delete all but latest NUMBER_OF_POSTS posts
		$sql = "DELETE FROM `facebook` WHERE idpost NOT IN ( SELECT idpost FROM ".
			"(SELECT idpost FROM `facebook` ORDER BY date DESC LIMIT ".$NUMBER_OF_POSTS.") t);";
		
		insert($sql);
	}
}	
/**
 * gets access token from fb server, this function is
 * meant to be used in the admin panel section so the user
 * can eventually generate permanent access token using their
 * client_id and app_secret from facebook.
 *
 * The client_id and app_secret can be found on developers.facebook.com
 * after creating a new app.
 *
 */
function getAccessToken($client_id, $app_secret){
	$base_url = "https://graph.facebook.com/";	
	// the next 2 assignment statements are only in here for
	// testing/demonstration purposes
	$client_id = "404036536424538";
	$app_secret = "ef89c41bbf28984534579abebf1ae876";
	
	// http get request
	$url = $base_url."oauth/access_token?client_id=".$client_id.
		"&client_secret=".$app_secret."&grant_type=client_credentials";

	// execute http get request
	$reply = file_get_contents($url);
	// parse token out, and return
	return str_replace("access_token=", "", $reply);			
}

/**
 * gets a resultset of the most recent posts from db
 */
function getNewestFromDb(){
	$sql = "SELECT idpost FROM facebook ORDER BY date DESC LIMIT 1";
	return query($sql)->fetch_assoc();
}

/*
 * a debug function for printing posts received
 */
function printPosts($posts){
	foreach($posts['data'] as $post){
    		$message = $post['message'];
    		$post_time = $post['created_time'];
		$status_type = $post['status_type'];
		$id = $post['id'];
		echo $id.' '.$message .' '. $post_time.' '.$status_type."<br>";
	}
}
?>

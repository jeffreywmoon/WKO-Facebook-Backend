# WKO-Facebook-Backend
A  PHP backend for the WKO website. Still in beta. 

## Where does this script stand?
Currently, this script is fully functional. That having been said, some of the SQL calls could be cleaned up a little bit. Particularily, the call to clear all but the latest 5 posts from the db. I have also removed the access tokens from the source, just so potential malicious users don't steal my fb access token and wreak havok in my name. 

## Use
Simply call the update() function in 'fbookposts.php' to sync the latest N posts from the facebook feed with the database. To set the number of posts to maintain, change the global NUMBER\_OF\_POSTS in the file 'fbookposts.php'. The update() should be called whenever the server wants to sync with the facebook feed.

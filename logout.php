<?php
if (!isset($_GET['quit'])) {

	print "<h4>To complete your log out, please click \"OK\" then<br>
		\"Cancel\" in this <a href=\"logout.php?quit=y\">log in box</a>. Do not fill in a pass-<br>
		word. This should clear your ID from the cache<br>
		of your browser.

	        <p>Go <a href=\"index.php\">back to the site</a>.</h4>";

	} else {
	        header('WWW-Authenticate: Basic realm="This Realm"');
	        header('HTTP/1.0 401 Unauthorized');
	        // if a session was running, clear and destroy it
	        session_start();
	        session_unset();
	        session_destroy();
	        echo "<h3>Logged out!</h3><h4>Go <a href=\"index.php\">back to the site</a>.</h4>";
	}
?>

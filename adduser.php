<script language="php">

// <html>
// <title>Updating Notes...</title>
// <head><link rel="stylesheet" type="text/css" href="assetdb.css" /></head>
// <body>


	// Get passed data
	$fname = $_POST[pass_fname];
	$lname = $_POST[pass_lname];
	$dept = $_POST[pass_dept];

	// Connect to DB
	connect_to_db();

	// Get new userid
	$query = "SELECT max(userid) AS highest FROM user_tbl";
	$result = pg_query($query);
	if (!$result) {
		// If query returns an error
		print "<html>\n";
		print "<title>Adding a user...</title>\n";
		print "<head><link rel=\"stylesheet\" type=\"text/css\" href=\"assetdb.css\" /></head>\n";
		print "<body>\n";
		include("top_menu.php");
		print "Problem with query: " . $query . "<br/>\n";
		print pg_last_error();
		print "</body></html>\n";
	}

	$myrow = pg_fetch_assoc($result);
	$new_userid =  $myrow[highest] + 1;

	// Set up UPDATE query
	$query = "INSERT INTO user_tbl (userid,firstname,lastname,department,active) VALUES ('" . $new_userid . "','" . $fname . "','" . $lname . "','".$dept."','t')";

	// Run query
	$result = pg_query($query);
	if (!$result) {

		// If query returns an error
		print "<html>\n";
		print "<title>Adding a user...</title>\n";
		print "<head><link rel=\"stylesheet\" type=\"text/css\" href=\"assetdb.css\" /></head>\n";
		print "<body>\n";
		include("top_menu.php");
		print "Problem with query: " . $query . "<br/>\n";
		print pg_last_error();
		print "</body></html>\n";

	} else {

		// If query is successful, add a note

		$insert_query2 = "INSERT INTO user_notes_tbl (userid,note,datetime,signature)
					VALUES (".$new_userid.",'User file created','". date('m/d/Y h:i A')."','". $_SERVER['PHP_AUTH_USER'] ."')";

		$result2 = pg_query($insert_query2);

		if (!$result2) {

			// If query returns an error
			print "<html>\n";
			print "<title>Adding a user...</title>\n";
			print "<head><link rel=\"stylesheet\" type=\"text/css\" href=\"assetdb.css\" /></head>\n";
			print "<body>\n";
			include("top_menu.php");
			print "Problem with query: " . $insert_query2 . "<br/>\n";
			print pg_last_error();
			print "</body></html>\n";

		} else {

			// If both inserts are successful, redirect back to the list page
			header( "Location: employee_list.php" ) ;
		}

	}

</script>

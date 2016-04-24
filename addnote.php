<script language="php">

// <html>
// <title>Updating Notes...</title>
// <head><link rel="stylesheet" type="text/css" href="assetdb.css" /></head>
// <body>


	// Get passed data
	$current_id = $_POST[pass_id];
	$new_vaule = pg_escape_string($_POST[new_value]);
	$signature = pg_escape_string($_POST[signature]);
	$current_tbl =  $_POST[pass_table];
	if ($current_tbl=="user_notes_tbl") {$id_type="userid";} else {$id_type="assetid";}

	// Connect to DB
	$db = pg_connect("host=$_ENV["HOST"] port=$_ENV["PORT"] dbname=$_ENV["DBNAME"] user=$_ENV["USER"] password=$_ENV["USER_PASS"]");

	// Set up UPDATE query
	$query = "INSERT INTO ".$current_tbl." (".$id_type.",note,datetime,signature) VALUES ('" . $current_id . "','" . $new_vaule . "','" . date('m/d/Y h:i:s A') . "','$signature')";

	// Run query
	$result = pg_query($query);
	if (!$result) {

		// If query returns an error
		print "<html>\n";
		print "<title>Updating Notes...</title>\n";
		print "<head><link rel=\"stylesheet\" type=\"text/css\" href=\"assetdb.css\" /></head>\n";
		print "<body>\n";
		include("top_menu.php");
		print "Problem with query: " . $query . "<br/>\n";
		print pg_last_error();
		print "</body></html>\n";

	} else {

		// If query is successful

		// Redirect code:
		if ($current_tbl=="user_notes_tbl") {
			header( "Location: user_details.php?user=$current_id" ) ;
		} else {
			header( "Location: tag_details.php?asset=$current_id" ) ;
		}

		// Temporary success page.
//		print "<html>\n";
//		print "<title>Updating Database...</title>\n";
//		print "<head><link rel=\"stylesheet\" type=\"text/css\" href=\"assetdb.css\" /></head>\n";
//		print "<body>\n";
//		include("top_menu.php");
//		print "<h1>Update successful: " . $query . "</h1><br/>\n";
//		print "</body></html>\n";

	}

</script>

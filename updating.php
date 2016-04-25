<script language="php">

function error_check($passed_result, $passed_query) { //check queries for errors
	if (!$passed_result) {
		print "<html>\n";
		print "<title>Updating Database...</title>\n";
		print "<head><link rel=\"stylesheet\" type=\"text/css\" href=\"assetdb.css\" /></head>\n";
		print "<body>\n";
		include("top_menu.php");
		print "\n<p>\nProblem with query: " . $query . "<br/>\n";
		print pg_last_error();
		print "</body></html>\n";
		exit();
	}
	return;
}

	// Get passed data
	$current_tbl = $_POST[pass_table];
	$current_asset = $_POST[pass_assetid];
	$current_field = $_POST[pass_field];
	$current_userid = $_POST[pass_currentid];
	$new_value = $_POST[new_value];

	// Check assetid type (depends on table)
	if ($current_tbl == 'pc_detail_tbl') {
		$asset_identifier = 'pc_assetid';
	} elseif ($current_tbl == 'user_tbl') {
		$asset_identifier = 'userid';
	} else {
		$asset_identifier = 'assetid';
	}

	// Connect to DB
	connect_to_db();

	// Check for Assignment change
	if ($current_tbl == 'assignment_tbl') {

		// Prevent redundant entries
		if ( $new_value == $current_userid ) {
			header( "Location: tag_details.php?asset=$current_asset&field=assign" );
		}

		$query = "INSERT INTO $current_tbl (datetime,assetid_fkey,userid_fkey) VALUES ('".date('m/d/Y h:i:s A')."',$current_asset,'$new_value')";
		$result = pg_query($query);
		error_check($result, $query);

		// Change asset status depending on movement
		if ( $current_userid == "1" ) {
			$query = "UPDATE asset_tbl SET status = 'Active' WHERE assetid ='$current_asset'";
			$result = pg_query($query);
			error_check($result, $query);
		}
		if ( $new_value == "1" ) {
			$query = "UPDATE asset_tbl SET status = 'Unknown' WHERE assetid ='$current_asset'";
			$result = pg_query($query);
			error_check($result, $query);
		}
		// Default after assignment change, return to original page
		header( "Location: tag_details.php?asset=$current_asset&field=status" );

		// Script ends here for Assignment changes.

	} else {

		// This section is for non-assignment database changes, such as asset_tbl, pc_detail_tbl, console_detail_tbl, user_tbl.

		$query = "SELECT * FROM $current_tbl WHERE $asset_identifier = $current_asset";
		$result = pg_query($query);

		// Create a new subtable entry if needed
		if (pg_num_rows($result) == 0) {
			$query = "INSERT INTO $current_tbl ($asset_identifier) VALUES ($current_asset)";
			$result = pg_query($query);
			error_check($result, $query);
		}

		// Check for a NULL value, and use the appropriate UPDATE query
		if (!$new_value) {$query = "UPDATE $current_tbl SET $current_field=NULL WHERE $asset_identifier = $current_asset";}
		            else {$query = "UPDATE $current_tbl SET $current_field='$new_value' WHERE $asset_identifier = $current_asset";}

		// Run query
		$result = pg_query($query);
		error_check($result, $query);

		// Check for linked fields
		if ( ($current_tbl=="asset_tbl") && ($current_field=="active") && ($new_value=="f") ) {
			// Set status to Decomissioned
			$query = "UPDATE asset_tbl SET status='Decomissioned' WHERE assetid = $current_asset";
			$result = pg_query($query);
			error_check($result, $query);

			// Assign to user "None"
			$query = "INSERT INTO assignment_tbl (datetime,assetid_fkey,userid_fkey) VALUES ('".date('m/d/Y h:i:s A')."',$current_asset,'199')";
			$result = pg_query($query);
			error_check($result, $query);
		}

		// If the script has not exited by this point, redirect to detail page
		if ($current_tbl == "user_tbl") {
			header( "Location: user_details.php?user=$current_asset&field=note" );
		} else {
			header( "Location: tag_details.php?asset=$current_asset&field=note" );
		}
	}
</script>

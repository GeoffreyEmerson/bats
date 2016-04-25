<?php

	include("functions.php");
	include("jscript.html");
	html_header('HI Asset DB - Asset Detail','new_value');
	include("top_menu.php");

	// Connect to database
	// and initialize passed parameters

	$current_tbl = "asset_tbl"; // I might use this variable later to make the SELECT statements more versatile.
	if ( isset($_GET['field']) ) {  // This is used if a request to edit a field has been made.
		$edit_field = $_GET['field'];
	} else {
		$edit_field="";
	}


	connect_to_db();

	// Set the assetid variable
	if ( isset($_GET['asset']) ) { 		// This is the normal route to get the assetid
		$assetid = $_GET['asset'];
		$query = "SELECT * FROM $current_tbl WHERE assetid='$assetid'";
	} elseif ( isset($_POST['tag_number']) ) {	// This is for searches by tag number
		$tag_number = $_POST['tag_number'];
		$query = "SELECT * FROM ".$current_tbl." WHERE thq='".$tag_number."'";
	} else {
		// If there is no asset or tag to search by...
		print "<h1>No Asset Variable Passed!</h1>";
		exit();
	}

	$asset_result = pg_query($query);
        if (!$asset_result) {
       	    echo "Problem with query: " . $query . "<br/>";
            echo pg_last_error();
	    exit();
	}

	if (pg_num_rows($asset_result)==0) {

		print "<h2>Number of rows returned: ".pg_num_rows($asset_result)."</h2>";
		print "<h1>Tag Not Found!</h1>\n\n";
		print "<h2>Use this form to create an entry for: " . $tag_number . "</a></h2>\n";

		include("new_asset_module1.php");

		exit();

	}

	$asset_row = pg_fetch_assoc($asset_result); // Converts the results into an easily parsable array.


// Create main asset data list

	print "<table class=outerframe>"; // Outer table
	print "<tr><td class=outerframe>\n";
	print "<table class=noframe >\n"; // Inner table 1

	if ($asset_row['active']=="f") { print "<tr><td colspan=2 style=\"color:red;\"><b>DECOMISSIONED</b></td></tr>\n"; }

	print "<tr>";
	print "<td NOWRAP style=\"width:100;\">Asset ID</td>";
	print "<td NOWRAP style=\"width:250;\">". $asset_row['assetid'] ."</td>";
	print "</tr>\n";

	detail_row($current_tbl, $asset_row['assetid'], $asset_row['thq'], 'thq', $edit_field);
	detail_row($current_tbl, $asset_row['assetid'], $asset_row['type'], 'type', $edit_field);
	detail_row($current_tbl, $asset_row['assetid'], $asset_row['description'], 'description', $edit_field);
	detail_row($current_tbl, $asset_row['assetid'], $asset_row['serial'], 'serial', $edit_field, $asset_row);
	detail_row($current_tbl, $asset_row['assetid'], $asset_row['purchasedate'], 'purchasedate', $edit_field);
	detail_row($current_tbl, $asset_row['assetid'], $asset_row['status'], 'status', $edit_field);

// Fetch PC detail

	if ($asset_row['type']=="PC") {
		$sub_tbl = "pc_detail_tbl";

		$query = "SELECT * FROM $sub_tbl WHERE pc_assetid=".$asset_row['assetid'];

		$pc_result = pg_query($query);
	        if (!$pc_result) {
	            echo "Problem with query: " . $query . "<br/>";
	            echo pg_last_error();
	            exit();
		}
		$pc_row = pg_fetch_assoc($pc_result);

		// Build PC Data list

		detail_row($sub_tbl, $asset_row['assetid'], $pc_row['computernumber'], 'computernumber', $edit_field);
		detail_row($sub_tbl, $asset_row['assetid'], $pc_row['videocard'], 'videocard', $edit_field);
		detail_row($sub_tbl, $asset_row['assetid'], $pc_row['videobus'], 'videobus', $edit_field);
		detail_row($sub_tbl, $asset_row['assetid'], $pc_row['cpu'], 'cpu', $edit_field);
		detail_row($sub_tbl, $asset_row['assetid'], $pc_row['harddrive'], 'harddrive', $edit_field);
		detail_row($sub_tbl, $asset_row['assetid'], $pc_row['memory'], 'memory', $edit_field);
		detail_row($sub_tbl, $asset_row['assetid'], $pc_row['batch'], 'batch', $edit_field);
		detail_row($sub_tbl, $asset_row['assetid'], $pc_row['expresscode'], 'expresscode', $edit_field);
		detail_row($sub_tbl, $asset_row['assetid'], $pc_row['warranty'], 'warranty', $edit_field);
		detail_row($sub_tbl, $asset_row['assetid'], $pc_row['mayadongle'], 'mayadongle', $edit_field);
	}
	detail_row($current_tbl, $asset_row['assetid'], $asset_row['active'], 'active', $edit_field);
	print "</table>\n";
	print "</td><td class=outerframe>\n";


// Inner table 2 - User Assignments

	// Look up current and past user assignments
	$assignment_query = "SELECT * FROM asset_tbl
				LEFT JOIN assignment_tbl ON (asset_tbl.assetid=assignment_tbl.assetid_fkey)
				LEFT JOIN user_tbl ON (assignment_tbl.userid_fkey=user_tbl.userid)
					WHERE (asset_tbl.assetid='".$asset_row['assetid']."')
					ORDER BY assignment_tbl.datetime DESC";
	$assignment_result = pg_query($assignment_query);
	if (!$assignment_result) {
		echo "Problem with query: " . $assignment_query . "<br/>\n";
		echo pg_last_error();
		exit();
	}

	// Get the first row for use with the New User Assignment form
	$assignment_row = pg_fetch_assoc($assignment_result);

	print "<table style=\"width:400;\">\n";

	print "<tr><td colspan=2> User Assignment";
	if ($edit_field !='assign') {
		print" (<a href=\"tag_details.php?asset=".$asset_row['assetid']."&field=assign\">change</a>)";
	}
	print "</td></tr>\n";

	// Display update field when requested
	if ($edit_field=="assign") {

		print "<tr><td colspan=2>\n";
		print "<form name=\"form\" method=\"post\" action=\"updating.php\">\n";
		print "<INPUT TYPE=hidden NAME=pass_table VALUE=\"assignment_tbl\">\n";
		print "<INPUT TYPE=hidden NAME=pass_currentid VALUE=\"".$assignment_row[userid]."\">\n";
		print "<INPUT TYPE=hidden NAME=pass_assetid VALUE=".$asset_row[assetid].">\n";
		print "<SELECT NAME=\"new_value\">\n";

		// Query all names from active users to create a list of names to chose from

		$query = "SELECT * FROM user_tbl WHERE active='t' ORDER BY firstname";
		$user_result = pg_query($query);

		if (!$user_result) {
			echo "</form></td></tr></table>Problem with query: " . $query . "<br/>\n";
			echo pg_last_error();
			echo "</table></body>";
			exit();
		}

		while( $option_row = pg_fetch_assoc($user_result) ) {

			print "<OPTION VALUE=\"".$option_row['userid']."\"";

			if ($option_row['userid'] == $assignment_row['userid']) {
				print " selected=\"selected\"";  // Makes the current assignee selected
			}

			print ">".$option_row['firstname']." ".$option_row['lastname']."</OPTION>\n";

		}
		print "</SELECT> <INPUT TYPE=submit VALUE=\"Update\">";
		print "</td></tr>\n";
	}

	// Get first row for current user assignment
	if ($assignment_row['userid_fkey'] !="" ) {
		print "<tr><td style=\"width: 250;\"><a href=user_details.php?user=" . $assignment_row['userid'] . ">";
		print $assignment_row['firstname'] . " " . $assignment_row['lastname'];
		print "</a></td><td>" . date( 'n/d/Y g:i A' , strtotime($assignment_row['datetime']) ). "</td></tr>\n";
		print "</table>\n";

		// Get second row for history of user assignments
		$assignment_row = pg_fetch_assoc($assignment_result);
		if ($assignment_row['userid_fkey'] !="" ) {
			print "<p>\n";
			print "<table style=\"width:400;\">\n";
			print "<tr><td colspan=2>Assignment History</td></tr>\n";
			print "<tr><td style=\"width:250;\"><a href=user_details.php?user=" . $assignment_row['userid'] . ">";
			print $assignment_row['firstname'] . " " . $assignment_row['lastname'];
			print "</a></td><td>" . date( 'n/d/Y g:i A' , strtotime($assignment_row['datetime']) ) . "</td></tr>\n";
			// Loop until all previous assignments have been listed
			while($assignment_row = pg_fetch_assoc($assignment_result)) {
				print "<tr><td>" . $assignment_row['firstname'] . " " . $assignment_row['lastname'] . "</td>";
				print "<td>" . date( 'n/d/Y g:i A' , strtotime($assignment_row['datetime']) ) . "</td></tr>\n";
			}
		}
	} else {
		print "<tr><td colspan=\"2\"> No assignment </td></tr>\n";
	}

	print "</table>\n";
	print "</td></tr>\n";
	print "</table>\n"; // End of outer table


// Create Notes table below main table

	print "<table width=800>\n";
	print "<tr><td colspan=2>Date</td><td>Note</td></tr>\n";
	print "<tr>\n";
	print "<td colspan=3>\n";

	if ($edit_field == 'note'){
		print "<p>&nbsp\n";
		print "<FORM method=\"post\" name= \"form\" action=\"addnote.php\">\n";
		print "<INPUT TYPE=hidden NAME=pass_id VALUE=".$asset_row['assetid'].">\n";
		print "<INPUT TYPE=hidden NAME=signature VALUE='". $_SERVER['PHP_AUTH_USER'] ."'>\n";
		print "<INPUT TYPE=hidden NAME=pass_table VALUE=\"asset_notes_tbl\">\n";
		print "<textarea cols=\"50\" rows=\"4\" name=\"new_value\"></textarea>\n";
		print "<br>\n";

		print "<table class=outerframe align=center><tr>\n";
		print "<td><INPUT TYPE=submit NAME=submit VALUE=\"Add Note\"></td>\n";
		print "</FORM>\n";

		print "\n";
		print "<FORM method=\"post\" name=\"fake_form\" action=\"tag_details.php?asset=".$asset_row['assetid']."\">\n";
		print "<td><INPUT TYPE=submit NAME=submit value=\"Cancel\"></td>\n";
		print "</FORM>\n";
		print "</tr></table>\n";
	} else {
		print "<a href=\"tag_details?asset=".$asset_row['assetid']."&field=note\">Add note</a></td>\n";
	}

	print "</td>\n";
	print "</tr>\n\n";

	// Query for notes

	$query = "SELECT * FROM asset_notes_tbl WHERE assetid='".$asset_row['assetid']."' ORDER BY datetime DESC";

	$notes_result = pg_query($query);
	if (!$notes_result) {
            echo "Problem with query: " . $query . "<br/>\n";
            echo pg_last_error();
            exit();
	}

	// List notes, if any
	if ( pg_num_rows($notes_result) ) {
		while($note_row = pg_fetch_assoc($notes_result)) {
			print "<tr><td style=\"border-right-style: hidden;\">";
			print date( 'n/d/Y g:i A' , strtotime($note_row['datetime']) );
			print "</td>\n<td style=\"border-left-style: hidden;\">";
			if ( $note_row['signature'] ) {
				print " <img src=note.gif title=\"Note added by: $note_row[signature]\">";
			} else {
				print "&nbsp;";
			}
			print "</td><td class=\"notetext\">";
			print $note_row['note'];
			print "</td></tr>\n";
		}
	} else {
		print "<tr><td colspan=3> No notes </td></tr>\n";
	}

	print "</table>\n\n</body>\n</html>";

?>

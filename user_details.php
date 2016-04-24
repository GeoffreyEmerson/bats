<?php

	include("functions.php");
	include("jscript.html");
	html_header('HI Asset DB - User Detail','new_value');
	include("top_menu.php");

	// Connect to database
	// and initialize passed parameters

	$db = pg_connect("host=$_ENV["HOST"] port=$_ENV["PORT"] dbname=$_ENV["DBNAME"] user=$_ENV["USER"] password=$_ENV["USER_PASS"]");
	$current_tbl = "user_tbl";

	if ( ($userid = $_GET["user"]) || ($userid == 0) ) {
		$query = "SELECT * FROM $current_tbl WHERE userid='$userid'";
		$result = pg_query($query);
	        if (!$result) {
        	    echo "Problem with query: " . $query . "<br/>";
	            echo pg_last_error();
		    exit();
		}

		$myrow = pg_fetch_assoc($result); // Converts the results into an easily parsable array.

	} else { print "Error! No user selected.";}

	$edit_field = $_GET["field"];	// This is used if a request to edit a field has been made.

// Create main asset data list

	print "<table class=outerframe>\n"; // Outer table
	print "<tr><td class=outerframe>";
	print "<table>\n"; // Inner table 1
	print "<tr>";
	print "<td NOWRAP style=\"width:100;\">User ID</td>";
	print "<td NOWRAP style=\"width:250;\">". $myrow[userid] ."</td>";
	print "</tr>\n";

	detail_row($current_tbl, $myrow[userid], $myrow[firstname], 'firstname', $edit_field);
	detail_row($current_tbl, $myrow[userid], $myrow[lastname], 'lastname', $edit_field);
	detail_row($current_tbl, $myrow[userid], $myrow[department], 'department', $edit_field);
	detail_row($current_tbl, $myrow[userid], $myrow[extension], 'extension', $edit_field);
	detail_row($current_tbl, $myrow[userid], $myrow[user_status], 'user_status', $edit_field);
	detail_row($current_tbl, $myrow[userid], $myrow[active], 'active', $edit_field);

	print "</table>\n";
	print "</td><td class=outerframe>\n";


// Inner table 2 - User Assignments

	print "<table style=\"width:400;\">\n";

	$user_query = "SELECT * FROM asset_tbl a
			LEFT OUTER JOIN pc_detail_tbl ON (pc_assetid=assetid)
			LEFT JOIN (
				SELECT DISTINCT ON (assetid_fkey) * FROM assignment_tbl
				LEFT JOIN user_tbl ON (userid_fkey=userid)
				ORDER BY assetid_fkey, datetime desc
			) AS usernames
			ON assetid=assetid_fkey
			WHERE userid = '$userid'
			ORDER BY type ASC, description ASC";

	$result = pg_query($user_query);
	if (!$result) {
		print "Problem with query: " . $user_query . "\n<br>\n";
		print pg_last_error();
		print "\n\n<br>Yeah, that was the error...";
		exit();
	}

	print "<tr><td colspan=3> Assigned Equipment </td></tr>";

	if ( pg_num_rows($result)>0 ) {
		while ( $assign_row = pg_fetch_assoc($result) ) {
			print "<tr><td><a href=tag_details.php?asset=" . $assign_row['assetid'] . ">";
			if($assign_row['thq']) { print $assign_row['thq'];} else { print"?";}
			print "</a></td>";
			print "<td style=\"width: 250;\">";
			if($assign_row['type']=='PC') {
				print "[". $assign_row['computernumber'] ."] - ";
			}
			print $assign_row['description'] . "</td>";
			print "<td>" . date( 'n/d/Y g:i A' , strtotime($assign_row['datetime']) ). "</td></tr>\n";

		}
	} else {
		print "<tr><td colspan=\"2\"> No assignments </td></tr>\n";
	}

	print "</table>\n";
	print "</td></tr>\n";
	print "</table>\n"; // End of outer table


// Create Notes table below main table

	print "<table width=800>\n";
	print "<tr><td colspan=2>Date</td><td>Note</td></tr>\n";
	print "<tr>\n";
	print "<td colspan=3>\n";

	if ($edit_field == 'note') {
		print "<FORM name=\"form\" method=\"post\" action=\"addnote.php\">\n";
		print "<INPUT TYPE=hidden NAME=pass_id VALUE=$userid>\n";
		print "<INPUT TYPE=hidden NAME=signature VALUE='". $_SERVER['PHP_AUTH_USER'] ."'>\n";
		print "<INPUT TYPE=hidden NAME=pass_table VALUE=\"user_notes_tbl\">\n";
		print "<textarea cols=\"50\" rows=\"4\" name=\"new_value\"></textarea>\n";
		print "<INPUT TYPE=submit VALUE=\"Add Note\">\n</FORM>\n";
	} else {
		print "<a href=\"user_details?user=".$userid."&field=note\">Add note</a></td>\n";
	}

	print "</td>\n";
	print "</tr>\n\n";

	// Query for notes

	$query = "SELECT * FROM user_notes_tbl WHERE userid='$userid' ORDER BY datetime DESC";

	$notes_result = pg_query($query);
	if (!$result) {
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
			if ($note_row[signature]) {
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

	print "</table>\n\n";
	print "</body>\n</html>";

?>

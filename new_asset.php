<?php

	include("functions.php");

	connect_to_db();

	// Defining variables that are only used for passed values
	$duplicate = FALSE;
	$post_thq = "";
	$post_type = "";
	$post_description = "";
	$post_custom_desc = "";
	$post_serial = "";
	$post_date = "";


	// Recursive check for duplicate THQ tag
	// This section gets ignored if this is the initial page load.

	if ( isset($_POST['new_thq']) ) {
		$post_thq = $_POST['new_thq'];
		if ( isset($_POST['new_type']) ) { $post_type = $_POST['new_type']; }
		if ( isset($_POST['new_description']) ) { $post_description = $_POST['new_description']; }
		if ( isset($_POST['custom_desc']) ) { $post_custom_desc = $_POST['custom_desc']; }
		if ( isset($_POST['new_serial']) ) { $post_serial = $_POST['new_serial']; }
		if ( isset($_POST['new_date']) ) { $post_date = $_POST['new_date']; }

		// Prioritize a custom description over a selected description
		if ($post_custom_desc=="") {
			$new_desc = $post_description;
		} else {
			$new_desc = $post_custom_desc;
		}

		// Check for duplicate thq#
		$query = "SELECT * FROM asset_tbl WHERE thq='".$post_thq."'";
		$check_result = pg_query($query);
	        if (!$check_result) {
	       	    echo "Problem with query: " . $query . "<br/>";
	            echo pg_last_error();
		    exit();
		}

		// If there are no duplicates, submit the info to a new row, load the detail page for the new tag

		if (pg_num_rows($check_result)==0) {

			// Find next assetid
			$query = "SELECT assetid FROM asset_tbl ORDER BY assetid DESC LIMIT 1";
			$result = pg_query($query);
			if (!$result) {
				print "Problem with query: " . $query . "<br/>\n";
				print pg_last_error();
				exit();
			}
			$myrow = pg_fetch_assoc($result);
			$highest = $myrow['assetid'];
			$new_asset = $highest+1;

			// Create INSERT statement
			$insert_query = "INSERT INTO asset_tbl (assetid,thq,type,description,serial";
			if ($post_date) { $insert_query = $insert_query . ",purchasedate"; }
			$insert_query = $insert_query.	",active) VALUES ('$new_asset','$post_thq','$post_type','$new_desc','$post_serial'";
			if ($post_date) { $insert_query = $insert_query . ",'$post_date'"; }
			$insert_query = $insert_query . ",'t')";

			// Execute INSERT
			$insert_result = pg_query($insert_query);
			error_check($insert_result,$insert_query);

			// Add a note to the new asset for the creation date.
			$insert_query2 = "INSERT INTO asset_notes_tbl (assetid,note,datetime,signature)
					VALUES (".$new_asset.",'Asset created','". date('m/d/Y h:i:s A')."','". $_SERVER['PHP_AUTH_USER'] ."')";

			$result = pg_query($insert_query2);
			error_check($result,$insert_query2);

			header( "Location: tag_details.php?asset=$new_asset" ) ; // Effectively ends the script by redirecting the browser



		} else {
			$duplicate = TRUE;
		}

	}

// This is where the page starts upon first load.



	include("jscript.html");
	html_header('HI Asset DB - New Asset','new_thq');
	include("top_menu.php");

	include("new_asset_module1.php");


/*	if ( isset($_GET['asset']) ) { 		// This is if another page passes a suggested THQ tag number
		$assetid = $_GET['asset'];
	}

	// Create form

	print "<form name=\"form\" method = \"post\" action=\"new_asset.php\">";

	print "<table class=noframe>\n";

	if ($duplicate) { print "<tr><td colspan=2 style=\"color:red;\"><b>DUPLICATE THQ #</b></td></tr>\n"; }

	print "<tr>";
	print "<td NOWRAP style=\"width:100;\">THQ #</td>";
	print "<td NOWRAP style=\"width:250;\"><INPUT TYPE=text NAME=\"new_thq\" VALUE=\"".$post_thq.$assetid."\"></td>"; // Only one of the two variables will be valid at a time, the other will print nothing.
	print "</tr>\n";

	print "<tr><td>Type</td><td>";
	print "<SELECT NAME=\"new_type\">\n";
	$query = "SELECT type FROM asset_tbl GROUP BY type ORDER BY type";
	$select_result = pg_query($query);
	while( $option_row = pg_fetch_assoc($select_result) ) {
		print "<OPTION VALUE=\"".htmlentities($option_row['type'])."\"";
		if ($option_row['type'] == $post_type) {print " selected=\"selected\"";}
		print ">".htmlentities($option_row['type'])."</OPTION>\n";
	}
	print "</SELECT>";
	print "</td></tr>\n";

	print "<tr><td>Description</td><td>";
	print "<SELECT NAME=\"new_description\">\n";
	print "<OPTION VALUE=\"\"";
	if (!$post_description) {print " selected=\"selected\"";}
	print ">Select Description from this list</OPTION>\n";
	$query = "SELECT description FROM asset_tbl GROUP BY description ORDER BY description";
	$select_result = pg_query($query);
	while( $option_row = pg_fetch_assoc($select_result) ) {
		print "<OPTION VALUE=\"".htmlentities($option_row['description'])."\"";
		if ( ($post_description) && ($option_row['description'] == $post_description) ) {print " selected=\"selected\"";}
		print ">".htmlentities($option_row['description'])."</OPTION>\n";
	}
	print "</SELECT>";
	print "<br>Or Type Description Here<br>\n";
	print "<INPUT TYPE=text size=\"35\" NAME=\"custom_desc\" VALUE=\"".$post_custom_desc."\"> (needs testing)";
	print "</td></tr>\n";

	print "<tr>";
	print "<td NOWRAP style=\"width:100;\">Serial</td>";
	print "<td NOWRAP style=\"width:250;\"><INPUT TYPE=text NAME=\"new_serial\" VALUE=\"".$post_serial."\"></td>";
	print "</tr>\n";

	print "<tr>";
	print "<td NOWRAP style=\"width:100;\">Purchase Date</td>";
	print "<td NOWRAP style=\"width:250;\"><INPUT TYPE=text NAME=\"new_date\" VALUE=\"".$post_date."\"></td>";
	print "</tr>\n";

	print "</table>\n";
	print "<p><INPUT TYPE=submit VALUE=\"Submit\">";
	print "</FORM>\n";

	print "</body>\n</html>\n";
 */

?>

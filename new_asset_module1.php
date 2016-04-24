<?php

// This is a PHP module.
// It should only be loaded within other PHP segments.


	if ( isset($tag_number) ) {
		$assetid = $tag_number;
	}

	if ( isset($_GET['asset']) ) { 		// This is if another page passes a suggested THQ tag number
		$assetid = $_GET['asset'];
	}

/*	include("jscript.html");
	html_header('HI Asset DB - New Asset','new_thq');
	include("top_menu.php");
 */
	// Create form

	print "<form name=\"form\" method = \"post\" action=\"new_asset.php\">";

	print "<table class=noframe>\n";

	if ($duplicate) { print "<tr><td colspan=2 style=\"color:red;\"><b>DUPLICATE THQ #</b></td></tr>\n"; }

	print "<tr>";
	print "<td NOWRAP style=\"width:100;\">THQ #</td>";
	print "<td NOWRAP style=\"width:250;\"><INPUT TYPE=text NAME=\"new_thq\" VALUE=\"".$assetid."\"></td>";
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
?>

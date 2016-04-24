<?php
include("functions.php");
html_header('HI Asset DB - Main Menu','');
?>

<h1>Main Menu</h1>

<TABLE style="width:500px;"><tr><td style="padding:10px; width:250px;">

<a href=asset_list.php>Full Asset List</a><br>
<a href=pc_list.php>Computers</a><br>
<a href=asset_list.php?search=type&key=console>Consoles</a><br>
<a href=asset_list.php?search=type&key=monitor>Monitors</a>
<p>
<a href=new_asset.php>Add Asset to Database</a>

</td><td style="padding:10px; vertical-align: text-top;">

<a href=employee_list.php>User List</a>
<p>
<a href=employee_list.php?add='t'>Add a user</a>

</td></tr>
<tr><td colspan=2  style="padding:10px;vertical-align: text-center;" >

<FORM method="post" action="tag_details.php">Jump to THQ#: <INPUT type="text" name="tag_number"> <INPUT type="submit" value="Go"></FORM>
</td>
</tr></table>
<p>&nbsp;<p>
<h2>Other links</h2>
<p><a href=multi_decom.php>Decomission Assets</a>
<p><a href=phpinfo.php>PHP Info</a>
<p><a href=test/asset_list.php>Beta Tracker</a>
<p>

<p>

</body>
</html>

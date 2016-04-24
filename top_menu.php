<table class="header">
<tr>
<td class="header"><a href=index.php>Main Menu</a></td>
<td class="header"><a href="" onClick="return clickreturnvalue()" onMouseover="dropdownmenu(this, event, menu1, '150px')" onMouseout="delayhidemenu()">Assets</a></td>
<td class="header"><a href="" onClick="return clickreturnvalue()" onMouseover="dropdownmenu(this, event, menu3, '150px')" onMouseout="delayhidemenu()">Users</a></td>
<td class="header"><a href="" onClick="return clickreturnvalue()" onMouseover="dropdownmenu(this, event, menu2, '150px')" onMouseout="delayhidemenu()">Views</a></td>
<td class="tag"><FORM method="post" action="tag_details.php">Jump to THQ#: <INPUT type="text" name="tag_number"> <INPUT type="submit" value="Go"></FORM></td>
<td class="tag">Logged in: <?php print "{$_SERVER['PHP_AUTH_USER']}\n"; ?></td>
<td class="tag"><a href="logout.php">[Log out]</a></td>
</tr>
</table>

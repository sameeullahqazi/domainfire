<style>
	#tbl_rows tr td
	{
		border-left: 1px solid #cccccc;
		border-bottom: 1px solid #cccccc;
	}

</style>

<div>
	<form method="post" tabstop=true>
		Code (for executing update statements):<input type='password' tabindex='100' name='code' value='<?php print $code;?>' /><br />
		SQL:<textarea tabindex='102' name="sql" id="sql" style="width:500px;height:200px;font-size:14px;"><?php print $sql;?></textarea>
		<input tabindex='103' type="submit" value="Run" />
		
			<?php 
			if(!empty($_POST))
			{
				print $msg;
				print "<table id='tbl_rows' style='font-size: 16px;border-spacing:5px;border:1px solid gray;'>";
				if(empty($rows)) 
				{
					print "<tr>No rows found!</tr>";	
				} else {
					print "<tr>";
					foreach($rows[0] as $key => $value)
						print "<th>$key</th>";
					print "</tr>";
				
					foreach($rows as $i => $row) {
						print "<tr>";
						foreach($row as $key => $value)
							print "<td><xmp>$value</xmp></td>";
						print "</tr>";
					} 
				}
				print "</table>";
			}
			?>
	</form>
</div>

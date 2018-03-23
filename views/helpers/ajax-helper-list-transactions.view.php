<div>
	<form method='post' id='frm_search' name='frm_search'>
		<input type='button' id='btn_first_page' name='btn_first_page' value="<<" title="Show First Page" data-ajax-helper='ajax-helper-list-transactions' data-ajax-refresh-content='div-list-transactions' data-ajax-form='frm_search' data-btn_first_page=1 />
		<input type='button' id='btn_previous_page' name='btn_previous_page' value="<" title="Show Previous Page"  />
		<input type='text' name='txt_page_number' value="<?php print $page_number;?>" title="Set Page Number" style="width:30px;" /> of <?php print $num_pages;?> Pages.
		<input type='button' id='btn_next_page' name='btn_next_page' value=">" title="Show Next Page"  />
		<input type='button' id='btn_last_page' name='btn_last_page' value=">>" title="Show Last Page"  />
		<input type='text' name='txt_limit' value="<?php print $limit;?>" title="Number of Records per Page" style="width:30px;" />
		<input type='hidden' name='hdn_num_rows' value="<?php print $num_rows;?>" />
		<input type='hidden' name='hdn_num_pages' value="<?php print $num_pages;?>" />
		<?php print "Showing " . ($offset + 1). " to " . ($offset + $limit < $num_rows ? $offset + $limit : $num_rows) . " out of $num_rows records";?>
	</form>
	<table>
		<tr>
			<th>Domain</th>
			<th>Charge ID</th>
		</tr>
		<?php 
			if($num_rows > 0)
			{
				foreach($rows as $i => $row)
				{
					print "<tr><td>" . $row['domain_name']. "</td><td>" . $row['stripe_charge_id']. "</td></tr>";
				}
			}
			else
			{
				print "<tr><td colspan=2>No transactions found.</td></tr>";
			}
		?>
	</table>
</div>
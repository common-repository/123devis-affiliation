<div class="wrap">
<h2><?php _e( 'Plugin History', 'sm_translate' );?></h2>
<p>
	<?php 
		_e( 'Data displayed per timezone', 'sm_translate');
		print " : " . $tzs . ".<br>";
		_e( 'Manage your timezone setting in', 'sm_translate');
		print " <a href=\"options-general.php\">";
		_e( 'Settings > General', 'sm_translate');
		print "</a>";
	?>
</p>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="history">
	<thead>
		<tr>
			<th>Date</th>
			<th>Type</th>
			<th>Path</th>
			<th>Message</th>
			<th>User</th>
		</tr>
	</thead>
	<tbody>
	
	</tbody>
	<tfoot>
	
	</tfoot>
</table>


<script type="text/javascript">
   jQuery(document).ready(function($) {
      $('#history').dataTable( {
         "bProcessing": true,
         "bPaginate": true,
         "bServerSide": true,
         "sAjaxSource": "admin-ajax.php?action=sm_ajax_history_data",
         "aaSorting": []
         <?php if ($sm_locale){?>
         ,"oLanguage": {
            "sUrl": "<?php print plugins_url('ui/js/datatables.1.9.4.' . $sm_locale . '.txt', dirname(__FILE__));?>"
         }
         <?php } ?>
      } );
   });
</script>
</div>
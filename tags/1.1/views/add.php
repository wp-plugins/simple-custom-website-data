<a class="actionbtn" href="<?php echo site_url();?>/wp-admin/admin.php?page=cwd-management">Cancel</a>
<h3 class="title">Add New Record</h3>
<form name="input" action="<?php echo site_url();?>/wp-admin/admin.php?page=cwd-management&view=proc" method="post">
<input type="text" name="ref" placeholder="Reference" required><br>
<textarea type="text" name="data" placeholder="Data" required></textarea><br>
<?php wp_nonce_field('add_action', 'smp_add_action') ?>
<input type="hidden" name="cwdaction" value="add">
<input class="button button-primary" type="submit" value="Submit">
</form>

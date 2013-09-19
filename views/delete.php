<?php
$current = $this->getById($_GET['id']);
?>

Are you sure you wish to delete the entry "<?php echo $current->ref;?>"?<br><br>
<a class="actionbtn" href="<?php echo site_url();?>/wp-admin/admin.php?page=cwd-management&view=proc&del=y&id=<?php echo $_GET['id'];?>">Yes</a> <a class="actionbtn" href="<?php echo site_url();?>/wp-admin/admin.php?page=cwd-management">No</a>
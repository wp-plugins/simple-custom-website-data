<?php
global $cwd;
$cwd->processUserRequest();
$location =  site_url() . '/wp-admin/admin.php?page=cwd-management';
?>
<meta http-equiv="refresh" content="0; url=<?php echo $location?>">
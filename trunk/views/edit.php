<a class="actionbtn" href="<?php echo site_url();?>/wp-admin/admin.php?page=cwd-management">Cancel</a>
<?php
$current = $this->getById($_GET['id']);
// var_dump($current);
// die();

$data = stripslashes($current->data);
if ($this->isJson($data) && !$this->isMulti($data, true))
{
    $processed = '';
    $data_arr = json_decode($data);
    $loop = 1;
    $arr_count = count($data_arr);
    foreach ($data_arr as $key => $value) {
        $processed .= $key . '=' . $value;
        if(($loop) == $total) $processed .= PHP_EOL;
        $loop++;
    }
    $data = $processed;
}

?>
<h3>Editing record "<?php echo $current->ref ?>"</h3>
<form name="input" action="<?php echo site_url();?>/wp-admin/admin.php?page=cwd-management&view=proc" method="post">
<textarea type="text" name="data" placeholder="Data" required><?php echo $data ?></textarea><br>
<input type="hidden" name="cwdaction" value="edit">
<input type="hidden" name="edit" value="y">
<input type="hidden" name="id" value="<?php echo $_GET['id']?>">
<input type="hidden" name="_edit_rec" value="<?php echo wp_create_nonce( 'edit_rec-' .  $_GET['id']);?>">
<input class="button button-primary" type="submit" value="Edit">
</form>
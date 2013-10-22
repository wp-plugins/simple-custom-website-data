<?php global $cwd?>
<h3 class="page-title">Dashboard</h3>
<a class="actionbtn" href="<?php echo site_url();?>/wp-admin/admin.php?page=cwd-management&view=add">Add New</a> <a class="actionbtn" href="<?php echo site_url();?>/wp-admin/admin.php?page=cwd-management&view=user">User Guide</a>
    <?php if(isset($message)):?>
    <div id="message" class="updated below-h2">
        <p><?php echo $message?></p>
    </div>
    <?php endif;?>

    <h3>Available Records</h3>

    <?php
        //create array
        $cwd_records = array();
        foreach ($cwd->getAll() as $row) {
            $cwd_records[$row->ref] = array(
                                    'id' => $row->id,
                                    'ref' => $row->ref,
                                    'data' => $cwd->processData($row->data),
                                        );
        }
?>
<?php if(!empty($cwd_records)):?>
    <table id="box-table">
    <tr>
        <th>Reference</th>
        <th>Shortcode</th>
        <th>Output</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($cwd_records as $record) :?>
            <tr>
                <td><?php echo $record['ref'];?></td>

                    <?php  if(!is_array($record['data'])):?>
                        <td>
                            <?php echo '<span class="select">[cwd ref="' . $record['ref'] . '"]</span>';?>
                        </td>
                        <td>
                            <?php echo $record['data']; ?>
                        </td>
                    <?php else:?>
                    <td>
                        <span class="arraydata">The data stored is an array.<br>
                        Refer to the <a href="<?php echo site_url();?>/wp-admin/admin.php?page=cwd-management&view=user">user guide</a> for help.</span>
                    </td>
                    <td>
                        <pre>
                        <?php print_r($record['data']); ?>
                        </pre>
                    </td>
                    <?php endif;?>

                <td><a href="<?php echo site_url();?>/wp-admin/admin.php?page=cwd-management&view=edit&id=<?php echo $record['id'];?>">edit</a> <a href="<?php echo site_url();?>/wp-admin/admin.php?page=cwd-management&view=delete&id=<?php echo $record['id'];?>">delete</a> </td>
            </tr>
    <?php endforeach;?>
<?php else:?>
    You don't have any records yet! Take a peek at the <a href="<?php echo site_url();?>/wp-admin/admin.php?page=cwd-management&view=user">user guide</a> to get started
<?php endif;?>

    </table>
</div>
<?php
/*
Plugin Name: Custom Website Data
Plugin URI: http://dev.dannyweeks.com/cwd/index.php
Version: 1.4
Author: Danny Weeks
Author URI: http://dannyweeks.com/
Description: Allows user to add custom data to be used as either returned values or as shortcodes
*/

class CustomWebsiteData
{
    // property declaration
    private $jal_db_version = "1.0";
    private $tablename = "custom_website_data";

    //construct
    function __construct( ){
        register_activation_hook(__FILE__, $this->jal_install());
        //register_uninstall_hook(    __FILE__, $this->uninstall() );
        add_action( 'admin_menu', array( $this, 'register_my_custom_menu_page') );
        add_shortcode( 'cwd', array($this, 'cwd_func') );
        if (is_admin()) {
            add_action('admin_notices', array( $this, 'showAdminMessages'));
        }
        if( !session_id() && function_exists('session_start')){
            session_start();
            $_SESSION['cwd_started'] = true;
        }

        if($_GET['export'] == true && $_GET['page'] == 'cwd-management' && is_admin())
        {
            $csv = '';
            foreach ($this->getAll() as $row) {
                $csv .=  $row->ref . "," . $row->data . "\n";
            }

            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"cwd-export_" . date("Y-m-d_H-i-s") . ".csv\";" );
            header("Content-Transfer-Encoding: binary");

            echo $csv;
            exit;
        }


    }

    protected function newInstallMsg(){
        if (!get_option('cwd_newmsg')) {
            $this->showMessage('Thanks for installing Custom Website Data, check out the <a href="' . site_url() . '/wp-admin/admin.php?page=cwd-management&view=user"> User Guide</a> to learn about CWD and get started.');
            add_option('cwd_newmsg', true);
        }
    }

    // Create db table when plugin is activated
    protected function jal_install () {
        global $wpdb;
        global $jal_db_version;

        $table_name = $wpdb->prefix . $this->tablename;
        if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

             $sql = "CREATE TABLE " . $table_name . " (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                ref tinytext NOT NULL,
                data tinytext NOT NULL,
                UNIQUE KEY id (id)
                );";

             require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
             dbDelta($sql);

             add_option("jal_db_version", $this->jal_db_version);

        }
    }

    //menu and admin page

    public function register_my_custom_menu_page(){
        add_menu_page( 'Custom Website Data', 'Custom Data', 'manage_options', 'cwd-management', array($this, 'cwd_management_page'), plugins_url( ($_GET['page'] !== 'cwd-management')?'simple-custom-website-data/img/cwd.png' : 'simple-custom-website-data/img/active_cwd.png' ), 28 );
    }

    public function cwd_management_page(){
        require_once 'cwd-admin.php';
    }

    //create shortcodes

    public function cwd_func($atts){
        extract(shortcode_atts(array(
          'ref' => null,
       ), $atts));
        return $this->processOutput($ref);
    }

    // Views

    public function showView($view = null){
        switch ($view) {
            case 'dash':
                require 'views/dash.php';
                break;
            case 'delete':
                require 'views/delete.php';
                break;

            case 'add':
                require 'views/add.php';
                break;

            case 'edit':
                require 'views/edit.php';
                break;

            case 'proc':
                require 'proc.php';
                break;

            case 'user':
                require 'views/user.php';
                break;

            case 'utility':
                require 'views/utility.php';
                break;

            case 'import':
                require 'views/import.php';
                break;

            case 'import_proc':
                require 'views/import_proc.php';
                break;

            default:
                $this->newInstallMsg();
                require 'views/dash.php';
                break;
        }
    }

    //data manipulation

    public function insertData($ref, $data, $skip_proc = false){
        global $wpdb;

        if (!$skip_proc) {
            $data = $this->processData($data);
        }

        $table_name = $wpdb->prefix . $this->tablename;
        $wpdb->query(
            $wpdb->prepare(
                " INSERT INTO $table_name
                    ( id, ref, data )
                    VALUES ( %d, %s, %s )
                ",
                         NULL, $ref, $data
                    )
                );
            }

    public function updateRecord($id, $data){
        global $wpdb;
        $data = $this->processData($data);
        $table_name = $wpdb->prefix . $this->tablename;
        if($this->idExists($id)){

            $up_data = array(
                            'data'=> $data
                            );

            $where = array(
                        'id' => $id
                        );

            $wpdb->update( $table_name, $up_data, $where, $format = null, $where_format = null );
        }
    }

    public function deleteRecord($id){
        global $wpdb;
        $table_name = $wpdb->prefix . $this->tablename;
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $table_name
                 WHERE  id = %d
                "
            ,$id)
                );
    }

    public function idHandler(){
        if (isset($_REQUEST['id'])) {
            return $_REQUEST['id'];
        }
        return false;

    }

    //Process Requests

    public function processUserRequest(){
        $cwd_id = $this->idHandler();

        // Add Record
        if (isset($_POST['ref']) && isset($_POST['data']) && $_POST['cwdaction'] == 'add') {

            if ( !empty($_POST) && check_admin_referer('cwd_add_action','cwd_add_name') ) {

                $this->insertData($this->xss_filter($_POST['ref']), $this->xss_filter($_POST['data']));
                $this->setMessage('The record "' . $_POST['ref'] . '" has been added') ;
            }

        }

        // Delete Record

        elseif($_GET['del'] == 'y' && $cwd_id){
            if (wp_verify_nonce( $_GET['_del_rec'], 'del_rec-' .  $_GET['id'])) {
                $this->deleteRecord($this->xss_filter($cwd_id));
                $this->setMessage('The record has been deleted') ;
            }
            else
            {
                $this->setMessage('Securty check failed');
            }


        }

        // Edit Record
        elseif($_POST['edit'] == 'y' && $cwd_id){
            if (wp_verify_nonce( $_POST['_edit_rec'], 'edit_rec-' .  $_POST['id']))
            {
                $this->updateRecord($_POST['id'], $this->xss_filter($_POST['data']));
                $this->setMessage('The record was updated');
            }
            else
            {
                $this->setMessage('Securty check failed');
            }

        }
    }


    // Utility

    public function xss_filter($string)
    {
        if($this->isJson(stripslashes($string)))
        {
            return strip_tags($string);
        }
        return strip_tags(htmlentities($string));
    }

    public function processData($data){
        if (is_array($data)) {
            return json_encode($data);
        }
        elseif($this->isJson($data)){
            return json_decode($data, true);
        }
        elseif(strstr($data, "\n") && strstr($data, '=')){
            $retArr = array();
            $data = str_replace(array("\r", "\n"), '|', $data);
            $exploded = explode('|', $data);
            foreach ($exploded as $entry => $value) {
                $single_ex = explode('=', $value);
                if (!empty($single_ex[0])) {
                    $retArr[$single_ex[0]] = $single_ex[1];
                }
            }
            return json_encode($retArr);
        }
        else{
            return $data;
        }
    }

    public function processOutput($ref = null){
        $data = $this->retRecord($ref)->data;
        if ($this->isJson($data)) {
            return json_decode($data, true);
        }
        elseif($this->isJson(stripcslashes($data)))
        {
            return json_decode(stripcslashes($data), true);
        }elseif(!is_null($data)){
            return $data;
        }else{
            return false;
        }
    }

    public function getAll(){
        global $wpdb;
        $table_name = $wpdb->prefix . $this->tablename;
        $row = $wpdb->get_results(
            $wpdb->prepare(
            "
                SELECT *
                FROM $table_name
            ", null
                    )
            );

        return $row;
    }

    public function idExists($id){
        global $wpdb;
        $table_name = $wpdb->prefix . $this->tablename;
        $row = $wpdb->get_row(
            $wpdb->prepare(
            "
                SELECT id
                FROM $table_name
                WHERE id = %s
            ",
            $id
                    )
            );

        return $row;
    }

    public function getRecordIdByRef($ref)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->tablename;
        $row = $wpdb->get_row(
            $wpdb->prepare(
            "
                SELECT id
                FROM $table_name
                WHERE ref = %s
            ",
            $ref
                    )
            );
        return $row->id;
    }

    public function retRecord($ref){
        global $wpdb;
        $table_name = $wpdb->prefix . $this->tablename;
        $row = $wpdb->get_row(
            $wpdb->prepare(
            "
                SELECT data
                FROM $table_name
                WHERE ref = %s
            ",
            $ref
                    )
            );

        return (count($row) > 0)? $row : false;
    }

    public function isJson($string) {
        if (!is_numeric($string)) {
            json_decode($string);
            return (json_last_error() == JSON_ERROR_NONE);
        }
        return false;
    }

    public function isMulti($a, $json = null) {
        if($json == true)
        {
            $a = cwd_objectToArray(json_decode($a));
        }
        $rv = array_filter($a,'is_array');
        if(count($rv)>0) return true;
        return false;
    }

    public function getById($id){
        global $wpdb;
        $table_name = $wpdb->prefix . $this->tablename;
        $row = $wpdb->get_row(
            $wpdb->prepare(
            "
                SELECT *
                FROM $table_name
                WHERE id = %s
            ",
            $id
                    )
            );

        return $row;
    }

    //UI

    public function showMessage($message, $errormsg = false)
    {
        if ($errormsg) {
            echo '<div id="message" class="error">';
        }
        else {
            echo '<div id="message" class="updated fade">';
        }

        echo '<p><strong>' . $message . '</strong></p></div>';
    }

    public function showAdminMessages()
    {
        if ($_SESSION['cwd_message']) {
            $this->showMessage($_SESSION['cwd_message']);
            $_SESSION['cwd_message'] = false;
        }
    }

    public function setMessage($message = null){
        if ($_SESSION['cwd_started']) {
            $_SESSION['cwd_message'] = $message;
        }
    }

    //Uninstall

    public function uninstall(){
        delete_option('cwd_newmsg');
    }

}

//init class
$cwd = new CustomWebsiteData;


//Advanced Functions

function cwd_getThe($ref = null){
    global $cwd;
    return $cwd->processOutput($ref);
}

function cwd_updateThe($ref, $data){
    global $cwd;
    if ($cwd->retRecord($ref)) {

        if (is_array($data)) {
            $filtered_data = array();
            foreach ($data as $key => $value) {
                $filtered_data[$key] = $cwd->xss_filter($value);
            }
            $cwd->updateRecord($cwd->getRecordIdByRef($ref), $filtered_data);
            return true;
        }
        elseif(is_string($data) || is_numeric($data)){
            $cwd->updateRecord($cwd->getRecordIdByRef($ref), $data);
            return true;
        }
        else
        {
            return false;
        }

    }
    else
    {
        return false;
    }

}

function cwd_objectToArray($obj) {
  if(!is_array($obj) && !is_object($obj)) return $obj;
  if(is_object($obj)) $obj = get_object_vars($obj);
  return array_map(__FUNCTION__, $obj);
}
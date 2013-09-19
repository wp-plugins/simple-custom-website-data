<?php
/*
Plugin Name: Custom Website Data
Plugin URI: http://dev.dannyweeks.com/cwd
Version: 1.0
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
        add_menu_page( 'Custom Website Data', 'Custom Data', 'manage_options', 'cwd-management', array($this, 'cwd_management_page'), plugins_url( 'custom-website-data/img/cwd.png' ), 28 );
    }

    public function cwd_management_page(){
        require_once 'cwd-admin.php';
    }

    //create shortcodes

    function cwd_func($atts){
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

            default:
                $this->newInstallMsg();
                require 'views/dash.php';
                break;
        }
    }

    //data manipulation

    public function insertData($ref,$data){
        global $wpdb;

        $data = $this->processData($data);

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
        if (isset($_POST['ref']) && isset($_POST['data']) && $_POST['cwdaction'] == 'add') {
            $this->insertData($_POST['ref'], $_POST['data']);
            $this->setMessage('The record "' . $_POST['ref'] . '" has been added') ;
        }
        elseif($_GET['del'] == 'y' && $cwd_id){
            $this->deleteRecord($cwd_id);
            $this->setMessage('The record has been deleted') ;

        }
        elseif($_POST['edit'] == 'y' && $cwd_id){
            $this->updateRecord($_POST['id'], $_POST['data']);
            $this->setMessage('The record was updated');
        }
    }


    // Utility

    public function processData($data){
        if (is_array($data)) {
            return json_encode($data);
        }
        elseif($this->isJson($data)){
            return json_decode($data, true);
        }
        elseif(strstr($data, "\n") && strstr($data, '=')){
            $retArr = array();
            $data = str_replace("\n", '|', $data);
            $exploded = explode('|', $data);
            foreach ($exploded as $entry => $value) {
                $single_ex = explode('=', $value);
                $retArr[$single_ex[0]] = $single_ex[1];
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

        return $row;
    }

    public function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
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


//Advanced Function
function cwd_getThe($ref = null){
    global $cwd;
    return $cwd->processOutput($ref);
}
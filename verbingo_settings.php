<?php
/*
 Plugin Name: verbingo-translator
 Description: This is a verbingo. Use shortcode [verbingo_translate] to display form on page or use the widget. For more info please check readme file.
 Version: 1.0
 Plugin URI: http://www.verbatimsolutions.com/
 Author: Verbingo
 Author URI: http://www.verbatimsolutions.com/
 License: GNU General Public License v3 or later
*/
define('ABSPATH', dirname(__FILE__) . '/');
error_reporting(0);
// Enqueues plugin scripts
function verbingo_scripts() {	
	if(!is_admin())
	{
		wp_enqueue_style('verbingo_style', plugins_url('verbingo_style.css',__FILE__));
        wp_register_script('verbingo_script', plugins_url('verbingo_script.js', __FILE__ ),array('jquery'));
        wp_enqueue_script('jquery');
        wp_enqueue_script('verbingo_script');    
	}
}
add_action('wp_enqueue_scripts', 'verbingo_scripts');
// The sidebar widget
function register_verbingo_widget() {
	register_widget( 'verbingo_widget' );
}
add_action( 'widgets_init', 'register_verbingo_widget' );
// Create Tables Here with Wordpress Prefix
register_activation_hook(__FILE__,'verbingo_create_table');
function verbingo_create_table()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $tablename=$wpdb->prefix.'translation';
    if($wpdb->get_var("SHOW TABLES LIKE '$tablename'")!=$tablename)
    {
            $sql = "CREATE TABLE $tablename (
                    tran_id INT(11) NOT NULL AUTO_INCREMENT,
                    original TEXT NOT NULL, 
                    lang CHAR(5) NOT NULL, 
                    translated TEXT, 
                    source TINYINT NOT NULL, 
                    PRIMARY  KEY (tran_id)
                    )$charset_collate;";  
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
    }    
    $tablename12=$wpdb->prefix.'ver_selected';
    if($wpdb->get_var("SHOW TABLES LIKE '$tablename12'")!=$tablename12)
    {
            $sql = "CREATE TABLE $tablename12 (
                    id INT(50) NOT NULL AUTO_INCREMENT,
                    code VARCHAR(25) NOT NULL, 
                    name VARCHAR(25) NOT NULL, 
                    status ENUM('1','0') DEFAULT '0',                 
                    PRIMARY  KEY (id)
                    )$charset_collate;";  
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

        $jsonresponse = wp_remote_get("http://6vbs-ww5x.accessdomain.com/verbingodev/VerbingoTranslationApi/index.php/VerbingoLanguages/getVerbingoLanuages/bfa0b9feb3a20283f1c0f38c86a7cf51/json");
        $languagepairs = wp_remote_retrieve_body($jsonresponse);
        $pairsarray = json_decode($languagepairs,true);
        
            foreach($pairsarray['languages'] as $data)
            {               
                $code=trim($data['code']);
                $name=trim($data['name']);
            $wpdb->query($wpdb->prepare("INSERT INTO $tablename12(code, name) VALUES(%s,%s)", array($code,$name)));        
            }
                    
    }    
}
// Ajax Calls 
function verbingo_translation_process()
{
    $Oject=new verbingo_plugin_translation();
    $urlpath=sanitize_text_field($_POST['URLpath']);
    $source=sanitize_text_field($_POST['sourcelanguage']);
    $target=sanitize_text_field($_POST['tar_language']);
    $_SESSION['tar_language']=$target;
    if($_SESSION['tar_language']=='en')
    {
        
    }
    else
    {
    $Oject->verbingo_iserttranslation($source,$target,$urlpath);
    }
    exit;
}
function verbingo_translate_process()
{
     $Oject=new verbingo_plugin_translation();
     $urlpath=sanitize_text_field($_POST['URLpath']);
     $language=sanitize_text_field($_POST['language']);
     $Translated=$Oject->verbingo_iserttranslation('load',$language,$urlpath);
     echo $Translated;
     die();
}
function verbingo_editor_process()
{
        $text=$_POST['text'];
        $search_text = "%".$text."%";
        $code=sanitize_text_field($_POST['code']);
        global $wpdb;
        $translation_record = $wpdb->prefix."translation";
        $get_post_content_db = $wpdb->get_results($wpdb->prepare("SELECT tran_id,original FROM $translation_record WHERE lang=%s and original like %s",$code, $search_text),ARRAY_A);
        $original="";
        foreach ($get_post_content_db as $d)
        {
            $original.='<li value="'.$d['tran_id'].'" >'.$d['original'].'</li>';
        }   
        echo $original;
        exit();
}
function verbingo_edit_process()
{
         $Oject=new verbingo_plugin_translation();
         $id=sanitize_text_field($_POST['id']);
         $translated=$Oject->verbingo_edit_text($id);
         echo $translated;
         exit();     
}
add_action('wp_ajax_verbingo_translation','verbingo_translation_process');
add_action( 'wp_ajax_nopriv_verbingo_translation', 'verbingo_translation_process');
add_action('wp_ajax_verbingo_translate','verbingo_translate_process');
add_action( 'wp_ajax_nopriv_verbingo_translate', 'verbingo_translate_process');
add_action('wp_ajax_verbingo_editor','verbingo_editor_process');
add_action('wp_ajax_verbingo_edit','verbingo_edit_process');
// Includes All pages
include 'verbingo_main.php';
include 'verbingo_admin.php';
include 'verbingo_class.php';
include 'verbingo_widget_form.php';
include 'verbingo_widget.php';
?>
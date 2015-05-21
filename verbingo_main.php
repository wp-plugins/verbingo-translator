<?php
// Start session for captcha validation 
define('ABSPATH', dirname(__FILE__) . '/');
session_start();
$_SESSION['verbingo-rand'] = isset($_SESSION['verbingo-rand']) ? $_SESSION['verbingo-rand'] : rand(100, 999);
// Get Admin Selected Languages 
   add_action('init','verbingo_languages_pairs');                  
   function verbingo_languages_pairs()
        {
            global $wpdb;
            global $languagelist;
            $table_name = $wpdb->prefix."ver_selected";
            $languagelist12 = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE status = %d",1),ARRAY_A);
                
            global $languagelistsr;
           $languagelist.='<option value="en" selected="selected">English</option>';
           foreach($languagelist12 as $data)
            {
 
                if($data['code']=="en")
                {
                }
                else
                {
                     $languagelist.='<option value="'.$data[code].'">'.$data[name].'</option>';
                }
            }

        }
add_action('init', 'verbingo_register_shortcodes');
function verbingo_register_shortcodes() {
    //register shortcode   
    add_shortcode('verbingo_translate', 'verbingo_shortcode');
}
// The shortcode
function verbingo_shortcode($atts) {
    global $filenameext;
	extract(shortcode_atts(array(
		"label_src" 		=> __('Source Language') ,
		"label_tar" 		=> __('Target Language') ,
        "label_api" 		=> __('Enter Your Api Key') ,
        "label_email" 		=> __('Enter Your Email') ,
		"label_submit" 		=> __('Submit') ,
	), $atts));
    
    $_SESSION['labels']=array(
		"label_src" 		=> __('Source Language') ,
		"label_tar" 		=> __('Target Language') ,
        "label_email" 		=> __('Enter Your Email') ,
		"label_submit" 		=> __('Submit'));
        
    // Transalate a File    
     if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['transalat']) ) 
    {
		
    	//echo $_SESSION['filename'];
        $emailUrl="http://6vbs-ww5x.accessdomain.com/verbingodev/VerbingoTranslationApi/index.php/VerbingoApiTranslate/getFileStatistics/". $_SESSION['apikey']."/".$_SESSION['filename']."/".$_SESSION['srce_language']."/".$_SESSION['target_language']."/json";
        $response = wp_remote_get($emailUrl);
       //print_r ($response);
       $data = wp_remote_retrieve_body($response);
        $rest = json_decode($data,true);
		//print_r($rest);
        echo '<form name="verbingo_link "method="post" action="">
                <table id="tbl">
                <thead><th>Translated File has been sent to your mail</th>
                </thead>
               	</table>
                </form>';    
				session_destroy();   
    
    }
     // Upload a File
    
   	 if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['btn_upload']) ) 
    	{
          
		$_SESSION['send_email']=sanitize_text_field($_POST['send_email']);
		$_SESSION['srce_language']=sanitize_text_field($_POST['src_language']);
		$_SESSION['target_language']=sanitize_text_field($_POST['ver_subject']);
        $_SESSION['apikey']=sanitize_text_field($_POST['apikey']);
       $uploadedfile = $_FILES['upload_file'];
       $uploadedfile['name'] = str_replace(' ','',$uploadedfile['name']);
         
       $filepath=$uploadedfile['tmp_name'];
       $filename= $uploadedfile['name'];
     // print_r($_FILES['upload_file']); exit;
       $_SESSION['filename'] = $filename;
       $dir=wp_upload_dir();
       $_SESSION['filellocation']=$dir['baseurl'].'/';   
         
        if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
        add_filter( 'upload_dir','verbingo_upload');
        function verbingo_upload($dir)
       {
            return array(
            'path'   => $dir['basedir'],
            'url'    => $dir['baseurl'],
            'subdir' => '',
            ) + $dir;
        }
        //............
        $upload_overrides = array( 'test_form' => false );
                $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
            if ( $movefile && !isset( $movefile['error'] ) ) {
      
       $folderurl = "http://6vbs-ww5x.accessdomain.com/verbingodev/VerbingoTranslationApi/index.php/VerbingoApi/setBaseURL/".$_SESSION['apikey']."/".$_SESSION['filellocation'];
       wp_remote_get($folderurl);
       $stUrl="http://6vbs-ww5x.accessdomain.com/verbingodev/VerbingoTranslationApi/index.php/VerbingoApi/getFileStatistics/". $_SESSION['apikey']."/".$_SESSION['filename']."/".$_SESSION['srce_language']."/".$_SESSION['target_language']."/json";
         $response = wp_remote_get($stUrl);
        $data = wp_remote_retrieve_body($response);
        $res = json_decode($data,true);
    		if ($res=="")
    		{
    			 echo "Your ApiKey is invalid, Please Varify it Or You file did Not upload succesfully.";
            }
    		else
    		{
    			echo '<div id="div1"><table id="tbl" style="width: 335px;">
    				<form name="verbingo_link "method="post" action="">
                    <thead><th>Total words</th>
                           <th>'.$res['data']['totalWords'].'</th>
                           <th>Duplicate words</th>
                           <th>'.$res['data']['duplicateSegmentsWords'].'</th>
                    </thead>
                    <tbody>
                    <tr>
                     <td colspan="4" style="text-align:center;"><input type="submit" value="Translate" name="transalat" id="transalt"></td>
                     </tr>
                    </tbody></table>
                    </form></div>';
    		
    		}
      } 
      else 
      {
                echo $movefile['error'];
      }
        
	}

    global $languagelist;
	// The Verbingo file Translation interface
	$email_form = '<form class="verbingo" id="verbingo" method="post" action="" enctype="multipart/form-data">
		
		<p><label for="verbingo_name">'.$label_src.' </label></p>
		<p><select name="src_language" id="verbingo_name">
        '.$languagelist.'
        </select>
        </p><p><label for="ver_subject">'.$label_tar.' </label></p>
		<p><select name="ver_subject" id="ver_subject">'.$languagelist.'
        </select>
        </p><p><label for="ver_subject">'.$label_api.' </label></p>
        <input type="text" name="apikey" id="apikey" placeholder="Please Enter Api Key" style="width:50%"/></p>
        <p></p>
        <br>
        <p><input type="file" name="upload_file" id="upload_file" style="width:50%"/><input type="submit" value="Upload" style="margin-left:70px; width:20%" name="btn_upload" id="btn_upload"/></p>
		<p></p></form>';
        
        if($sent == true) {
		unset($_SESSION['verbingo-rand']);
		return $info;
	} else {
		return $info.$email_form;
	}
}
?>

<?php

// Start session for captcha validation
session_start();
$_SESSION['verbingo-widget-rand'] = isset($_SESSION['verbingo-widget-rand']) ? $_SESSION['verbingo-widget-rand'] : rand(100, 999);

// The shortcode
function verbingo_widget_shortcode($atts) {
	extract(shortcode_atts(array(
		"label_tar" 		=> __('Translate') ,
		), $atts));

       function verbingo_languages_widget()
        {
            
            global $wpdb;
            $table_name = $wpdb->prefix."ver_selected";
            $languageeidget12 = $wpdb->get_results("SELECT * FROM $table_name WHERE status=1",ARRAY_A);
           $languageeidget.='<option value="en">English</option>';
           foreach($languageeidget12 as $data)
            {
 
                if($data['code']==$_SESSION['tar_language'])
                {
                    $languageeidget.='<option value="'.$data[code].'" selected="selected">'.$data[name].'</option>';
                }
                else
                {
                     $languageeidget.='<option value="'.$data[code].'">'.$data[name].'</option>';
                }
            }
            return $languageeidget;
        }
        $languageeidget=verbingo_languages_widget();
    
	$email_form = '<form class="verbingo" id="verbingo_widget" method="post" action="">
        <p><label for="ver_subject" >'.$label_tar.' </label></p>
		<select name="lang_widget" id="lang_widget" onchange="changelanguage(this.value);">'
        .$languageeidget.
        '</select>
	</form>';
	if($sent == true) {
		unset($_SESSION['verbingo-widget-rand']);
		return $info;
	} else {
		return $info.$email_form;
	}
} 

add_shortcode('verbingo-widget', 'verbingo_widget_shortcode');

?>
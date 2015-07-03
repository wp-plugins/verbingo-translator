<?php
    //define('ABSPATH', dirname(__FILE__) . '/');
       global $wpdb;
    // Update Language for Translation
            
            if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['addlanguage']) ) 
        {
             
             $codes=$_POST['codes']; 
             $table_name = $wpdb->prefix."ver_selected";
             for ($i=0; $i<sizeof($codes); $i++)
             {
                $codespart=explode(",",$codes[$i]);
                $codespart0=sanitize_text_field(trim($codespart[0]));
                if($codespart[1]=="q")
                      {
                        $wpdb->query($wpdb->prepare("UPDATE $table_name SET status=%d WHERE code=%s",1,$codespart0));
                    
                      }
                      else
                      {
                        $wpdb->query($wpdb->prepare("UPDATE $table_name SET status=%d WHERE code=%s",0,$codespart0));
                      }
           
            }
        }
         //.. End Of Update Language
         if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['changetranslation']) ) 
    	{

		  $id=sanitize_text_field($_POST['tran_id']);
		  $trans=sanitize_text_field(trim($_POST['pagetran']));
          
             $tabletranslation = $wpdb->prefix."translation";
             $wpdb->query($wpdb->prepare("UPDATE $tabletranslation SET translated=%s WHERE tran_id=%d",$trans,$id));
  
        }
       
    class verbingo_plugin_admin
    {
         /** verbingo_plugin father class */
            private $general_settings_key = 'ver_language';
            private $advanced_settings_key = 'ver_setting';
            private $support_settings_key = 'ver_support';
            private $about_settings_key = 'ver_about';
            private $translation_settings_key = 'ver_translation';
            private $plugin_options_key = 'verbingo_options';
            private $plugin_settings_tabs = array();
            private $localleft = 'left';
                
            function __construct() 
            {
                add_action( 'init', array( &$this, 'verbingo_scripts_admin'));
                add_action( 'init', array( &$this, 'load_settings'));
                add_action( 'admin_init', array( &$this, 'register_language_settings' ));
                add_action( 'admin_init', array( &$this, 'register_setting_settings' ));
                add_action( 'admin_init', array( &$this, 'register_support_settings' ));
                add_action( 'admin_init', array( &$this, 'register_about_settings' ));
                add_action( 'admin_init', array( &$this, 'register_translation_settings' ));
                add_action( 'admin_menu', array( &$this, 'add_admin_menus' ));
            }  
              
            /** UTILITY FUNCTIONS * */
            private function sections($head, $text = '') {
                echo '<h2>' . $head . '</h2>';
                echo '<div class="col-wrap">';
                if ($text) echo '<p>' . $text . '</p>';
            }
        
            private function sectiontop() {
                echo '</div>';
            }
                
             private function header($head) 
             {
                 echo '<h3>'.$head.' </h3>';
             }

            // Get All Language From Server
            function get_languages_option()
            {
             
                global $wpdb;
                $table_name = $wpdb->prefix."ver_selected";
                $languagecodelist = $wpdb->get_results("SELECT * FROM $table_name",ARRAY_A);
                 $arraycolum=array();
    			foreach($languagecodelist as $data)
    			{
    				$arraycolum[$data['code']]= $data['name'];
    				
    			}
                 
                 return $arraycolum;
            }
            function verbingo_scripts_admin()
            {
                wp_register_style('verbingo_style', plugins_url('verbingo_style.css',__FILE__));
                wp_enqueue_style('verbingo_style');
                wp_register_script('verbingo_script', plugins_url('verbingo_script.js', __FILE__ ),array( 'jquery' ));
                //wp_enqueue_script('jquerylib',plugins_url('jquery-1.11.0.min.js', __FILE__ ));
                wp_enqueue_script('jquery');
                wp_enqueue_script('verbingo_script');
            }
            // Load Settings
            function load_settings() 
            {
                $this->general_settings = (array) get_option( $this->general_settings_key );
                $this->advanced_settings = (array) get_option( $this->advanced_settings_key );
                $this->support_settings = (array) get_option( $this->support_settings_key );
                $this->about_settings = (array) get_option( $this->about_settings_key );
                $this->translation_settings = (array) get_option( $this->translation_settings_key );
            
            
                // Merge with defaults
                $this->general_settings = array_merge( array(
                    'general_option' => 'General value'
                ), $this->general_settings );
            
                $this->advanced_settings = array_merge( array(
                    'advanced_option' => 'Advanced value'
                ), $this->advanced_settings );
                
                // Merge with defaults
                $this->support_settings = array_merge( array(
                    'support_option' => 'Support Settings'
                ), $this->support_settings );
            
                $this->about_settings = array_merge( array(
                    'about_option' => 'About Settings'
                ), $this->about_settings );
                
                $this->translation_settings = array_merge( array(
                    'translation_option' => 'Translation Settings'
                ), $this->translation_settings );
            }  
            
            // Register Language Tab Setting
            function register_language_settings() 
            {
                $this->plugin_settings_tabs[$this->general_settings_key] = 'Language';
            
                register_setting( $this->general_settings_key, $this->general_settings_key );
                add_settings_section( 'section_general', 'Language Option', array( &$this, 'section_general_desc' ), $this->general_settings_key );
                //add_settings_field( 'general_option', 'A General Option', array( &$this, 'field_general_option' ), $this->general_settings_key, 'section_general' );
            } 
            
            
            // Call Language Setting Page
            function section_general_desc() 
            { 
        $result1= $this->get_languages_option();
        global $wp_locale;
        if ($wp_locale->text_direction == 'rtl') {
            echo '<style type="text/css">
	#sortable li, #default_lang li { float: right !important;}
        .logoicon {
            float:left !important;
        }
        </style>';
        }

        echo '<div id="default_lang" style="overflow:auto;padding-bottom:10px;">
        <h3> Select Languages From Here</h3>
        </div>';
        // list of languages
        echo '<form method="post" action="" enctype="multipart/form-data"><div style="overflow:auto; clear: both; margin-top: -2%;">';
        echo '<ul id="sortable" style="display:block">';
         global $wpdb;
         $table_name = $wpdb->prefix."ver_selected";
         
        foreach ($result1 as $langcode => $langname) {
        $languagematch = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE code=%s and status = %d",$langcode,1),ARRAY_A);
          if(!empty($languagematch))
            {
                
        
            echo '<li id="'.$langcode.'" class="language lng_active" value="'.$langcode.'" name="'.$langname.'">
            <div style="float:left">
            <img src="'.plugins_url("img/flags/", __FILE__).$langcode.'.png" title="" alt="">
            '.$langname.'
            </div><input type="hidden" name="names[]" value="'.$langname.'"/>
            <input class="testing" type="hidden" name="codes[]" value="'.$langcode.''.',q"/>
            <img width="16" height="16" alt="'.$langcode.'" class="logoicon" title="Verbingo" src="'. plugins_url("img/", __FILE__).'verbingoicon.png"/></img>
            <img width="16" height="16" alt="'.$langcode.'" class="logoicon" title="Verbingo" src="'. plugins_url("img/", __FILE__).'googleicon.png"/></img>';
            echo '</li>';
                            
            }
            else
            {
            echo '<li id="'.$langcode.'" class="language" value="'.$langcode.'" name="'.$langname.'">
            <div style="float:left">
            <img src="'.plugins_url("img/flags/", __FILE__).$langcode.'.png" title="" alt="">
            '.$langname.'
            </div><input type="hidden" name="names[]" value="'.$langname.'"/>
            <input class="testing" type="hidden" name="codes[]" value="'.$langcode.''.',"/>
            <img width="16" height="16" alt="'.$langcode.'" class="logoicon" title="Verbingo" src="'.plugins_url("img/", __FILE__).'verbingoicon.png"/></img>
            <img width="16" height="16" alt="'.$langcode.'" class="logoicon" title="Verbingo" src="'.plugins_url("img/", __FILE__).'googleicon.png"/></img>';
            echo '</li>';
                                
            }
            
        }
        echo '</ul></div><br><input type="submit" id="langsubmit" value="Save Changes" class="button-primary" name="addlanguage" style="margin-top:40px;z-index:2147483647; padding: 0px;"/>';
                
  }
            // Calling Setting Page for Settings Tabs
            function field_general_option()
             {?>
                <!--<input type="text" name="<?php //echo $this->general_settings_key; ?>[general_option]" value="<?php// echo esc_attr( $this->general_settings['general_option'] );?>" />-->
             <?php }    
            
        // Register Advance Settings
        function register_setting_settings() 
        {
        $this->plugin_settings_tabs[$this->advanced_settings_key] = 'Settings';
        register_setting( $this->advanced_settings_key, $this->advanced_settings_key );
        add_settings_section( 'section_advanced', '', array( &$this, 'section_advanced_desc' ), $this->advanced_settings_key );
       // add_settings_field( 'advanced_option', 'An Advanced Option', array( &$this, 'field_advanced_option' ), $this->advanced_settings_key, 'section_advanced' );
        }

        
    function section_advanced_desc() 
    { 
        echo '<br>';
        $this->sections(__('Translation related settings','verbingo'));

        /*
         * Insert permissions section in the admin page
         */
        $this->header(__('Who can translate ?', 'verbingo'));
       	echo '<p>  <label> <input type="checkbox" name="" id="" value="1">';
        echo _e( 'Allow visitors to suggest translations?', 'bing-translator' );
        echo '</label> </p>';
        echo '<p>  <label> <input type="checkbox" name="" id="" value="2">';
        echo _e( 'Allow visitors to receive email?', 'bing-translator' );
        echo '</label> </p>';
        $this->sectiontop();
        
        $this->sections(__('General settings', 'verbingo'));
        echo '<br>';
        echo __('Verbingo Api Key:', 'verbingo') . ' <input type="text" size="32" class="regular-text" style="margin-left:51px;" /><br/>';
        echo '<br>';
        echo __('Enter Your Email Address:', 'verbingo') . ' <input type="text" size="32" class="regular-text" /><br/>';
        echo '<br>';
        echo '<p>'; submit_button(); echo '</p>'; 
        $this->sectiontop();
         
    }

    // Register Support Settings
    
     function register_support_settings() 
        {
        $this->plugin_settings_tabs[$this->support_settings_key] = 'Support';
        register_setting( $this->support_settings_key, $this->support_settings_key );
        add_settings_section( 'section_support', '', array( &$this, 'section_support_desc' ), $this->support_settings_key );
        }

        
    function section_support_desc() 
    { 
        
        echo '<p>';
        $this->sections(__('Verbingo support', 'verbingo')
                , __('Have you encountered any problem with our plugin and need our help?, verbingo') . '<br>' .
                __('Do you need to ask us any question? , verbingo') . '<br>' .
                __('You have two options:, verbingo') . '<br>');
        $this->sectiontop();
        $this->header(__('Our free support', 'verbingo'));
        echo '<div class="col-wrap">';
        echo __('There are many channels to reach us and we do try to help as fast as we can', 'verbingo') . '<br>';
        echo __('You can contact us through our contact form on our web site', 'verbingo') . '<br>';
        echo __('Create a ticket for us if you have found any bugs', 'verbingo') . '<br>';
        echo __('Reach us via different forums:', 'verbingo');
        echo '<ul style="list-style-type:disc;margin-' . $this->localleft . ':20px;">';
        echo '<li><a href="http://verbatimsolutions.com">';
        echo __('Our support forum on wordpress.org', 'verbingo');
        echo '<li><a href="http://verbatimsolutions.com">';
        echo __('Our internal development site, with wiki and tickets', 'verbingo');
        echo '</a></li><li><a href="https://www.facebook.com/VerbatimSolutions">';
        echo __('Our facebook page', 'verbingo');
        echo '</a></li><li><a href="https://plus.google.com/+VerbatimSolutions/posts">';
        echo __('Our google plus page', 'verbingo');
        echo '</a></li></ul>';
        echo __('Contact us directly via:', 'verbingo');
        echo '<ul style="list-style-type:disc;margin-' . $this->localleft . ':20px;">';
        echo '<li><a href="http://verbatimsolutions.com">' . __('Our contact form','verbingo') . '</a></li>';
        echo '<li><a href="http://verbatimsolutions.com">' . __('Suggest a Feature', 'verbingo') . '</a></li>';
        echo '<li><a href="http://verbatimsolutions.com">' . __('Report a Bug','verbingo') . '</a></li>';
        echo '</ul>';

        echo '</div>';
        $this->header(__('Professional support option', 'verbingo'));
        echo '<div class="col-wrap">';
        echo __('For the low low price of $99, we will take express action on your request. By express we mean that your issue will become our top priority, and will resolve ASAP', 'verbingo') . '<br>';
        echo __('This includes helping with various bugs, basic theme/plugins conflicts, or just telling you where the ON button is', 'verbingo') . '<br>';
        echo __('Full money back guarentee! If your problem remains unresolved or you are simply unhappy we will refund your paypal account as soon as you ask (as long as paypal allows it, don\'t come to us three years later!)', 'verbingo') . '<br>';
        echo __('So hit the following button. Thanks!', 'verbingo') . '<br>';
        echo '<br/>
<form action="" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="KCCE87P7B2MG8">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynow_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
  ';
        echo '</div>';
        $this->header(__('Donations', 'verbingo'));
        echo '<div class="col-wrap">';
        echo __('If you just want to show that you care, this is the button for you. But please think twice before doing this. It will make us happier if you just do something nice for someone in your area, contribute to a local charity, and let us know that you did that :)', 'verbingo') . '<br>';
        echo '<br/>
<form action="" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="4E52WJ8WDK79J">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>';
        echo '</div>';
        
    
    }
    
    
  
    // Register About Settings
    
     function register_about_settings() 
        {
        $this->plugin_settings_tabs[$this->about_settings_key] = 'About';
        register_setting( $this->about_settings_key, $this->about_settings_key );
        add_settings_section( 'section_about', '', array( &$this, 'section_about_desc' ), $this->about_settings_key );
        }

        
    function section_about_desc() 
    { 
        echo '<br>';
        $this->sections(__('About Verbingo', 'verbingo'));
        echo __('verbingo was started at 2012 and is dedicated to provide tools to ease website translation and File Translation.','verbingo');
        echo '<br/>';
        echo __('Learn more about us in the following online presenses', 'verbingo');
        echo '<ul style="list-style-type:disc;margin-' . $this->localleft . ':20px;">';
        echo '<li><a href="http://verbatimsolutions.com">';
        echo __('Our website', 'verbingo');
        echo '</a></li><li><a href="http://verbatimsolutions.com">';
        echo __('Our blog', 'verbingo');
        echo '</a></li><li><a href="https://twitter.com/verbatim">';
        echo __('Our twitter account (feel free to follow!)', 'verbingo');
        echo '</a></li><li><a href="https://www.facebook.com/VerbatimSolutions">';
        echo __('Our facebook page (feel free to like!)', 'verbingo');
        echo '</a></li><li><a href="https://plus.google.com/+VerbatimSolutions/posts">';
        echo __('Our google plus page (add us to your circles!)', 'verbingo');
        echo '</a></li><li><a href="http://verbatimsolutions.com">';
        echo __('Our youtube channel','verbingo');
        echo '</a></li></ul>';


        $this->sectiontop();
    }
  
    
    
     // Register Registration Settings
        function register_translation_settings() 
        {
        $this->plugin_settings_tabs[$this->translation_settings_key] = 'Editor';
        register_setting( $this->translation_settings_key, $this->translation_settings_key );
        add_settings_section( 'section_translation', '', array( &$this, 'section_translation_desc' ), $this->translation_settings_key );
         }

        
    function section_translation_desc() 
    { 
                global $wpdb;
                $tablelanguage = $wpdb->prefix."ver_selected";
                $languagecode = $wpdb->get_results($wpdb->prepare("SELECT * FROM $tablelanguage WHERE status = %d",1),ARRAY_A);
             
         
        echo '<br>';
        $this->sections(__('Translation Related Setting','verbingo'));
        echo '<br><form name="changetext"><div class="text">';
        echo _e('<label for="pagetitle">Select Page Title</label>'). '<select name="pagetitle" id="pagetitles">';
        
        echo '<option value="">No Need</option>';
        echo '</select></div><div class="text"><label class="languagecode">Select Language</label><select name="languagecode" id="langaugeselected">';
        
           foreach($languagecode as $data)
            {
 
            echo '<option value="'.$data[code].'">'.$data[name].'</option>';
            }
            
        
       echo  '</select></div><br/>';
       
        echo '<br><div class="text" style="width:100%;">';
        echo _e('<label for="pagetitle">Original Text</label>', 'verbingo'). '<textarea  rows="4" cols="70" name="pagetitle" id="gettranslationtext" placeholder="Type Text Here...."></textarea></div><ul class="yell_tag" id="gettextoriginal" style="display:none; top:224px; left: 192.25px; position:absolute;"></ul><br/>';
        echo '<br><input type="hidden" name="tran_id" id="tr_id" value="">';
        echo '<div class="text" style="width:100%;">';
        echo _e('<label for="pagetitle">Translated Text</label>', 'verbingo'). '<textarea  rows="4" cols="70" name="pagetran" id="getbyid" placeholder="Enter Text Here...."></textarea></div><br/>';
        echo '<br>';
        echo '<p>';
        echo '<input type="submit" id="changetranslation" value="Save Changes" class="button-primary" name="changetranslation" style="margin-top:40px; margin-left: 178px;"/>';
        echo '</p></form>'; 
        $this->sectiontop();
         
    }

    
    // Add Menu Here
    function add_admin_menus() {
    
    add_menu_page('verbingo_dashboard', 'Verbingo', 'manage_options', $this->plugin_options_key, array( &$this, 'plugin_options_page' ),''.plugins_url("img/", __FILE__).'ohticon.png');
    
    }
    
    function plugin_options_page() {
    $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key; ?>
    <div class="wrap">
        <?php $this->plugin_options_tabs(); ?>
        <form method="post" action="options.php">
            <?php wp_nonce_field( 'update-options' ); ?>
            <?php settings_fields( $tab ); ?>
            <?php do_settings_sections( $tab ); ?>
            <?php //submit_button(); ?>
        </form>
    </div>
    <?php
        }
        
    function plugin_options_tabs() {
    $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key;

    $scren=screen_icon();
    echo '<h2 class="nav-tab-wrapper">';
    foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
        $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
        echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
    }
    echo '</h2>';
}
        
}
add_action( 'plugins_loaded', create_function( '', '$verbingo_admin_side = new verbingo_plugin_admin;' ) );
?>
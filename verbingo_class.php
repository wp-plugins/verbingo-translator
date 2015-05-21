<?php
    class verbingo_plugin_translation
    {
    
            private $dom;
            function __construct() 
            {
   
            } 
            // Get the translated text and modify it here
            public function verbingo_edit_text($id)
            {
                 global $wpdb;
                 $translation_record = $wpdb->prefix."translation";
                 $get_translation = $wpdb->get_row($wpdb->prepare("SELECT translated FROM $translation_record WHERE tran_id=%d",$id),ARRAY_A);
                 $original = trim($get_translation['translated']);
                 return $original;
            } 
            
            // First We check Translation. If exist do not insert it agian, otherwise insert translation
           public function verbingo_iserttranslation($s_lang,$target,$URLpath)
            {
                  $dom = new DOMDocument("1.0", "utf-8");
                if($target!='en')
                {
                     $contents=file_get_contents($URLpath);
                     @$dom->loadHTML($contents);
                     $rootElement = $dom->documentElement;
                    if($s_lang=='load')
                    {
                        $this->verbingo_recursiveIteration_fetechByTagName($dom, $rootElement,$s_lang,$target);
                        $Translated=$dom->saveHTML();
                        return $Translated;
                    }
                    else
                    {
                       
                        $this->verbingo_recursiveIteration_fetechByTagName($dom, $rootElement,$s_lang,$target);
                    }
                }      
            }
            // First Time check if Translation is not exist then insert translation into translation table
            function verbingo_getTranslation($source,$s_lang,$target)
            {
                global $wpdb;
                $translation_record = $wpdb->prefix."translation";
                $transaltedcontent = $wpdb->get_row($wpdb->prepare("SELECT original FROM $translation_record WHERE original = %s and lang = %s and translated!=''", addslashes(trim($source)),$target),ARRAY_A);
            if(empty($transaltedcontent))
            {
                $apiUrl="http://6vbs-ww5x.accessdomain.com/verbingodev/VerbingoTranslationApi/index.php/VerbingoApi/getSegmentTranslation/bfa0b9feb3a20283f1c0f38c86a7cf51/".rawurlencode($source)."/en/".$target."/";
                $jsonresponse = wp_remote_get($apiUrl);
                $languagepairs = wp_remote_retrieve_body($jsonresponse);
                $pairsarray = json_decode($languagepairs,true);
                $gettranslatedtext=$pairsarray['data']['translation'];
                    if($gettranslatedtext!="")
                    {   
                    $inset_translation = $wpdb->prefix."translation";
                    $original=addslashes(trim($source));
                    $lang=$target;
                    $translated=addslashes($gettranslatedtext);
                    $wpdb->query($wpdb->prepare("INSERT INTO $inset_translation(original, lang, translated,source)VALUES (%s,%s,%s,%d)", array($original,$lang,$translated,1)));
                    }
                }
                else
                {
                  return $source;
                }
       } 
       public function verbingo_TransltePage($source,$target)
       {
                global $wpdb;
                $table_name21 = $wpdb->prefix."translation";
                $contetntranslation = $wpdb->get_row($wpdb->prepare("SELECT original,translated FROM $table_name21 WHERE original = %s and lang = %s and translated!=''", addslashes(trim($source)),$target),ARRAY_A);
                if(!empty($contetntranslation))
                 {
                    return $contetntranslation['translated'];
                 }  
                 else
                 {
                     return $source;
                 }
       }
       function verbingo_recursiveIteration_fetechByTagName($dom, $root,$s_lang,$target)
        {
        for ($i = 0; $i < $root->childNodes->length; $i++)
        {
            $child = $root->childNodes->item($i);
            if ($child->nodeType == XML_TEXT_NODE) {
                if($s_lang=='load')
                {
                 $translation=$this->verbingo_TransltePage($child->nodeValue,$target);
                }
                else
                {
                 $translation=$this->verbingo_getTranslation($child->nodeValue,$s_lang,$target);   
                }
            $child->nodeValue = $translation;
            }
        }
        for ($i = 0; $i < $root->childNodes->length; $i++) 
        {
            //The $nodeId is now the parent ID of its child node!
            $child = $root->childNodes->item($i);
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                $this->verbingo_recursiveIteration_fetechByTagName($dom, $child,$s_lang,$target);
            }
        }
     }
    }
?>
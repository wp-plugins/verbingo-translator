var base_url= window.location.origin;
//base_url=base_url.replace('/wp-content/plugins','');
var URLpath=document.URL;  
jQuery(document).ready(function(){
    
    var language=jQuery('#lang_widget :selected').val();
   if(language=='en' || typeof language == 'undefined' || language=='')
   {
	    jQuery('#lang_widget option[value='+language+']').attr('selected','selected');
   }
   else
   {        
                
                if(URLpath.search("wp-admin") != -1)
                {
   
                } 
                else
                {   
                    //jQuery("html").remove();
                jQuery.post(base_url+'/wp-admin/admin-ajax.php', {action: 'verbingo_translate', URLpath : URLpath,language : language},function(data){
                document.open();
                document.write('');
				document.write(data);
                document.close();
                jQuery('#lang_widget option[value='+language+']').attr('selected','selected');
    	         });
                
             }
   }
    jQuery(":file").css("background-color", "red");
    jQuery("#btn_upload").click(function(){
        
        var srcn_ame=jQuery('#verbingo_name option:selected').val();
        var tar_name=jQuery('#ver_subject option:selected').val();
        var email=jQuery('#ver_email').val();
        var upload_file=jQuery('#upload_file').val();
      
        if(srcn_ame==tar_name)
        {
            alert('Please Select Different Language');
            return false;  
        }
        else if(upload_file=="")
        {
            alert('Please Select A File');
            return false;  
        }
        else
        {
            return true; 
        }
    });
//.......................Editor Script......................

jQuery("#gettranslationtext").bind("change paste keyup", function(e)
{
   
   jQuery('#gettextoriginal').css('display','block');
   var text = jQuery(this).val();
   var code = jQuery("#langaugeselected").val();
  
     jQuery("#gettextoriginal").html('');
     base_url=base_url.replace('/wp-admin/admin.php','');
 console.log( "Handler for .keypress() called." + e.keyCode);
 jQuery.post(base_url+'/wp-admin/admin-ajax.php', {action: 'verbingo_editor', text:text,code:code},function(data){
                
                        jQuery("#gettextoriginal").html(data);
    	         }); 
});
//................................

jQuery('#gettextoriginal').delegate('li', 'click', function () {
            var str=jQuery(this).text();
            var id=jQuery(this).val();
            document.getElementById("gettranslationtext").value = str;
            document.getElementById("tr_id").value = id;
            
            jQuery.post(base_url+'/wp-admin/admin-ajax.php', {action: 'verbingo_edit', id : id},function(data){
                
                       var data=data.trim();
                       document.getElementById("getbyid").value = data;  
    	         }); 

             jQuery("#gettextoriginal").html('');
 
});
//........................................................

    jQuery("#sortable>li").click(function(){
        var hello = jQuery(this).find(".testing").val();
        var idli = jQuery(this).attr('id');
        if(jQuery(this).hasClass('lng_active'))
        {
            jQuery(this).removeClass('lng_active');
            jQuery(this).find(".testing").val(idli+",");
        }
        else
        {
             jQuery(this).addClass("lng_active");
             jQuery(this).find(".testing").val(hello+"q");
        }
    });
});
 function changelanguage(value)
     {
        tar_language =value;
        jQuery("#lang_widget").find('option').removeAttr("selected");
        jQuery('#lang_widget option[value='+tar_language+']').attr('selected','selected');
        if(tar_language=='en')
        {
            jQuery.post(base_url+'/wp-admin/admin-ajax.php', {action: 'verbingo_translation',sourcelanguage :'en',tar_language : tar_language,URLpath: URLpath},function(data){
            location.reload();
            });
        }
        else
        {
            alert("Request has been sent for translation. Please wait.");
            jQuery.post(base_url+'/wp-admin/admin-ajax.php', {action: 'verbingo_translation',sourcelanguage :'en',tar_language : tar_language,URLpath: URLpath},function(data){
                location.reload();
            });
        }
     }
/* Commnetivity Foundation v4 UX functionality */
function commnetivity_ui_leftnav_enable() {
	$('div[id=commnetivity_actions]').html('<a class="prev">&laquo; Back</a><a class="next">More sresults &raquo;</a>');
	$(".scrollable").scrollable({ vertical: true, mousewheel: true });
	$('input[name=commnetivity_leftnav_search]').each(function() {
		var default_value = this.value;
		$(this).focus(function() {
			if(this.value == default_value) {
				this.value = '';
			}
		});
		$(this).blur(function() {
			if(this.value == '') {
				this.value = default_value;
			}
		});
	});
	return true;
}
function commnetivity_ui_leftnav_disable() {
	$('div[id=commnetivity_actions]').empty();
	return true;
}

/* End of Foundation 4 UX functions */
function process_subnav_responses(XHR) {
	if ( XHR.display != undefined ) {
		$('div[id=control_panel_window]').html(XHR.display);
	}
	if ( XHR.height != undefined ) {
		$('div[id=control_panel_window]').css("height",XHR.height);
	}
	if ( XHR.javascript != undefined ) { eval(XHR.javascript); }
	statusBarOff();
	$('div[id=commnetivity_toolbar_dialog]').hide().html();
	
	return true;
}
function statusBarOff(){ $('div[id=control_panel_status_bar]').removeClass('animated').addClass('idle').show(); }

function resize_height() {
	$(document).ready(function() {
		equalHeight($('div[id=control_panel_window]'));
		equalHeight($('div[id=commnetivity_leftnav_container]'));
	});
}

function equalHeight(group) {
	var tallest = 0;
	group.each(function() {
		var thisHeight = $(this).height();
		if(thisHeight > tallest) {
			tallest = thisHeight;
		}
	});
	group.height(tallest);
}
 
//global variable, this will store the highest height value

function resize_details() {
		var commnetivity_window = $('div[id=control_panel_window]').width();
	   var leftnav_width = $('div[id=commnetivity_leftnav]').width();
	   var preview_width = $('div[id=commnetivity_preview]').width();
	   var details_width = $('div[id=commnetivity_details]').width();
		var new_width = (commnetivity_window - 3 - leftnav_width - preview_width-4); //5
		$('div[id=commnetivity_details]').animate({width: new_width}, "fast");
}



function resize_details_after_preview_hide() {
		var preview_width = $('div[id=commnetivity_preview]').width();
		var details_width = $('div[id=commnetivity_details]').width();
		var new_width = (details_width + preview_width);
		$('div[id=commnetivity_details]').css("border-right","0px");
		$('div[id=commnetivity_details]').animate({width: new_width}, "fast");   
}


$('div[id=commnetivity_toolbar] a[href=add]').live('click', function(){
	var rail = $('div[id=commnetivity]').attr('rail');
	$('div[id=commnetivity_details]').slideUp('slow');
	$('div[id=commnetivity_toolbar_dialog]').show();
   		$.ajax({
        url: rail+'/add/new',
        type: "GET",
        cache: false,
       	dataType: 'json',
		success: function(response) {
				$('div[id=commnetivity_toolbar_dialog]').html(response.display);
        	return false;
    	}
	});
	$('div[id=commnetivity_toolbar] a[href=add]').hide();
	return false;
});
$('div[id=commnetivity_toolbar_dialog] a[href=close]').live('click',function(){
	$('div[id=commnetivity_toolbar_dialog]').hide();
	$('div[id=commnetivity_details]').slideDown('fast');
	$('div[id=commnetivity_toolbar] a[href=add]').show();
	return false;
});
$('div[id=commnetivity_toolbar_dialog]').hide();

$('a[id=phpedit]').hide();
$(document).bind('keypress', function(event) {
    if( event.which === 26 && event.ctrlKey ) {
        $('div[id=commnetivity]').toggle();
    }
});


$(document).ready(function() {
	$('a[id=editor]').live('click', function(){
		var width = $('div[id=content]').width();
		var height = $('div[id=content]').height();
		var editor_link_html = "";
		 $('div[id=control_panel]').slideUp(function(){});
       $.ajax({
            url: '/commnetivity/content',
            type: "POST",
            data: {vpath: '$vpath', width: width, height: height, mode: 'editor'},
            cache: false,
            dataType: 'json',
            success: function(response) {
                $('a[id=editor]').hide();
                $('div[id=content]').html(response.display);
                eval(response.javascript);
            }
        }); 
        return false;
    });

	// Old X button
    $('a[id=control_panel_toggler]').click(function(){
        $('div[id=control_panel]').slideToggle(function(){});
    });
    $('ul[id=control_panel_main_tabs] li a').live('click', function() {
		statusBarOff();
        var rail = $(this).attr('href');
        $('#commnetivity').attr("rail", rail);
        $('ul[id=control_panel_main_tabs] li').removeClass('current');
        $(this).parent().addClass('current');
        var test = $('ul[id=control_panel_main_tabs] li a').text();
        $('div[id=control_panel_status_bar]').html('<div id="commnetivity_actions">&nbsp;</div>').removeClass('idle').addClass('animated').fadeIn('slow');
            $('#control_panel_window').fadeOut(200, function(){
            	$.ajax({
                	url: rail, data: {vpath: '$vpath'}, dataType: "json", cache: false,
                	success: function(response) {
                	    if ( response.display != undefined ) {
                	        $('#control_panel_window').fadeOut(300, function(){
                	        	$('#control_panel_window').html(response.display).fadeIn(400);
                	        });
                	        var tabs = $('ul[id=control_panel_subnav_tabs]').html();
                	    } else {
                	        $('#control_panel_window').empty().append("<h1><p>Error: /ui/tab.[tabname].php</p></h1>").slideDown('fast');
            		    }
            	        $('div[id=control_panel_status_bar]').removeClass('animated').addClass('idle').show();
            	        $('div[id=control_panel_subnav_tabs]').empty();
            	        $('ul[id=control_panel_subnav_tabs]').appendTo('#control_panel_status_bar');
                        resize_height();
            	    }, error: function(XMLHttpRequest, textStatus, errorThrown){
            	        $('div[id=control_panel_status_bar]').removeClass('animated').addClass('idle').show();
            	        $('div[id=control_panel_subnav_tabs]').empty();
            	        $('ul[id=control_panel_subnav_tabs]').appendTo('#control_panel_status_bar');
            	        $('div[id=content').html("The requested panel was not loaded.");
            	    }
            	});
            });
            return false;
        });
    /* Perform action on the click of Sub-navigation */
    $('ul[id=control_panel_subnav_tabs] li a').live('click',function() {
        var rail = $(this).attr('href');
        $('#commnetivity').attr("rail", rail);
        $('div[id=commnetivity_actions]').html('<div id="commnetivity_actions">&nbsp;</div>');
        $('ul[id=control_panel_subnav_tabs] li').removeClass('current');
        $(this).parent().addClass('current');
        $('div[id=control_panel_status_bar]').removeClass('idle').addClass('animated');
        $('#control_panel_window').fadeOut(200, function(){
        	$('#control_panel_window').empty();
        });
        $.ajax({
                url: rail, type: "GET", data: {vpath: "$vpath"}, dataType: "json", cache: false, async: false,
                success: function(response) {
                    $('#control_panel_window').fadeIn(400, function(){
	                    process_subnav_responses(response);
                        resize_height();
                        //alert("Subnav content has been loaded.");
                    });
                    $('#control_panel_window').fadeIn(300);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
	                process_subnav_responses(response);
					$('div[id=control_panel_window]').html(errorThrown);
					
                    statusBarOff();
                }
            });
            return false;
        });
	});
$('div[id=control_panel]').hide();
$('div[id=control_panel_window]').html("");



$('div[id=commnetivity_leftnav] a').live('click', function() {
	resize_details();
    var id = $(this).attr('id');
	$('div[selected=true]').removeAttr('selected');
	$('div[id=commnetivity_preview]').empty();
	$('div[id=commnetivity_details]').html('<span id="wait">Please wait...</span>').slideUp('fast',function(){});
    $('div[id=commnetivity_toolbar_dialog]').slideUp(900).html();
	$(this).parent().attr('selected', 'true');
	var rail = $('#commnetivity').attr("rail");
	
    $.ajax({
    	url: rail+"/details/"+id, global: false, type: "GET", dataType: "json",
        success: function(XHR){


        	if ( XHR.details != undefined ) { $('div[id=commnetivity_details]').empty().append(XHR.details).slideDown('fast').show(); }
        	if ( XHR.preview != undefined ) {
            	//var held = $('div[id=commnetivity_preview]').html("<h1><i>Recalculating screen</i></h1>");
            	$('div[id=commnetivity_preview]').empty().append(XHR.preview).slideDown('fast');
           	} else {
                   	$('#commnetivity_preview').hide();
				//recalcwindows();
            }
        	if ( XHR.javascript != undefined ) { eval(XHR.javascript); }
        	statusBarOff();
            $('a[id=cmspreviewx]').hide();
        }
	});
	
	return false;
});

$('a[id=cmspreview]').live('click', function() {
	var href = $(this).attr('href');
	$.ajax({
		url: href, cache: false,
		success: function(response) {
			$('div[id=commnetivity_preview]').html(response).show();
			$('a[id=cmspreview]').hide();
			$('a[id=cmspreviewx]').show();
		}
	});
	
	return false;
});

$('a[id=cmspreviewx]').live('click', function() {
	$('a[id=cmspreview]').show();
	$('a[id=cmspreviewx]').hide();
	$('div[id=commnetivity_preview]').hide();
	resize_details_after_preview_hide();
	
	return false;
});
/* 
$("#control_panel_window a").live('click', function(){
       var href = $(this).attr('href');
       $('div[id=control_panel_window]').html("Please wait... getting panel or screen...");
       $('div[id=control_panel_status_bar]').removeClass('idle');
       $('div[id=control_panel_status_bar]').addClass('animated').fadeIn('slow');
        $.ajax({
		url: href, global: false, type: "GET",
		dataType: "json",
		success: function(results){
			$('div[id=control_panel_window]').html(results.display).slideDown('fast');
			$('div[id=control_panel_status_bar]').removeClass('animated');
			$('div[id=control_panel_status_bar]').addClass('idle').show();
			statusBarOff();
		}, error: function(XMLHttpRequest, textStatus, errorThrown) {
			statusBarOff();
		}
	});
	return false;
}); */

$("#control_panel_window input, #control_panel_window textarea, #control_panel_window select, #control_panel_window checkbox" ).live('change', function () {
	var name = $(this).attr("name");
	var value = $(this).attr("value");
	var rail = $('#commnetivity').attr("rail");
   	$('div[id=control_panel_status_bar]').removeClass('idle');
   	$('div[id=control_panel_status_bar]').addClass('animated').fadeIn('slow');
   	var id = $('div[selected=selected] a').attr('id');
	$.ajax({
		url: rail+"/update/"+id, cache: false, async: false, type: "POST", data: { vpath: "$vpath", name : name, value : value, rail: rail, id: id}, dataType: "json",
		success: function(XHR){
			if ( XHR.announce != undefined ) {
				$(XHR.announce).fadeIn(300).insertAfter($('#commnetivity'));
				$('div[class=rpc_msg_warn], div[class=rpc_msg_ok], div[class=rpc_msg_error]').delay(2000).animate({"top":"-=80px"},1500).animate({"top":"-=0px"},1000).animate({"opacity":"0"},700);
			}
			if ( XHR.display != undefined ) {
				$('div[id=control_panel_window]').html(response.display);
			}
			if ( XHR.leftnav != undefined ) {
				$.ajax({
					url: XHR.leftnav, dataType: 'json', type: 'GET', data: {xhr: true}, async: true,
					success: function(XHR) {
						if ( XHR.display != undefined ) { $('div[id=commnetivity_leftnav]').html(XHR.display); }
						if ( XHR.javascript != undefined ) { eval(XHR.javascript); }
						if ( XHR.javascript != undefined ) { $('#commnetivity_preview').show(); } else { $('#commnetivity_preview').hide(); }
					}, error: function(XMLHttpRequest, textStatus, errorThrown) {
						alert("There was an error fetching a replacement panel.");
					}
				});
			}
			if ( XHR.screen != undefined ) {
				$.ajax({
					url: XHR.screen, dataType: 'json', data: {xhr: true}, cache: false, async: false,
					success: function(XHR) {
						$('div[id=control_panel_window]').slideUp('fast').html(XHR.display).slideDown('slow');
					}, error: function(XMLHttpRequest, textStatus, errorThrown) {
						alert("There was an error fetching a replacement panel.");
					}
				});
			}
			if ( XHR.javascript != undefined ) { eval(XHR.javascript); } // Eval the JS.
			if ( XHR.window != undefined ) { $('div[id=control_panel_window]').html(XHR.display); }
			var tabs = $('div[id=control_panel_window] ul[id=control_panel_subnav_tabs]').html();
			if ( tabs != undefined ) {
				$('div[id=control_panel_window] ul[id=control_panel_subnav_tabs]').remove();
				$('div[id=control_panel_status_bar] ul[id=control_panel_subnav_tabs]').fadeOut(300, function() {
				$('div[id=control_panel_status_bar] ul[id=control_panel_subnav_tabs]').html(tabs).fadeIn(300);
			});
		}
		statusBarOff();
		}, error: function(XMLHttpRequest, textStatus, errorThrown) {
			$('<div class="rpc_msg_err">'+textStatus+' : '+errorThrown+'</div>').fadeIn(300).insertAfter($('body')).delay(2000).animate({"top":"-=80px"},1500).animate({"top":"-=0px"},1000).animate({"opacity":"0"},700);
		}
	});
	statusBarOff();
	return false;
});

$("#control_panel_window textarea" ).live('keyup', function (e) { if(e.keyCode == 13) { return false; } });
var uploader = new qq.FileUploader({
    element: document.getElementById('hotdrop'),
    action: '/commnetivity/upload',
    sizeLimit: 524288000,
    minSizeLimit: 0,
	allowedExtensions: ["jpg", "jpeg", "xml", "bmp", "gif", "png", "mp3", "ogg", "avi", "flv", "mov", "mp4", "mpeg", "tiff", "eps", "otf", "doc", "docx", "xls", "xlsx", "odt", "pdf", "zip", "rar", "gzip", "tar", "gz", "wmv", "m2ts"]
});
$('div[class=qq-upload-drop-area]').live('click', function(){ $(this).hide(); });
		
$('div[id=commnetivity_leftnav_search]').change(function(){return false;}).keyup(function() {
	var query = $('input[name=commnetivity_leftnav_search]').val();
	// get screen from rail and then use a library to format the query accordingly... such as audio/video/recipes, etc.
});

if ( $('div[id=commnetivity]').is(':visible')){
	$("body").click(function () {
		//alert("body of doc clicked");
    });
}
$('a[id=cmspreviewx]').hide();



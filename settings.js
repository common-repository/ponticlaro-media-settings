jQuery(function($) {
	$('.media-item a.describe-toggle-off').after('<a href="#" class="describe-toggle-on insert-quick-link">Insert</a>');
	$('.media-item .insert-quick-link').click(function() {
		$(this).parent().find('.savesend input[type=submit]').click();
		return false;
	});

return;
	$('.savesend').append('<span style="position:absolute;right:10px;" class="pt-prefs"><label ><input type="checkbox" class="insertshortcode" name="insertshortcode" id="insertshortcode" value="1" /> Insert shortcode</label> &nbsp; <label ><input type="checkbox" class="keepwindowopen" name="keepwindowopen" id="keepwindowopen" value="1" /> Keep open</label></span>')
	$('.savesend .pt-prefs input[type=checkbox]').click(function() {
		var today = new Date();
		var date = new Date(today.getTime() + 864000)
		var id = $(this).attr('id');
		if ($(this).attr('checked')) {
			document.cookie = id+"=1;path=/;expires=" + date.toGMTString();
		} else {
			document.cookie = id+"=0;path=/;expires=Thu, 01-Jan-1970 00:00:01 GMT";
		}
		update_pt_prefs()
	});
	update_pt_prefs();
});

function update_pt_prefs() {
	jQuery(['keepwindowopen', 'insertshortcode']).each(function(i, pref) {
		if (document.cookie.indexOf(pref) >= 0) {
			jQuery('.savesend .pt-prefs input.'+pref).attr('checked', true);
		} else {
			jQuery('.savesend .pt-prefs input.'+pref).attr('checked', false);
		}
	});
}

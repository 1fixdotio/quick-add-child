(function ( $ ) {
	"use strict";

	var post_ID = $('#post_ID').val(),
		parent_id = ( '' == $('#parent_id').val() || ! $('#parent_id').length ) ? 0 : $('#parent_id').val(),
		addNew = $('.add-new-h2'),
		html = '';

	html += ' <a id="add_new_sibling" href="post-new.php?post_type=' + typenow + '&parent_id=' + parent_id + '" class="add-new-h2" target="_blank">' + quick_add_child_js_params.add_new_sibling + '</a>';
	html += '<a id="add_new_child" href="post-new.php?post_type=' + typenow + '&parent_id=' + post_ID + '" class="add-new-h2" target="_blank">' + quick_add_child_js_params.add_new_child + '</a>';

	$(html).insertAfter(addNew);

	if ( quick_add_child_js_params.hide_add_new == 'on' )
		addNew.hide();

}(jQuery));
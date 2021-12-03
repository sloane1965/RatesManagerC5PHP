function styleButtons(el) {
	el.addClass('hide');
	el.siblings('button.sib').toggleClass('hide');
	el.siblings
}

function editTable(el) {
	// First, get current values of the current line (wherever "Edit" was clicked) 
	var td_parent = el.closest('.button_container');
	var full_item_arr = {}; // This will store all item values before they are edited.
	
	var editable_content = td_parent.siblings('.editable').each(function() {
		var item_type = $(this).attr('data-item-type'); // First grab the item type
		var current_val = $(this).html(); // Then the current value of the item
		
		full_item_arr[item_type] = current_val;
		
		// Second, show inputs on each editable td and add a placeholder with current value
		$(this).children('input').toggleClass('hide');
		$(this).children('p').toggleClass('hide');
	});
}

$(".edit_table_button, .stop_edit_button").on('click', function() {
	var el = $(this);
	
	styleButtons(el);
	return editTable(el);
});

edit_table_button
update_table_button
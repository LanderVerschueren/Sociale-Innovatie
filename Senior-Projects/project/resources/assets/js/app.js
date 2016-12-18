$('.card_choice_link').click(function(event) {
	event.preventDefault();
	console.log(this);

	var choice_name = $(this).attr('href');
	var id = "[id='" + choice_name + "']";
	var checkBoxes = $("input[name='" + choice_name + "']");

	console.log(checkBoxes);

	$(id).toggleClass('card_choice_selected');
	checkBoxes.prop("checked", !checkBoxes.prop("checked"));
});

$( "#sortable" ).sortable({
    placeholder: "card_choice_order_drag",
    tolerance: "pointer",
    update: function( event, ui ) {
    	
   	}
});
$( "#sortable" ).disableSelection();

$('.card_choice_order_link').click(function(event) {
	event.preventDefault();
});
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
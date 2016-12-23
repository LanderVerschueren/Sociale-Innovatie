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
    tolerance: "pointer"
    /*,
    update: function( event, ui ) {
    	var order = []; 
                //loop trought each li...
                $('#sortable div').each( function(e) {

               //add each li position to the array...     
               // the +1 is for make it start from 1 instead of 0
               order.push( $(this).attr('id') );
           });
              // join the array as single variable...
              //var positions = order.join(';')
               //use the variable as you need!
               console.log(order);
   	}*/
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

 $('#modal1').modal({
    in_duration: 250,
    out_duration: 150,
    ending_top: '15%'
 });

require('./bootstrap');
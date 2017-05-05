$('#datepicker').datepicker();

$('.card_choice_link_admin').click(function(event) {
  event.preventDefault();
});

$('.card_choice_link').click(function(event) {
	event.preventDefault();
	console.log(this);

	var choice_name = $(this).attr('href');
	var id = "[id='" + choice_name + "']";
	var checkBoxes = $("input[name='" + choice_name + "']");

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
 

require('./bootstrap');

$(function() {
    $('#descriptionModal').on("show.bs.modal", function (e) {
        console.log($(e.relatedTarget).data('title'));
        $("#descriptionModalLabel").html($(e.relatedTarget).data('title'));
        $("#descriptionModalParagraph").html($(e.relatedTarget).data('description'));
    });

    $('#editModal').on("show.bs.modal", function (e) {
        console.log($(e.relatedTarget).data());
        $("#editModalLabel").html($(e.relatedTarget).data('title'));
        $("#editModalParagraph").html($(e.relatedTarget).data('description'));
        $('#editModalNameInput').val($(e.relatedTarget).data('title'));
        $('#editModalNameLabel').addClass('active');
        $('#editModalStartdateInput').val($(e.relatedTarget).data('start'));
        $('#editModalEnddateInput').val($(e.relatedTarget).data('end'));
    });

    $('#deleteModal').on("show.bs.modal", function (e) {
        console.log($(e.relatedTarget).data('title'));
        $("#deleteModalLabel").html($(e.relatedTarget).data('title'));
        $("#deleteModalParagraph").html($(e.relatedTarget).data('description'));
        $("#deleteChoice").attr('href', '/deleteChoice/' + $(e.relatedTarget).data('id'));
    });

    $('#editChoice').on("show.bs.modal", function (e) {
        console.log($(e.relatedTarget).data('title'));
        $("#choiceId").val($(e.relatedTarget).data('id'));
        $("#choiceName").val($(e.relatedTarget).data('title'));
        $("#choiceDescription").val($(e.relatedTarget).data('description'));
        $("#choiceMin").val($(e.relatedTarget).data('min'));
        $("#choiceMax").val($(e.relatedTarget).data('max'));
        $.ajax({
            type: 'GET',
            url: "/choiceCheck",
            data: {id: $(e.relatedTarget).data('id')},
            success: function (response) {
                response.forEach(myFunction);
            }
        });
    });

    $('#editChoice').on("hide.bs.modal", function (e) {
        location.reload();
    });

    function myFunction(item, index, arr) {
        $("#group"+item[0]).prop("checked", true);
        console.log(item[0]);
    }
});



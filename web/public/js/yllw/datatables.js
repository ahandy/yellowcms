$(document).ready(function() {
	var datatable = $(".datatable");
	if(datatable.length) {
		datatable.dataTable({
   			"sPaginationType": "full_numbers",
   			"aaSorting": [],	 
        	"sScrollX": "100%",
        	"bScrollCollapse": true,
        	"bPaginate": false,
        	"aoColumnDefs": [
            { "sWidth": "10%", "aTargets": [ -1 ] }
            ]
		});

		var processHref = datatable.attr('rel');
		if(typeof processHref === "undefined" || processHref.length < 1) {
			console.error("Please supply a rel attribute to the datatable.");
			return;
		}

		// $('td', datatable).click(function() {
		// 	var id = $(this).parent('tr').attr('data-id');
		// 	if(typeof id === "undefined" || id.length < 1) {
		// 		console.error("Row does not have a valid id.");
		// 	}

		// 	var column = $(this).attr('data-column');
		// 	if(typeof column === "undefined" || column.length < 1) {
		// 		console.error("td does not have a valid column name.");
		// 	}

		// 	processHref += "/" + id + "/" + column;

		// 	var th = $("th", datatable).eq($(this).index());
		// 	var type = th.attr('data-type');

		// 	$(this).editable(processHref, {
		// 		type: type,
		// 		submit    : 'Update'
		// 	});
		// })
	}
})
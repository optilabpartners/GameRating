$(document).ready(function() {
	var selected = [];
	var table = $('#games').DataTable({
		ajax: {
			url: ajaxurl+'?action=games',
		},
		lengthMenu: [ 20, 30, 50, 70, 100 ],
		ordering: false,
		searching: false,
		deferRender: true,
		serverSide: true,
		dataSrc: 'data',
		columns: [
			{ data: 'id' },
			{ data: 'game_id' },
			{ data: 'game_url_code' },
			{ data: 'game_date' },
			{ data: 'season' },
			{ data: 'hteam' },
			{ data: 'vteam' },
			{ data: 'buzzer_beater' },
			{ data: 'overtime' },
		],
		rowId: 'id',
		rowCallback: function( row, data) {
            if ( $.inArray(data.id, selected) !== -1 ) {
                $(row).addClass('selected');
            }
        }
	});

	$('#games tbody').on('click', 'tr', function () {
        var id = this.id;
        var index = $.inArray(id, selected);
        if ( index === -1 ) {
            selected.push( id );
        } else {
            selected.splice( index, 1 );
        }
        $(this).toggleClass('selected');
    } );

	table.on( 'draw', function () {
		$('#importButton').show();
	} );

	$('#importButton').on('click', function() {
		console.log(table.rows('.selected'));
		$.ajax({
			url: ajaxurl+'?action=game',
			type: 'PUT',
			data: JSON.stringify(selected),
			success: function(response) {
				response = JSON.parse(response);
				if (response.result == 0) {
					alert(response.message);
				} else {
			    	table.rows('.selected').remove().draw();
					if (response.message != undefined) {
						alert(response.message);
					}
				}
			}
		});
		selected = [];
	})

});
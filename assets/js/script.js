(function( $ ){

	let debounceTimeout;

	$(document).ready(function(){
		$("input[type=file]").on('change', function(input){
			let inst = $(this).parent('.custom-file-upload').children('[type="hidden"]').val()
			let name = $(this).parent('.custom-file-upload').children('span')
			if (this.files && this.files[0]) {   
		        name.html(this.files[0].name)
		    }else{
		    	name.html(inst)
		    }
		})

		pTable = $("#projects_table").DataTable({
	  		"bPaginate": false,
	    	"bLengthChange": false,
	    	"bFilter": true,
	    	"bInfo": false,
	    	"bAutoWidth": false,
	    	language: {
	        	searchPlaceholder: "Search"
	    	},
	    	"aoColumnDefs" : [ {
			    "bSortable" : false,
			    "aTargets" : [ "sort_disabled" ]
			} ]
	  	});
	  	stacksTable = $("#stacks").DataTable({
	  		"bPaginate": false,
	    	"bLengthChange": false,
	    	"bFilter": true,
	    	"bInfo": false,
	    	"bAutoWidth": false,
	    	language: {
	        	searchPlaceholder: "Search"
	    	},
	    	"aoColumnDefs" : [ {
			    "bSortable" : false,
			    "aTargets" : [ "sort_disabled" ]
			} ]
	  	}); 
	  	sTable = $("#stack_table").DataTable({
	  		"bPaginate": false,
	    	"bLengthChange": false,
	    	"bFilter": true,
	    	"bInfo": false,
	    	"bAutoWidth": false,
	    	language: {
	        	searchPlaceholder: "Search"
	    	},
	    	"aoColumnDefs" : [ {
			    "bSortable" : false,
			    "aTargets" : [ "sort_disabled" ]
			} ]
	  	});
	  	eTable = $("#export_table").DataTable({
	  		"bPaginate": false,
	    	"bLengthChange": false,
	    	"bFilter": true,
	    	"bInfo": false,
	    	"bAutoWidth": false,
	    	language: {
	        	searchPlaceholder: "Search"
	    	},
	    	"aoColumnDefs" : [ {
			    "bSortable" : false,
			    "aTargets" : [ "sort_disabled" ]
			} ]
	  	});
	  	evTable = $("#event_table").DataTable({
	  		"bPaginate": false,
	    	"bLengthChange": false,
	    	"bFilter": true,
	    	"bInfo": false,
	    	"bAutoWidth": false,
	    	language: {
	        	searchPlaceholder: "Search"
	    	},
	    	"aoColumnDefs" : [ {
			    "bSortable" : false,
			    "aTargets" : [ "sort_disabled" ]
			} ]
	  	});

		$("input[name=projects_search]").on('keyup', function(){
			pTable.search($(this).val()).draw()
		})
		$("input[name=event_filter]").on('keyup', function(){
			
			evTable.search($(this).val()).draw()
		})
		$('input[name=event_filter]').on('input', function(e) {
			console.log(this.value)
		  	if('' == this.value) {
		    	evTable.search($(this).val()).draw()
		  	}
		});
		// $("input[name=action_filter]").on('keyup', function(){
		// 	sTable.search($(this).val()).draw()
		// })
		$(".select-tab").on('click', function(){
			let tab_id = $(this).attr('data-tab_id');
			$('.tabs .tab').removeClass('active');
			$('#tab_'+tab_id).addClass('active');
			$('.buttons .tab_buton').addClass('disabled');
			$('.buttons .tab_buton[data-tab_id="'+tab_id+'"]').removeClass('disabled');
		})

		// $(document).ready(function() {
		//   	$("#projects_table").DataTable({
		//   		"bPaginate": false,
		//     	"bLengthChange": false,
		//     	"bFilter": true,
		//     	"bInfo": false,
		//     	"bAutoWidth": false,
		//     	language: {
		//         	searchPlaceholder: "Search"
		//     	},
		//     	"aoColumnDefs" : [ {
		// 		    "bSortable" : false,
		// 		    "aTargets" : [ "sort_disabled" ]
		// 		} ]
		//   	});
		// });

		$(document).on('click',".drop-down_menu_show", function(){
			$(this).closest('td').find('ul').show()
		})
		$(document).on('click','.drop-down_menu ul .delete', function () {
		  $('#exampleModalCenter_delete').modal('show');
		  $('#exampleModalCenter_delete').attr('data-id',$(this).attr('data-id'))
		  $(this).closest('ul').hide()
		})
		$(document).on('click','.drop-down_menu ul .delete_export', function () {
		  $('#exampleModalCenter_delete_export').modal('show');
		  $('#exampleModalCenter_delete_export').attr('data-id',$(this).attr('data-id'))
		  $(this).closest('ul').hide()
		})
		$(document).on('click','.drop-down_menu ul .copy_export', function () {
		  $('#exampleModalCenter_copy_export').modal('show');
		  $('#exampleModalCenter_copy_export').attr('data-id',$(this).attr('data-id'))
		  $(this).closest('ul').hide()
		})
		$(document).on('click','.drop-down_menu ul .copy', function () {
		  $('#exampleModalCenter_copy').modal('show');
		  $('#exampleModalCenter_copy').attr('data-id',$(this).attr('data-id'))
		  $(this).closest('ul').hide()
		})
		$(document).on('click','.modal .close', function () {
		  $(this).closest('.modal').modal('hide');
		})
		$(document).on('click','.modal .close_btn', function () {
		  $(this).closest('.modal').modal('hide');
		})
		$(document).on('click','.modal .close_btn', function () {
		  $(this).closest('.modal').modal('hide');
		})


		$(document).on('click','#exampleModalCenter_delete .yes_btn', function (e) {
		  	e.preventDefault()
	      	let data = new FormData()
	      	let modal = $(this).closest('.modal')
	      	data.append('action', 'timeshow_delete_project')
	      	data.append('project_id', $(this).closest('.modal').attr('data-id'))
	      	modal.modal('hide')

	      	$.ajax({
	        	url:         timeshow_ajax.url,
	        	type:        'POST',
	        	cache:        false,
	        	processData: false,
	        	contentType: false,
	        	dataType: "json",
	        	data:        data,
	        	beforeSend:  function () {
	          		$('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
	        	success:     function (response) {
	 				$('.backmodal').remove()
	 				pTable.clear().draw();	
	 				pTable.rows.add(response.data);
	 				pTable.columns.adjust().draw();
	 				$('.btn-next').addClass('disabled');
	        	},
	      	})
		})

		$(document).on('click','#exampleModalCenter_copy .yes_btn', function (e) {
		  	e.preventDefault()
	      	let data = new FormData()
	      	let modal = $(this).closest('.modal')
	      	data.append('action', 'timeshow_copy_project')
	      	data.append('project_id', $(this).closest('.modal').attr('data-id'))
	      	modal.modal('hide');

	      	$.ajax({
	        	url:         timeshow_ajax.url,
	        	type:        'POST',
	        	cache:        false,
	        	processData: false,
	        	contentType: false,
	        	dataType: "json",
	        	data:        data,
	        	beforeSend:  function () {
	          		$('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
	        	success:     function (response) {
	 				$('.backmodal').remove()
	 				pTable.clear().draw();	
	 				pTable.rows.add(response.data);
	 				pTable.columns.adjust().draw();
	 				$('.btn-next').addClass('disabled');
	        	},
	      	})
		})
// ai_docs
		$(document).on('click','#exampleModalCenter_delete_export .yes_btn', function (e) {
		  	e.preventDefault()
	      	let data = new FormData()
	      	let modal = $(this).closest('.modal')
	      	let project_id = $('#tab_4').attr('data-project_id')
	      	data.append('action', 'timeshow_delete_export')
	      	data.append('project_id', project_id)
	      	data.append('export_id', $(this).closest('.modal').attr('data-id'))
	      	modal.modal('hide')

	      	$.ajax({
	        	url:         timeshow_ajax.url,
	        	type:        'POST',
	        	cache:        false,
	        	processData: false,
	        	contentType: false,
	        	dataType: "json",
	        	data:        data,
	        	beforeSend:  function () {
	          		$('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
	        	success:     function (response) {
	 				$('.backmodal').remove()
	 				eTable.clear().draw();	
	 				eTable.rows.add(response.data);
	 				eTable.columns.adjust().draw();
	 			
	        	},
	      	})
		})

		$(document).on('click','#exampleModalCenter_copy_export .yes_btn', function (e) {
		  	e.preventDefault()
	      	let data = new FormData()
	      	let modal = $(this).closest('.modal')
	      	data.append('action', 'timeshow_copy_export')
	      	data.append('project_id', $(this).closest('.modal').attr('data-id'))
	      	modal.modal('hide');

	      	$.ajax({
	        	url:         timeshow_ajax.url,
	        	type:        'POST',
	        	cache:        false,
	        	processData: false,
	        	contentType: false,
	        	dataType: "json",
	        	data:        data,
	        	beforeSend:  function () {
	          		$('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
	        	success:     function (response) {
	 				$('.backmodal').remove()
	 				eTable.clear().draw();	
	 				eTable.rows.add(response.data);
	 				eTable.columns.adjust().draw();
	 			
	        	},
	      	})
		})

		$(document).on('click','.import_stacks', function (e) {

			e.preventDefault()
			let project_id = $('.tab').attr('data-project_id')
	      	let data = new FormData()
	      	data.append('action', 'import_stacks')
	      	data.append('file', $('[name="stacks"]')[0].files[0])
	      	data.append('project_id', project_id)
	      	// data.append('item_key', cart_item_key)

	      	$.ajax({
	        	url:         timeshow_ajax.url,
	        	type:        'POST',
	        	cache:        false,
	        	processData: false,
	        	contentType: false,
        		dataType: "json",
	        	data:        data,
	        	beforeSend:  function () {
	          		// $('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
	        	success:     function (response) {
	 				$('.backmodal').remove()
	 				stacksTable.clear().draw();	
	 				stacksTable.rows.add(response.data_stacks);
	 				stacksTable.columns.adjust().draw();
	 		
	        	},
	      	})
		})

		$(document).on('change','#projects_table [type="checkbox"]', function (e) {
			$('#projects_table [type="checkbox"]').prop('checked', false);
			$(this).prop('checked', true);
			$('.btn-next').removeClass('disabled');
			let project_id = $(this).find('[type="checkbox"]').attr('data-id');
			$('.load_event').attr('data-project_id', project_id)
			$('.export_content').attr('data-project_id', project_id)
		})

		$(document).on('click','#projects_table tbody tr', function (e) {
			$('#projects_table [type="checkbox"]').prop('checked', false);
			$(this).find('[type="checkbox"]').prop('checked', true);
			$('.btn-next').removeClass('disabled');
			let project_id =$(this).find('[type="checkbox"]').attr('data-id');
			$('.load_event').attr('data-project_id', project_id)
			$('.stack_waiting_btn').addClass('disabled');
			$('.stack_waiting_text').addClass('active');

			let data = new FormData()
	      	data.append('action', 'load_stacks')
	      	data.append('project_id', project_id)
	      	$('.stack_waiting_text').append('<div class="backmodal"><div class="two"></div></div>')

	      	$.ajax({
	        	url:         timeshow_ajax.url,
	        	type:        'POST',
	        	cache:        false,
	        	processData: false,
	        	contentType: false,
        		dataType: "json",
	        	data:        data,
	        	beforeSend:  function () {
	          		// $('.stack_waiting_text').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
	        	success:     function (response) {
	 				$('.backmodal').remove()
	 				$('.stack_waiting_btn').removeClass('disabled');
	 				$('.stack_waiting_text').removeClass('active');
	 				stacksTable.clear().draw();	
	 				stacksTable.rows.add(response.data);
	 				stacksTable.columns.adjust().draw();
	 				// sTable.clear().draw();	
	 				// sTable.rows.add(response.data_stacks);
	 				// sTable.columns.adjust().draw();
	        	},
	      	})
		})

		$(document).on('click', '.load_selected_stack', function(){
			$('#projects_table tbody input:checked').trigger('click');
		})

		$(document).on('click','#stacks tbody tr', function (e) {
			$('#stacks [type="checkbox"]').prop('checked', false);
			$(this).find('[type="checkbox"]').prop('checked', true);
		})

		$(document).on('click','.load_event', function (e) {
			e.preventDefault()
			let project_id = $(this).attr('data-project_id');
			$('.event_generate').attr('data-project_id',project_id)
			$('.load_stack').attr('data-project_id',project_id)
			$('.tab').attr('data-project_id',project_id)

			let data = new FormData()
	      	data.append('action', 'get_timeshow_project')
	      	data.append('project_id', project_id)

	      	$.ajax({
	        	url:         timeshow_ajax.url,
	        	type:        'POST',
	        	cache:        false,
	        	processData: false,
	        	contentType: false,
        		dataType: "json",
	        	data:        data,
	        	beforeSend:  function () {
	          		// $('.stack_waiting_text').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
	        	success:     function (response) {
	 				// $('.backmodal').remove()
	 				$('[name="event_timeshow_name"]').val(response.project.timeshow_name);
	 				$('[name="event_song_lable"]').val(response.project.song_lable);
	 				$('[name="event_upload_time"]').val(response.project.upload_time);
	 				$('[name="event_audio_file_lable"]').val(response.project.audio_file_lable);
	 				$('[name="event_genre"]').val(response.project.genre);
	 				$('[name="event_speed"]').val(response.project.speed);
	 				$('[name="event_length"]').val(response.project.length);
	 				$('[name="event_artist"]').val(response.project.artist);

	 				$('[name="stack_timeshow_name"]').val(response.project.timeshow_name);
	 				$('[name="stack_song_lable"]').val(response.project.song_lable);
	 				$('[name="stack_upload_time"]').val(response.project.upload_time);
	 				$('[name="stack_audio_file_lable"]').val(response.project.audio_file_lable);
	 				$('[name="stack_genre"]').val(response.project.genre);
	 				$('[name="stack_speed"]').val(response.project.speed);
	 				$('[name="stack_length"]').val(response.project.length);
	 				$('[name="stack_artist"]').val(response.project.artist);
	 				if(response.project.timeshow_note_1){
	 					$('.librosa_img_respon').html('<div><img decoding="async" src="'+response.project.timeshow_note_1+'"></div>')
	 				}else{
	 					$('.librosa_img_respon').html('')
	 				}
	 				$('[name="stack_artist"]').val(response.project.artist);
	        	},
	      	})
		})

		$(document).on('click','.load_stack', function (e) {
			e.preventDefault()
			let project_id = $(this).attr('data-project_id');

			let data = new FormData()
	      	data.append('action', 'get_timeshow_events_stacks')
	      	data.append('project_id', project_id)

	      	$.ajax({
	        	url:         timeshow_ajax.url,
	        	type:        'POST',
	        	cache:        false,
	        	processData: false,
	        	contentType: false,
        		dataType: "json",
	        	data:        data,
	        	beforeSend:  function () {
	          		$('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
	        	success:     function (response) {
	 				$('.backmodal').remove()
	 				evTable.clear().draw();	
	 				evTable.rows.add(response.data);
	 				evTable.columns.adjust().draw();
	 				eTable.clear().draw();	
	 				eTable.rows.add(response.data_exports);
	 				eTable.columns.adjust().draw();
	 				// sTable.clear().draw();	
	 				// sTable.rows.add(response.data_stacks);
	 				// sTable.columns.adjust().draw();
	 				$('#stacks tbody tr:eq(0)').trigger('click')
	 				$('.save_stack').attr('data-stack',response.stack_id)
	        	},
	      	})
		})

		$(document).on('click','.event_generate', function (e) {
			e.preventDefault()
			let project_id = $(this).attr('data-project_id');
			let data = new FormData()
	      	data.append('action', 'save_timeshow_project')
	      	data.append('project_id', project_id)
	      	data.append('timeshow_name', $('[name="event_timeshow_name"]').val())
	      	data.append('speed', $('[name="event_speed"]').val())
	      	data.append('song_lable', $('[name="event_song_lable"]').val())
	      	data.append('artist', $('[name="event_artist"]').val())
	      	data.append('audio_file_lable', $('[name="event_audio_file_lable"]').val())
	      	data.append('upload_time', $('[name="event_upload_time"]').val())
	      	data.append('genre', $('[name="event_genre"]').val())
	      	data.append('length', $('[name="event_length"]').val())


	      	$('[name="stack_timeshow_name"]').val($('[name="event_timeshow_name"]').val());
			$('[name="stack_song_lable"]').val($('[name="event_song_lable"]').val());
			$('[name="stack_upload_time"]').val($('[name="event_upload_time"]').val());
			$('[name="stack_audio_file_lable"]').val($('[name="event_audio_file_lable"]').val());
			$('[name="stack_genre"]').val($('[name="event_genre"]').val());
			$('[name="stack_speed"]').val($('[name="event_speed"]').val());
			$('[name="stack_length"]').val($('[name="event_length"]').val());
			$('[name="stack_artist"]').val($('[name="event_artist"]').val());
	      	$.ajax({
	        	url:         timeshow_ajax.url,
	        	type:        'POST',
	        	cache:        false,
	        	processData: false,
	        	contentType: false,
        		dataType: "json",
	        	data:        data,
	        	beforeSend:  function () {
	          		$('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
	        	success:     function (response) {
	 				$('.backmodal').remove()

	        	},
	      	})
		})

		$(document).on('change','table [type="checkbox"]',function(){
			if ($(this).is(':checked')) {
				$(this).closest('tr').attr('style','background-color: #c7c7c7;')
			}else{
				$(this).closest('tr').attr('style','background-color: transparent;')
			}
		})

		// $(document).on('click','.save_stack', function (e) {
		// 	e.preventDefault()
		// 	let project_id = $(this).attr('data-project_id');
		// 	let data = new FormData()
	    //   	data.append('action', 'save_stack')

	    //   	$.ajax({
	    //     	url:         timeshow_ajax.url,
	    //     	type:        'POST',
	    //     	cache:        false,
	    //     	processData: false,
	    //     	contentType: false,
        // 		dataType: "json",
	    //     	data:        data,
	    //     	beforeSend:  function () {
	    //       		$('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
	    //     	},
	    //     	success:     function (response) {
	 	// 			$('.backmodal').remove()

	    //     	},
	    //   	})
		// })

		$(document).on('keyup','.project_details', function (e) {
			e.preventDefault()
	      	let current = $(this)
	      	saveProjectDetails(current)
		})

		$(document).on('click','.add_to_actions', function (e) {
			e.preventDefault()

			let stack_id = $('#stacks tbody .stack_row:checked').attr('data-id');
			if(typeof stack_id == 'undefined'){

				alert('Plase choose your stack')
				return false
			}
					
	      	let project_id = $(this).closest('.tab').attr('data-project_id')
	      	let event_id = $(this).closest('tr').find('input').attr('data-id')
	      	let data = new FormData()
	      	data.append('action', 'move_to_actions')
	      	data.append('project_id', project_id)
	      	data.append('event_id', event_id)
	      	data.append('stack_id', stack_id)
	      	$.ajax({
                url:         timeshow_ajax.url,
                type:        'POST',
                processData: false,
                contentType: false,
                dataType: "json",
                data:        data,
                beforeSend:  function () {
	          		$('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
                success:     function (response) {
                	$('.backmodal').remove()
                	sTable.clear().draw();	
	 				sTable.rows.add(response.data);
	 				sTable.columns.adjust().draw();
	 				evTable.clear().draw();	
	 				evTable.rows.add(response.events_data);
	 				evTable.columns.adjust().draw();
                }
              })
		})

		$(document).on('click','.add_sel_to_actions', function (e) {
			e.preventDefault()

			let stack_id = $('#stacks tbody .stack_row:checked').attr('data-id');
			if(typeof stack_id == 'undefined'){

				alert('Plase choose your stack')
				return false
			}
					
			let events_ids = [];		
			$( "#event_table tbody tr" ).each(function( index ) {
				let item = $(this).find('[data-id]')
				if(item.is(":checked")){
					events_ids.push(item.attr('data-id'))
				}
			});

			if (events_ids.length == 0) {
			    alert('Plase choose events!')
				return false
			}

	      	let project_id = $(this).closest('.tab').attr('data-project_id')
	      	$('[name="event_filter"]').val('')

	      	let data = new FormData()
	      	data.append('action', 'move_all_sel_to_actions')
	      	data.append('project_id', project_id)
	      	data.append('stack_id', stack_id)
	      	data.append('events_ids', events_ids)
	      	$.ajax({
                url:         timeshow_ajax.url,
                type:        'POST',
                processData: false,
                contentType: false,
                dataType: "json",
                data:        data,
                beforeSend:  function () {
	          		$('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
                success:     function (response) {
                	$('.backmodal').remove()
                	
                	sTable.clear().draw();	
	 				sTable.rows.add(response.data);
	 				sTable.columns.adjust().draw();
	 				evTable.search('').draw();
	 				evTable.clear().draw();	
	 				evTable.rows.add(response.events_data);
	 				evTable.columns.adjust().draw();
	 				$('#tab_3 .select_all').prop('checked',false)
                }
              })
		})

		$(document).on('click','.delete_action', function (e) {
			e.preventDefault()
	      	let action_id = $(this).closest('tr').find('input').attr('data-id')
	      	let stack_id = $('#stacks tbody .stack_row:checked').attr('data-id')
	      	let project_id = $(this).closest('.tab').attr('data-project_id')
	      	let data = new FormData()
	      	data.append('action', 'delete_action')
	      	data.append('action_id', action_id)
	      	data.append('stack_id', stack_id)
	      	data.append('project_id', project_id)
	      	$.ajax({
                url:         timeshow_ajax.url,
                type:        'POST',
                processData: false,
                contentType: false,
                dataType: "json",
                data:        data,
                beforeSend:  function () {
	          		$('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
                success:     function (response) {
                	$('.backmodal').remove()
                	sTable.clear().draw();	
	 				sTable.rows.add(response.data);
	 				sTable.columns.adjust().draw();
	 				evTable.clear().draw();	
	 				evTable.rows.add(response.events_data);
	 				evTable.columns.adjust().draw();
                }
              })
		})

		$(document).on('click','.add_stack', function (e) {
			e.preventDefault()
	      	let project_id = $(this).closest('.tab').attr('data-project_id')
	      	let stack_name = $('[name="stack_name"]').val()
	      	let stack_type = $('[name="stack_type"]').val()
	      	let stack_number = $('[name="stack_number"]').val()
	      	let stack_color = $('[name="stack_color"]').val()
	  		if(stack_name == ''){
	  			alert('Plase enter a stackname')
				return false
	  		}

	      	let data = new FormData()
	      	data.append('action', 'add_stack')
	      	data.append('project_id', project_id)
	      	data.append('stack_name', stack_name)
	      	data.append('stack_type', stack_type)
	      	data.append('stack_number', stack_number)
	      	data.append('stack_color', stack_color)
	      	$.ajax({
                url:         timeshow_ajax.url,
                type:        'POST',
                processData: false,
                contentType: false,
                dataType: "json",
                data:        data,
                beforeSend:  function () {
	          		$('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
                success:     function (response) {
                	$('.backmodal').remove()
                	$('.add_stack').removeClass('active')
	      			$('.save_stack').removeClass('active')
                	stacksTable.clear().draw();	
	 				stacksTable.rows.add(response.data);
	 				stacksTable.columns.adjust().draw();
	 				$('#stacks input[data-id="'+stack_id+'"]').prop('checked', true)
                }
              })
		})

		$(document).on('change','#stacks .stack_row', function (e) {
			e.preventDefault()
			let project_id = $(this).closest('.tab').attr('data-project_id')
	      	let stack_id = $(this).attr('data-id')
	      	let data = new FormData()
	      	data.append('action', 'get_stack')
	      	data.append('stack_id', stack_id)
	      	data.append('project_id', project_id)
	      	$.ajax({
                url:         timeshow_ajax.url,
                type:        'POST',
                processData: false,
                contentType: false,
                dataType: "json",
                data:        data,
                beforeSend:  function () {
	          		// $('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
                success:     function (response) {
                	$('.backmodal').remove()
                	sTable.clear().draw();	
	 				sTable.rows.add(response.data);
	 				sTable.columns.adjust().draw();
	 				evTable.clear().draw();	
	 				evTable.rows.add(response.events_data);
	 				evTable.columns.adjust().draw();
                }
              })
		})

		$(document).on('click','#stacks tbody tr', function (e) {
			e.preventDefault()
			let project_id = $(this).closest('.tab').attr('data-project_id')
	      	let stack_id = $(this).find('.stack_row').attr('data-id')
	      	let data = new FormData()
	      	data.append('action', 'get_stack')
	      	data.append('stack_id', stack_id)
	      	data.append('project_id', project_id)
	      	$.ajax({
                url:         timeshow_ajax.url,
                type:        'POST',
                processData: false,
                contentType: false,
                dataType: "json",
                data:        data,
                beforeSend:  function () {
	          		$('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
                success:     function (response) {
                	$('.backmodal').remove()
                	sTable.clear().draw();	
	 				sTable.rows.add(response.data);
	 				sTable.columns.adjust().draw();
	 				evTable.clear().draw();	
	 				evTable.rows.add(response.events_data);
	 				evTable.columns.adjust().draw();
	 				$('[name="stack_name"]').val(response.stack_name)
	 				$('[name="stack_color"]').val(response.stack_color)
	 				$('[name="stack_number"]').val(response.stack_number)
	 				$('[name="stack_type"] option').prop('selected', false)
	 				$('[name="stack_type"] option[value="'+response.stack_type+'"]').attr('selected', 'selected')
                }
              })
		})

		$(document).on('click','.save_stack', function (e) {
			e.preventDefault()
	      	let stack_id = $('#stacks tbody .stack_row:checked').attr('data-id');
	      	let project_id = $(this).closest('.tab').attr('data-project_id')
	      	let data = new FormData()
	      	data.append('action', 'save_stack')
	      	data.append('stack_id', stack_id)
	      	data.append('stack_number', $('[name="stack_number"]').val())
	      	data.append('stack_color', $('[name="stack_color"]').val())
	      	data.append('stack_type', $('[name="stack_type"]').val())
	      	data.append('stack_name', $('[name="stack_name"]').val())
	      	data.append('project_id', project_id)
	      	$.ajax({
                url:         timeshow_ajax.url,
                type:        'POST',
                processData: false,
                contentType: false,
                dataType: "json",
                data:        data,
                beforeSend:  function () {
	          		// $('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
                success:     function (response) {
                	$('.backmodal').remove()
                	if(response.error){
                		alert(response.error)
                	}else{

	    				stacksTable.clear().draw();	
		 				stacksTable.rows.add(response.data);
		 				stacksTable.columns.adjust().draw();
		 				$('.add_stack').removeClass('active')
		      			$('.save_stack').removeClass('active')	
		 				$('#stacks [data-id="'+stack_id+'"]').prop('checked', true)
                	}
                	
                }
              })
		})

		$(document).on('click','.delete_stack', function (e) {
			e.preventDefault()
	      	let stack_id = $('#stacks tbody .stack_row:checked').attr('data-id');

	      	let data = new FormData()
	      	data.append('action', 'delete_stack')
	      	data.append('stack_id', stack_id)
	      	data.append('project_id', $(this).closest('.tab').attr('data-project_id'))

	      	$.ajax({
                url:         timeshow_ajax.url,
                type:        'POST',
                processData: false,
                contentType: false,
                dataType: "json",
                data:        data,
                beforeSend:  function () {
	          		$('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
                success:     function (response) {
                	$('.backmodal').remove()
    				stacksTable.clear().draw();	
	 				stacksTable.rows.add(response.data);
	 				stacksTable.columns.adjust().draw();
	 				$('#stack_table tbody').html('')
	 				$('#stacks input[data-id="'+stack_id+'"]').prop('checked', true)
                }
              })
		})

		$(document).on('change','[name="select_field"]', function (e) {
			let value = $(this).val()
		
			if(value == 'actions_type'){
				
				let data = new FormData()
				data.append('action', 'update_action_menu')
				data.append('stack_type', $('#stacks tbody .stack_row:checked').closest('tr').find('.stack_type').html())
				$.ajax({
	                url:         timeshow_ajax.url,
	                type:        'POST',
	                processData: false,
	                contentType: false,
	                dataType: "json",
	                data:        data,
	                beforeSend:  function () {
		          		// $('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
		        	},
	                success:     function (response) {
	                	$('.backmodal').remove()
	                	$('[name="select_action"]').html(response.data)
	    				$('[name="bulk_action"]').removeClass('active')
	    				$('.delete_actions').removeClass('active')
						$('[name="select_action"]').addClass('active')
	                }
	            })
			}else if(value == 'action_delete'){
				$('.delete_actions').addClass('active')
				$('[name="bulk_action"]').removeClass('active')
				$('[name="select_action"]').removeClass('active')
			}else{
				$('.delete_actions').removeClass('active')
				$('[name="bulk_action"]').addClass('active')
				$('[name="select_action"]').removeClass('active')
			}
		});

		$(document).on('keyup','[name="bulk_action"]', function (e) {
			e.preventDefault()

			clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(function () {

		      	let stack_id = $('#stacks tbody .stack_row:checked').attr('data-id');

		      	let actions_ids = [];		
				$( "#stack_table tbody tr" ).each(function( index ) {
					let item = $(this).find('[data-id]')
					if(item.is(":checked")){
						actions_ids.push(item.attr('data-id'))
					}
				});

				if (actions_ids.length == 0) {
				    alert('Plase choose actions!')
				    $('[name="bulk_action"]').val('')
					return false
				}

		      	let data = new FormData()
		      	data.append('action', 'udate_actions')
		      	data.append('stack_id', $('#stacks tbody .stack_row:checked').attr('data-id'))
		      	data.append('project_id', $(this).closest('.tab').attr('data-project_id'))
		      	data.append('select_field', $('[name="select_field"]').val())
		      	data.append('bulk_action', $('[name="bulk_action"]').val())
		      	data.append('actions_ids', actions_ids)

		      	$.ajax({
	                url:         timeshow_ajax.url,
	                type:        'POST',
	                processData: false,
	                contentType: false,
	                dataType: "json",
	                data:        data,
	                beforeSend:  function () {
		          		$('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
		        	},
	                success:     function (response) {
	                	$('.backmodal').remove()
	                	$('[name="bulk_action"]').val('')
	    				sTable.clear().draw();	
		 				sTable.rows.add(response.data);
		 				sTable.columns.adjust().draw();
		 				$('#tab_3 .select_all').prop('checked',false)
	                }
	              })
	      	}, 500);
		})

		$(document).on('click','.delete_actions',function(e){
			e.preventDefault()

	      	let stack_id = $('#stacks tbody .stack_row:checked').attr('data-id');

	      	let actions_ids = [];		
			$( "#stack_table tbody tr" ).each(function( index ) {
				let item = $(this).find('[data-id]')
				if(item.is(":checked")){
					actions_ids.push(item.attr('data-id'))
				}
			});

			if (actions_ids.length == 0) {
			    alert('Plase choose actions!')
			    $('[name="bulk_action"]').val('')
				return false
			}

	      	let data = new FormData()
	      	data.append('action', 'udate_actions')
	      	data.append('stack_id', $('#stacks tbody .stack_row:checked').attr('data-id'))
	      	data.append('project_id', $('[name="select_action"]').closest('.tab').attr('data-project_id'))
	      	data.append('select_field', $('[name="select_field"]').val())
	      	data.append('bulk_action', 'action_delete')
	      	data.append('actions_ids', actions_ids)

	      	$.ajax({
	            url:         timeshow_ajax.url,
	            type:        'POST',
	            processData: false,
	            contentType: false,
	            dataType: "json",
	            data:        data,
	            beforeSend:  function () {
	          		$('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
	            success:     function (response) {
	            	$('.backmodal').remove()
	            	$('[name="bulk_action"]').val('')
					sTable.clear().draw();	
	 				sTable.rows.add(response.data);
	 				sTable.columns.adjust().draw();
	 				evTable.clear().draw();	
	 				evTable.rows.add(response.events_data);
	 				evTable.columns.adjust().draw();
	 				$('#tab_3 .select_all').prop('checked',false)
	            }
	          })
	      	
		})

		$(document).on('change','[name="select_action"]', function (e) {
			e.preventDefault()

			clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(function () {

		      	let stack_id = $('#stacks tbody .stack_row:checked').attr('data-id');

		      	let actions_ids = [];		
				$( "#stack_table tbody tr" ).each(function( index ) {
					let item = $(this).find('[data-id]')
					if(item.is(":checked")){
						actions_ids.push(item.attr('data-id'))
					}
				});

				if (actions_ids.length == 0) {
				    alert('Plase choose actions!')
				    $('[name="bulk_action"]').val('')
					return false
				}

		      	let data = new FormData()
		      	data.append('action', 'udate_actions')
		      	data.append('stack_id', $('#stacks tbody .stack_row:checked').attr('data-id'))
		      	data.append('project_id', $('[name="select_action"]').closest('.tab').attr('data-project_id'))
		      	data.append('select_field', $('[name="select_field"]').val())
		      	data.append('bulk_action', $('[name="select_action"]').val())
		      	data.append('actions_ids', actions_ids)

		      	$.ajax({
	                url:         timeshow_ajax.url,
	                type:        'POST',
	                processData: false,
	                contentType: false,
	                dataType: "json",
	                data:        data,
	                beforeSend:  function () {
		          		$('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
		        	},
	                success:     function (response) {
	                	$('.backmodal').remove()
	                	$('[name="bulk_action"]').val('')
	    				sTable.clear().draw();	
		 				sTable.rows.add(response.data);
		 				sTable.columns.adjust().draw();
		 				$('#tab_3 .select_all').prop('checked',false)
	                }
	              })
	      	}, 500);
		})

		$(document).on('keyup','.action_value', function (e) {
			let value = parseFloat($(this).val());
			console.log(value)
			if (!isNaN(value)) {
			   	if(value <= 0){
			   		$(this).val(0)
			   	}
			   	if(value >= 100){
			   		$(this).val(100)
			   	}
			}
		})

		$(document).on('click','.delete_stack', function (e) {
			e.preventDefault()
	      	let stack_id = $('#stacks tbody .stack_row:checked').attr('data-id');

	      	let data = new FormData()
	      	data.append('action', 'delete_stack')
	      	data.append('stack_id', stack_id)
	      	data.append('project_id', $(this).closest('.tab').attr('data-project_id'))

	      	$.ajax({
                url:         timeshow_ajax.url,
                type:        'POST',
                processData: false,
                contentType: false,
                dataType: "json",
                data:        data,
                beforeSend:  function () {
	          		$('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
                success:     function (response) {
                	$('.backmodal').remove()
    				stacksTable.clear().draw();	
	 				stacksTable.rows.add(response.data);
	 				stacksTable.columns.adjust().draw();
	 				// $('#stack_table tbody').html('')
                }
              })
		})

		$(document).on('click','.convert_btn', function (e) {
			e.preventDefault()
	      	let stack_id = $('#stacks tbody .stack_row:checked').attr('data-id');

	      	let data = new FormData()
	      	data.append('action', 'timeshow_add_export')
	      	data.append('project_id', $(this).closest('.tab').attr('data-project_id'))
	      	data.append('export_lable', $('[name="export_lable"]').val())
	      	data.append('timeshow_number', $('[name="timeshow_number"]').val())
	      	data.append('export_status', $('[name="stack_type1"]').val())
	      	data.append('stack_id', $('#stacks tbody .stack_row:checked').attr('data-id'))

	      	$.ajax({
                url:         timeshow_ajax.url,
                type:        'POST',
                processData: false,
                contentType: false,
                dataType: "json",
                data:        data,
                beforeSend:  function () {
	          		$('#timeshow_generator').append('<div class="backmodal"><div class="two"></div></div>')
	        	},
                success:     function (response) {
                	$('.backmodal').remove()
                	if(response.error){
                		alert(response.error)
                	}else{
                		eTable.clear().draw();	
		 				eTable.rows.add(response.data);
		 				eTable.columns.adjust().draw();
                	}
    				
	 				// $('#stack_table tbody').html('')
                }
              })
		})

		$(document).on('change','.select_all', function (e) {
			e.preventDefault()
	     	if($(this).is(':checked')){
	     		$(this).closest('table').find('tr').each(function( index ) {
				  	$(this).find('[type="checkbox"]').prop('checked',true);
				});
	     	}else{
	     		$(this).closest('table').find('tr').each(function( index ) {
				  	$(this).find('[type="checkbox"]').prop('checked',false);
				});
	     	}
	      	
		})

		$(document).on('keyup','.action_item', function (e) {
			e.preventDefault()
	      	let current = $(this)
	      	saveActionItem(current)
		})
		$(document).on('change','.action_item', function (e) {
			e.preventDefault()
	      	let current = $(this)
	      	saveActionItem(current)
		})
		$(document).on('change','.stack_fields input', function (e) {
			e.preventDefault()

	      	$('.add_stack').addClass('active')
	      	$('.save_stack').addClass('active')
		})

		function saveActionItem(current){

            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(function () {

            	let action_id = parseFloat(current.closest('tr').find('.action_id').val())

            	if(action_id < 1){
            		current.closest('tr').find('.action_id').val(1)
            		action_id = 1
            	}

            	if(action_id > 20){
            		current.closest('tr').find('.action_id').val(20)
            		action_id = 20
            	}

                let data = new FormData()
                data.append('action', 'save_action_item')
                data.append('actions_index_id', current.closest('tr').find('[type="checkbox"]').attr('data-id'))
                data.append('action_id', action_id)
                data.append('action_type', current.closest('tr').find('.action_type').val())
                data.append('actions_lable', current.closest('tr').find('.actions_lable').val())
                data.append('action_value', current.closest('tr').find('.action_value').val())
                data.append('stack_id', $('#stacks tbody .stack_row:checked').attr('data-id'))

              $.ajax({
                url:         timeshow_ajax.url,
                type:        'POST',
                data:        data,
                processData: false,
                contentType: false,
                dataType: "json",
                success:     function (response) {
                	if(response.error){
                		alert(response.error);
                	}
                }
              })
            }, 500);
        }

		function saveProjectDetails(current){

            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(function () {
                let data = new FormData()
                data.append('action', 'save_timeshow_project')
                data.append('project_id', current.closest('.tab').attr('data-project_id'))
                data.append('timeshow_name', current.closest('.content_tab').find('[name="stack_timeshow_name"]').val())
                data.append('song_lable', current.closest('.content_tab').find('[name="stack_song_lable"]').val())
                data.append('artist', current.closest('.content_tab').find('[name="stack_artist"]').val())
                data.append('audio_file_lable', current.closest('.content_tab').find('[name="stack_audio_file_lable"]').val())
                data.append('upload_time', current.closest('.content_tab').find('[name="stack_upload_time"]').val())
                data.append('genre', current.closest('.content_tab').find('[name="stack_genre"]').val())
                data.append('speed', current.closest('.content_tab').find('[name="stack_speed"]').val())
                data.append('length', current.closest('.content_tab').find('[name="stack_length"]').val())

              $.ajax({
                url:         timeshow_ajax.url,
                type:        'POST',
                processData: false,
                contentType: false,
                dataType: "json",
                data:        data,
                success:     function (response) {
                	$('[name="event_timeshow_name"]').val(response.timeshow_name)
                    $('[name="event_song_lable"]').val(response.song_lable)
                    $('[name="event_artist"]').val(response.artist)
                    $('[name="event_audio_file_lable"]').val(response.audio_file_lable)
                    $('[name="event_upload_time"]').val(response.upload_time)
                    $('[name="event_genre"]').val(response.genre)
                    $('[name="event_speed"]').val(response.speed)
                    $('[name="event_length"]').val(response.length)
                    $('[name="stack_timeshow_name"]').val(response.timeshow_name)
                    $('[name="stack_song_lable"]').val(response.song_lable)
                    $('[name="stack_artist"]').val(response.artist)
                    $('[name="stack_audio_file_lable"]').val(response.audio_file_lable)
                    $('[name="stack_upload_time"]').val(response.upload_time)
                    $('[name="stack_genre"]').val(response.genre)
                    $('[name="stack_speed"]').val(response.speed)
                    $('[name="stack_length"]').val(response.length)
                }
              })
            }, 500);
        }

		$(document).mouseup(function (e) {
		    var container = $(".drop-down_menu ul");
		    if (container.has(e.target).length === 0){
		        container.hide();
		    }
		});

		const progressBar = $('#progress-bar');
	    const progressContainer = $('#progress-container');
	    const progressCircle = $('#progress-circle');
	    const progressPrcent = $('#progress-text');

	    function startFakeProgress(targetPercent, duration = 3000, callback) {
	        let start = parseFloat(progressBar.css('width')) / progressContainer.width() * 100 || 0;
	        let step = 50; // оновлення кожні 50ms
	        let increment = (targetPercent - start) / (duration / step);

	        $('#progress-container-wrapper').removeClass('hidden');

	        const interval = setInterval(() => {
	            start += increment;
	            if (start >= targetPercent) {
	                start = targetPercent;
	                clearInterval(interval);
	                if (callback) callback();
	            }
	            progressBar.css('width', start + '%');
	            $('#progress-text').text(Math.round(start) + '%');
	        }, step);
	    }

	    function finishProgress() {
	        progressBar.css('width', '100%');
	        $('#progress-text').text('100%');
	        setTimeout(() => {
	        }, 500);
	    }

	    function pollTask(taskId, successCallback) {
	        let formData = new FormData();
	        formData.append('action', 'ai_check_task');
	        formData.append('task_id', taskId);

	        $.ajax({
	            url: timeshow_ajax.url,
	            type: 'POST',
	            data: formData,
	            processData: false,
	            contentType: false,
	            success: function(response) {
	                if(response.success) {
	                    if(response.data.status === 'done') {
	                        // після завершення швидко до 100%
	                        startFakeProgress(100, 500, () => {
	                            finishProgress();
	                            $('.backmodal').remove()
	                            successCallback({ data: { result: response.data.result } });
	                        });
	                    }else if(response.data.status === 'error') {
	                        // після завершення швидко до 100%
	                        startFakeProgress(100, 500, () => {
	                            finishProgress();
	                            $('.backmodal').remove()
	                            successCallback({ data: { result: response.data } });
	                        });
	                    } else {
	                        // просуваємо прогрес трохи до 70-90%
	                        startFakeProgress(70, 1000);
	                        setTimeout(function() {
	                            pollTask(taskId, successCallback);
	                        }, 3000);
	                    }
	                } else {
	                    // $(container).html('<p style="color:red;">Error: ' + response.data + '</p>');
	                    finishProgress();
	                }
	            },
	            error: function(xhr, status, error) {
	                // $(container).html('<p style="color:red;">Error: ' + error + '</p>');
	                finishProgress();
	            }
	        });
	    }

	    function sendAjaxRequest(form, successCallback) {
	      	let data = new FormData()
	      	data.append('action', 'timeshow_create_project_worker')
	      	data.append('projects_name',form.find('[name="project_name"]').val())
	      	let fileInput = form.find('[name="file"]')[0].files[0];
			let mediaInput = form.find('[name="mediafile"]')[0].files[0];

			if (!fileInput && !mediaInput) {
			    alert('Please select a file or media file!');
			    return false; 
			}

			if (fileInput) {
			    data.append('file', fileInput);
			}

			if (mediaInput) {
			    data.append('mediafile', mediaInput);
			}

	        $.ajax({
	            url:         timeshow_ajax.url,
	        	type:        'POST',
	        	cache:        false,
	        	processData: false,
	        	contentType: false,
        		dataType: "json",
	        	data:        data,
	            beforeSend: function() {
	                $('.projects').append('<div class="backmodal"><div class="two"></div></div>')
	                startFakeProgress(30, 600);
	            },
	            success: function(response) {
	                if (response.success) {
	                    let taskId = response.data.task_id;
	                    pollTask(taskId, successCallback);
	                }else if(response.error){

	                } else {
	                    // $(errorContainer).html('<p style="color:red;">Error: ' + response.data + '</p>');
	                    finishProgress();
	                }
	            },
	            error: function(xhr, status, error) {
	                // $(errorContainer).html('<p style="color:red;">Error: ' + error + '</p>');
	                finishProgress();
	            }
	        });
	    }

	    function finishProgress() {
		    $('#progress-bar').css('width', '100%');
		    $('#progress-text').text('100%');
		}

	    $(document).on('submit','.create_project_form', function (e) {
	        e.preventDefault();

	        let form = $(this)
	        $('.progres_load_text').attr('class', 'progres_load_text');

	        sendAjaxRequest(form,
	            function(response) {
					if(response.data.result.status == 'error'){
	            		$('.progres_load_text').addClass('active red')
	            		$('.progres_load_text').html(response.data.result.error);
	            	}else{
	            		$('.progres_load_text').addClass('active green')
	            		$('.progres_load_text').html('Done');
	            		pTable.clear().draw();	
		 				pTable.rows.add(response.data.result.data);
		 				pTable.columns.adjust().draw();
		 				$('.btn-next').addClass('disabled');
	            	}
	            	
	            }
	        );
	    });

	    // $(document).on('submit','.create_project_form', function (e) {

		// 	e.preventDefault()
	    //   	let data = new FormData()
	    //   	data.append('action', 'timeshow_create_project')
	    //   	data.append('projects_name', $(this).find('[name="project_name"]').val())
	    //   	let fileInput = $(this).find('[name="file"]')[0].files[0];
		// 	let mediaInput = $(this).find('[name="mediafile"]')[0].files[0];

		// 	if (!fileInput && !mediaInput) {
		// 	    alert('Please select a file or media file!');
		// 	    return false; 
		// 	}

		// 	if (fileInput) {
		// 	    data.append('file', fileInput);
		// 	}

		// 	if (mediaInput) {
		// 	    data.append('mediafile', mediaInput);
		// 	}
	    //   	// data.append('item_key', cart_item_key)

	    //   	$.ajax({
	    //     	url:         timeshow_ajax.url,
	    //     	type:        'POST',
	    //     	cache:        false,
	    //     	processData: false,
	    //     	contentType: false,
        // 		dataType: "json",
	    //     	data:        data,
	    //     	beforeSend:  function () {
	    //       		$('.projects').append('<div class="backmodal"><div class="two"></div></div>')
	    //     	},
	    //     	success:     function (response) {
	 	// 			$('.backmodal').remove()
	 	// 			pTable.clear().draw();	
	 	// 			pTable.rows.add(response.data);
	 	// 			pTable.columns.adjust().draw();
	 	// 			$('.btn-next').addClass('disabled');
	    //     	},
	    //   	})
		// })

	})
})( jQuery );

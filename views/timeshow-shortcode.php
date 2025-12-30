<?php
$current_user_id = get_current_user_id();
$projects = get_timeshow_projects($current_user_id);

?>
		<div id="timeshow_generator">
			
			<div class="timeshow_generator-tabs content_tab">
				<div class="buttons">
					<a href="javascript:void(0)" data-tab_id="1" class="btn btn-lg btn-tab tab_buton">Selection</a>
					<a href="javascript:void(0)" data-tab_id="2" class="btn btn-lg btn-tab tab_buton disabled">Event Setting</a>
					<a href="javascript:void(0)" data-tab_id="3" class="btn btn-lg btn-tab tab_buton disabled">Stack Configuration</a>
					<a href="javascript:void(0)" data-tab_id="4" class="btn btn-lg btn-tab tab_buton disabled">Export</a>
				</div>
				<div class="credits">
					<span class="btn btn-lg credits">Credits: 60</span>
				</div>
			</div>

			<div class="tabs">

				<div id="tab_1" class="tab active">

					<form class="content_tab create_project_form" action="javascript:void(0)">
						<div class="col-md-12">
						    <label for="project_name" class="form-label">Project Name:</label>
						    <input type="text" class="form-control" required name="project_name" id="project_name" placeholder="My Project" required>
						    <div class="invalid-feedback">invalid-feedback</div>
						</div>

						<div class="upload_files">

							<div class="file_button">
						        <div class="file-search-button">
						          <label for="file-upload" class="custom-file-upload btn">
						            <span>Upload Reaper Marker</span>
						            <input type="hidden" value="Select	your file for upload">
						            <input id="file-upload" name="file" type="file" accept=".csv, text/csv"/>
						          </label>
						          
						        </div>
						    </div>

						    <div class="file_button">
						        <div class="file-search-button">
						          <label for="mediafile-upload" class="custom-file-upload btn">
						            <span>Select	your	Audio	for upload (mp3	/	Wav)</span>
						            <input type="hidden" value="Select	your	Audio	for upload (mp3	/	Wav)">
						            <input id="mediafile-upload" name="mediafile" type="file" accept=".mp3, .wav"/>
						          </label>	          
						        </div>
						    </div>

						    <div class="upload_button">
						    	<input type="submit" class="btn btn-blue" value="UPLOAD">
						    </div>

						</div>

						 <div id="progress-container-wrapper" class="w-full max-w-[800px] mt-4 relative hidden">
		                    <div id="progress-text" class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-sm font-bold text-gray-700 ">0%</div>
		                    <div id="progress-container" class="w-full bg-gray-200 rounded-full h-6">
		                        <div id="progress-bar" class="bg-blue-500 h-6 rounded-full w-0 transition-all duration-300 "></div>
		                        <!-- <div id="progress-circle" class="absolute -top-3 -right-3 w-6 h-6 border-4 border-blue-500 rounded-full animate-spin hidden"></div> -->
		                    </div>
		                </div>
		                <span class="progres_load_text">
		                	
		                </span>

					</form>

					<span class="line"></span>

					<div class="projects content_tab">
							
						<form class="form_projects_search" action="javascript:void(0)">
							<input type="text" class="form-control" name="projects_search" placeholder="Searchs">
						</form>

						<label>Your	Files:</label>
						<div class="tableFixHead">
							<table id="projects_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
							      <thead>
							        <tr>
							          <th class="sort_disabled"></th>
							          <th>Project Name:</th>
							          <th>Date:</th>
							          <th>File:</th>
							          <th>Audio	File:</th>
							          <th class="sort_disabled">...</th>
							        </tr>
							      </thead>
							      <tbody>
							       <?= show_timeshow_projects($projects) ?>
							      </tbody>
							</table>
						</div>
						<div class="next_button reverse">
							<a href="javascript:void(0)" data-tab_id="2" class="btn btn-lg btn-tab btn-next load_event select-tab disabled">Next<img src="<?= TIME_URL ?>assets/img/arrow-right.png"></a>

						</div>
					</div>

				</div>
				
				<div id="tab_2" class="tab">
					<div class="content_tab">
						<div class="half_content">
							<div class="event_left">
								<input type="text" class="form-control" name="event_timeshow_name" id="event_timeshow_name" placeholder="Project name" required="">
								<div class="half_content">
									<div class="">
										<input type="text" class="form-control lil_input" name="event_song_lable" id="event_song_lable" placeholder="song_lable" required="">
									</div>
								</div>
							</div>
							<div class="event_right">
								<div class="half_content half_content_lil">
									<input type="text" class="form-control lil_input" name="event_artist" id="event_artist" placeholder="artist" required="">
									<input type="text" class="form-control lil_input" name="event_upload_time" id="event_upload_time" placeholder="upload_time" required="">
									<input type="text" class="form-control lil_input" name="event_audio_file_lable" id="event_audio_file_lable" placeholder="audio_file_lable" required="">
									<input type="text" class="form-control lil_input" name="event_genre" id="event_genre" placeholder="genre" required="">
									<input type="text" class="form-control lil_input" name="event_speed" id="event_speed" placeholder="speed" required="">
									<input type="text" class="form-control lil_input" name="event_length" id="event_length" placeholder="length" required="">
								</div>
							</div>
						</div>							
					</div>

					<span class="line"></span>

					<div class="content_tab">
						<div class="librosa_img_respon">
							
						</div>
						<div class="event_text_top">
							Timelinen with waveform, all events and tools to edit the events
						</div>	
						<div class="event_text_bottom">
							Text Eingabe Feld für Op&onen und Beschreibung zur Automa&schen	Erzeugung von Events
						</div>	
						<a href="javascript:void(0)" class="btn btn-blue event_generate">Generate</a>
						<div class="next_button">
							<a href="javascript:void(0)" data-tab_id="1" class="btn btn-lg btn-tab btn-next select-tab load_selected_stack"><img src="<?= TIME_URL ?>assets/img/arrow-left.png">Back</a>
							<span class="stack_waiting_text">Please wait. Stacks loading...</span>
							<a href="javascript:void(0)" data-tab_id="3" class="btn btn-lg btn-tab btn-next select-tab load_stack stack_waiting_btn">Next<img src="<?= TIME_URL ?>assets/img/arrow-right.png"></a>

						</div>				
					</div>

				</div>

				<div id="tab_3" class="tab stack_content">
					<div class="content_tab">
						<div class="half_content">
							<div class="event_left">
								<input type="text" class="form-control project_details" name="stack_timeshow_name" id="project_name" placeholder="Project name" required="">
								<div class="half_content">
									<div class="">
										<input type="text" class="form-control lil_input project_details" name="stack_song_lable" id="project_name" placeholder="input_table_lable" required="">
									</div>
								</div>
							</div>
							<div class="event_right">
								<div class="half_content half_content_lil">
									<input type="text" class="form-control lil_input project_details" name="stack_artist" id="project_name" placeholder="artist" required="">
									<input type="text" class="form-control lil_input project_details" name="stack_upload_time" id="project_name" placeholder="upload_time" required="">
									<input type="text" class="form-control lil_input project_details" name="stack_audio_file_lable" id="project_name" placeholder="audio_file_lable" required="">
									<input type="text" class="form-control lil_input project_details" name="stack_genre" id="project_name" placeholder="genre" required="">
									<input type="text" class="form-control lil_input project_details" name="stack_speed" id="project_name" placeholder="speed" required="">
									<input type="text" class="form-control lil_input project_details" name="stack_length" id="project_name" placeholder="length" required="">
								</div>
							</div>
						</div>							
					</div>

					<span class="line"></span>

					<div class="content_tab">
						

						<div class="table your_stacks">
		
							<label>Your	stacks:</label>
							<div class="tableFixHead">
								<table id="stacks" class="table table-striped table-bordered" cellspacing="0" width="100%">
								      <thead>
								        <tr>
								          <th class="sort_disabled"></th>
								          <th>Stack Name:</th>
								          <th>Stack Type:</th>
								          <th>Number:</th>
								          <th>Color:</th>
								        </tr>
								      </thead>
								      <tbody>
								 <!--        <tr>
								          <td><input type="checkbox"></td>
								          <td>time_stamp</td>
								          <td>action_id</td>
								          <td>Action_type</td>
								          <td>actions_lable</td>
								        </tr> -->
								      </tbody>
								</table>
							</div>
					    </div>

						<a href="javascript:void(0)" class="btn btn-blue add_stack">Add Stack</a> 
						   
						<a href="javascript:void(0)" class="btn btn-red delete_stack">Delete Stack</a>

							<form id="import_stacks">
								<label for="stack-import" class="custom-file-upload btn">
						            <span>Upload stacks</span>
						            <input type="hidden" value="Select	your file for upload">
						            <input id="stack-import" name="stacks" required="" type="file" accept=".csv, text/csv">
						        </label>
								<a href="javascript:void(0)" class="btn btn-green import_stacks">import Stacks</a> 
							</form>


						<span style="margin: 25px 0;" class="line"></span>

						<div class="half_content stack_fields">
							<div class="stack_left">
								<label for="stack_name" class="form-label">Stack Name:</label>
								<input type="text" class="form-control lil_input" name="stack_name" id="stack_name" placeholder="stack_lable" required="">
							</div>
							<div class="stack_right">
								<div class="half_content half_content_lil">
									<div class="stack_type">
										<label for="stack_type" class="form-label">Stack Type:</label>
										<select class="form-select lil_select" name="stack_type" aria-label="Default select example">
										  <option value="Sequence">Sequence</option>
										  <option value="Time Range">Time Range</option>
										</select>
									</div>
									<div class="number">
										<label for="stack_number" class="form-label">Number:</label>
										<input type="number" class="form-control lil_input" name="stack_number" id="stack_name" placeholder="stack_id" value="1000" required="">
									</div>
									<div class="color">
										<label for="stack_color" class="form-label">Color:</label>
										<input type="color" name="stack_color" class="form-control form-control-color lil_color" id="myColor" value="#CCCCCC" title="Choose a color">
									</div>
								</div>	
							</div>
							
						</div>
						<a href="javascript:void(0)" style="background: #61a8f3; margin-bottom: 25px;" class="btn btn-blue save_stack">Update</a>  

						<div class="half_content table_mob" style="column-gap: 10px;">
						    
						    <div class="table" style="width:40%">
						    	<div class="col-md-6">
								    <input type="search" class="form-control lil_input" name="event_filter"placeholder="Filter" required="">
								</div>
								<div class="tableFixHead">
									<table id="event_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
									      <thead>
									        <tr>
									          <th class="sort_disabled"><input type="checkbox" class="select_all"></th>
									          <th class="sort_disabled">ID:</th>
									          <th>Time:</th>
									          <th class="sort_disabled">Name:</th>
									          <th class="sort_disabled">Color:</th>
									          <th class="sort_disabled"></th>
									        </tr>
									      </thead>
									      <tbody>
									        
									      </tbody>
									</table>
								</div>
						    </div>
						    
						    <div class="transfer_sel_events" style="width:40px; position: relative;">
						    	<a class="add_sel_to_actions" style="position: absolute;top: 50%;" href="javascript:void(0)"><img style="width: 40px;" decoding="async" src="<?= TIME_URL ?>/assets/img/arrow-right.png"></a>
						    </div>

							<div class="table" style="width:58%">
								<div class="half_content select_field_action">
									<div class="it_r">
									    <select class="form-select lil_select" name="select_field" aria-label="Default select example">
											  <!-- <option value="action_id">ID</option> -->
											  <option value="actions_lable">Name</option>
											  <option value="action_id">Action ID</option>
											  <option value="actions_type">Action</option>
											  <option value="action_value">Value</option>
											  <option value="action_delete">Delete actions</option>
											</select>
									</div>
							    	<div class="it_r">
									    <input type="search" class="form-control lil_input bulk_action active" name="bulk_action" placeholder="Bulk Action" required="">
									    <select class="form-select lil_select select_action" name="select_action" aria-label="Default select example">
										
										</select>
										<a href="javascript:void(0)" style="background: #61a8f3; margin-bottom: 25px;" class="btn btn-blue delete_actions" >Delete</a>	
									</div>
								</div>
								<div class="tableFixHead">
									<table id="stack_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
									      <thead>
									        <tr>
									          <th class="sort_disabled"><input type="checkbox" class="select_all"></th>
									          <th class="sort_disabled"></th>
									          <th>Time:</th>
									          <th class="sort_disabled">ID:</th>
									          <th class="sort_disabled">Action:</th>
									          <th class="sort_disabled">Name:</th>
									          <th class="sort_disabled">Value:</th>
									        </tr>
									      </thead>
									      <tbody>
									       
									      </tbody>
									</table>
								</div>
						    </div>
						</div>

						

						<div class="next_button">
							<a href="javascript:void(0)" data-tab_id="2" class="btn btn-lg btn-tab btn-next select-tab"><img src="<?= TIME_URL ?>assets/img/arrow-left.png">Back</a>
							<a href="javascript:void(0)" data-tab_id="4" class="btn btn-lg btn-tab btn-next select-tab">Next<img src="<?= TIME_URL ?>assets/img/arrow-right.png"></a>

						</div>
								
					</div>

				</div>

				<div id="tab_4" class="tab export_content">
					<div class="content_tab">
						<div class="half_content">
							<div class="event_left">
								<input type="text" class="form-control project_details" name="stack_timeshow_name" id="project_name" placeholder="Project name" required="">
								<div class="half_content">
									<div class="">
										<input type="text" class="form-control lil_input project_details" name="stack_song_lable" id="project_name" placeholder="input_table_lable" required="">
									</div>
								</div>
							</div>
							<div class="event_right">
								<div class="half_content half_content_lil">
									<input type="text" class="form-control lil_input project_details" name="stack_artist" id="project_name" placeholder="artist" required="">
									<input type="text" class="form-control lil_input project_details" name="stack_upload_time" id="project_name" placeholder="upload_time" required="">
									<input type="text" class="form-control lil_input project_details" name="stack_audio_file_lable" id="project_name" placeholder="audio_file_lable" required="">
									<input type="text" class="form-control lil_input project_details" name="stack_genre" id="project_name" placeholder="genre" required="">
									<input type="text" class="form-control lil_input project_details" name="stack_speed" id="project_name" placeholder="speed" required="">
									<input type="text" class="form-control lil_input project_details" name="stack_length" id="project_name" placeholder="length" required="">
								</div>
							</div>
						</div>	
						<div class="col-md-3">
							<div class="export_system">
								<label for="stack_name" class="form-label">Export System:</label>
								<select class="form-select lil_select" name="stack_type1" aria-label="Default select example">
								  <option value="grandMA3">grandMA3</option>
								  <option value="One">One</option>
								  <option value="Two">Two</option>
								  <option value="Three">Three</option>
								</select>
							</div>
						</div>
						<div class="half_content">
							<div class="export_left">
								<label for="stack_name" class="form-label">Timecode	Name:</label>
								<input type="text" class="form-control lil_input" name="export_lable" id="stack_name" placeholder="export_lable" required="">
							</div>
							<div class="export_right">
								<div class="half_content half_content_lil">
									<div class="number">
										<label for="stack_name" class="form-label">Number:</label>
										<input type="text" class="form-control lil_input" name="timeshow_number" id="stack_name" placeholder="1000" required="">
									</div>
									<div class="export">

										<a href="javascript:void(0)" data-project_id="" class="btn btn-lg btn-tab btn-next convert_btn">Convert<img src="<?= TIME_URL ?>assets/img/arrow-right.png"></a>
									</div>
								</div>	
							</div>
							
						</div>						
					</div>

					<span class="line"></span>
					<div class="content_tab">
						<label>Your	Shows:</label>
							<div class="tableFixHead">
								<table id="export_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
							      <thead>
							        <tr>
							          <th class="sort_disabled"></th>
							          <th>Project Name:</th>
							          <th>Status:</th>
							          <th>Date:</th>
							          <th>File:</th>
							          <th>Audio File:</th>
							          <th class="sort_disabled">...</th>
							        </tr>
							      </thead>
							      <tbody>
							        <tr>
							          <td><a href="javascript:void(0)" class="load"><img src="<?= TIME_URL ?>assets/img/eject-symbol-svgrepo-com.svg"></a></td>
							          <td>timeshow_name</td>
							          <td>Converted MA3</td>
							          <td>upload_time</td>
							          <td>input_table_lable</td>
							          <td>audio_file_lable</td>
							          <td>
							          	<a class="drop-down_menu_show" href="javascript:void(0)">...</a>
							          	<div class="drop-down_menu">
					                        <ul style="display: none;">
					                            <li><a class="copy_export" data-id="1" href="javascript:void(0)">Copy</a></li>
					                            <li><a class="delete_export" data-id="1" href="javascript:void(0)">Delete</a></li>
					                        </ul>
					                    </div>
							          </td>
							        </tr>
							        
							      </tbody>
							</table>
						</div>
						<div class="next_button">
							<a href="javascript:void(0)" data-tab_id="3" class="btn btn-lg btn-tab btn-next select-tab"><img src="<?= TIME_URL ?>assets/img/arrow-left.png">Back</a>

						</div>
					</div>
				</div>

			</div>

</section>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter_delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary close_btn" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary yes_btn">Yes</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="exampleModalCenter_copy" data-id="" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to copy?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary close_btn" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary yes_btn">Yes</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="exampleModalCenter_delete_export" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary close_btn" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary yes_btn">Yes</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="exampleModalCenter_copy_export" data-id="" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to copy?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary close_btn" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary yes_btn">Yes</button>
      </div>
    </div>
  </div>
</div>
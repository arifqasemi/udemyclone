<?php

namespace App\Controllers;

if(!defined("ROOT")) die ("direct script access denied");

/**
 * single course class
 */
use App\Core\Controller;
use App\Models\Auth;
use App\Models\Category;
use App\Models\Language_model;
use App\Models\Level_model;
use App\Models\Price_model;
use App\Models\Currency_model;
use App\Models\Course_meta;
use App\Models\Course_lecture;

use App\Models\Course as CourseModel;

class Course extends Controller
{
	
	public function index($slug = null)
	{

        $course = new CourseModel();
		$data['title'] = "Course";

		//red the course data
		$data['row'] = $course->first(['slug'=>$slug]);
		
		//red all courses order by trending value
		$query = "select * from courses where approved = 0 order by trending desc limit 5";
		$data['trending'] = $course->query($query);
 
		$this->view('front/course',$data);
	}
	

	public function courses($action = null, $id = null)
	{


		// if(!Auth::logged_in())
		// {
		// 	message('please login to view the admin section');
		// 	redirect('login');
		// }

		$user_id =Auth::getId();
        $course = new CourseModel();
	
	
	

	

		// 	//courses view
			$data['rows'] = $course->where(['user_id'=>$user_id]);

		// }

		$this->view('admin/courses',$data);
	}



	public function add_course(){
        $course = new CourseModel();
		$user_id =Auth::getId();
		$category = new Category();

		$data['rows'] = $course->where(['user_id'=>$user_id]);


	
		
			
			$data['categories'] = $category->findAll('asc');

			if($_SERVER['REQUEST_METHOD'] == "POST")
			{
				
				if($course->validate($_POST))
				{
					
					$_POST['date'] = date("Y-m-d H:i:s");
					$_POST['user_id'] = $user_id;
					$_POST['price_id'] = 1;

					$course->insert($_POST);
				
					message("Your Course was successfuly created");
					redirect('course/courses');
					// if($row){
					// 	redirect('course/courses');
					// }else{
					// 	// redirect('admin/courses');
					// }
				}

				$data['errors'] = $course->errors;
			}

		
		$this->view('admin/add_course',$data);

	}



	public function edit_course($id){
		$course = new CourseModel();

		$user_id = Auth::getId();
		$category = new Category();
		$language = new Language_model();
		$level = new Level_model();
		$price = new Price_model();
		$currency = new Currency_model();
		$course_meta = new Course_meta();
		$course_lectures = new Course_lecture();	
	
		$categories = $category->findAll('asc');
		$languages = $language->findAll('asc');
		$levels = $level->findAll('asc');
		$prices = $price->findAll('asc');
		$currencies = $currency->findAll('asc');




		$data['row'] = $row = $course->first(['user_id'=>$user_id,'id'=>$id]);
              


		        /*******/   // show course details with tabs /**********/
		if($_SERVER['REQUEST_METHOD'] == "POST"){
				if(!empty($_POST['data_type']) && $_POST['data_type'] == "read")
				{
					if($_POST['tab_name'] == "course-landing-page")
					{


						include views_path('course-edit-tabs/course-landing-page');
						return;

					}else
					if($_POST['tab_name'] == "course-messages")
					{

						include views_path('course-edit-tabs/course-messages');
						return;

					}else
					if($_POST['tab_name'] == "intended-learners")
					{

						include views_path('course-edit-tabs/intended-learners');
						return;

					}else
					if($_POST['tab_name'] == "curriculum")
					{

						include views_path('course-edit-tabs/curriculum');
                         return;
					}	

				}
			    };


		        /*******/   // save course details  /**********/
				if($_SERVER['REQUEST_METHOD'] == "POST"){
				if(!empty($_POST['data_type']) && $_POST['data_type'] == "save"){
					if($_POST['tab_name'] == "course-landing-page"){
						
						
						if($course->validate($_POST)){
		
						
								//check if a temp image exists
								if($row->course_image_tmp != "" && file_exists($row->course_image_tmp) )
								{
		
								
									//delete currect course image
									if(file_exists($row->course_image))
									{
										unlink($row->course_image);
									}
		
									$_POST['course_image'] = $row->course_image_tmp;
									$_POST['course_image_tmp'] = "";
								}
		
								$course->update($id,$_POST);
		
								message('your Course updated successfully!');
								return;
						}else{
							
							$info['error'] = $course->errors;
							$info['data_type'] = "errors";
							echo json_encode($info);
							return;
						}
									
					  
						}
					
						
				}
				}
			   /*******/   // save course image  /**********/

				if($_SERVER['REQUEST_METHOD'] == "POST"){
					if(!empty($_POST['data_type']) && $_POST['data_type'] == "upload_course_image")
					{

						$folder = "uploads/courses/";
						if(!file_exists($folder))
						{
							mkdir($folder,0777,true);
						}

						$errors = [];
						if(!empty($_FILES['image']['name']))
						{

							$destination = $folder . time() . $_FILES['image']['name'];
							move_uploaded_file($_FILES['image']['tmp_name'], $destination);

							//delete old temp file
							if(file_exists($row->course_image_tmp))
							{
								unlink($row->course_image_tmp);
							}

							
						$course->update($id,['course_image_tmp'=>$destination]);
								
							
						}
						//show($_POST);
						//show($_FILES);
					return;
					}
					}




				/*******/   // save course message /**********/
				if($_SERVER['REQUEST_METHOD'] == "POST"){

					if(!empty($_POST['data_type']) && $_POST['data_type'] == "save"){
						if($_POST['tab_name'] == "course-messages"){
								

                            if($course->edit_validate($_POST,$id,$_POST['tab_name'])){
								
								$course->update($id,$_POST);

								
							$info['data_type'] = "course message is updated successfully!";
							echo json_encode($info);
							return;
							}else{

                              $info['error']=$course->errors;
							  $info['data_type'] = "errors";
							  echo json_encode($info);
							  return;

							}


					}
					}
					}




				/*******/   // save course intended learners tab /**********/

				if($_SERVER['REQUEST_METHOD'] == "POST"){

						if(!empty($_POST['data_type']) && $_POST['data_type'] == "save"){
							if($_POST['tab_name'] == "intended-learners"){
						
								$meta_data = [];
								foreach($_POST as $key => $value){
									if(!empty($value)){
									$meta_id =preg_replace("/^[a-zA-Z\-]+_/","",$key);
									$key = preg_replace("/_[0-9]+$/","",$key);
									$meta_data[$key][]=$value;

									}
									unset($meta_data['data_type']);
									unset($meta_data['tab_name']);
									unset($meta_data['save']);
					
								}
                         	// show($meta_data);
							// return;


									//disabled all records from this course id
									$old_records = $course_meta->where(['course_id'=>$id,'tab'=>$_POST['tab_name']]);
									$old_ids = [];
									$old_ids_index = 0;

									if($old_records)
									{
										$old_ids = array_column($old_records, 'id');
										foreach ($old_records as $record) {
											
											$course_meta->update($record->id,['disabled'=>1]);
										}
									}
								
									if(!empty($meta_data))
									{
									
										foreach ($meta_data as $key => $rows) {
											
											$data_type = $key;
										
											foreach ($rows as $value) {
												
												$arr['course_id'] 	= $id;
												$arr['data_type'] 	= $data_type;
												$arr['value'] 		= $value;
												$arr['disabled'] 	= 0;
												$arr['tab'] 		= $_POST['tab_name'];
										
												if ($old_ids_index < count($old_ids)) {
													$my_old_id = $old_ids[$old_ids_index];
													$old_ids_index++;
													$course_meta->update($my_old_id, $arr);
												} else {
													$course_meta->insert($arr);
												}


											}
										}
									}
								
								$info['data_type'] = "course message is updated successfully!";
								echo json_encode($info);
								return;
								
								
							}
						}
					}	


				/*******/   // read course intended learners tab/**********/

				if($_SERVER['REQUEST_METHOD'] == "POST"){
					if(!empty($_POST['data_type']) && $_POST['data_type'] == "get_meta"){

						$meta=$course_meta->where(['course_id'=>$id,'tab'=>$_POST['tab_name'],'disabled'=>0]);
                        $info['data']=$meta;
						$info['data_type'] = "meta_data";
						echo json_encode($info);
						return;
					}


				}


				/*******/   // save course curriculum tab/**********/


				if($_SERVER['REQUEST_METHOD'] == "POST"){

					if(!empty($_POST['data_type']) && $_POST['data_type'] == "save"){
						if($_POST['tab_name'] == "curriculum"){   
						
							$meta_data = [];
							$meta_data_uids = [];
							$section_id = [];
							$index_for_lecture = 0;

							$index_for_uid = 0;

							$old_lectures_ids=[];
                            
								foreach($_POST as $key => $value){


									/******section id *****/

									if (preg_match("/^lecture_[0-9]+_[0-9]+/", $key)) {
										$the_section_id = preg_replace("/^lecture_[0-9]+_/", "", $key);
										$section_id[] = $the_section_id;
									}

									 /******section title *****/
									if(!empty($value) && preg_match("/^curriculum/",$key)){
									$key = preg_replace("/_[0-9]+$/","",$key);
									$meta_data[$key][]=$value;

									}

									 /******section description *****/

									if(!empty($value) && preg_match("/^description/",$key)){
										$key = preg_replace("/_[0-9]+$/","",$key);
										$meta_desc[]=$value;
	
										}
								     	 /******section uids *****/

									if( preg_match("/^uid_curriculum/",$key)){
										$key = preg_replace("/_[0-9]+$/","",$key);
										$meta_data_uids[]=$value;
									}

									 /******section lecture *****/

									 if( preg_match("/^lecture_[0-9]+/",$key)){
										$key = preg_replace("/_[0-9]+$/","",$key);
										$meta_data_lectures[]=$value;
									}
									/******section file *****/
									if( preg_match("/^files_[0-9]+/",$key)){
										$key = preg_replace("/_[0-9]+$/","",$key);
										$meta_data_files[]=$value;
									}

									/****** check for video section file *****/
									$folder = "uploads/courses/";

									if (!file_exists($folder)) {
										mkdir($folder, 0777, true);
									}
									
									$filenames = []; 
									
									foreach ($_FILES as $newkey => $file) {
										if (preg_match("/^files_[0-9]+/", $newkey)) {
									
											if (!empty($file['name'])) {
												$filename = $folder . time() . $file['name'];
												move_uploaded_file($file['tmp_name'], $filename);
												$filenames[] = $filename; 

											}

										}
									}
									
									
									unset($meta_data['data_type']);
									unset($meta_data['tab_name']);
									unset($meta_data['save']);
									unset($meta_data['uid_curriculum']);
									unset($meta_data['description']);
									unset($meta_data['lecture']);

								}
                        //    show($filenames);
						// 		return;
									//disabled all records from this course id
									$old_records = $course_meta->where(['course_id'=>$id,'tab'=>$_POST['tab_name']]);
									$old_ids = [];
									$old_ids_index = 0;

									if($old_records)
									{
										$old_ids = array_column($old_records, 'id');
										foreach ($old_records as $record) {
						
											$course_meta->update($record->id,['disabled'=>1]);
										}
									}
							
									if(!empty($meta_data))
									{
							
										foreach ($meta_data as $key => $rows) {
											
											$data_type = $key;
										
											foreach ($rows as  $value) {
												
												$arr['course_id'] 	= $id;
												$arr['data_type'] 	= $data_type;
												$arr['value'] 		= $value;
												$arr['disabled'] 	= 0;
												$arr['tab'] 		= $_POST['tab_name'];
										        $arr['uid']         =$meta_data_uids[$old_ids_index];
												$arr['description'] =$meta_desc[$old_ids_index];
												if ($old_ids_index < count($old_ids)) {
													$my_old_id = $old_ids[$old_ids_index];
													$old_ids_index++;
													$course_meta->update($my_old_id, $arr);
												} else {
											

													$course_meta->insert($arr);
												}

											}
										}
									}


									//disabled all records in course lecture 
									$old_lectures_records = $course_lectures->where(['course_id'=>$id,'disabled'=>0]);

									if($old_lectures_records)
									{
										$old_lectures_ids = array_column($old_lectures_records, 'id');
										foreach ($old_lectures_records as $records) {
						
											$course_lectures->update($records->id,['disabled'=>1]);
										}
									}
									
									if(!empty($meta_data_lectures)){

										// show($section_id);
										// return;
										foreach($meta_data_lectures as $lec){
											$arr['course_id'] 	= $id;
											$arr['uid'] = $section_id[$index_for_uid] ?? null;
										    $arr['title']=$lec;
											$arr['description']='this is';
											$arr['file'] = $filenames[$index_for_uid] ?? null;
											$arr['disabled']=0;
											if ($index_for_lecture < count($old_lectures_ids)) {
												$lect_old_id = $old_ids[$index_for_lecture];
												$index_for_lecture++;
												$index_for_uid++;

												$course_lectures->update($lect_old_id, $arr);
											} else {
												$arr['uid'] = $section_id[$index_for_uid] ?? null;

											    $arr['file'] = $filenames[$index_for_uid] ?? null;
												$arr['course_id'] 	= $id;

												$course_lectures->insert($arr);
												$index_for_lecture++;
												$index_for_uid++;


											}
											
										}
										

									}
								
								$info['data_type'] = "course curriculum is updated successfully!";
								echo json_encode($info);
								return;

						}
					}
				}


				/*******/   // read course intended learners tab/**********/

				if($_SERVER['REQUEST_METHOD'] == "POST"){
					if(!empty($_POST['data_type']) && $_POST['data_type'] == "curriculum"){

						$meta_data=$course_meta->where(['course_id'=>$id,'tab'=>$_POST['tab_name'],'disabled'=>0]);
						$lectureses=[];
						if(!empty($meta_data)){
							foreach ($meta_data as $row) {
							$lectures = $course_lectures->where(['uid' => $row->uid,'disabled'=>0,'course_id'=>$id]);
						
								$lectureses[] = $lectures;
							}
						}
					
						$info['lectures']=$lectureses;
						$info['data']=$meta_data;
						$info['data_type'] = "curriculum_data";
						echo json_encode($info);
						return;
					}


				}

			$this->view('admin/edit_course',$data);
}





   public function delete_course($id){
				$course->delete($row->id);
				message("Course deleted successfully");
				redirect('course/courses');
   }



   public function single_course($slug){


			$course = new CourseModel();
			$single_course =$course->where(['slug'=>$slug]);

            $data['row']=$single_course[0];
			return $this->view('front/single_course',$data);
			
   }
}
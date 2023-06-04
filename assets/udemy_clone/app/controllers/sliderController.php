<?php
namespace App\Controllers;
use App\Models\Auth;
use App\Models\Slider;
use App\Core\Controller;

class SliderController extends Controller {
    public function slider_images($id = null)
	{

		if(!Auth::logged_in())
		{
			message('please login to view the admin section');
			redirect('login');
		}

		$slider = new Slider();
		$data['rows'] = [];
		$rows = $slider->where(['disabled'=>0]);
		
		if($rows)
		{
			foreach ($rows as $key => $obj) {
				$num = $obj->id;
				$data['rows'][$num] = $obj;
			}
		}

		$id = $_POST['id'] ?? 0;
		$row = $slider->first(['id'=>$id]);

		if($_SERVER['REQUEST_METHOD'] == "POST")
		{
 
			$folder = "uploads/images/";
			if(!file_exists($folder))
			{
				mkdir($folder,0777,true);
				file_put_contents($folder."index.php", "<?php //silence");
				file_put_contents("uploads/index.php", "<?php //silence");
			}
				
			$allowed = ['image/jpeg','image/png'];

			if(!empty($_FILES['image']['name'])){

				if($_FILES['image']['error'] == 0){

					if(in_array($_FILES['image']['type'], $allowed))
					{
						//everything good
						$destination = $folder.time().$_FILES['image']['name'];

						$_POST['image'] = $destination;

					}else{
						$slider->errors['image'] = "This file type is not allowed";
					}
				}else{
					$slider->errors['image'] = "Could not upload image";
				}
			}

 			if($slider->validate($_POST,$id))
 			{

 				if(!empty($destination))
 				{
					move_uploaded_file($_FILES['image']['tmp_name'], $destination);

					resize_image($destination);
					if($row && file_exists($row->image))
					{
						unlink($row->image);
					} 					
 				}

				if($row)
				{
					unset($_POST['id']);
					$slider->update($id,$_POST);
				}else{
					$slider->insert($_POST);
				}

				//message("Image saved successfully");
				//redirect('admin/profile/'.$id);
 			}

			if(empty($slider->errors)){
				$arr['message'] = "Image saved successfully";
			}else{
				$arr['message'] = "Please correct these errors";
				$arr['errors'] = $slider->errors;
			}

			echo json_encode($arr);

 			die;
		}

		$data['title'] = "Slider images";
		$data['errors'] = $slider->errors;

		$this->view('admin/slider-images',$data);
	}

}
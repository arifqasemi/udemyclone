<?php


namespace App\Controllers;
use App\Core\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\Slider;

class Home extends Controller
{
		
	public function index()
	{

		$course = new Course();
		$category = new Category();

		$data['title'] = "Home";

		//red all courses 
		$data['rows'] = $course->where(['approved'=>0],'desc',7);
		
		
		//red all courses order by trending value
		$query = "select * from courses where approved = 0 order by trending desc limit 5";
		$data['trending'] = $course->query($query);
		
		if($data['rows']){
			$data['first_row'] = $data['rows'][0];
			unset($data['rows'][0]);

			$total_rows = count($data['rows']);
			$half_rows = round($total_rows / 2);

			$data['rows1'] = array_splice($data['rows'], 0,$half_rows);
			$data['rows2'] = $data['rows'];

		}

		//load slider images
		$slider = new Slider();
		$slider->order = 'asc';
		$data['images'] = $slider->where(['disabled'=>0]);

		$this->view('front/home',$data);
	}
}
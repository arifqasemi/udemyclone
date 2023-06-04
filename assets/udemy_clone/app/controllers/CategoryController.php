<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Auth;
use App\Models\Category;
use App\Models\Course;
if(!defined("ROOT")) die ("direct script access denied");

/**
 * category class
 */

class CategoryController extends Controller
{
	
	public function index($slug = null)
	{

		$course = new Course();
		$category = new Category();

		$data['title'] = "Category";

		//red all courses 
		$query = "SELECT c.*,cat.category,cat.slug as catslug FROM courses as c join categories as cat on cat.id = c.category_id where cat.slug = :slug";
		$data['rows'] = $course->query($query,['slug'=>$slug]);
		
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


		$this->view('front/category',$data);
	}//// method end

	public function categories($action = null, $id = null)
	{

		if(!Auth::logged_in())
		{
			message('please login to view the admin section');
			redirect('login');
		}

		$user_id = Auth::getId();
		$category = new Category();

		$data = [];
		$data['action'] = $action;
		$data['id'] = $id;


			//courses view
		$data['rows'] = $category->findAll();

		$this->view('admin/categories',$data);
	}//// method end

	public function add_category(){
		
			
		$category = new Category();

			if($_SERVER['REQUEST_METHOD'] == "POST")
			{
				// if(user_can('add_categories'))
				// {
					if($category->validate($_POST))
					{
						
						$_POST['slug'] = str_to_url($_POST['category']);
						$category->insert($_POST);
						message("Your category was successfuly created");
						redirect('admin/categories');
					}
				// }else{
				// 	$category->errors['category'] = "You are not allowed to perform this action";
				// }

				$data['errors'] = $category->errors;

			}

		$this->view('admin/add_category');


		}//// method end
	

	public function edit_category($id){
		
	
			$category = new Category();

			$data['row'] = $row = $category->first(['id'=>$id]);
			
			if($_SERVER['REQUEST_METHOD'] == "POST" && $row)
			{
				if($category->validate($_POST))
				{
					
					$category->update($row->id, $_POST);
					message("Your category was successfuly edited");
					redirect('admin/categories');
				}

				$data['errors'] = $category->errors;
			}

		$this->view('admin/edit_category',$data);

	}//// method end
	


	public function delete_category($id){
	

		$category = new Category();
		$data['row'] = $row = $category->first(['id'=>$id]);
			
			

					
				$category->delete($row->id);
				message("Your category was successfuly deleted");
				redirect('CategoryController/categories');

				$data['errors'] = $category->errors;

		
 

		
	}//// method end
}
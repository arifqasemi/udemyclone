<?php 

namespace App\Models;

/**
 * course-meta model
 */
use App\Core\Model;
class Course_meta extends Model
{
	
	public $errors = [];
	protected $table = "courses_meta";

	protected $allowedColumns = [

		'course_id',
		'data_type',
		'value',
		'disabled',
		'tab',
		'uid',
		'description',
		 
	];

	public function validate($data)
	{
		$this->errors = [];

		if(empty($data['currency']))
		{
			$this->errors['currency'] = "A currency is required";
		}

		if(empty($data['symbol']))
		{
			$this->errors['symbol'] = "A currency symbol is required";
		}

 
		
		if(empty($this->errors))
		{
			return true;
		}

		return false;
	}


}
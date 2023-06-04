<?php

namespace App\Controllers;
use App\Models\Auth;
use App\Models\Role;
use App\Core\Controller;

class RoleController extends Controller {
    public function roles($action = null, $id = null)
	{

		if(!Auth::logged_in())
		{
			message('please login to view the admin section');
			redirect('login');
		}

		$user_id = Auth::getId();
		$role = new Role();

		$data = [];
		$data['action'] = $action;
		$data['id'] = $id;



			//courses view
			$data['rows'] = $role->findAll();

			if($_SERVER['REQUEST_METHOD'] == "POST")
			{

				//disable all permissions
			
				$query = "update permissions_map set disabled = 1 where id > 0";
				$role->query($query);

				foreach ($_POST as $key => $permission) {
					
					if(preg_match("/[0-9]+\_[0-9]+/", $key))
					{
						$role_id = preg_replace("/\_[0-9]+/", "", $key);

						$arr = [];
						$arr['role_id'] = $role_id;
						$arr['permission'] = $permission;	

						//check if record exists
						$query = "select id from permissions_map where permission = :permission && role_id = :role_id limit 1";
						$check = $role->query($query,$arr);
						if($check)
						{
							//update
							$query = "update permissions_map set disabled = 0 where permission = :permission && role_id = :role_id limit 1";
						}else
						{
							//insert into permissions table
							$query = "insert into permissions_map (role_id,permission) values (:role_id,:permission)";

						}
                       
						$role->query($query,$arr);
					}
				}

				redirect('roleController/roles');
			}

	

		$this->view('admin/roles',$data);
	}
}
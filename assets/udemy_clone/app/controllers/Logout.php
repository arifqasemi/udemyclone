<?php 

namespace App\Controllers;
use App\Core\Controller;
use App\Models\Auth;

/**
 * logout class
 */
class Logout extends Controller
{
	
	public function index()
	{

		Auth::logout();

		redirect('home');
	}
	
}
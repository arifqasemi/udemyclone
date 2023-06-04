<aside id="sidebar" class="sidebar">

<ul class="sidebar-nav" id="sidebar-nav">

  <?php if(user_can('view_dashboard')):?>
  <li class="nav-item">
    <a class="nav-link " href="<?=ROOT?>/admin/dashboard">
      <i class="bi bi-grid"></i>
      <span>Dashboard</span>
    </a>
  </li><!-- End Dashboard Nav -->
  <?php endif;?>

  <li class="nav-item">
    <a class="nav-link " href="<?=ROOT?>/course/courses">
      <i class="bi bi-camera-reels"></i>
      <span>My Courses</span>
    </a>
  </li><!-- End Dashboard Nav -->

  <?php if(user_can('view_categories')):?>
    <li class="nav-item">
      <a class="nav-link " href="<?=ROOT?>/CategoryController/categories">
        <i class="bi bi-list"></i>
        <span>Categories</span>
      </a>
    </li><!-- End Dashboard Nav -->
  <?php endif;?>

  <?php if(user_can('view_roles')):?>
    <li class="nav-item">
      <a class="nav-link " href="<?=ROOT?>/roleController/roles">
        <i class="bi bi-people"></i>
        <span>User Roles</span>
      </a>
    </li><!-- End Dashboard Nav -->
   <?php endif;?>

  <li class="nav-item">
    <a class="nav-link " href="<?=ROOT?>/admin/lessons">
      <i class="bi bi-person-video3"></i>
      <span>Enrolled Courses</span>
    </a>
  </li><!-- End Dashboard Nav -->

  <li class="nav-item">
    <a class="nav-link " href="<?=ROOT?>/course/courses">
      <i class="bi bi-hourglass-split"></i>
      <span>Watch History</span>
    </a>
  </li><!-- End Dashboard Nav -->
  
  <?php if(user_can('view_sales')):?>
  <li class="nav-item">
    <a class="nav-link " href="<?=ROOT?>/admin/sales">
      <i class="bi bi-cash-coin"></i>
      <span>Sales</span>
    </a>
  </li><!-- End Dashboard Nav -->
  <?php endif;?>

  <li class="nav-item">
    <a class="nav-link " href="<?=ROOT?>/admin/profile">
      <i class="bi bi-person"></i>
      <span>Profile</span>
    </a>
  </li><!-- End Dashboard Nav -->

  <?php if(user_can('edit_slider_images')):?>
   <li class="nav-item">
    <a class="nav-link " href="<?=ROOT?>/sliderController/slider-images">
      <i class="bi bi-images"></i>
      <span>Slider Images</span>
    </a>
  </li><!-- End Dashboard Nav -->
  <?php endif;?>
  
  <li class="nav-heading">Go to</li>

  <li class="nav-item">
    <a class="nav-link " href="<?=ROOT?>">
      <i class="bi bi-globe"></i>
      <span>Home</span>
    </a>
  </li><!-- End Dashboard Nav -->

</ul>

</aside>

<?php $this->view('admin/includes/admin-header',$data) ?>
<div class="card col-md-5 mx-auto">
    <div class="card-body">
      <h5 class="card-title">New category</h5>

      <!-- <?php if(user_can('add_categories')):?> -->

      <!-- No Labels Form -->
      <form method="post" class="row g-3">
        
        <div class="col-md-12">
          <input value="<?=set_value('category')?>" name="category" type="text" class="form-control <?=!empty($errors['category']) ? 'border-danger':'';?>" placeholder="Category name">

          <?php if(!empty($errors['category'])):?>
            <small class="text-danger"><?=$errors['category']?></small>
          <?php endif;?>

        </div>
 
        <div class="col-md-12">
          <label>Active:</label>
          <select name="disabled" class="form-select">
            
            <option value="0" selected="">Yes</option>
            <option value="1">No</option>

          </select>

        </div>
    
        <div class="text-center">
          <button type="submit" class="btn btn-primary">Save</button>

          <a href="<?=ROOT?>/admin/categories">
            <button type="button" class="btn btn-secondary">Cancel</button>
          </a>
        </div>
      </form><!-- End No Labels Form -->
      <!-- <?php else:?>
        <div class="alert alert-danger text-center">You dont have permission to perform this action!</div>
          <a href="<?=ROOT?>/admin/categories">
            <button type="button" class="btn btn-secondary">Back</button>
          </a>
      <?php endif;?> -->

    </div>
  </div>
  <?php $this->view('admin/includes/admin-footer',$data) ?>
<?php $this->view('admin/includes/admin-header',$data) ?>

<style>
  
  .tabs-holder{
    display: flex;
    margin-top: 10px; 
    margin-bottom: 10px;
    justify-content: center;
    text-align: center;
    flex-wrap: wrap;
  }

  .my-tab{
    flex:1;
    border-bottom: solid 2px #ccc;
    padding-top: 10px;
    padding-bottom: 10px;
    cursor: pointer;
    user-select: none;
    min-width: 150px;

  }
  .my-tab:hover{
    color: #4154f1;
  }

  .active-tab{
    color: #4154f1;
    border-bottom: solid 2px #4154f1;
  }

  .hide{
    display: none;
  }

  .loader{
    position: relative;
    width:200px;
    height:200px;
    left: 50%;
    top: 50%;
    transform: translateX(-50%);
    opacity: 0.5;
  }

</style>

  <div class="card">

    <div class="card-body">
      <h5 class="card-title">
        Categories 
        <a href="<?=ROOT?>/CategoryController/add_category">
          <button class="btn btn-primary float-end"><i class="bi bi-plus"></i> New category</button>
        </a>
      </h5>

      <?php if(user_can('view_categories')):?>

          <!-- Table with stripped rows -->
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Category</th>
                <th scope="col">Active</th>
                <th scope="col">slug</th>
                <th scope="col">Action</th>
              </tr>
            </thead>

            <?php if(!empty($rows)):?>
              <tbody>

                <?php foreach($rows as $row):?>
                  <tr>
                    <th scope="row"><?=$row->id?></th>
                    <td><?=esc($row->category)?></td>
                    <td><?=esc($row->disabled ? 'No':'Yes')?></td>
                    <td><?=esc($row->slug)?></td>
                    <td>
                      <a href="<?=ROOT?>/CategoryController/edit_category/<?=$row->id?>">
                        <i class="bi bi-pencil-square"></i> 
                      </a>
                      <a href="<?=ROOT?>/CategoryController/delete_category/<?=$row->id?>">
                        <i class="bi bi-trash-fill text-danger"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach;?>

              </tbody>
            <?php else:?>
              <tr><td colspan="10">No records found!</td></tr>
            <?php endif;?>

          </table>
          <!-- End Table with stripped rows -->
      <?php else:?>
        <div class="alert alert-danger text-center">You dont have permission to perform this action!</div>
      <?php endif;?>

    </div>
  </div>




<?php $this->view('admin/includes/admin-footer',$data) ?>
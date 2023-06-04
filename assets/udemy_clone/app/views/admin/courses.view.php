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
        My Courses 
        <a href="<?=ROOT?>/course/add_course">
          <button class="btn btn-primary float-end"><i class="bi bi-camera-video-fill"></i> Add Course</button>
        </a>
      </h5>

      <!-- Table with stripped rows -->
      <table class="table table-striped">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Title</th>
            <th scope="col">Image</th>
            <th scope="col">Instructor</th>
            <th scope="col">Category</th>
            <th scope="col">Price</th>
            <th scope="col">Primary Subject</th>
            <th scope="col">Date</th>
            <th scope="col">Action</th>
          </tr>
        </thead>

        <?php if(!empty($rows)):?>
          <tbody>

            <?php foreach($rows as $row):?>
              <tr>
                <th scope="row"><?=$row->id?></th>
                <td><?=esc($row->title)?></td>
                <td><img src="<?=get_image($row->course_image)?>" style="width: 100px;height: 100px;object-fit: cover;"/></td>
                <td><?=esc($row->user_row->name ?? 'Unknown')?></td>
                <td><?=esc($row->category_row->category ?? 'Unknown')?></td>
                <td><?=esc($row->price_row->name ?? 'Unknown')?></td>
                <td><?=esc($row->primary_subject)?></td>
                <td><?=get_date($row->date)?></td>
                <td>
                  <a href="<?=ROOT?>/course/edit_course/<?=$row->id?>">
                    <i class="bi bi-pencil-square"></i> 
                  </a>
                  <a href="<?=ROOT?>/courses/delete_course/<?=$row->id?>">
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

    </div>
  </div>


<script>
  
  var tab = sessionStorage.getItem("tab") ? sessionStorage.getItem("tab"): "intended-learners";
  var dirty = false;
  var get_meta = true;

  function show_tab(tab_name)
  {
 
    var contentDiv = document.querySelector("#tabs-content");
    show_loader(contentDiv);

    //change active tab
    var div = document.querySelector("#"+tab_name);
    var children = div.parentNode.children;
    for (var i = 0; i < children.length; i++) {
      children[i].classList.remove("active-tab");
    }

    div.classList.add("active-tab");


    send_data({
      tab_name:tab,
      data_type:"read",
    });

    disable_save_button(false);

  }

  function send_data(obj)
  {
    
    var myform = new FormData();
    for(key in obj){
      myform.append(key,obj[key]); 
    }

    var ajax = new XMLHttpRequest();

    document.querySelector(".js-save-progress-inner").style.width = 0 + "%";
    document.querySelector(".js-save-progress-inner").innerHTML = 0 + "%";
    document.querySelector(".js-save-progress").classList.remove("hide");
    ajax.upload.addEventListener('progress',function(e){

      var percent = Math.round((e.loaded / e.total) * 100);
      document.querySelector(".js-save-progress-inner").style.width = percent + "%";
      document.querySelector(".js-save-progress-inner").innerHTML = percent + "%";

    });

    ajax.addEventListener('readystatechange',function(){

      if(ajax.readyState == 4){

        if(ajax.status == 200){
          //everything went well
          //alert("upload complete");
          document.querySelector(".js-save-progress").classList.add("hide");
          handle_result(ajax.responseText);
        }else{
          //error
          alert("an error occurred");
        }
      }
    });
 
    ajax.open('post','',true);
    ajax.send(myform);

  }


  function handle_result(result)
  {

    console.log(result);
    if(result.substr(0,2) == '{"')
    {
      var obj = JSON.parse(result);
      if(typeof obj == 'object'){

        if(obj.data_type == "save"){

          //alert(obj.data);

          //clear all errors
          var error_containers = document.querySelectorAll(".error");
          for (var i = 0; i < error_containers.length; i++) {
            error_containers[i].innerHTML = "";
          }

          //show any errors
          if(typeof obj.errors == 'object')
          {
            for(key in obj.errors)
            {
              document.querySelector(".error-"+key).innerHTML = obj.errors[key];
            }

          }else{
            disable_save_button(false);
            dirty = false;
            // alert(obj.data);
            // window.location.reload();
          }
        }else 
        if(obj.data_type == "get-meta"){

          var obj_name = tab.replaceAll("-","_");
          window[obj_name].handle_result(obj.data);
        }

      }

    }else{

      //load tab content
      var contentDiv = document.querySelector("#tabs-content");
      contentDiv.innerHTML = result;

      //do stuff after tab is loaded
      var obj_name = tab.replaceAll("-","_");

      if(get_meta){
        get_meta = false;
        window[obj_name].get_meta(<?=$row->id?>);
      }

    }

  }

  function set_tab(tab_name)
  {

    if(dirty)
    {
      //ask user to save when switching tabs
      if(!confirm("Your changes were not saved. continue?!"))
      {
        return;
      }
    }

    get_meta = true;
    tab = tab_name;
    sessionStorage.setItem("tab", tab_name);

    dirty = false;
    show_tab(tab_name);

  }

  function something_changed(e)
  {
    dirty = tab;
    disable_save_button(true);
  }

  function disable_save_button(status = false)
  {
    if(status){
      document.querySelector(".js-save-button").classList.remove("disabled");
    }else{
      document.querySelector(".js-save-button").classList.add("disabled");
    }
  }

  function show_loader(item)
  {
    item.innerHTML = '<img class="loader" src="<?=ROOT?>/assets/images/loader.gif">';
  }

  show_tab(tab);

  /*******************
  for saving content
  ********************/

  function save_content()
  {
    var content = document.querySelector("#tabs-content");
    var inputs = content.querySelectorAll("input,textarea,select");

    var obj = {};
    obj.data_type = "save";
    obj.tab_name = tab;

    for (var i = 0; i < inputs.length; i++) {
       
      var key = inputs[i].name;
      obj[key] = inputs[i].value;

      if(inputs[i].type == 'file')
        obj[key] = inputs[i].files[0];

      if(inputs[i].getAttribute('uid'))
        obj['uid_'+key] = inputs[i].getAttribute('uid');

      /*
      if(inputs[i].getAttribute('index'))
        obj['index_'+key] = inputs[i].getAttribute('index');
      */
    }

    send_data(obj);

  }

  var course_image_uploading = false;
  var ajax_course_image = null;

  function upload_course_image(file)
  {

    if(course_image_uploading){

      alert("please wait while the other image uploads");
      return;
    }

    //validate image extension
    var allowed_types = ['jpg','jpeg','png'];
    var ext = file.name.split(".").pop();
    ext = ext.toLowerCase();

    if(!allowed_types.includes(ext))
    {
      alert("Only files of this type allowed: "+allowed_types.toString(","));
      return;
    }

    //display an image preview
    var img = document.querySelector(".js-image-upload-preview");
    var link = URL.createObjectURL(file);
    img.src = link;

    //begin upload
    course_image_uploading = true;

    document.querySelector(".js-image-upload-info").innerHTML = file.name;
    document.querySelector(".js-image-upload-info").classList.remove("hide");
    document.querySelector(".js-image-upload-input").classList.add("hide");
    document.querySelector(".js-image-upload-cancel-button").classList.remove("hide");

    var myform = new FormData();
    ajax_course_image = new XMLHttpRequest();

    ajax_course_image.addEventListener('readystatechange',function(){

      if(ajax_course_image.readyState == 4){

        if(ajax_course_image.status == 200){
          //everything went well
          //alert("upload complete");
          //alert(ajax_course_image.responseText);
        }

        course_image_uploading = false;
        document.querySelector(".js-image-upload-info").classList.add("hide");
        document.querySelector(".js-image-upload-input").classList.remove("hide");
        document.querySelector(".js-image-upload-cancel-button").classList.add("hide");

      }
    });

    ajax_course_image.addEventListener('error',function(){
      alert("an error occurred");
    });

    ajax_course_image.addEventListener('abort',function(){
      alert("upload aborted");
    });

    ajax_course_image.upload.addEventListener('progress',function(e){

      var percent = Math.round((e.loaded / e.total) * 100);
      document.querySelector(".progress-bar-image").style.width = percent + "%";
      document.querySelector(".progress-bar-image").innerHTML = percent + "%";

    });
 
    myform.append('data_type','upload_course_image');
    myform.append('tab_name',tab);
    myform.append('image',file);
    myform.append('csrf_code',document.querySelector(".js-csrf_code").value);

    ajax_course_image.open('post','',true);
    ajax_course_image.send(myform);

  }

  function ajax_course_image_cancel()
  {
    ajax_course_image.abort();
  }

  var course_video_uploading = false;
  var ajax_course_video = null;

  function upload_course_video(file)
  {

    if(course_video_uploading){

      alert("please wait while the other video uploads");
      return;
    }

    //validate video extension
    var allowed_types = ['mp4'];
    var ext = file.name.split(".").pop();
    ext = ext.toLowerCase();

    if(!allowed_types.includes(ext))
    {
      alert("Only files of this type allowed: "+allowed_types.toString(","));
      return;
    }

    //display an video preview
    var vdo = document.querySelector(".js-video-upload-preview");
    var link = URL.createObjectURL(file);
    vdo.src = link;

    //begin upload
    course_video_uploading = true;

    document.querySelector(".js-video-upload-info").innerHTML = file.name;
    document.querySelector(".js-video-upload-info").classList.remove("hide");
    document.querySelector(".js-video-upload-input").classList.add("hide");
    document.querySelector(".js-video-upload-cancel-button").classList.remove("hide");

    var myform = new FormData();
    ajax_course_video = new XMLHttpRequest();

    ajax_course_video.addEventListener('readystatechange',function(){

      if(ajax_course_video.readyState == 4){

        if(ajax_course_video.status == 200){
          //everything went well
          //alert("upload complete");
          //alert(ajax_course_video.responseText);
        }

        course_video_uploading = false;
        document.querySelector(".js-video-upload-info").classList.add("hide");
        document.querySelector(".js-video-upload-input").classList.remove("hide");
        document.querySelector(".js-video-upload-cancel-button").classList.add("hide");

      }
    });

    ajax_course_video.addEventListener('error',function(){
      alert("an error occurred");
    });

    ajax_course_video.addEventListener('abort',function(){
      alert("upload aborted");
    });

    ajax_course_video.upload.addEventListener('progress',function(e){

      var percent = Math.round((e.loaded / e.total) * 100);
      document.querySelector(".progress-bar-video").style.width = percent + "%";
      document.querySelector(".progress-bar-video").innerHTML = percent + "%";

    });
 
    myform.append('data_type','upload_course_video');
    myform.append('tab_name',tab);
    myform.append('video',file);
    myform.append('csrf_code',document.querySelector(".js-csrf_code").value);

    ajax_course_video.open('post','',true);
    ajax_course_video.send(myform);

  }

  function ajax_course_video_cancel()
  {
    ajax_course_video.abort();
  }

</script>




<?php $this->view('admin/includes/admin-footer',$data) ?>
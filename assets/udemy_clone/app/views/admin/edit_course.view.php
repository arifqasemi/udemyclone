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
      <h5 class="card-title">Edit Course</h5>
      <?php if(!empty($row)):?>
    <h5 class="error1"></h5>
        <div class="float-end">
          <button onclick="course_message.save_content()" class="js-save-button btn btn-success disabled">Save</button>
          <a href="<?=ROOT?>/admin/courses">
            <button class="btn btn-primary">Back</button>
          </a>
        </div>   
        <!--tabs-->
        <br>
        <br>
          <div class="tabs-holder">
            <div onclick="course_message.set_tab(this.id,this)" id="intended-learners" class="my-tab active-tab">Intended Learners</div>
            <div onclick="course_message.set_tab(this.id,this)" id="curriculum" class="my-tab">Curriculum</div>
            <div onclick="course_message.set_tab(this.id,this)" id="course-landing-page" class="my-tab">Course landing page</div>
            <div onclick="course_message.set_tab(this.id,this)" id="promotions" class="my-tab">Promotions</div>
            <div onclick="course_message.set_tab(this.id,this)" id="course-messages" class="my-tab">Course messages</div>
          </div>
        <!--end tabs-->
        <!--div-tabs-->
        <div oninput="course_message.something_changed(event)" >
          <div id="tabs-content">
            
          </div>
        </div>
        <!--end div-tabs-->
 

      <?php else:?>
        <div>That course was not found!</div>
      <?php endif;?>

    </div>
  </div>



  <script>


            /*******************
               course message OOP javascript
              ********************/


    tab = sessionStorage.getItem("tab") ? sessionStorage.getItem("tab"): "intended-learners";

   var course_message ={
       dirty  :false,
      get_meta : true,

      show_tab:function(tab_name){

        var contentDiv = document.querySelector("#tabs-content");
        this.show_loader(contentDiv);

        //change active tab
        var div = document.querySelector("#"+tab_name);
        var children = div.parentNode.children;
        for (var i = 0; i < children.length; i++) {
          children[i].classList.remove("active-tab");
        }

        div.classList.add("active-tab");


       this.send_data({
          tab_name:tab,
          data_type:"read",
        });

          },



       send_data:function(obj){
        var myform = new FormData();
        for(key in obj){
          myform.append(key,obj[key]); 
        }

        var ajax = new XMLHttpRequest();

    // document.querySelector(".js-save-progress-inner").style.width = 0 + "%";
    // document.querySelector(".js-save-progress-inner").innerHTML = 0 + "%";
    // document.querySelector(".js-save-progress").classList.remove("hide");
    // ajax.upload.addEventListener('progress',function(e){

    //   var percent = Math.round((e.loaded / e.total) * 100);
    //   document.querySelector(".js-save-progress-inner").style.width = percent + "%";
    //   document.querySelector(".js-save-progress-inner").innerHTML = percent + "%";

    // });

    ajax.addEventListener('readystatechange',function(){

      if(ajax.readyState == 4){

        if(ajax.status == 200){
          //everything went well
          //alert("upload complete");
          // document.querySelector(".js-save-progress").classList.add("hide");
          this.handle_result(ajax.responseText);
        }else{
          //error
          alert("an error occurred");
        }
          }
        }.bind(this));
    
        ajax.open('post','',true);
        ajax.send(myform);
       },




  handle_result:function(result){
  // alert(result);
        if (this.isJSON(result)) {
    // The data is JSON

            var jsonResult = JSON.parse(result);
   

            var error_containers = document.querySelectorAll(".error");
          for (var i = 0; i < error_containers.length; i++) {
            error_containers[i].innerHTML = "";
          }
          //show any errors
          if(typeof jsonResult.error == 'object')
          {
              for(key in jsonResult.error)
              {
                document.querySelector(".error-"+key).innerHTML = jsonResult.error[key];
              }
          }else

          if(jsonResult.data_type =="meta_data"){

            intended_learners.handle_result(jsonResult);
            console.log(jsonResult);
          }
          if(jsonResult.data_type =="curriculum_data"){

            curriculum.handle_result(jsonResult);
            lecture.handle_result(jsonResult.lectures);
          }

          } else {

            var contentDiv = document.querySelector("#tabs-content");
              contentDiv.innerHTML = result;
              if(tab =='intended-learners'){
                intended_learners.initailiz();

            }
            if(tab =='curriculum'){
                curriculum.initailiz();

            }
          
          }

            this.dirty = false;


       },



       isJSON:function(data){
        try {
          JSON.parse(data);
          return true;
        } catch (e) {
          return false;
        }

       },



       set_tab:function(tab_name){
        if(this.dirty)
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

            this.dirty = false;
            this.show_tab(tab_name);

       },
      


       something_changed:function(e){
            dirty = tab;
          this.disable_save_button(true);
       },




       disable_save_button:function(status =false){
        if(status){
            document.querySelector(".js-save-button").classList.remove("disabled");
          }else{
            document.querySelector(".js-save-button").classList.add("disabled");
          }

       },

     

       show_loader:function(item){
        item.innerHTML = '<img class="loader" src="<?=ROOT?>/assets/images/gifloader.gif">';

       },




       save_content:function(){
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

          this.send_data(obj);
          this.show_tab(tab);

            },


            test:function(result){
              console.log(result);
            }
   }


   course_message.show_tab(tab);

          /******************OOP ends *************/




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
          alert(ajax_course_image.responseText);
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




<script>

 
  /*******************
   intended learners OOP javascript
  ********************/



   var item_to_drug = null;
   var item_drug_to = null;
             


   var intended_learners=
   {
  
    counter : 0, // Initialize the counter property
      


    initailiz:function(){
      course_message.send_data({
          tab_name:tab,
          data_type:"get_meta",
        });

      },
   
    add_new:function(section,data){
      
      var mydiv = document.createElement('div');
        mydiv.classList.add('row');
        mydiv.classList.add('js-input');
        mydiv.classList.add('my-4');
        mydiv.classList.add('mx-2');
        mydiv.setAttribute('onclick','intended_learners.intended_learners_action(event)');
        mydiv.setAttribute('ondragstart','intended_learners.intended_learners_dragstart(event)');
        mydiv.setAttribute('ondragover','intended_learners.intended_learners_dragover(event)');
        mydiv.setAttribute('ondragend','intended_learners.intended_learners_dragend(event)');
        mydiv.setAttribute('draggable','true');
   
        mydiv.innerHTML = `
          <input value="${data.value}" type="text"  class="col-md-9 py-2" name="${data.name}_${this.counter++}" placeholder="${data.placeHolder}">
          <div id="delete" min=""  class="col-md-1 text-center border" style="cursor:pointer;">
              <i id="delete" min="" class="bi bi-trash-fill text-danger fs-4"></i>
          </div>
          <div class="col-md-1 text-center border d-flex" style="cursor:pointer;" >
            <i id="move-up" class="bi bi-caret-up-fill fs-4"></i>
            <i id="move-down" class="bi bi-caret-down-fill fs-4"></i>
          </div>
          <div id="move" class="col-md-1 text-center border" style="cursor:pointer;">
            <i class="bi bi-arrows-move fs-4"></i>
          </div>

          `;
          document.querySelector('.'+section).appendChild(mydiv);
         
    },



    intended_learners_action:function(e){
      var action = e.target.id;
      if(action =='delete'){
        var min = 1;
        if(e.currentTarget.parentNode.children.length <= min){
          alert(`you cant delete. you need to have min ${min}`);
          return;
        }

        
        e.currentTarget.remove();
        course_message.something_changed();
      }else
      if(action =='move-up'){
        var move_to = e.currentTarget;
        var where_to_move=e.currentTarget.previousElementSibling;
        var container = e.currentTarget.parentNode;
        container.insertBefore(move_to,where_to_move);
        course_message.something_changed();

      }
      if(action =='move-down'){
        var move_to = e.currentTarget;
        var where_to_move=e.currentTarget.nextElementSibling.nextElementSibling;
        var container = e.currentTarget.parentNode;
        container.insertBefore(move_to,where_to_move);
        course_message.something_changed();

      }

    },


    intended_learners_dragstart:function(e){
      item_to_drug  =e.currentTarget;
    },


    intended_learners_dragover:function(e){
      item_drug_to  =e.currentTarget;

    },


    intended_learners_dragend:function(e){
      item_drug_to.parentNode.insertBefore(item_to_drug,item_drug_to.nextElementSibling);

    },


    handle_result:function(result){
        var meta = result.data;
        if(!meta ==""){
           for(var i =0; i<meta.length; i++){
          if(meta[i].data_type =="students-learn"){
          this.add_new('js-students-learn',{value: meta[i].value,  name:'students-learn',placeHolder:'Example: Define the roles and responsibilities of a project manager'});

          }
          if(meta[i].data_type =="prerequisites"){
          intended_learners.add_new('js-prerequisites',{value: meta[i].value, name:'prerequisites',placeHolder:'Example: No programming experience needed. You will learn everything you need to know'});
          }

          if(meta[i].data_type =="whose-course"){
          intended_learners.add_new('js-whose-course',{value: meta[i].value, name:'whose-course',placeHolder:'Example: Beginner Python developers curious about data science'});
          }
        }
        }
       

    },
   }
    


  

</script>




<script>

 

  /*******************
    curriculum OOP javascript
  ********************/


   var item_to_drug = null;
   var item_drug_to = null;
             


   var curriculum =
   {
  
    counter : 0, // Initialize the counter property
      


    initailiz:function(){
      course_message.send_data({
          tab_name:tab,
          data_type:"curriculum",
        });

      },
   
    add_new:function(section,data){
      
      var mydiv = document.createElement('div');
        mydiv.classList.add('row');
        mydiv.classList.add('js-input');
        mydiv.classList.add('my-4');
        mydiv.classList.add('mx-2');
        mydiv.setAttribute('onclick','curriculum.action(event)');
        mydiv.setAttribute('ondragstart','curriculum.dragstart(event)');
        mydiv.setAttribute('ondragover','curriculum.dragover(event)');
        mydiv.setAttribute('ondragend','curriculum.dragend(event)');
        mydiv.setAttribute('draggable','true');


        mydiv.innerHTML = `
        <div class="border p-3 " style="background:#e6ec1445;">
        <div class="d-flex ">
          <div class="flex-grow-1 " style="">
          <input value="${data.value}" type="text" uid="${data.uid}"  class="col-md-9 my-2 py-2  form-control" name="${data.name}_${this.counter++}" placeholder="${data.placeHolder}">
          <input  type="text" value="${data.description}"  class="col-md-9 my-2 py-2   form-control"  name="description_${this.counter++}" placeholder="description">
          </div>
           
          <div id="delete" min=""  class="col-md-1 text-center d-flex justify-content-center align-items-center border" style="cursor:pointer;">
              <i id="delete" min="" class="bi bi-trash-fill text-danger fs-4 align-self-center"></i>
          </div>
          <div class="col-md-1 text-center border d-flex justify-content-center align-items-center " style="cursor:pointer;" >
            <i id="move-up" class="bi bi-caret-up-fill fs-4"></i>
            <i id="move-down" class="bi bi-caret-down-fill fs-4"></i>
          </div>
          <div id="move" class="col-md-1 text-center d-flex justify-content-center align-items-center border" style="cursor:pointer;">
          <i class="bi bi-arrows-move fs-4"></i>
        </div>
          
          </div>
            <div>
            <button onclick="lecture.add_new('js-lecture_${data.uid}',{description:'', value:'', uid:${data.uid}, placeHolder:'Enter title',name:'lecture'})" type="button" class="btn btn-sm btn-primary js-curriculum-add">+ Add Lecture</button>
            <div class="lectures"></div>
            <h4 class="pt-5 px-5">Lectures:</h4>
            <div class="js-lecture_${data.uid}"></div>
            </div>
          </div>
          `;
          document.querySelector('.'+section).appendChild(mydiv);
          lecture.input_counts++;

    },



   action:function(e){
      var action = e.target.id;
      if(action =='delete'){
        var min = 1;
        if(e.currentTarget.parentNode.children.length <= min){
          alert(`you cant delete. you need to have min ${min}`);
          return;
        }

        
        e.currentTarget.remove();
        course_message.something_changed();
      }else
      if(action =='move-up'){
        var move_to = e.currentTarget;
        var where_to_move=e.currentTarget.previousElementSibling;
        var container = e.currentTarget.parentNode;
        container.insertBefore(move_to,where_to_move);
        course_message.something_changed();

      }
      if(action =='move-down'){
        var move_to = e.currentTarget;
        var where_to_move=e.currentTarget.nextElementSibling.nextElementSibling;
        var container = e.currentTarget.parentNode;
        container.insertBefore(move_to,where_to_move);
        course_message.something_changed();

      }

    },


   dragstart:function(e){
      item_to_drug  =e.currentTarget;
    },


   dragover:function(e){
      item_drug_to  =e.currentTarget;

    },


   dragend:function(e){
      item_drug_to.parentNode.insertBefore(item_to_drug,item_drug_to.nextElementSibling);

    },


    handle_result:function(result){
        var meta = result.data;
        if(!meta ==""){
           for(var i =0; i<meta.length; i++){
          this.add_new('js-curriculum',{description: meta[i].description, value: meta[i].value, uid:meta[i].uid, placeHolder:'Enter title',name:'curriculum'});

         
        }
        }
       

    },
   }
    

   

  

</script>



<script>




  /*******************
    lecture OOP javascript
  ********************/
   var lecture =
   {
  
    counter : 0, // Initialize the counter property
      
    input_counts:0,

    initailiz:function(){
      course_message.send_data({
          tab_name:tab,
          data_type:"lecture",
        });

      },
   
    add_new:function(section,data){
      console.log(data.files);
      var mydiv = document.createElement('div');
        mydiv.classList.add('row');
        mydiv.classList.add('js-input');
        mydiv.classList.add('my-4');
        mydiv.classList.add('mx-2');
      


        mydiv.innerHTML = `
        <div class="border p-3 " >
          <input  type="text"   class="col-md-9 my-2 py-2  form-control" name="${data.name}_${this.input_counts++}_${data.uid}" value="${data.value}" placeholder="lecture title">
          <input  type="file"   class="col-md-9 my-2 py-2  form-control" name="files_${this.input_counts++}_${data.uid}">
         <video controls style="width:200px;">
         <source type="video/mp4" src="<?=ROOT?>/${data.files}" ></source>
         </video>
          </div>
          </div>
          `;
          document.querySelector('.'+section).appendChild(mydiv);
    },





    handle_result:function(result){
        var meta = result;
        if(!meta ==""){
          meta.forEach(function(subArray) {
          subArray.forEach(function(element) {
            lecture.add_new(`js-lecture_${element.uid}`,{files:element.file, description:'', value:element.title, uid:element.uid, placeHolder:'Enter title',name:'lecture'});
          });
        });
          //  for(var i =0; i<meta.length; i++){
          // this.add_new('js-curriculum',{description: meta[i].description, value: meta[i].value, uid:meta[i].uid, placeHolder:'Enter title',name:'curriculum'});

         
        // }
        }
       

    },
   }



   
    function getTimeRand() {
        var currentTime = new Date().getTime();
        var randomNum = Math.floor(Math.random() * 990) + 10;
        return currentTime + randomNum;
    }

</script>
  <?php $this->view('admin/includes/admin-footer',$data) ?>
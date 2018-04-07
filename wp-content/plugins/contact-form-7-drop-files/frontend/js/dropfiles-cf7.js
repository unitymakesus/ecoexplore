document.addEventListener( 'wpcf7mailsent', function( event ) {
    $(".cf7-drop-upload").val("");
    $(".cf7-drop-statusbar").remove();
}, false );
function cf7_sendFileToServer(formData,status){
  //var uploadURL = cf7_dropfiles.url_plugin + "frontend/upload.php"; //Upload URL
  var uploadURL = cf7_dropfiles.ajax_url; //Upload URL
  var extraData ={}; //Extra Data.
  var jqXHR=jQuery.ajax({
          xhr: function() {
            var xhrobj = jQuery.ajaxSettings.xhr();
            if (xhrobj.upload) {
                    xhrobj.upload.addEventListener('progress', function(event) {
                        var percent = 0;
                        var position = event.loaded || event.position;
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                        }
                        //Set progress
                        status.setProgress(percent);
                    }, false);
                }
            return xhrobj;
        },
      url: uploadURL,
      type: "POST",
    contentType:false,
    processData: false,
        cache: false,
        data: formData,
        success: function(data){
          status.setProgress(100);
          if( data.status =="ok" ) {
              status.name_upload(data.text);
              var name = jQuery(".cf7-drop-upload").val();
            if(name == "" ) {
                name = data.text;
            }else{
               name = name +"|"+data.text;
            }
            jQuery(".cf7-drop-upload").val(name); 
          }else{
              if( !data.text ) {
                //alert(data);
                status.text("Error: POST Content-Length limit");
              }else{
                  status.text(data.text);
              }
              
          }
                
    }
    }); 

  status.setAbort(jqXHR);
}

var rowCount=0;
function cf7_createStatusbar(obj){
   rowCount++;
   var row="odd";
   if(rowCount %2 ==0) row ="even";



     this.statusbar = jQuery("<div class='cf7-drop-statusbar "+row+"'></div>");
     this.filename = jQuery("<div class='cf7-drop-filename'></div>").appendTo(this.statusbar);
     this.size = jQuery("<div class='cf7-drop-filesize'></div>").appendTo(this.statusbar);
     this.progressBar = jQuery("<div class='cf7-drop-progressBar'><div></div></div>").appendTo(this.statusbar);
     this.abort = jQuery('<div class="cf7-drop-abort"><a href="#">Abort</a></div>').appendTo(this.statusbar);
     this.remove = jQuery('<div style="display:none" class="cf7-drop-remove"><a href="#">Remove</a></div>').appendTo(this.statusbar);
     obj.after(this.statusbar);
    
     this.text = function(txt){   
          this.progressBar.addClass("cf7-text-error").html(txt);
          this.remove.hide();
    }
     this.name_upload = function(txt){   
          this.remove.attr('data-name', txt);
    }

    this.setFileNameSize = function(name,size)
    {
      var sizeStr="";
      var sizeKB = size/1024;
      if(parseInt(sizeKB) > 1024)
      {
        var sizeMB = sizeKB/1024;
        sizeStr = sizeMB.toFixed(2)+" MB";
      }
      else
      {
        sizeStr = sizeKB.toFixed(2)+" KB";
      }
    
      this.filename.html(name);
      this.size.html(sizeStr);
    }
    this.setProgress = function(progress){   
      var progressBarWidth =progress*this.progressBar.width()/ 100;  
      this.progressBar.find('div').animate({ width: progressBarWidth }, 10).html(progress + "%&nbsp;");
      if(parseInt(progress) >= 100)
      {
        this.abort.hide();
        this.remove.show();
      }
    }

  this.setAbort = function(jqxhr){
    var sb = this.statusbar;
    this.abort.click(function()
    {
      jqxhr.abort();
      sb.hide();
    });
  }
}
function cf7_handleFileUpload(files,obj){

  var max = obj.attr("data-max");
  var name = jQuery(".cf7-drop-upload").val();
  var count = (name.match(/\|/g) || []).length;
  if(name != "" ){
      count++;
  }
  if( max == "" ){
    max = 0;
  }
  var max_limit = max - count + 1;
  if ( max != 0 && parseInt(files.length)>=max_limit){
      alert("You can only upload a maximum of "+max+" files");
    }else{
     for (var i = 0; i < files.length; i++) {
        var fd = new FormData();
        fd.append('file', files[i]);
        fd.append('size', obj.data("size") );
        fd.append('type', obj.data("type") );
        fd.append('action', "cf7_dropfiles_upload" );
        var status = new cf7_createStatusbar(obj); //Using this we can set progress.
        status.setFileNameSize(files[i].name,files[i].size);
        cf7_sendFileToServer(fd,status);
     
     }
  }
}
jQuery(document).ready(function($){

  var obj = jQuery(".cf7-dragandrophandler");
  obj.on('dragenter', function (e) 
  {
    e.stopPropagation();
    e.preventDefault();
    jQuery(this).css('border', '2px solid #0B85A1');
  });
  obj.on('dragover', function (e) 
  {
     e.stopPropagation();
     e.preventDefault();
  });
  obj.on('drop', function (e) 
  {
    
     jQuery(this).css('border', '2px dotted #0B85A1');
     e.preventDefault();
     var files = e.originalEvent.dataTransfer.files;
     //We need to send dropped files to Server
     cf7_handleFileUpload(files,obj);
  });
  jQuery(document).on('dragenter', function (e) 
  {
    e.stopPropagation();
    e.preventDefault();
  });
  jQuery(document).on('dragover', function (e) 
  {
    e.stopPropagation();
    e.preventDefault();
    obj.css('border', '2px dotted #0B85A1');
  });
  jQuery(document).on('drop', function (e) 
  {
    e.stopPropagation();
    e.preventDefault();
  });
  obj.find('a').on('click', function (e) 
  {
    //e.stopPropagation();
    e.preventDefault();
    $(this).closest(".cf7-dragandrophandler-container").find('.input-uploads').click();
  });

   obj.closest(".cf7-dragandrophandler-container").find('.input-uploads').on('change', function (e) 
  {
     var files = this.files;
     //We need to send dropped files to Server
     cf7_handleFileUpload(files,obj);
  });

  $("body").on("click",".cf7-drop-remove a",function(e){
     e.preventDefault();
     var cr_name = $(this).closest('.cf7-drop-remove').data("name") ;
     var data = {
      'action': 'cf7_dropfiles_remove',
      'name': cr_name
    };
    var name = $('.cf7-drop-upload').val().split("|");
    
    for (var i=name.length-1; i>=0; i--) {
        if (name[i] === cr_name) {
            name.splice(i, 1);
        }
    }

     $('.cf7-drop-upload').val( name.join("|") );
     $(this).closest('.cf7-drop-statusbar').remove();
     jQuery.post(cf7_dropfiles.ajax_url, data, function(response) {
      
    });
     
  })
});
tinymce.init({
        toolbar : "imageupload",
        setup: function(editor) {
            var inp = $('<input id="tinymce-uploader" type="file" name="pic" accept="image/*" style="display:none">');
            $(editor.getElement()).parent().append(inp);

            inp.on("change",function(){
                var input = inp.get(0);
                var file = input.files[0];
                var fr = new FileReader();
                fr.onload = function() {
                    var img = new Image();
                    img.src = fr.result;
                    editor.insertContent('<img src="'+img.src+'"/>');
                    inp.val('');
                }
                fr.readAsDataURL(file);
            });

            editor.addButton( 'imageupload', {
                text:"IMAGE",
                icon: false,
                onclick: function(e) {
                    inp.trigger('click');
                }
            });
        }
    });



<script src="{{ asset('js/tinymce/js/tinymce/tinymce.js') }}"></script>
    <script type="text/javascript">
        tinymce.init({
            selector: '.episode-content',
            height: 500,
            plugins: 'imagetools link table charmap hr nonbreaking insertdatetime advlist lists textcolor wordcount spellchecker imagetools colorpicker',
            toolbar : "formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat imageupload",
            setup: function(editor) {
                var inp = $('<input id="tinymce-uploader" type="file" name="pic" accept="image/*" style="display:none">');
                $(editor.getElement()).parent().append(inp);

                inp.on("change",function(){
                    var input = inp.get(0);
                    var file = input.files[0];
                    var fr = new FileReader();
                    fr.onload = function() {
                        var img = new Image();
                        img.src = fr.result;
                        editor.insertContent('<img src="'+img.src+'"/>');
                        inp.val('');
                    }
                    fr.readAsDataURL(file);
                });

                editor.addButton( 'imageupload', {
                    text:"Insert Image",
                    icon: false,
                    onclick: function(e) {
                        inp.trigger('click');
                    }
                });
            }     
        });
    </script>



selector: '.episode-content',
            height: 500,
            theme: 'modern',
            powerpaste_allow_local_images: true,
            plugins: 'print preview fullpage paste searchreplace autolink directionality visualblocks visualchars fullscreen image imagetools link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists textcolor wordcount spellchecker imagetools media  link contextmenu colorpicker textpattern help',
            toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link image | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat',
            image_advtab: true,
            templates: [
                { title: 'Test template 1', content: 'Test 1' },
                { title: 'Test template 2', content: 'Test 2' }
            ],
            content_css: [
                '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
                '//www.tinymce.com/css/codepen.min.css'
            ]






////////////////



<script>
tinymce.init({
    selector: '#myTextarea',
    plugins: 'image code',
    toolbar: 'undo redo | image code',
    
    // without images_upload_url set, Upload tab won't show up
    images_upload_url: 'upload.php',
    
    // override default upload handler to simulate successful upload
    images_upload_handler: function (blobInfo, success, failure) {
        var xhr, formData;
      
        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', 'upload.php');
      
        xhr.onload = function() {
            var json;
        
            if (xhr.status != 200) {
                failure('HTTP Error: ' + xhr.status);
                return;
            }
        
            json = JSON.parse(xhr.responseText);
        
            if (!json || typeof json.location != 'string') {
                failure('Invalid JSON: ' + xhr.responseText);
                return;
            }
        
            success(json.location);
        };
      
        formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());
      
        xhr.send(formData);
    },
});
</script>





<?php
// Allowed origins to upload images
$accepted_origins = array("http://localhost", "http://107.161.82.130", "http://codexworld.com");

// Images upload path
$imageFolder = "images/";

reset($_FILES);
$temp = current($_FILES);
if(is_uploaded_file($temp['tmp_name'])){
    if(isset($_SERVER['HTTP_ORIGIN'])){
        // Same-origin requests won't set an origin. If the origin is set, it must be valid.
        if(in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)){
            header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        }else{
            header("HTTP/1.1 403 Origin Denied");
            return;
        }
    }
  
    // Sanitize input
    if(preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])){
        header("HTTP/1.1 400 Invalid file name.");
        return;
    }
  
    // Verify extension
    if(!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))){
        header("HTTP/1.1 400 Invalid extension.");
        return;
    }
  
    // Accept upload if there was no origin, or if it is an accepted origin
    $filetowrite = $imageFolder . $temp['name'];
    move_uploaded_file($temp['tmp_name'], $filetowrite);
  
    // Respond to the successful upload with JSON.
    echo json_encode(array('location' => $filetowrite));
} else {
    // Notify editor that the upload failed
    header("HTTP/1.1 500 Server Error");
}
?>






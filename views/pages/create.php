<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<script type="text/javascript" src="<?=base_url('assets/plugins/tinymce/tinymce.min.js');?>"></script>
<script type="text/javascript">
   /** @namespace tinimce */
   tinymce.init({
      selector: "#post_content",
      toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent table",
      toolbar2: "fontsizeselect styleselect | link unlink anchor image | forecolor backcolor code",
      image_advtab: true,
      fontsize_formats: "8px 10px 12px 14px 18px 24px 36px",
      relative_urls: false,
      remove_script_host: false,
      plugins: [
         "advlist autolink link image lists charmap print preview hr anchor pagebreak",
         "searchreplace wordcount visualblocks visualchars insertdatetime nonbreaking",
         "table contextmenu directionality emoticons paste textcolor",
         "code autoresize"
      ],
      external_filemanager_path:"<?=base_url();?>assets/plugins/filemanager/",
      filemanager_title:"Media",
      external_plugins: { 
         "filemanager" : "<?=base_url();?>assets/plugins/filemanager/plugin.min.js"
      }
   });

   /** @namespace posts */
   $( document ).ready( function() {
      /* Triger Tinymce plugins */
      $('.tiny-text').on('click', function (e) {
         e.preventDefault();
         $('.tiny-text').addClass('btn-success');
         $('.tiny-visual').removeClass('btn-success').addClass('btn-default');
         tinymce.EditorManager.execCommand('mceRemoveEditor',true, 'post_content');
      });

      /* Remove Tinymce plugins */
      $('.tiny-visual').on('click', function (e) {
         e.preventDefault();
         $('.tiny-visual').addClass('btn-success');
         $('.tiny-text').removeClass('btn-success').addClass('btn-default');
         tinymce.EditorManager.execCommand('mceAddEditor',true, 'post_content');
      });

      /* Browse File */
      $('#browse-file').on('click', function() {
         var options = {
            url: '<?=base_url('assets/plugins/filemanager/dialog.php?type=1&amp;field_id=post_image');?>',
            size: 'lg' 
         }
         eModal.iframe(options, 'Media Library'); // size: eModal.size.lg,
      });

      $( '#submit' ).on('click', function(event) {
         event.preventDefault();
         var field_data = {
            post_title: $('#post_title').val(),
            post_content: tinyMCE.get('post_content').getContent(),
            post_status: $('#post_status').val(),
            post_visibility: $('#post_visibility').val(),
            post_comment_status: $('#post_comment_status').val()
         };
         // send data
         $.post('<?=$action;?>', field_data, function(response) {
            var res = H.stringToJSON(response);
            H.growl(res.type, H.message(res.message));
            if (res.action == 'save') {
               $('input[type="text"]').val('');
               $('#post_status').val('publish');
               $('#post_visibility').val('public');
               $('#post_comment_status').val('open');
               tinyMCE.get('post_content').setContent('');
               $('#post_title').focus();
            }
         }).fail( function( xhr, textStatus, errorThrown ) {
            xhr.textStatus = textStatus;
            xhr.errorThrown = errorThrown;
            if( !errorThrown ) errorThrown = 'Unable to load resource, network connection or server is down?';
            H.growl('error', textStatus + ' ' + errorThrown + '<br/>' + xhr.responseText );
         });    
      });  
   });
</script>
<section class="content-header">
   <h1><i class="fa fa-edit"></i> <?=$title;?></h1>
 </section>
<section class="content">
   <form>
   <div class="row">
      <div class="col-lg-8">
         <div class="panel panel-default">
            <div class="panel-body">
               <div class="form-group" style="margin-bottom: 10px;">
                  <input id="post_title" name="post_title" value="<?=($query ? $query->post_title : '');?>" autofocus required="true" type="text" class="form-control input-lg" placeholder="Enter title here..." style="font-size: 16px">
               </div>
               <div class="form-group">
                  <div class="btn-group">
                     <button class="btn-success btn btn-xs tiny-visual">Visual</button>
                     <button class="btn btn-xs tiny-text">Text</button>
                  </div>
               </div>
               <div class="form-group">
                  <textarea rows="25" id="post_content" name="post_content" class="form-control ckeditor"><?=($query ? $query->post_content : '');?></textarea>
               </div>
            </div>
         </div>
      </div>
      <div class="col-lg-4">
         <div class="box box-default">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-edit"></i> Publikasi</h3>
            </div>
            <div class="box-body">
               <div class="form-group">
                  <div class="row">
                     <div class="col-lg-6">
                        <label class="control-label" for="post_status">Status</label>
                        <?=form_dropdown('post_status', ['publish' => 'Diterbitkan', 'draft' => 'Konsep'], ($query ? $query->post_status : ''), 'class="form-control input-sm" id="post_status"');?>
                     </div>
                     <div class="col-lg-6">
                        <label class="control-label" for="post_visibility">Akses</label>
                        <?=form_dropdown('post_visibility', ['public' => 'Publik', 'private' => 'Private'], ($query ? $query->post_visibility : ''), 'class="form-control input-sm" id="post_visibility"');?>        
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label" for="post_comment_status">Komentar</label>
                  <?=form_dropdown('post_comment_status', ['open' => 'Diizinkan', 'close' => 'Tidak Diizinkan'], ($query ? $query->post_comment_status : ''), 'class="form-control input-sm" id="post_comment_status"');?>
               </div>
            </div>
            <div class="box-footer">
               <div class="btn-group pull-right">
                  <button type="reset" class="btn btn-info btn-sm"><i class="fa fa-retweet"></i> ATUR ULANG</button>
                  <button type="submit" id="submit" class="btn btn-primary btn-sm"><i class="fa fa-send-o"></i> SIMPAN</button> 
               </div>
            </div>
         </div>
      </div>
   </div>
   </form>
</section>
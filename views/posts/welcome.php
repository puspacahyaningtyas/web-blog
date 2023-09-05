<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<script type="text/javascript" src="<?=base_url('assets/plugins/tinymce/tinymce.min.js');?>"></script>
<script type="text/javascript">
	/** @namespace tinymce */
	tinymce.init({
		selector: "#post_content",
		toolbar1: "undo redo bold italic underline alignleft aligncenter alignright alignjustify bullist numlist outdent indent table",
		toolbar2: "fontsizeselect styleselect link unlink anchor forecolor backcolor code",
		image_advtab: true,
		fontsize_formats: "8px 10px 12px 14px 18px 24px 36px",
		// relative_urls: true,
		remove_script_host: false,
		plugins: [
			"advlist autolink link image lists charmap print preview hr anchor pagebreak",
			"searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
			"table contextmenu directionality emoticons paste textcolor",
			"code youtube autoresize "
		],
		convert_urls: false,
		relative_urls: false
	});

	// Save
	function save() {
		$('#submit').attr('disabled', 'disabled');
		$('#loading').show();
		var values = {
			post_content: tinyMCE.get('post_content').getContent()
		};
		$.post('<?=site_url("blog/welcome/save");?>', values, function(response) {
			var res = H.stringToJSON(response);
			H.growl(res.type, H.message(res.message));
			$('#post_content').val('');
			$('#submit').removeAttr('disabled');
			$('#loading').hide();
		});
	}
</script>
<section class="content-header">
   <h1><i class="fa fa-bullhorn text-red"></i> <?=ucwords(strtolower($title));?></h1>
 </section>
 <section class="content">
 	<div class="panel panel-default">
		<div class="panel-body">			
			<form role="form">
				<div class="box-body">
					<div class="form-group">
               	<textarea rows="25" id="post_content" name="post_content" class="form-control ckeditor"><?=$query?></textarea>
            	</div>
				</div>
				<div class="box-footer">
               <button type="submit" onclick="save(); return false;" class="btn btn-primary"><i class="fa fa-save"></i> SIMPAN</button>
               <img id="loading" style="display: none;" src="<?=base_url('assets/img/loading.gif');?>">
            </div>
         </form>
		</div>
	</div>
 </section>
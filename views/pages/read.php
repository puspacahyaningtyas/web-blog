<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<?php $this->load->view('backend/grid_index');?>
<script type="text/javascript">
    var _grid = 'PAGES';
    new GridBuilder( _grid , {
        controller:'blog/pages',
        fields: [
            { 
                header: '<input type="checkbox" class="check-all">', 
                renderer:function(row) {
                    return CHECKBOX(row.id, 'id');
                },
                exclude_excel : true,
                sorting: false
            },
            { 
                header: '<i class="fa fa-cog"></i>', 
                renderer:function(row) {
                    return Ahref(_BASE_URL + 'blog/pages/create/' + row.id,'Edit');
                },
                exclude_excel : true
            },
            { header:'Judul', renderer:'post_title' },
            { header:'Penulis', renderer:'author' },
            { header:'Tanggal', renderer:'created_at', type:'date' }
        ],
        can_add:false,
        extra_buttons: '<a class="btn btn-warning btn-sm add" href="' + _BASE_URL + 'blog/pages/create'+'" data-toggle="tooltip" data-placement="top" title="Add"><i class="fa fa-plus"></i></a>' 
    });
</script>
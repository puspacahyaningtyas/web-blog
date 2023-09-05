<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<?php $this->load->view('backend/grid_index');?>
<script type="text/javascript">
    DS.Majors = <?=$majors_dropdown;?>;
    var _grid = 'CLASS_GROUPS', _form = _grid + '_FORM';
	new GridBuilder( _grid , {
        controller:'students/class_groups',
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
                    return A(_form + '.OnEdit(' + row.id + ')', 'Edit');
                },
                exclude_excel : true,
                sorting: false
            },
    		{ header:'Kelas', renderer:'class_group', sorting: false }
    	]
    });

    new FormBuilder( _form , {
	    controller:'students/class_groups',
	    fields: [
            { label:'Kelas', name:'class' },
            { label:'Sub Kelas', name:'sub_class' },
            { label:'Jurusan / Program Keahlian', name:'major_id', type:'select', datasource:DS.Majors }
	    ]
  	});
</script>
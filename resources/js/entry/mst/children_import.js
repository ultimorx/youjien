'use strict';

require('../../common');

$(document).ready(() => {

    const list = new Vue({
        el: '#list',
        data: {
            list: null,
            result_msg: '',
            error_msg: ''
        }
    });


    const editForm = new Vue({
        el: '#edit-form',
        data: {
            csvFile: null,
            validation_error: null
        }
    });

    // ファイル選択すると、ファイル名を表示
    $('.custom-file-input').on('change',function(){
        let name = ($(this)[0].files.length > 0)? $(this)[0].files[0].name: '';
        $(this).next('.custom-file-label').html(name);
    })

    $(document).on('click', '#btn-import', () => {
        UploadImport();
    });

    $('form').on('change', 'input[type="file"]', function(e){
        $('#list').hide();
        list.list = null;
    });

    function UploadImport() {
        var files = $('#csvfile')[0].files;
        // console.log('UploadImport()', this, files);
        // return;

        var formData = new FormData();
    	$.each(files, function(i, file){
    		formData.append('file', file);
    	});
    	$.ajax({
            url: '/api/mst/children/import',
    		type: 'post',
    		data: formData,
    		processData: false,
    		contentType: false,
    		// dataType: 'html', // この指定をしてはいけない
    		// success: function(res) {
            //     console.log('success', res.length, res);
            // }
        }).done((res) => {
            console.log('done', res.save_list.length, res);
            $('#list').show();
            if( res.save_list.length > 0 ) {
                list.list = res.save_list;
            }
            list.result_msg = res.result_msg;
            list.error_msg = res.error_msg;
        });
    }
});

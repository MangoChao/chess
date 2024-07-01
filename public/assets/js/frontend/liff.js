define(['jquery', 'bootstrap', 'frontend', 'form', 'template'], function ($, undefined, Frontend, Form, Template) {
    var validatoroptions = {
        invalid: function (form, errors) {
            $.each(errors, function (i, j) {
                Layer.msg(j);
            });
        }
    };
    var Controller = {
        index: function () {
            var mload = layer.load();
            // Toastr.options.timeOut = '2500';
            // Form.api.bindevent($("#mform"), function (mthis, result, ret){
            //     let code = result.data.code;
            //     if(code == 0){
            //         let line_link = result.data.line_link;
            //         $('#line_link').attr('href',line_link);
            //         $('.step_box').hide();
            //         $('#step02_box').show();
            //     }else if(code == 1){
            //         // let phone = result.data.phone;
            //         // $('#step03_phone').val(phone);
            //         // $('.step_box').hide();
            //         // $('#step03_box').show();
            //     }else{
            //         Layer.msg(result.msg);
            //     }
            // });
            
            // Form.api.bindevent($("#mform2"), function (mthis, result, ret){
            //     let code = result.data.code;
            //     if(code == 0){
            //         let line_link = result.data.line_link;
            //         $('#line_link').attr('href',line_link);
            //         $('.step_box').hide();
            //         $('#step02_box').show();
            //     }else{
            //         Layer.msg(result.msg);
            //     }
            // });

            
            // $(document).on("click", ".btn_iscust", function () {
            //     let options = {
            //         url: Config.url.api+'/customer/iscust', 
            //         type: "POST",
            //         data: $('#mform').serialize(),
            //         dataType: 'json',
            //         success: function (ret) {
            //             let code = ret.data.code;
            //             if(code == 0){
            //                 let line_link = ret.data.line_link;
            //                 $('#line_link').attr('href',line_link);
            //                 $('.step_box').hide();
            //                 $('#step02_box').show();
            //             }else if(code == 1){
            //                 Toastr.error(ret.msg);
            //             }else{
            //                 Layer.msg(ret.msg);
            //             }
            //         },
            //         error: function (ret) {
            //             Layer.msg(ret.msg);
            //         }
            //     };
            //     $.ajax(options);
            // });

            // console.log(liff_id);
            liff.init({liffId: liff_id}).then(() => {
                // setTimeout(function () {
                //     if($('#step0_box')){
                //         $('#step0_box_loding').hide();
                //         $('#step0_box').slideDown();
                //     }
                // },5000);
                var liffContext = liff.getContext();
                if(liffContext.userId){
                    // $('#userid').val(liffContext.userId);
                    // $('#step01_form_box_loding').hide();
                    // $('#step01_form_box').slideDown();

                    
                    // let options = {
                    //     url: Config.url.api+'/customer/qrlog?uid='+liffContext.userId
                    // };
                    // $.ajax(options);

                    // Layer.msg(liffContext.userId);
                }else{
                    Layer.msg('請由正確網址登入系統"');
                }
                layer.close(mload);
            });
        },
    };
    return Controller;
});

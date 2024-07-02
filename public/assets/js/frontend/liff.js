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
            let liff_id = '1655633839-6Jmn4YaZ';
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
                    
                    $(document).on("click", "#test", function () {
                        liff.sendMessages([
                            {
                                type: "text",
                                text: "Hello, World!",
                            },
                        ])
                        .then(() => {
                            console.log("message sent");
                        })
                        .catch((err) => {
                            console.log("error", err);
                        });
                    });

                }else{
                    Layer.msg('載入異常');
                }
                layer.close(mload);
            });
        },
    };
    return Controller;
});

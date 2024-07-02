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
            $(document).on("click", ".cho .chess_row a", function () {
                let cho = $(this).data("id");

                let choChessArray = [];
                let cho_chess = $("#cho_chess").val();
                if(cho_chess == ""){
                    choChessArray = [cho];
                }else{
                    choChessArray = cho_chess.split(',');
                    choChessArray.push(cho);
                }
                if(choChessArray.length <= 5){
                    $("#cho_chess").val(choChessArray.join(',')); 
    
                    const ids = ['cho1', 'cho2', 'cho3', 'cho4', 'cho5'];
                    ids.forEach((id, index) => {
                        $("#"+id).text(chess[choChessArray[index]]);
                    });
                    $(this).addClass("active");
                    
                    if(choChessArray.length == 5){
                        $(".send_btn").removeClass("hide");
                    }
                }
            });

            var mload = layer.load();
            let liff_id = '1655633839-6Jmn4YaZ';
            liff.init({liffId: liff_id}).then(() => {
                var liffContext = liff.getContext();
                if(liffContext.userId){
                    $(document).on("click", ".send_btn", function () {
                        let cho_chess = $("#cho_chess").val();
                        liff.sendMessages([
                            {
                                type: "text",
                                text: cho_chess,
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

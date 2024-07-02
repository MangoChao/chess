define(['jquery'], function ($) {
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

            let liff_id = '1655633839-W9D6zO39';
            liff.init({liffId: liff_id}).then(() => {
                var liffContext = liff.getContext();
                if(liffContext.userId){
                    $(document).on("click", ".send_btn", function () {
                        let cho_chess = $("#cho_chess").val();
                        liff.sendMessages([
                            {
                                type: "text",
                                text: Config.url.furl+'?cho='+cho_chess,
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
            });
        },
    };
    return Controller;
});

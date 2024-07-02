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
                $(this).addClass("active");
                let cho = $(this).data("id");

                let cho_chess = $("#cho_chess").val();
                let choChessArray = cho_chess.split(',');
                console.log(choChessArray);
                choChessArray.push(cho);
                console.log(choChessArray);
                $("#cho_chess").val(choChessArray.join(',')); 

                const ids = ['cho1', 'cho2', 'cho3', 'cho4', 'cho5'];
                ids.forEach((id, index) => {
                    $("#"+id).text(choChessArray[index]);
                });
            });

            var mload = layer.load();
            let liff_id = '1655633839-6Jmn4YaZ';
            liff.init({liffId: liff_id}).then(() => {
                var liffContext = liff.getContext();
                if(liffContext.userId){
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

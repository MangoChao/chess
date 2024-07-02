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
            let cho_chess = $("#cho_chess").val();
            choChessArray = cho_chess.split(',');
            $("#cho_chess").val(choChessArray.join(',')); 

            const ids = ['cho1', 'cho2', 'cho3', 'cho4', 'cho5'];
            ids.forEach((id, index) => {
                $("#"+id).text(chess[choChessArray[index]]);
            });
                
        },
    };
    return Controller;
});

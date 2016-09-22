<script>
$(document).ready(function() {
    table = $('#stat_table').DataTable( {
        "lengthMenu": [[100, 200, 500, -1], [100, 200, 500, "Все"]]
    } );

    $('.checkAll').on('click', function(e) {
        $('.cb-element').attr('checked', $(e.target).prop('checked'));
        if($(e.target).prop('checked')){
            $('.cb-element').prop('checked', true);
        }
    });
} );

function changeComp() {
    var comp_id = $("#comp_id option:selected").val();
    $.post("modules/user_comp/list_comp_u.php", {comp_id:comp_id},
            function(data){
                //alert(data);
                var obj = jQuery.parseJSON(data);
                if(obj.result=='OK'){
                    table.destroy();
                    $('#u_table').html(obj.html);
                    table = $('#stat_table').DataTable( {
                        "lengthMenu": [[100, 200, 500, -1], [100, 200, 500, "Все"]]
                    } );

                }
                else{
                    swal("Ошибка Сервера!", "Объект ненайден !", "error");
                }
            });
}

function addUComp() {
    var comp_id = $("#comp_id option:selected").val();
    var users = new Array();
    var ROOT_ID = {ROOT_ID};
    var num = parseInt($('#num_art').val());
    $('.cb-element').each(function(i,elem) {
        if($(elem).prop('checked')){
            users.push(this.id);
        }
    });
    console.log(users);
    $.post("modules/user_comp/add_comp_u.php", {comp_id:comp_id, users:users, ROOT_ID:ROOT_ID},
            function(data){
                //alert(data);
                var obj = jQuery.parseJSON(data);
                if(obj.result=='OK'){
                    $('#u_table').html(obj.html);
                    if(num==1) {
                        swal("Успешно", "Пользователям назначен комплекс задач.", "success");
                    }
                }
                else{
                    swal("Ошибка Сервера!", "Объект ненайден !", "error");
                }
            });

    if(num>1){
        for(var i=2;i<=num;i++) {
            comp_id = $("#comp_id" + i + " option:selected").val();
            $.post("modules/user_comp/add_comp_u.php", {comp_id:comp_id, users:users, ROOT_ID:ROOT_ID},
                    function(data){
                        //alert(data);
                    });
        }
        swal("Успешно", "Пользователям назначен комплекс задач.", "success");
    }
}

function addArtSel() {
    var ROOT_ID = {ROOT_ID};
    var num = parseInt($('#num_art').val())+1;
    $.post("modules/user_comp/add_art_sel.php", {ROOT_ID:ROOT_ID, num:num},
            function(data){
                var obj = jQuery.parseJSON(data);
                if(obj.result=='OK'){
                    $('#ArtSelDiv').append(obj.html);
                    $('#num_art').val(num);
                }
                else{
                    swal("Ошибка Сервера!", "Объект ненайден !", "error");
                }
            });
}

function delArtSel() {
    var num = parseInt($('#num_art').val());
    //alert($("#comp_id"+num+" option:selected").val());
    if(num>1){
        $("#comp_id"+num).parent('p').remove();
        $("#comp_id"+num).remove();
        num=num-1;
        $('#num_art').val(num);

    }
    else{
        swal("Ошибка!", "Нельзя удалить все статьи !", "error");
    }

}
</script>
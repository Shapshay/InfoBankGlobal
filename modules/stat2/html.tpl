<link href="adm/inc/will_pickdate/style.css" media="screen" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="adm/inc/will_pickdate/jquery.mousewheel.js"></script>
<script type="text/javascript" src="adm/inc/will_pickdate/will_pickdate.js"></script>
<script>
$(document).ready(function() {
    table = $('#stat_table').DataTable( {
        "lengthMenu": [[100, 200, 500, -1], [100, 200, 500, "Все"]]
    } );

    $('#date_start').will_pickdate({
        format: 'd-m-Y',
        inputOutputFormat: 'd-m-Y',
        days: ['Понедельник', 'Вторник', 'Среда', 'Четверг','Пятница', 'Суббота', 'Воскресенье'],
        months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
        timePicker: false,
        timePickerOnly: false,
        militaryTime: false,
        allowEmpty:true ,
        yearsPerPage:10
    });

    $('#date_end').will_pickdate({
        format: 'd-m-Y',
        inputOutputFormat: 'd-m-Y',
        days: ['Понедельник', 'Вторник', 'Среда', 'Четверг','Пятница', 'Суббота', 'Воскресенье'],
        months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
        timePicker: false,
        timePickerOnly: false,
        militaryTime: false,
        allowEmpty:true ,
        yearsPerPage:10
    });
} );

function ShowStatTable(){
    var date_start = $('#date_start').val();
    var date_end = $('#date_end').val();
    //alert(limit);
    $('#table_rows').html('');
    $('#waitGear').show();
    $.post("modules/stat2/show_stat.php", {date_start:date_start,date_end:date_end},
            function(data){
                //alert(data);

                var obj = jQuery.parseJSON(data);
                if(obj.result=='OK'){
                    table.destroy();
                    $('#table_rows').html(obj.html);
                    console.log(obj.sql);
                    table = $('#stat_table').DataTable( {
                        "lengthMenu": [[50, 100, 500, -1], [50, 100, 500, "Все"]]
                    } );
                }
                else{
                    swal("Ошибка Сервера!", "Сбой записи !", "error");
                }
            });
}
</script>
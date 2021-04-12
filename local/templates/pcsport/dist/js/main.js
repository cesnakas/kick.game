$(function () {
    $('#datetimepicker2').datetimepicker({
        locale: 'ru',
        icons: {
            time: "fas fa-clock",
            date: "fas fa-calendar-alt",
            //up: "fa fa-arrow-up",
            //down: "fa fa-arrow-down"
        }
    });
    
    $('.dashboard-time').datetimepicker({
        locale: 'ru',
        icons: {
            time: "fas fa-clock",
            date: "fas fa-calendar-alt",
            //up: "fa fa-arrow-up",
            //down: "fa fa-arrow-down"
        }
    });
    
    $('#calendarDashboard').datepicker({
        
        onSelect: function(fd, d, picker) {
            location.href = "/dashboard?date=" + fd; // /news?date=2014-02-22
        }
    });
    /*
    function createNext(index) {
        
        if ($('#input-' + index).length == 0) {
            
            let next = $('<div class="form-group" style="position: relative;">\n' +
                '          <label>'+ index +' игра укажите дату</label>\n' +
                '          <input type="text" name="dateTime[]" class="form-control dashboard-time" value="">\n' +
                '        </div>');
            
            next.on('change', function() {
                createNext(index + 1);
               
            });
            
            
            next.appendTo($('.wrap-input'));
    
            
        }
        
    }
    
    createNext(2);
    */
});

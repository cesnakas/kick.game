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
    $('#calendarDashboardTournament').datepicker({

        onSelect: function(fd, d, picker) {
            location.href = "/dashboard/tournament-results?date=" + fd; // /news?date=2014-02-22
        }
    });
    $('#calendarDashboardInvites').datepicker({

        onSelect: function(fd, d, picker) {
            location.href = "/dashboard/invites?date=" + fd; // /news?date=2014-02-22
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

    var max_fields = 50;
    var wrapper = $(".prizePlaces");
    var add_button = $(".add_form_field");

    var x = 1;
    $(add_button).click(function(e) {
        e.preventDefault();
        if (x < max_fields) {
            x++;
            $(wrapper).append('<div class="col-md-2">\n' +
              '        <div class="form-group">\n' +
              '          <label>'+x+ 'место</label><input type="text" class="form-control" name="prize[]"/></div><a href="#" class="delete">Delete</a></div>'); //add input box
        } else {
            alert('You Reached the limits')
        }
    });

    $(wrapper).on("click", ".delete", function(e) {
        e.preventDefault();
        $(this).parent('div').remove();
        x--;
    })
});
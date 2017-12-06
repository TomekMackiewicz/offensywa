$(document).ready(function() {

    $.timepicker.regional['pl'] = {
        timeOnlyTitle: 'Wybierz godzinę',
        timeText: 'Czas',
        hourText: 'Godzina',
        minuteText: 'Minuta',
        secondText: 'Sekunda',
        millisecText: 'Milisekunda',
        timezoneText: 'Strefa czasowa',
        currentText: 'Teraz',
        closeText: 'Gotowe',
        timeFormat: 'hh:mm tt',
        amNames: ['AM', 'A'],
        pmNames: ['PM', 'P'],
        ampm: false
    };
    $.timepicker.setDefaults($.timepicker.regional['pl']);    
    
    $('.btn-danger').on('click', function(e) {
        e.preventDefault();
        confirmDelete(this.closest("form"));
    });

    function confirmDelete(form) {
        var result = confirm('Are you sure you want to delete this question?');
        if (result === true) {
            $(form).submit();
        }
    }; 

    $("#bars li .bar").each(function(key, bar){
        var percentage = $(this).data('percentage');
        $(this).animate({
            'height':percentage+'%'
        }, 1000);
    });

    $(".datetimepicker").datetimepicker({
        timeFormat: 'HH:mm',
        dateFormat : 'dd-mm-yy',
        changeMonth: true,
        changeYear: true                        
        //controlType: 'select'
        //addSliderAccess: true,
        //sliderAccessArgs: { touchonly: true }
    });                    

    $(".datepicker").datepicker({
        dateFormat : 'dd-mm-yy',
        changeMonth: true,
        changeYear: true                        
    });                    

    $(".monthpicker").datepicker({
        dateFormat : 'mm-yy',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        onClose: function(dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).val($.datepicker.formatDate('mm-yy', new Date(year, month, 1)));
        }        
    }); 

    $(".monthpicker").focus(function () {
        $(".ui-datepicker-calendar").hide();
        $("#ui-datepicker-div").position({
            my: "center top",
            at: "center bottom",
            of: $(this)
        });
    });    
    
    $(".timepicker").timepicker({
        timeFormat: 'HH:mm',
        stepMinute: 15
    });     

    $('#appbundle_payment_paymentCategory').change(function() {
        var amount = $('option:selected', this).attr('data-amount');
        console.log(amount);
        $('#appbundle_payment_amount').val(amount);
    });

    var $homeTeam = $('#appbundle_game_homeTeam');
    $homeTeam.change(function() {
        var $form = $(this).closest('form');
        var data = {};
        data[$homeTeam.attr('name')] = $homeTeam.val();
        $.ajax({
            url : $form.attr('action'),
            type: $form.attr('method'),
            data : data,
            success: function(html) {
                $('#appbundle_game_awayTeam').replaceWith(
                    $(html).find('#appbundle_game_awayTeam')
                );
            }
        });
    });                                   

    var matchDateInput = $('#appbundle_game_date');
    if(typeof matchDateInput.val() !== 'undefined') {
        var now = new Date();                        
        var homeTeamScore = $('#appbundle_game_homeTeamScore');
        var awayTeamScore = $('#appbundle_game_awayTeamScore');
        if(!matchDateInput.val().trim()) {
            $("#appbundle_game_homeTeamScore").prop('disabled', true);
            $("#appbundle_game_awayTeamScore").prop('disabled', true);                        
        } else {
            var splitHours = matchDateInput.val().split(" ");
            var splitMinutes = splitHours[1].split(":"); 
            var splitDate = splitHours[0].split("-");
            var matchDate = new Date(splitDate[2], splitDate[1] - 1, splitDate[0]);
            matchDate.setHours(splitMinutes[0],splitMinutes[1]);
            if(matchDate < now) {
                $("#appbundle_game_homeTeamScore").prop('disabled', false);
                $("#appbundle_game_awayTeamScore").prop('disabled', false);                                
            } else {
                $("#appbundle_game_homeTeamScore").prop('disabled', true);
                $("#appbundle_game_awayTeamScore").prop('disabled', true);                                 
            }

        }

        matchDateInput.change(function() {
            var splitHours = matchDateInput.val().split(" ");
            var splitMinutes = splitHours[1].split(":"); 
            var splitDate = splitHours[0].split("-");
            var matchDate = new Date(splitDate[2], splitDate[1] - 1, splitDate[0]);
            matchDate.setHours(splitMinutes[0],splitMinutes[1]);

            if(matchDate > now || !matchDateInput.val().trim()) {
                $("#appbundle_game_homeTeamScore").prop('disabled', true);
                $("#appbundle_game_awayTeamScore").prop('disabled', true);                        
            } else {
                $("#appbundle_game_homeTeamScore").prop('disabled', false);
                $("#appbundle_game_awayTeamScore").prop('disabled', false);                        
            }                    
        });                         
    }                   

    var t = $('.data-table').DataTable( {
        "columnDefs": [ {
            "searchable": false,
            "orderable": false,
            "targets": 0
        } ],
        "order": [[ 1, 'asc' ]],
        language: {
            processing:     "Przetwarzam...",
            search:         "Szukaj:",
            lengthMenu:    "Pokazuj _MENU_ elementów",
            info:           "Pokazuje od _START_ do _END_ z _TOTAL_ elementów",
            infoEmpty:      "Pokazuje od 0 do 0 z 0 elementów",
            infoFiltered:   "(z _MAX_ elementów ogółem)",
            infoPostFix:    "",
            loadingRecords: "Ładuję...",
            zeroRecords:    "Brak wyników",
            emptyTable:     "Pusta tablica",
            paginate: {
                first:      "Pierwsza",
                previous:   "Poprzednia",
                next:       "Następna",
                last:       "Ostatnia"
            },
            aria: {
                sortAscending:  ": activer pour trier la colonne par ordre croissant",
                sortDescending: ": activer pour trier la colonne par ordre décroissant"
            }
        }                         
    });

    t.on( 'order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    }).draw();

    function checkUniqueYear(value) {
        $.ajax({
            url: 'http://localhost:8000/admin/teams/unique-year/'+value,
            type: 'GET',
            data: value,
            success: function(response) {
                if (response.length > 0) {
                    $("#appbundle_team_year").closest('.form-group').addClass("has-error");
                    $("#appbundle_team_year")
                        .closest('.form-group')
                        .append(
                            "<span class=\"help-block\">"
                                +"<span class=\"glyphicon glyphicon-exclamation-sign\">"
                                +"</span>"
                                +" Ten rocznik już istnieje."
                            +"</span>"
                    );
                } else {
                    $("#appbundle_team_year").closest('.form-group').removeClass("has-error");
                    $("#appbundle_team_year").closest('.form-group').find( "span.help-block" ).remove();
                }

            },
            error: function(response) {
                console.log(response);
            }
        });         
    }    
    
    $("#appbundle_team_year").keyup(function() {
        var checked = $("#appbundle_team_isMy").prop('checked');
        var value = $("#appbundle_team_year").val();
        var isValidYear = /\d{4}/.test(value);

        if(checked && isValidYear) {
            checkUniqueYear(value);
        }        
    });     

    $("#appbundle_team_isMy").change(function() {
        var checked = $("#appbundle_team_isMy").prop('checked');
        var value = $("#appbundle_team_year").val();
        var isValidYear = /\d{4}/.test(value);

        if(checked && isValidYear) {
            checkUniqueYear(value);
        }        
    });    
    
});   
    
                     
             

                



                


              


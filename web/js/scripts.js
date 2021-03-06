$(document).ready(function() {

    /***************************************************************************
    
    Time / date pickers
     
    ***************************************************************************/

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

    $(".datetimepicker").datetimepicker({
        timeFormat: 'HH:mm',
        dateFormat : 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,                        
        stepMinute: 5
    });                    

    $(".datepicker").datepicker({
        dateFormat : 'dd-mm-yy',
        changeMonth: true,
        changeYear: true                        
    });                    

    $(".datemonthpicker").datepicker({
        dateFormat : 'dd-mm',
        changeMonth: true,
        changeYear: false,
        beforeShow: function (input, inst) {
            inst.dpDiv.addClass('monthDatePicker');
        },
        onClose: function(dateText, inst){
            inst.dpDiv.removeClass('monthDatePicker');
        }
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
        stepMinute: 5
    }); 
    
    /***************************************************************************
    
    ...
     
    ***************************************************************************/
   
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        confirmDelete(this.closest("form"));
    });

    function confirmDelete(form) {
        var result = confirm('Jesteś pewien?');
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

    $('#appbundle_payment_paymentCategory').change(function() {
        var amount = $('option:selected', this).attr('data-amount');
        $('#appbundle_payment_amount').val(amount);
    });

    /***************************************************************************
    
    Fill recipients field depending on email type choice
     
    ***************************************************************************/
    
    $('#appbundle_email_type').change(function() {
        var type = $('option:selected', this).val();
        
        if (type == 1) {
            chooseAll();           
        } else if (type == 2) {
            chooseGroup();
        } else if (type == 3) {
            chooseCustom();
        } else {
            $('#appbundle_email_recipients').val('');
        }

    });

    function chooseAll() {
        $.ajax({
            url : '/admin/email/get-all-recipients',
            type: 'GET',
            success: function(response) {
                $('#appbundle_email_recipients').val(response);
            }
        }); 
    }

    function chooseGroup() {
        $.ajax({
            url : '/admin/email/get-recipients-by-team',
            type: 'GET',
            success: function(response) {
                var options = '';
                options += "<option value='' disabled selected>Wybierz</option>";
                for(var i=0; i<response.length; i++) {
                    options += "<option value="+response[i]+">"+response[i]+"</option>";
                }
                showEmailsModal(options, 'group');       
            }
        });         
    }

    function chooseCustom() {
        $.ajax({
            url : '/admin/email/get-custom-recipients',
            type: 'GET',
            success: function(response) {
                var emails = '';
                for(var key in response) {
                    emails += "<div class='group'>";
                    emails += "<div class='checkbox'><label><input type='checkbox' class='select-all' name='year' id='year_"+key+"' value='"+key+"'><strong>Rocznik "+key+"</strong></label></div>";
                    emails += "<br>";
                    for(var email in response[key]) {
                        emails += "<label class='checkbox-inline'><input class='emails' type='checkbox' name='emails' value='"+response[key][email]+"'>"+response[key][email]+"</label>";                    
                    } 
                    emails += "</div>";
                    emails += "<hr>";
                }

                showEmailsModal(emails, 'custom');               
            }
        });
    }

    function showEmailsModal(options, type) {
        
        if (type === 'group') {            
            var groupModal = $(
                '<div class="modal fade" id="group-modal" tabindex="-1" role="dialog">'
                +'    <div class="modal-dialog" role="document">'
                +'        <div class="modal-content">'
                +'            <div class="modal-body">'
                +'                <select id="group-select" class="form-control">'+options+'</select>'
                +'            </div>'
                +'        </div>'
                +'    </div>'
                +'</div>'   
            ); 
            $('body').append(groupModal);
            $('#group-modal').modal("show");            
            
            $("body").on('change', '#group-select', function() {
                var year = $(this).val();
                $('#group-modal').modal("hide");
                
                $.ajax({
                    url : '/admin/email/get-by-year/' + year,
                    type: 'GET',
                    success: function(response) {                   
                        $('#appbundle_email_recipients').val(response);               
                    }
                    
                });
            });            
        }
        
        if (type === 'custom') {
            var customModal = $(
                '<div class="modal fade" id="custom-modal" tabindex="-1" role="dialog">'
                +'    <div class="modal-dialog" role="document">'
                +'        <div class="modal-content">'
                +'            <div class="modal-body">'+options+'</div>'
                +'            <div class="modal-footer">'
                +'                <button type="button" class="btn btn-default" id="choose-custom" data-dismiss="modal">Wybierz</button>'
                +'            </div>'        
                +'        </div>'
                +'    </div>'
                +'</div>'   
            );
            $('body').append(customModal);
            $('#custom-modal').modal("show"); 

            $('.select-all').each(function() {
                $(this).on("click", function() {
                    checkboxes = $(this).closest('.group').find('.emails');
                    if (this.checked) {
                        $(checkboxes).each(function() {
                            this.checked = true;
                        });
                    } else {
                        $(checkboxes).each(function() {
                            this.checked = false;
                        });
                    }
                });
            });            
            
            $("body").on('click', '#choose-custom', function() {
                var names = [];
                $('.modal-body input.emails:checked').each(function() {
                    names.push(this.value);
                });
                $('#custom-modal').modal("hide");
                $('#appbundle_email_recipients').val(names);
            });                        
        }       
    }

    /**************************************************************************/
    
    function selectAwayTeams(all = false) {
        if (!all) {
            var $homeTeam = $('#appbundle_game_homeTeam');
            var $form = $('#appbundle_game_homeTeam').closest('form');
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
        } else {
            var $homeTeam = $('#appbundle_game_homeTeam');
            var $form = $('#appbundle_game_homeTeam').closest('form');
            var data = {};
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
        }
    }
    
    var $homeTeam = $('#appbundle_game_homeTeam');
    var $gameCategory = $('#appbundle_game_category');
    
    // If league game - filter results
    if ($gameCategory.val() === 'match.league') {        
        selectAwayTeams();       
        $homeTeam.change(function() {           
            selectAwayTeams();   
        });
    // If not league game - get all teams    
    } else {        
        selectAwayTeams(true);  
    }
    
    // If game category changed...
    $(document).on('change', '#appbundle_game_category', function() {
        // If league game
        if ($gameCategory.val() === 'match.league') {
            // Imidiately filter results
            selectAwayTeams(); 
            // On home team change filter results or select all
            $homeTeam.change(function() {
                if ($gameCategory.val() === 'match.league') {
                    selectAwayTeams();
                } else {
                    selectAwayTeams(true);
                }
            });             
        } else {
            selectAwayTeams(true);
        }
    });
    
                                  

    /***************************************************************************
    
    1) Enable / disable goals inputs depending on game date 
    2) Show error if league game date not in season dates range
     
    ***************************************************************************/
    
    // 1
    var matchDateInput = $('#appbundle_game_date');
    var categoryInput = $('#appbundle_game_category');
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
            
            // 2            
            var category = $('#appbundle_game_category').val();
            var dateArray = matchDateInput.val().split("-");            
            // get year
            var yearAndTime = dateArray[2];
            var splitYear = yearAndTime.split(" ");
            var year = splitYear[0];
            // year-month-day format
            var formattedDate = year + "-" + dateArray[1] + "-" + dateArray[0];
            var errorMessage = 
                "<span class=\"help-block season-range-error\">"
                    + "<ul class=\"list-unstyled\">"
                        + "<li>"
                            + "<span class=\"glyphicon glyphicon-exclamation-sign\"></span>" 
                            + " Data meczu poza sezonem."
                        + "</li>"
                    + "</ul>"
                + "</span>";

            $.ajax({
                url : "/admin/games/check-league-game-date/"+formattedDate,
                type: "GET",
                data : formattedDate,
                success: function(resp) {
                    if (resp === false && category === "league.game") {
                        allowSubmit = false;                        
                        matchDateInput.parent().addClass("has-error");
                        // @FIXME Dirty hack to prevent error message showing 3 times (datepicker change issue)
                        if ($(".season-range-error").length === 0) {
                            matchDateInput.parent().append(errorMessage);
                        }                       
                    } else {
                        allowSubmit = true;
                        matchDateInput.parent().removeClass("has-error");
                        $(".season-range-error").remove();
                    }
                    
                    if (allowSubmit === false) {
                        $("#game-submit-button").prop('disabled', true);               
                    } else {
                        $("#game-submit-button").prop('disabled', false);
                    }                    
                }
            });
        });

        categoryInput.change(function() {
            var dateInput = $('#appbundle_game_date');
            var category = $('#appbundle_game_category').val();
            var dateArray = dateInput.val().split("-");
            
            // get year
            var yearAndTime = dateArray[2];
            var splitYear = yearAndTime.split(" ");
            var year = splitYear[0];            
            
            // year-month-day format
            var formattedDate = year + "-" + dateArray[1] + "-" + dateArray[0];            
            var errorMessage = 
                "<span class=\"help-block season-range-error\">"
                    + "<ul class=\"list-unstyled\">"
                        + "<li>"
                            + "<span class=\"glyphicon glyphicon-exclamation-sign\"></span>" 
                            + " Data meczu poza sezonem."
                        + "</li>"
                    + "</ul>"
                + "</span>";

            $.ajax({
                url : "/admin/games/check-league-game-date/"+formattedDate,
                type: "GET",
                data : formattedDate,
                success: function(resp) {
                    if (resp === false && category === "league.game") {
                        allowSubmit = false;                        
                        matchDateInput.parent().addClass("has-error");
                        // @FIXME Dirty hack to prevent error message showing 3 times (datepicker change issue)
                        if ($(".season-range-error").length === 0) {
                            matchDateInput.parent().append(errorMessage);
                        }                       
                    } else {
                        allowSubmit = true;
                        matchDateInput.parent().removeClass("has-error");
                        $(".season-range-error").remove();
                    }
                    
                    if (allowSubmit === false) {
                        $("#game-submit-button").prop('disabled', true);               
                    } else {
                        $("#game-submit-button").prop('disabled', false);
                    }                    
                }
            });
        });

    }                   
    
// Enable play ligue checkbox only if my team ----------------------------------

    $('input[name="appbundle_team[playsLeague]"]').attr("disabled", true);
    
    if ($('input[name="appbundle_team[isMy]"]').is(':checked')) {
       $('input[name="appbundle_team[playsLeague]"]').removeAttr("disabled");
    }
        
    $('input[name="appbundle_team[isMy]"]').on('click', function() {
        if ($(this).is(':checked')) {
            $('input[name="appbundle_team[playsLeague]"]').removeAttr("disabled");
        } 
        else {
            $('input[name="appbundle_team[playsLeague]"]').attr("disabled", true);
        }
    });        

// -----------------------------------------------------------------------------    
    
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

// @UniqueEntity instead (Team entity)

//    function checkUniqueYear(value) {
//        $.ajax({
//            url: '/admin/teams/unique-year/'+value,
//            type: 'GET',
//            data: value,
//            success: function(response) {
//                if (response.length > 0) {
//                    $("#appbundle_team_year").closest('.form-group').addClass("has-error");
//                    $("#appbundle_team_year")
//                        .closest('.form-group')
//                        .append(
//                            "<span class=\"help-block\">"
//                                +"<span class=\"glyphicon glyphicon-exclamation-sign\">"
//                                +"</span>"
//                                +" Ten rocznik już istnieje."
//                            +"</span>"
//                    );
//                } else {
//                    $("#appbundle_team_year").closest('.form-group').removeClass("has-error");
//                    $("#appbundle_team_year").closest('.form-group').find( "span.help-block" ).remove();
//                }
//
//            },
//            error: function(response) {
//                console.log(response);
//            }
//        });         
//    }    
    
//    $("#appbundle_team_year").keyup(function() {
//        var checked = $("#appbundle_team_isMy").prop('checked');
//        var value = $("#appbundle_team_year").val();
//        var isValidYear = /\d{4}/.test(value);
//
//        if(checked && isValidYear) {
//            checkUniqueYear(value);
//        }        
//    });     
//
//    $("#appbundle_team_isMy").change(function() {
//        var checked = $("#appbundle_team_isMy").prop('checked');
//        var value = $("#appbundle_team_year").val();
//        var isValidYear = /\d{4}/.test(value);
//
//        if(checked && isValidYear) {
//            checkUniqueYear(value);
//        }        
//    });       
    
    $('.gritter-close').on('click', function() {
        $('#gritter-notice-wrapper').fadeOut();
    });
    
    setTimeout(function() { 
        $('#gritter-notice-wrapper').fadeOut(); 
    }, 5000);
    
    // POST because home.pl sucks and does not support DELETE
    $("body").on('click', '.notification-delete', function() {
        var id = $(this).attr('data-id');
        $.ajax({
            url: '/admin/notifications/'+id,
            type: 'POST',
            data: id,
            success: function(response) {
                loadNotifications();
            },
            error: function(response) {
                alert(response);
            }
        }); 
    });
    
    function loadNotifications() {
        $.ajax({
            url: '/admin/notifications',
            type: 'GET',
            success: function(response) {
                $('#notifications').html(response);
            },
            error: function(response) {
                alert(response);
            }
        });        
    }

    // Select all checkboxes ---------------------------------------------------
    
    $('.delete-selected').attr("disabled", true);
    
    $('.select_all').on('click',function() {
        if (this.checked) {
            $('.list-checkbox').each(function() {
                this.checked = true;
            });
        } else {
             $('.list-checkbox').each(function() {
                this.checked = false;
            });
        }
    });
    
    // Activate delete button --------------------------------------------------
    
    $(document).on('change', '.list-form input[type=checkbox]', function () {
        var len = $('.list-form input[type=checkbox]:checked').length;
        if (len > 0) {
            $('.delete-selected').removeAttr("disabled");
        } else if (len === 0) {
            $('.delete-selected').attr("disabled", true);
        }
    }).trigger('change');    



});  


$(document).ready(function(){
    var elems           = document.querySelectorAll('select');
    var elemsTabs       = document.querySelectorAll('.tabs');
    var instances       = M.FormSelect.init(elems, {});

    if (elemsTabs[0].childElementCount > 0) {
        var instanceTabs    = M.Tabs.init(elemsTabs, {});
    }


    $('button').on('click', function(){
        var listPeoples = instances[0];
        var listTableVal = $('select#listTable').val();

        if (listPeoples.getSelectedValues().length > 0 && listTableVal != null) {

            var idsPeople   = listPeoples.getSelectedValues();
            var idTable     = listTableVal;
            $.ajax({
                type: "POST",
                url: '/nuntadmin/assignGuestsToTables',
                data: {idsPeople, idTable},
                error: function(jqXHR, textStatus, errorThrown) {
                   console.log(errorThrown);
                },
                success: function(response) {
                   if (response.response === true) {
                       reloadingPage();
                   }
                }
            });
        }
    });

});

function reloadGuests(e) {
    $.ajax({
        type: "GET",
        url: '/nuntadmin/updateGuests',
        data: 'delete=' + e,
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(errorThrown);
        },
        success: function(response) {
           if (response.response === true) {
               reloadingPage();
           }
        }
    });
};

function removeGuestFromTable(idPeople, idTable) {
    $.ajax({
        type: "DELETE",
        url: '/nuntadmin/deassignGuestToTable',
        data: {idPeople, idTable},
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(errorThrown);
        },
        success: function(response) {
           if (response.response === true) {
               reloadingPage();
           }
        }
    });
};

function removeAllGuestsFromTable(idTable) {
    $.ajax({
        type: "DELETE",
        url: '/nuntadmin/removeAllGuestsFromTable',
        data: {idTable},
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(errorThrown);
        },
        success: function(response) {
           if (response.response === true) {
               reloadingPage();
           }
        }
    });
};

function reloadingPage() {
    document.location.reload(true);
};
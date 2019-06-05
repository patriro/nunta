$(document).ready(function(){
    var elems = document.querySelectorAll('select');
    var instance = M.FormSelect.init(elems, {});

    $('#selectPeople').on('click', function(){
        console.log(instance.getSelectedValues());
    });

    $("#listPeople").on('change', function() {
        console.log(instance.getSelectedValues());
        console.log($("#listPeople option:selected").text());
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

function reloadingPage() {
    document.location.reload(true);
};
$(document).ready(function(){
    var textInput = document.getElementById('search');
    var timeout = null;
    var elem = document.querySelector('.autocomplete');
    var instance = null;

    var peopleResponse = [];

    M.AutoInit(); // ????? Juste pour l'icone ?
    textInput.onkeyup = function (e) {
        var value = textInput.value;
        if (value.length > 2) {
            clearTimeout(timeout);

            // Make a new timeout set to go off in 800ms
            timeout = setTimeout(function () {
                $.ajax({
                    type: "GET",
                    url: '/search',
                    data: 'q=' + value,
                    timeout: 5000,
                    beforeSend: function() {
                        instance = M.Autocomplete.init(elem, {});
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                       console.log(errorThrown);
                    },
                    success: function(htmlResponse) {
                        peopleResponse = [];
                        var datas = {};

                        for (var i = 0; i < htmlResponse.results.length; i++) {
                            datas[htmlResponse.results[i].lastName + ' ' + htmlResponse.results[i].firstName] = '';
                            peopleResponse.push(htmlResponse.results[i]);
                        }

                        instance.updateData(datas);
                        instance.open();
                   }
               });

           }, 600);
        }
    };

    $('#search').on('change', function(ele) {
        var valueOfInput = ele.currentTarget.value

        for (var i = 0; i < peopleResponse.length; i++) {
            var formatedPeopleName = peopleResponse[i].lastName + ' ' + peopleResponse[i].firstName;
            if (valueOfInput === formatedPeopleName) {
                getPeopleInfo(peopleResponse[i].id);
            }
        }
    });

    $('.comeBack').on('click', function() {
        cleanSearchedPeople();
    });

    $('#closeIcon').on('click', function() {
        $('#search').val('');
    });

    function getPeopleInfo(id) {

        $.ajax({
            type: "GET",
            url: '/peoplePlaces',
            data: 'id=' + id,
            beforeSend: function() {
            },
            error: function(jqXHR, textStatus, errorThrown) {
               console.log(errorThrown);
            },
            success: function(response) {
                var name = response.personInfo.firstName + ' ' + response.personInfo.lastName;
                var tableNr = response.tableInfo.id;
                var guests = response.tableInfo.guests;
                var appendGuest = '';
                var numberOfPeople = 0;

                for (var i = 0; i < guests.length; i++) {
                    var nameGuestInTable = guests[i].firstName + ' ' + guests[i].lastName;
                    var actif = '';
                    numberOfPeople++;

                    if (name === nameGuestInTable) {
                        actif = 'activePeople';
                    }
                    appendGuest += "<li class='collection-item " + actif + "'>" + nameGuestInTable + "</li>";
                }

                $('#defaultWedding').hide();
                $('.welcomeTo').empty();
                $('.tableNr').empty();
                $('.tableWith').empty();
                $('.numberHowManyPeople').empty();

                $('#searchCompleted').fadeIn(300);
                $('.welcomeTo').append(name);
                $('.tableNr').append(tableNr);
                $('.tableWith').append(appendGuest);
                $('.numberHowManyPeople').append(numberOfPeople);
           }
       });

    }

    function cleanSearchedPeople() {
        $('#searchCompleted').hide();
        $('#defaultWedding').show();
    }
});


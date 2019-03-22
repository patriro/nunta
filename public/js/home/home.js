$(document).ready(function(){
    var textInput = document.getElementById('search');
    var timeout = null;
    var elems = document.querySelector('.autocomplete');

    M.AutoInit();

    textInput.onkeyup = function (e) {
        var value = textInput.value;
        var instance;
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
                       $('input.autocomplete').autocomplete('');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                       console.log(errorThrown);
                    },
                    success: function(htmlResponse) {
                       var datas = {};

                       for (var i = 0; i < htmlResponse.results.length; i++) {
                           datas[htmlResponse.results[i].lastName + ' ' + htmlResponse.results[i].firstName] = '';
                       }

                       instance = M.Autocomplete.getInstance(elems);

                       instance.updateData(datas);
                       instance.open();
                   }
               });

           }, 600);
        }
    };

});



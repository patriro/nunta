$(document).ready(function(){
    var elems = document.querySelector('select');
    var instance = M.FormSelect.init(elems, {});

    $('#selectPeople').on('click', function(){
        console.log(instance.getSelectedValues());
    });

    $("#listPeople").on('change', function() {
        console.log(instance.getSelectedValues());
        console.log($("#listPeople option:selected").text());
    });
});
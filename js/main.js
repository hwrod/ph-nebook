// Enable Bootstrap tooltips.
$('[data-toggle="tooltip"]').tooltip();

// Event handlers.
$('.glyphicon-minus-sign').click(function(e){
    var conf = confirm("Are you sure?");
    return conf;
});

// Search
$('.search').on('input', function(e){
    var search = e.currentTarget.value;
    var regex = new RegExp(search, 'g');
    $('tr.data').show().each(function(i, tr){
        if (!regex.test($(tr).text())) {
            $(tr).hide();
        }

    });
});



var substringMatcher = function(strs) {
    return function findMatches(q, cb) {
        var matches, substringRegex;

        // an array that will be populated with substring matches
        matches = [];

        // regex used to determine if a string contains the substring `q`
        substrRegex = new RegExp(q, 'i');

        // iterate through the pool of strings and for any string that
        // contains the substring `q`, add it to the `matches` array
        $.each(strs, function(i, str) {
            if (substrRegex.test(str)) {
                matches.push(str);
            }
        });

        cb(matches);
    };
};

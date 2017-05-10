$(function () {

    // Materialize initialisations

    $('select').material_select();
    $(".button-collapse").sideNav();
    $('#catalogstable').tablesorter();
    $("#categoriesTable").tablesorter();
    $("#tableOrders").tablesorter();
    $("#productstable").tablesorter();
    $("#vatstable").tablesorter();

    // Comments fields management


    // Get the ul that holds the collection of tags
    var collectionHolder = $('#edit-comment-field-list');
    // add a delete link to all of the existing tag form li elements
    collectionHolder.find('li').each(function () {
        addTagFormDeleteLink($(this));
    });

    var commentCount = '{{ edit_form.comments|length }}';
    // adding a field for new comment on click
    $('.add-another-comment').click(function (e) {
        e.preventDefault();
        var commentList = jQuery('#edit-comment-field-list');
        // grab the prototype template
        var newWidget = commentList.attr('data-prototype');
        newWidget = newWidget.replace(/__name__/g, commentCount);
        commentCount++;
        // create a new list element and add it to the list
        var newLi = jQuery('<li></li>').html(newWidget);
        newLi.attr('class', 'charactersCount').appendTo(commentList);
        addTagFormDeleteLink(newLi);
    });


    function addTagFormDeleteLink($tagFormLi) {
        var $removeFormA = $('<a href="#" title="remove this field (doesn\'t delete comment)" class="addIcon"><i class="material-icons">remove_circle</i>');
        $tagFormLi.append($removeFormA);
        $removeFormA.on('click', function (e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
            // remove the li for the tag form
            $tagFormLi.remove();
        });
    }

    var fieldToCount = $('.charactersCount');
    var fieldToDisplay  = $('.liveCount');
    $(document).on('keyup', fieldToCount, (function (event) {
        var remainingChars = 200 - ($(event.target).val().length);
        if (remainingChars >= 0) {
            fieldToDisplay.text(remainingChars + ' characters left');
        } else {
            fieldToDisplay.text('Too many characters !');
        }
    }))

})

/**
 *
 */
function enableEdition() {
    $("#enableEditionButton").hide();
    $("#disableEditionButton").show();
    $("#saveChangesButton").show();
    $(".editable-form-input").attr("readonly",false);
}

/**
 *
 */
function disableEdition() {
    $("#disableEditionButton").hide();
    $("#saveChangesButton").hide();
    $("#enableEditionButton").show();
    $(".editable-form-input").attr("readonly",true);
}

/**
 *
 */
function saveChanges() {
    $("#actionInput").val("edit");
    $("#viewForm").submit();
}

/**
 *
 */
function deleteBook() {
    $("#actionInput").val("delete");
    $("#viewForm").submit();
}

$(document).ready(function() {
    $("#enableEditionButton").click(function(event) {
        enableEdition();
    });
    $("#disableEditionButton").click(function(event) {
        disableEdition();
    });
    $("#saveChangesButton").click(function(event) {
        saveChanges();
    });
    $("#deleteButton").click(function(event) {
        deleteBook();
    });
})
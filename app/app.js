//Testing the API
//Here's an array of test data that you can add to or tweak
//Wouldn't have this in the global scope at runtime
var testData = [{
        banks: ["2", "3", "4", "5"]
    },
    {
        banks: []
    },
    {
        banks: ["0", "0", "0"]
    },
    {
        banks: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10"]
    },
    {
        banks: ["23", "4", "9", "0", "553", "14", "1", "52", "2", "8", "24", "20", "1", "2", "3", "7"]
    }
];

//The function that tests the API with the given test data
//idx is the index in the testData array whose object we're sending to the API
//This function is used in a for loop later
function getRepeatCycle(idx) {
    //Grab the specified object at index idx and JSONify it
    var formData = JSON.stringify(testData[idx]);

    //Tell the frontend which test we're running
    $("#tests").append("<h2>Running test " + (idx + 1) + "</h2>\n");
    $("#tests").append("<p>Test: " + JSON.stringify(testData[idx]) + "</p>");

    //Append a paragraph to store the asynchronous results from an AJAX call
    $("#tests").append("<p id='res" + idx + "'></p>");

    //Send a set of test memory banks to the API using an AJAX call
    $.ajax({
        url: "api/reallocate.php",
        type: "POST",
        contentType: 'application/json',
        dataType: 'json',
        data: formData,
        success: function(result) {
            //Use the paragraph we appended above to display the results
            //Not very slick - would use a return value in the live scenario
            $("#res" + idx).append("<p>Result: " + JSON.stringify(result) + "</p>");
        },
        error: function(xhr, resp, text) {
            console.log("Error");
        }
    });
} //getRepeatCycle

//When the document is loaded...
$(function() {
    //Loop through all the test scenarios at the top and run them through the API
    for (var j = 0; j < testData.length; j++) {
        getRepeatCycle(j);
    } //for loop
});
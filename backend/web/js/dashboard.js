$(document).ready(function () {
    $.ajax({
        method: "GET",
        url: $('#dashboardplot').attr('data-url'),
        success: function(data){

            // var categories = JSON.parse(data);

            // console.log(data.data.targetCategory);
            createChart(data);
        },
        error: function(error_data){
            console.log("Endpoint GET request error");
            // console.log(error_data)
        }
    })
});

function createChart(data) {
    // Extract the labels and values from the data
    var categories = data.data.targetCategory;

    console.log(data['targetCategory']);
    var values = categories.map(function (item) {
        return item.x;
    });

    var minValue = Math.min.apply(null, values);
    var maxValue = Math.max.apply(null, values);
    // Create a new chart using Chart.js
    var ctx = document.getElementById("dashboardplot").getContext("2d");
    var myChart = new Chart(ctx, {
        type: "bar",
        data: {
            // labels: labels,
            datasets: [
                {
                    label: "Target Percentage",
                    data: categories,
                    // backgroundColor: 'rgba(0, 123, 255)' // Adjust the color as needed
                },
            ],
        },
        options: {
            responsive: true,
            indexAxis: "y",
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value, index, values) {
                            // Format the label as needed
                            return value + "%";
                        },
                        stepSize: 10,
                    },
                },
            },
        },
    });
}
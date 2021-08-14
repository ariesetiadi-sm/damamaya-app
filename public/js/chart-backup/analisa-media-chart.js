$(function () {
    let kategori = $("#kategori_analisa option:selected").text();
    let start_date = null;
    let end_date = null;

    $("#kategori_analisa").on("change", function () {
        kategori = $("#kategori_analisa option:selected").text();
    });

    if ($("#analisa-chart").length) {
        start_date = $("#start_date").val();
        end_date = $("#end_date").val();

        // Show default chart
        // analisa_chart(start_date, end_date, kategori);

        $("#btn_period_analisa").click(function () {
            start_date = $("#start_date").val();
            end_date = $("#end_date").val();
            analisa_chart(start_date, end_date, kategori);
        });

        setInterval(() => {
            analisa_chart(start_date, end_date, kategori);
        }, 60000);
    }
});

function analisa_chart(start_date, end_date, kategori) {
    if ($("#analisa-chart").length) {
        // Set new default font family and font color to mimic Bootstrap's default styling
        (Chart.defaults.global.defaultFontFamily = "Nunito"),
            '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = "#858796";

        function number_format(number, decimals, dec_point, thousands_sep) {
            // *     example: number_format(1234.56, 2, ',', ' ');
            // *     return: '1 234,56'
            number = (number + "").replace(",", "").replace(" ", "");
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep =
                    typeof thousands_sep === "undefined" ? "," : thousands_sep,
                dec = typeof dec_point === "undefined" ? "." : dec_point,
                s = "",
                toFixedFix = function (n, prec) {
                    var k = Math.pow(10, prec);
                    return "" + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : "" + Math.round(n)).split(".");
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || "").length < prec) {
                s[1] = s[1] || "";
                s[1] += new Array(prec - s[1].length + 1).join("0");
            }
            return s.join(dec);
        }

        // Get dates Name
        let url = $("#chart-card-analisa").data("route");
        let dates = [];
        let counts = [];
        let data = [];
        let analisa_table_str = ``;

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $("#_token").val(),
            },
        });

        $.ajax({
            url: url,
            data: {
                start_date: start_date,
                end_date: end_date,
                kategori: kategori,
            },
            type: "POST",
            success: function (chart) {
                counts = chart["counts"];
                dates = chart["dates"];
                data = chart["data"];

                $("#analisa-chart").remove();
                $("#analisa-table").remove();

                let canvas = `<canvas id="analisa-chart" style="display: block; height: 320px; width: 601px;" width="751" height="400" class="chartjs-render-monitor"></canvas>`;

                // Append new canvas
                $("#analisa-chart-area").append(canvas);

                // Jika ada data yang dihasilkan, maka tampilkan dalam bentuk table
                if (data.length > 0) {
                    analisa_table_str = `
                        <table class="table table-hover" id="analisa-table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Tanggal</th>
                                    <th>Issue Lokal</th>
                                    <th>Issue Nasional</th>
                                    <th>Kategori</th>
                                    <th>Nama Petugas</th>
                                </tr>
                            </thead>
                            <tbody>
                            `;

                    $.each(data, function (i, val) {
                        analisa_table_str += `
                            <tr>
                                <td>${i + 1}</td>
                                <td>${val.tanggal}</td>
                                <td>${val.issue_lokal}</td>
                                <td>${val.issue_nasional}</td>
                                <td>${val.kategori}</td>
                                <td>${val.nama_petugas}</td>
                            </tr>
                        `;
                    });

                    analisa_table_str += `</tbody></table>`;

                    $("#analisa-table-container").append(analisa_table_str);
                } else {
                    analisa_table_str = `
                        <h4 id="analisa-table" class="text-secondary text-center">Data Tidak Ditemukan</h4>
                    `;

                    $("#analisa-table-container").append(analisa_table_str);
                }

                // Area Chart Example
                var ctx = document.getElementById("analisa-chart");
                var myLineChart = new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: dates,
                        datasets: [
                            {
                                label: "Issue",
                                lineTension: 0,
                                backgroundColor: "rgba(78, 115, 223, 0.05)",
                                borderColor: "rgba(78, 115, 223, 1)",
                                pointRadius: 3,
                                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                                pointBorderColor: "rgba(78, 115, 223, 1)",
                                pointHoverRadius: 3,
                                pointHoverBackgroundColor:
                                    "rgba(78, 115, 223, 1)",
                                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                                pointHitRadius: 10,
                                pointBorderWidth: 2,
                                data: counts,
                            },
                        ],
                    },
                    options: {
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                left: 10,
                                right: 25,
                                top: 25,
                                bottom: 0,
                            },
                        },
                        scales: {
                            xAxes: [
                                {
                                    time: {
                                        unit: "date",
                                    },
                                    gridLines: {
                                        display: false,
                                        drawBorder: false,
                                    },
                                    ticks: {
                                        maxTicksLimit: 7,
                                    },
                                },
                            ],
                            yAxes: [
                                {
                                    ticks: {
                                        maxTicksLimit: 5,
                                        padding: 10,
                                        // Include a dollar sign in the ticks
                                        callback: function (
                                            value,
                                            index,
                                            values
                                        ) {
                                            return "" + number_format(value);
                                        },
                                    },
                                    gridLines: {
                                        color: "rgb(234, 236, 244)",
                                        zeroLineColor: "rgb(234, 236, 244)",
                                        drawBorder: false,
                                        borderDash: [2],
                                        zeroLineBorderDash: [2],
                                    },
                                },
                            ],
                        },
                        legend: {
                            display: false,
                        },
                        tooltips: {
                            backgroundColor: "rgb(255,255,255)",
                            bodyFontColor: "#858796",
                            titleMarginBottom: 10,
                            titleFontColor: "#6e707e",
                            titleFontSize: 14,
                            borderColor: "#dddfeb",
                            borderWidth: 1,
                            xPadding: 15,
                            yPadding: 15,
                            displayColors: false,
                            intersect: false,
                            mode: "index",
                            caretPadding: 10,
                            callbacks: {
                                label: function (tooltipItem, chart) {
                                    var datasetLabel =
                                        chart.datasets[tooltipItem.datasetIndex]
                                            .label || "";
                                    return (
                                        datasetLabel +
                                        ": " +
                                        number_format(tooltipItem.yLabel)
                                    );
                                },
                            },
                        },
                    },
                });
            },
        });
    }
}
<!DOCTYPE html>
<html>
<head>
    <title>LandSlide Data Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="myLineChart" width="400" height="200"></canvas>
    <script>
        // Lấy dữ liệu từ PHP và parse thành đối tượng JavaScript
        const data = {!! $data !!};

        const datasets = [
            {
                label: 'PZ1_(Digit)',
                data: data.map(item => ({ x: new Date(item.created_at), y: parseFloat(item.PZ1_Digit) })),
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: false,
                tension: 0.4
            }
        ];

        const ctx = document.getElementById('myLineChart').getContext('2d');
        const myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                datasets: datasets
            },
            options: {
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'day'
                        },
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'PZ1_(Digit) Value'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landslide Data Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom"></script>
    <style>
        #chartContainer {
            width: 90%;
            margin: auto;
            position: relative;
        }
        .toggle-legend {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }
        .arrow-icon {
            font-size: 18px;
            transition: transform 0.3s;
        }
        .arrow-icon.up {
            transform: rotate(180deg);
        }
    </style>
</head>
<body>
    <div style="text-align: center; margin: 20px 0;">
        <form id="dateRangeForm">
            <label for="start_date">Từ ngày:</label>
            <input type="date" id="start_date" name="start_date" value="{{ $startDate ?? '' }}">
            
            <label for="end_date">Đến ngày:</label>
            <input type="date" id="end_date" name="end_date" value="{{ $endDate ?? '' }}">
            
            <button type="submit">Áp dụng</button>
        </form>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <p>Số lượng đường đã vẽ: <span id="lineCount"></span></p>
    </div>
    <div id="chartContainer">
        <canvas id="landslideChart"></canvas>
        <div id="toggleLegendA" class="toggle-legend">
            <span class="arrow-icon">&#9650;</span>
        </div>
    </div>
    <div id="chartContainer">
        <canvas id="showChartB"></canvas>
        <div id="toggleLegendB" class="toggle-legend">
            <span class="arrow-icon">&#9650;</span>
        </div>
    </div>

    <script>
        // Lấy dữ liệu từ controller
        const datasets = {!! $chartData !!};
        const datasetsB = {!! $chartDataB !!};
        const lineCount = {!! $lineCount !!};

        // Cập nhật số lượng đường
        document.getElementById('lineCount').textContent = lineCount;

        // Định nghĩa các màu cho từng đường
        const colors = [
            'rgba(75, 192, 192, 1)',
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(201, 203, 207, 1)'
        ];

        // Áp dụng màu và style cho mỗi dataset
        datasets.forEach((dataset, index) => {
            dataset.borderColor = colors[index % colors.length];
            dataset.borderWidth = 2;
            dataset.fill = false;
            dataset.showLine = true;
            dataset.tension = 0.2;
        });
        datasetsB.forEach((dataset, index) => {
            dataset.borderColor = colors[index % colors.length];
            dataset.borderWidth = 2;
            dataset.fill = false;
            dataset.showLine = true;
            dataset.tension = 0.2;
        });

        const ctx = document.getElementById('landslideChart').getContext('2d');
        const landslideChart = new Chart(ctx, {
            type: 'line',
            data: {
                datasets: datasets
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: 'linear',
                        position: 'bottom',
                        title: {
                            display: true,
                            text: 'Calculated Tilt Values'
                        }
                    },
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Depth (m)'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Đo nghiêng, hướng Tây - Đông'
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.x !== null) {
                                    label += `(${context.parsed.x.toFixed(3)}, ${context.parsed.y})`;
                                }
                                return label;
                            }
                        }
                    },
                    zoom: {
                        pan: {
                            enabled: true,
                            mode: 'xy'
                        },
                        zoom: {
                            wheel: {
                                enabled: true,
                            },
                            pinch: {
                                enabled: true
                            },
                            mode: 'xy',
                        }
                    }
                }
            }
        });

        const chartB = document.getElementById('showChartB').getContext('2d');
        const showChartB = new Chart(chartB, {
            type: 'line',
            data: {
                datasets: datasetsB
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: 'linear',
                        position: 'bottom',
                        title: {
                            display: true,
                            text: 'Calculated Tilt Values'
                        }
                    },
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Depth (m)'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Đo nghiêng, hướng Bắc - Nam'
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.x !== null) {
                                    label += `(${context.parsed.x.toFixed(3)}, ${context.parsed.y})`;
                                }
                                return label;
                            }
                        }
                    },
                    zoom: {
                        pan: {
                            enabled: true,
                            mode: 'xy'
                        },
                        zoom: {
                            wheel: {
                                enabled: true,
                            },
                            pinch: {
                                enabled: true
                            },
                            mode: 'xy',
                        }
                    }
                }
            }
        });

        // Thêm chức năng ẩn/hiện legend cho cả hai biểu đồ
        function setupToggleLegend(chartInstance, toggleId) {
            const toggleLegend = document.getElementById(toggleId);
            const arrowIcon = toggleLegend.querySelector('.arrow-icon');

            toggleLegend.addEventListener('click', () => {
                const legendDisplay = chartInstance.options.plugins.legend.display;
                chartInstance.options.plugins.legend.display = !legendDisplay;
                chartInstance.update();

                arrowIcon.classList.toggle('up');
            });
        }

        setupToggleLegend(landslideChart, 'toggleLegendA');
        setupToggleLegend(showChartB, 'toggleLegendB');

        // Xử lý form chọn khoảng thời gian
        document.getElementById('dateRangeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            // Chuyển hướng với tham số mới
            window.location.href = `${window.location.pathname}?start_date=${startDate}&end_date=${endDate}`;
        });

        // Lọc dữ liệu theo thời gian
        function filterDataByDateRange(datasets, startDate, endDate) {
            return datasets.map(dataset => {
                if (dataset.label === 'Điểm chuẩn') return dataset;
                
                const filteredData = dataset.data.filter(point => {
                    const pointDate = new Date(dataset.label);
                    return pointDate >= new Date(startDate) && pointDate <= new Date(endDate);
                });
                
                return {...dataset, data: filteredData};
            }).filter(dataset => dataset.data.length > 0 || dataset.label === 'Điểm chuẩn');
        }

        // Áp dụng bộ lọc nếu có tham số thời gian
        const urlParams = new URLSearchParams(window.location.search);
        const startDate = urlParams.get('start_date');
        const endDate = urlParams.get('end_date');

        if (startDate && endDate) {
            datasets = filterDataByDateRange(datasets, startDate, endDate);
            datasetsB = filterDataByDateRange(datasetsB, startDate, endDate);
            
            // Cập nhật biểu đồ
            landslideChart.data.datasets = datasets;
            landslideChart.update();
            
            showChartB.data.datasets = datasetsB;
            showChartB.update();
            
            // Cập nhật số lượng đường
            document.getElementById('lineCount').textContent = datasets.length - 1; // Trừ 1 vì có điểm chuẩn
        }
    </script>
</body>
</html>
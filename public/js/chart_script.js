
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
function applyStyleToDatasets(datasets) {
    return datasets.map((dataset, index) => ({
        ...dataset,
        borderColor: colors[index % colors.length],
        borderWidth: 2,
        fill: false,
        showLine: true,
        tension: 0.2
    }));
}

datasets = applyStyleToDatasets(datasets);
datasetsB = applyStyleToDatasets(datasetsB);

// Tạo biểu đồ A
const ctx = document.getElementById('landslideChart').getContext('2d');
const landslideChart = new Chart(ctx, {
    type: 'line',
    data: { datasets },
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

// Tạo biểu đồ B
const chartB = document.getElementById('showChartB').getContext('2d');
const showChartB = new Chart(chartB, {
    type: 'line',
    data: { datasets: datasetsB },
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landslide Data Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom"></script>
    <link rel="stylesheet" href="{{ asset('css/chart_styles.css') }}">
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
    {{-- <script src="{{ asset('js/chart_script.js') }}"></script> --}}
    @include('note')
</body>
</html>
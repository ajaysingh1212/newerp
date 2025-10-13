@props([
    'title' => 'Chart Title',
    'value' => '0',
    'icon' => 'bar-chart-2',
    'canvasId' => 'chart1',
])

<div class="card shadow-sm rounded-lg p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h6 class="text-muted mb-1">{{ $title }}</h6>
            <h4 class="mb-0">{{ $value }}</h4>
        </div>
        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="fas fa-{{ $icon }}"></i>
        </div>
    </div>
    <div>
        <canvas id="{{ $canvasId }}" height="120"></canvas>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('activationChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Activations',
                data: {!! json_encode($data) !!},
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>

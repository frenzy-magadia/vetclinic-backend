@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#1e3a5f]">Reports & Analytics</h1>
            <p class="text-gray-600 mt-1">Clinic statistics and performance metrics</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="exportToPDF()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition">
                <i class="fas fa-file-pdf mr-2"></i>
                Export PDF
            </button>
            <button onclick="exportToExcel()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition">
                <i class="fas fa-file-excel mr-2"></i>
                Export Excel
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white border-l-4 border-green-600 shadow rounded-lg p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 p-3 rounded-full">
                    <i class="fas fa-calendar-check text-green-600 text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <dt class="text-sm font-medium text-gray-600 truncate">Completed Appointments</dt>
                    <dd class="text-2xl font-bold text-green-600 mt-1">
                        {{ $appointments_by_status->where('status', 'completed')->first()->count ?? 0 }}
                    </dd>
                </div>
            </div>
        </div>

        <div class="bg-white border-l-4 border-[#2c3e50] shadow rounded-lg p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-[#2c3e50] bg-opacity-10 p-3 rounded-full">
                    <i class="fas fa-paw text-[#2c3e50] text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <dt class="text-sm font-medium text-gray-600 truncate">Total Pets</dt>
                    <dd class="text-2xl font-bold text-[#2c3e50] mt-1">
                        {{ $pets_by_species->sum('count') }}
                    </dd>
                </div>
            </div>
        </div>

        <div class="bg-white border-l-4 border-[#d4911e] shadow rounded-lg p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-[#d4911e] bg-opacity-10 p-3 rounded-full">
                    <i class="fas fa-users text-[#d4911e] text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <dt class="text-sm font-medium text-gray-600 truncate">Active Clients</dt>
                    <dd class="text-2xl font-bold text-[#d4911e] mt-1">
                        {{ $pets_by_species->sum('count') }}
                    </dd>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Appointments by Status Chart -->
        <div class="bg-white shadow-lg rounded-lg border-t-4 border-[#1e3a5f] overflow-hidden">
            <div class="px-6 py-5">
                <h3 class="text-lg leading-6 font-medium text-[#1e3a5f] mb-4">Appointments by Status</h3>
                <div class="relative h-64">
                    <canvas id="appointmentsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Pets by Species Chart -->
        <div class="bg-white shadow-lg rounded-lg border-t-4 border-[#1e3a5f] overflow-hidden">
            <div class="px-6 py-5">
                <h3 class="text-lg leading-6 font-medium text-[#1e3a5f] mb-4">Pets by Species</h3>
                <div class="relative h-64">
                    <canvas id="petsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Appointments  -->
    <div class="bg-white shadow-lg rounded-lg border-t-4 border-[#1e3a5f] overflow-hidden">
        <div class="px-6 py-5">
            <h3 class="text-lg leading-6 font-medium text-[#1e3a5f] mb-4">Monthly Appointments Trend</h3>
            <div class="relative h-80">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Appointments by Status Table -->
    <div class="bg-white shadow-lg rounded-lg border-t-4 border-[#1e3a5f] overflow-hidden">
        <div class="px-6 py-5">
            <h3 class="text-lg leading-6 font-medium text-[#1e3a5f] mb-4">Appointments by Status</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#1e3a5f]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Count</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $total = $appointments_by_status->sum('count');
                        @endphp
                        @forelse($appointments_by_status as $status)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($status->status === 'scheduled') bg-[#d4911e] bg-opacity-10 text-[#d4911e]
                                    @elseif($status->status === 'confirmed') bg-[#0d47a1] bg-opacity-10 text-[#0d47a1]
                                    @elseif($status->status === 'in_progress') bg-[#2c3e50] bg-opacity-10 text-[#2c3e50]
                                    @elseif($status->status === 'completed') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $status->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $status->count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $total > 0 ? number_format(($status->count / $total) * 100, 1) : 0 }}%
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 whitespace-nowrap text-sm text-gray-500 text-center">
                                <i class="fas fa-inbox text-gray-300 text-3xl mb-2"></i>
                                <p>No appointment data available.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Appointments by Status Chart
const appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
const appointmentsChart = new Chart(appointmentsCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode($appointments_by_status->pluck('status')->map(function($status) { return ucfirst(str_replace('_', ' ', $status)); })) !!},
        datasets: [{
            data: {!! json_encode($appointments_by_status->pluck('count')) !!},
            backgroundColor: [
                '#d4911e',
                '#0d47a1',
                '#2c3e50',
                '#10B981',
                '#EF4444'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: {
                        size: 12
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        label += context.parsed + ' (' + percentage + '%)';
                        return label;
                    }
                }
            }
        }
    }
});

// Pets by Species Chart
const petsCtx = document.getElementById('petsChart').getContext('2d');
const petsChart = new Chart(petsCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($pets_by_species->pluck('species')) !!},
        datasets: [{
            label: 'Number of Pets',
            data: {!! json_encode($pets_by_species->pluck('count')) !!},
            backgroundColor: '#0d47a1',
            borderColor: '#1e3a5f',
            borderWidth: 1,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Monthly Appointments Trend
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Appointments',
            data: [12, 19, 15, 25, 22, 30, 28, 35, 32, 28, 24, 20],
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: '#10B981',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 5
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                align: 'end'
            }
        }
    }
});

// Export functions
function exportToPDF() {
    window.location.href = '{{ route("admin.reports.export", ["format" => "pdf"]) }}';
}

function exportToExcel() {
    window.location.href = '{{ route("admin.reports.export", ["format" => "excel"]) }}';
}
</script>
@endsection
@extends('frontend.layouts.app')

@section('content')

<style>
body{
    background:#f8f9fc;
}

.dashboard-container{
    padding:20px;
    max-width:1300px;
    margin:auto;
}

.dashboard-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.dashboard-title{
    font-size:20px;
    font-weight:600;
    color:#2e3a59;
}

/* SUMMARY CARDS */
.cards{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:15px;
    margin-bottom:20px;
}

.card-box{
    background:#fff;
    padding:16px;
    border-radius:12px;
    box-shadow:0 4px 14px rgba(0,0,0,0.05);
}

.card-number{
    font-size:24px;
    font-weight:700;
}

.card-label{
    margin-top:4px;
    font-size:13px;
    color:#6c757d;
}

/* SYSTEM MATCHED COLORS */
.on-time-color { color: #8b0000; } 
.late-color { color: #c2410c; }   
.absent-color { color: #9ca3af; }  
.dark{ color:#2e3a59; }

/* CHART SECTION */
.charts{
    display:grid;
    grid-template-columns:1.7fr 1fr;
    gap:15px;
}

.chart-box{
    background:#fff;
    padding:18px;
    border-radius:12px;
    box-shadow:0 4px 14px rgba(0,0,0,0.05);
    height:360px;
}

.chart-title{
    font-size:15px;
    font-weight:600;
    margin-bottom:10px;
    color:#2e3a59;
}

canvas{
    max-height:280px !important;
}

/* Responsive */
@media(max-width:992px){
    .cards{
        grid-template-columns:repeat(2,1fr);
    }
    .charts{
        grid-template-columns:1fr;
    }
}

/* Calendar / Date Picker Styling */
.dashboard-header form input[type="date"] {
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    font-weight: 500;
    color: #2e3a59;
    background-color: #fff;
    cursor: pointer;
    transition: all 0.2s ease;
}

.dashboard-header form input[type="date"]:focus {
    outline: none;
    border-color: #8b0000;
    box-shadow: 0 0 5px rgba(139, 0, 0, 0.3);
}
</style>

<div class="dashboard-container">

    <div class="dashboard-header">
        <div class="dashboard-title">
            📊 Attendance Dashboard – 
            {{ $selectedDate->format('F d, Y') }}
        </div>

        <form method="GET">
            <input type="date" name="date" value="{{ $selectedDate->format('Y-m-d') }}" onchange="this.form.submit()">
        </form>
    </div>

    <div class="cards">
        <div class="card-box">
            <div class="card-number on-time-color">{{ $presentToday }}</div>
            <div class="card-label">On-Time Today</div>
        </div>

        <div class="card-box">
            <div class="card-number late-color">{{ $lateToday }}</div>
            <div class="card-label">Late Today</div>
        </div>

        <div class="card-box">
            <div class="card-number absent-color">{{ $absentToday }}</div>
            <div class="card-label">Absent Today</div>
        </div>

        <div class="card-box">
            <div class="card-number dark">{{ $totalEmployees }}</div>
            <div class="card-label">Total Employees</div>
        </div>
    </div>

    <div class="charts">
        <div class="chart-box">
            <div class="chart-title">Daily Attendance (Last 7 Days)</div>
            <canvas id="dailyChart"></canvas>
        </div>

        <div class="chart-box">
            <div class="chart-title">Monthly Summary</div>
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// DAILY ATTENDANCE
new Chart(document.getElementById('dailyChart'), {
    type:'bar',
    data:{
        labels:{!! json_encode($last7DaysLabels) !!},
        datasets:[
            {
                label:'On-Time',
                data:{!! json_encode($onTimeLast7DaysData) !!},
                backgroundColor:'#8b0000'
            },
            {
                label:'Late',
                data:{!! json_encode($lateLast7DaysData) !!},
                backgroundColor:'#c2410c'
            },
            {
                label:'Absent',
                data:{!! json_encode($absentLast7DaysData) !!},
                backgroundColor:'#1a0000,'
            }
        ]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{
            legend:{ position:'top' }
        },
        scales:{
            y:{ beginAtZero:true, ticks: { stepSize: 1 } }
        }
    }
});

// MONTHLY SUMMARY
new Chart(document.getElementById('monthlyChart'), {
    type:'doughnut',
    data:{
        labels:['On-Time','Late','Absent'],
        datasets:[{
            data:[
                {{ $monthlyOnTime }},
                {{ $monthlyLate }},
                {{ $monthlyAbsent }}
            ],
            backgroundColor:[
                '#8b0000',
                '#c2410c',
                '#1a0000,'
            ]
        }]
    },
    options:{
        cutout:'65%',
        responsive:true,
        maintainAspectRatio:false,
        plugins:{
            legend:{ position:'bottom' }
        }
    }
});
</script>
@endsection
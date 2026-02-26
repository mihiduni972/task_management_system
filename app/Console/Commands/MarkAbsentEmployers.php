<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class MarkAbsentEmployers extends Command
{
    protected $signature = 'attendance:mark-absent';
    protected $description = 'Mark absent employers who did not check in today';

    public function handle()
    {
        $today = Carbon::today();

        $employers = User::where('user_type', 'employer')->get();

        foreach ($employers as $user) {

            $exists = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->exists();

            if (!$exists) {
                Attendance::create([
                    'user_id' => $user->id,
                    'date'    => $today,
                    'status'  => 'absent',
                ]);
            }
        }

        $this->info('Absent employers marked successfully.');
    }
}

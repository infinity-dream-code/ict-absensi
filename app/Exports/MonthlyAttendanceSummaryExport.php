<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Holiday;
use App\Models\Setting;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class MonthlyAttendanceSummaryExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Get all users
        $users = User::where('role', 'user')->orderBy('name')->get();

        // Get settings for check-in end time
        $settings = Setting::getSettings();
        $checkInEndTime = $settings->check_in_end ?: '09:00:00';

        // Get all attendances with filters
        $query = Attendance::with('user')->whereNotNull('check_in');

        if ($this->year !== null) {
            $query->whereYear('attendance_date', $this->year);
        }

        if ($this->month !== null) {
            $query->whereMonth('attendance_date', $this->month);
        }

        $attendances = $query->get();

        // Get all leaves with filters
        $leaveQuery = Leave::with('user');
        if ($this->year !== null) {
            $leaveQuery->whereYear('leave_date', $this->year);
        }
        if ($this->month !== null) {
            $leaveQuery->whereMonth('leave_date', $this->month);
        }
        $leaves = $leaveQuery->get();

        // Get holidays for the period
        $holidayQuery = Holiday::query();
        if ($this->year !== null) {
            $holidayQuery->where('year', $this->year);
        }
        if ($this->month !== null) {
            $holidayQuery->whereMonth('date', $this->month);
        }
        $holidays = $holidayQuery->pluck('date')->map(function($date) {
            return Carbon::parse($date)->format('Y-m-d');
        })->toArray();

        // Determine date range
        $startDate = null;
        $endDate = null;
        if ($this->year !== null && $this->month !== null) {
            $startDate = Carbon::create($this->year, $this->month, 1);
            $endDate = $startDate->copy()->endOfMonth();
        } elseif ($this->year !== null) {
            $startDate = Carbon::create($this->year, 1, 1);
            $endDate = $startDate->copy()->endOfYear();
        }

        // Process data per user
        $summary = collect();

        foreach ($users as $user) {
            $userAttendances = $attendances->where('user_id', $user->id);
            $userLeaves = $leaves->where('user_id', $user->id);

            $tepatWaktu = 0;
            $terlambat = 0;
            $wfo = 0;
            $wfh = 0;
            $wfa = 0;
            $cuti = 0;
            $izin = 0;
            $sakit = 0;
            $alpha = 0;

            // Count attendances
            foreach ($userAttendances as $attendance) {
                // Count by work type
                if ($attendance->work_type === 'WFO') {
                    $wfo++;
                } elseif ($attendance->work_type === 'WFH') {
                    $wfh++;
                } elseif ($attendance->work_type === 'WFA') {
                    $wfa++;
                }

                // Count by status (tepat waktu or terlambat)
                if ($attendance->check_in) {
                    $checkInEnd = Carbon::parse($attendance->attendance_date->format('Y-m-d') . ' ' . $checkInEndTime, 'Asia/Jakarta');
                    $checkInTime = Carbon::parse($attendance->check_in, 'Asia/Jakarta');

                    if ($checkInTime->gt($checkInEnd)) {
                        $terlambat++;
                    } else {
                        $tepatWaktu++;
                    }
                }
            }

            // Count leaves
            foreach ($userLeaves as $leave) {
                if ($leave->leave_type === 'cuti') {
                    $cuti++;
                } elseif ($leave->leave_type === 'izin') {
                    $izin++;
                } elseif ($leave->leave_type === 'sakit') {
                    $sakit++;
                }
            }

            // Calculate alpha (only if we have date range)
            if ($startDate && $endDate) {
                $attendanceDates = $userAttendances->pluck('attendance_date')->map(function($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                })->toArray();

                $leaveDates = $userLeaves->pluck('leave_date')->map(function($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                })->toArray();

                $currentDate = $startDate->copy();
                while ($currentDate->lte($endDate)) {
                    $dateStr = $currentDate->format('Y-m-d');
                    $dayOfWeek = $currentDate->dayOfWeek; // 0 = Sunday, 6 = Saturday

                    // Skip weekend (Saturday = 6, Sunday = 0)
                    if ($dayOfWeek != 6 && $dayOfWeek != 0) {
                        // Skip holidays
                        if (!in_array($dateStr, $holidays)) {
                            // If no attendance and no leave, count as alpha
                            if (!in_array($dateStr, $attendanceDates) && !in_array($dateStr, $leaveDates)) {
                                $alpha++;
                            }
                        }
                    }

                    $currentDate->addDay();
                }
            }

            $summary->push([
                'user' => $user,
                'tepat_waktu' => $tepatWaktu,
                'terlambat' => $terlambat,
                'wfo' => $wfo,
                'wfh' => $wfh,
                'wfa' => $wfa,
                'cuti' => $cuti,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpha' => $alpha,
            ]);
        }

        return $summary;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NIK',
            'Nama',
            'Tepat Waktu',
            'Terlambat',
            'WFO',
            'WFH',
            'WFA',
            'Cuti',
            'Izin',
            'Sakit',
            'Alpha'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row['user']->nik,
            $row['user']->name,
            $row['tepat_waktu'],
            $row['terlambat'],
            $row['wfo'],
            $row['wfh'],
            $row['wfa'],
            $row['cuti'],
            $row['izin'],
            $row['sakit'],
            $row['alpha'],
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        if ($this->year !== null && $this->month !== null) {
            $monthName = Carbon::create($this->year, $this->month, 1)->locale('id')->isoFormat('MMMM YYYY');
            return 'Rekap Absensi ' . $monthName;
        } elseif ($this->year !== null) {
            return 'Rekap Absensi Tahun ' . $this->year;
        } elseif ($this->month !== null) {
            $monthName = Carbon::create(null, $this->month, 1)->locale('id')->isoFormat('MMMM');
            return 'Rekap Absensi Bulan ' . $monthName;
        } else {
            return 'Rekap Absensi Semua';
        }
    }
}

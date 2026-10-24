<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Application;
use App\Models\Job;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use ApiResponse;

    public function dashboard(): JsonResponse
    {
        return $this->success([
            'total_users'           => User::count(),
            'new_users_this_week'   => User::where('created_at', '>=', now()->startOfWeek())->count(),
            'total_jobs'            => Job::whereIn('status', ['active', 'updated', 'closing_soon'])->count(),
            'total_applications'    => Application::count(),
            'active_subscriptions'  => Subscription::where('status', 'active')->count(),
            'revenue_this_month'    => DB::table('payments')
                ->where('status', 'succeeded')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount') ?? 0,
        ]);
    }

    public function usersByProfession(): JsonResponse
    {
        $data = DB::table('candidate_profiles')
            ->join('professions', 'candidate_profiles.profession_id', '=', 'professions.id')
            ->select('professions.name', DB::raw('count(*) as total'))
            ->groupBy('professions.name')
            ->orderByDesc('total')
            ->get();

        return $this->success($data);
    }

    public function jobsByProfession(): JsonResponse
    {
        $data = DB::table('jobs')
            ->join('professions', 'jobs.profession_id', '=', 'professions.id')
            ->whereIn('jobs.status', ['active', 'updated', 'closing_soon'])
            ->select('professions.name', DB::raw('count(*) as total'))
            ->groupBy('professions.name')
            ->orderByDesc('total')
            ->get();

        return $this->success($data);
    }

    public function applicationStatuses(): JsonResponse
    {
        $data = Application::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        return $this->success($data);
    }

    public function subscriptionRevenue(): JsonResponse
    {
        $data = DB::table('payments')
            ->where('status', 'succeeded')
            ->select(
                DB::raw("DATE_FORMAT(paid_at, '%Y-%m') as month"),
                DB::raw('SUM(amount) as revenue'),
                DB::raw('COUNT(*) as count')
            )
            ->whereNotNull('paid_at')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return $this->success($data);
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $request->validate(['report' => ['required', 'in:users,jobs,applications,subscriptions']]);

        $filename = $request->report . '_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($request) {
            $handle = fopen('php://output', 'w');

            match ($request->report) {
                'users' => $this->streamUsers($handle),
                'jobs'  => $this->streamJobs($handle),
                default => fputcsv($handle, ['No data']),
            };

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function streamUsers($handle): void
    {
        fputcsv($handle, ['ID', 'Name', 'Email', 'Country', 'Created At']);
        User::select('id', 'name', 'email', 'country', 'created_at')
            ->chunk(500, function ($users) use ($handle) {
                foreach ($users as $u) {
                    fputcsv($handle, [$u->id, $u->name, $u->email, $u->country, $u->created_at]);
                }
            });
    }

    private function streamJobs($handle): void
    {
        fputcsv($handle, ['ID', 'Title', 'Organisation', 'Location', 'Band', 'Status', 'Closing Date']);
        Job::select('id', 'title', 'organisation', 'location', 'band', 'status', 'closing_date')
            ->chunk(500, function ($jobs) use ($handle) {
                foreach ($jobs as $j) {
                    fputcsv($handle, [$j->id, $j->title, $j->organisation, $j->location, $j->band, $j->status?->value, $j->closing_date]);
                }
            });
    }
}

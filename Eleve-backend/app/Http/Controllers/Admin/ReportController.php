<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardReportService;

class ReportController extends Controller
{
    public function __construct(
        protected DashboardReportService $dashboardService
    ) {}

    public function index()
    {
        $metrics = $this->dashboardService->getDashboardMetrics();
        return view('admin.reports.index', $metrics);
    }
}

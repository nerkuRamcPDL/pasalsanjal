<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Phase 1 provides the dashboard shell; sales/order/commission KPIs
        // populate once the Orders & Financial modules land in later phases.
        $kpis = [
            'total_users' => User::count(),
            'total_vendors' => Vendor::count(),
            'pending_vendors' => Vendor::where('status', 'pending')->count(),
        ];

        $recentAudit = AuditLog::with('user')->latest('id')->limit(8)->get();

        return view('admin.dashboard', compact('kpis', 'recentAudit'));
    }
}

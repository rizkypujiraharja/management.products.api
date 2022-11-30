<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Modules\Reports\src\Models\RestockingReport;
use App\Traits\CsvFileResponse;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class RestockingReportController extends Controller
{
    use CsvFileResponse;

    /**
     * @param Request $request
     *
     * @return Application|ResponseFactory|Factory|Response|View
     */
    public function index(Request $request)
    {
        if ($request->has('filename')) {
            $report = new RestockingReport();
            $report->view = 'reports.restocking-report';

            return $report->response($request);
        }

        return view('reports.restocking-report', [
            'cached_restocking_report' => Cache::get('cached_restocking_report_user_id_'. auth()->id())
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscriberController extends AdminController
{
    public function index(Request $request)
    {
        return view('admin.subscribers', [
            'rows' => Subscriber::orderByDesc('id')->paginate(30),
            'total' => Subscriber::where('is_active', true)->count(),
            'user' => $this->admin(),
        ]);
    }

    public function toggle(int|string $id): RedirectResponse
    {
        $row = Subscriber::findOrFail((int) $id);
        $row->is_active = ! $row->is_active;
        $row->save();

        return back()->with('ok', 'सदस्यता स्थिति बदली।');
    }

    public function destroy(int|string $id): RedirectResponse
    {
        Subscriber::where('id', $id)->delete();

        return back()->with('ok', 'सदस्य हटा दिया गया।');
    }

    public function exportCsv(): StreamedResponse
    {
        $filename = 'subscribers-'.date('Y-m-d').'.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM so Excel renders Hindi correctly.
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['#', 'ईमेल', 'स्थिति', 'दिनांक']);

            $i = 0;
            Subscriber::orderByDesc('id')->chunk(200, function ($rows) use (&$i, $out) {
                foreach ($rows as $r) {
                    fputcsv($out, [
                        ++$i,
                        $r->email,
                        $r->is_active ? 'सक्रिय' : 'निष्क्रिय',
                        optional($r->created_at)->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}

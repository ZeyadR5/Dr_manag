<?php

namespace App\Http\Controllers;

use App\Services\OrthancService;
use Illuminate\Http\Request;

class StudyController extends Controller
{
    public function __construct(private OrthancService $orthanc)
    {
    }

    public function index(Request $request)
    {
        $searchDate = (string) $request->query('date', '');
        $searchName = (string) $request->query('name', '');
        $searchId = (string) $request->query('id', '');
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 10;
        $startIndex = ($page - 1) * $perPage;
        $today = now('Africa/Cairo')->format('Ymd');

        $query = [];
        if ($searchName === '' && $searchId === '') {
            $query['StudyDate'] = $searchDate ? str_replace('-', '', $searchDate) : $today;
        }
        if ($searchName !== '') {
            $query['PatientName'] = '*'.$searchName.'*';
        }
        if ($searchId !== '') {
            $query['PatientID'] = '*'.$searchId.'*';
        }

        $errorMsg = null;
        $allStudies = [];

        try {
            $allStudies = $this->orthanc->findStudies($query, true);
        } catch (\Throwable $e) {
            $allStudies = [];
            $errorMsg = '⚠️ فشل الاتصال بخادم الأشعة (Orthanc). تأكد من أنه قيد التشغيل.';
        }

        usort($allStudies, function ($a, $b) {
            $valueA = ($a['MainDicomTags']['StudyDate'] ?? '00000000').($a['MainDicomTags']['StudyTime'] ?? '000000');
            $valueB = ($b['MainDicomTags']['StudyDate'] ?? '00000000').($b['MainDicomTags']['StudyTime'] ?? '000000');

            return $valueB <=> $valueA;
        });

        $total = count($allStudies);
        $studies = array_slice($allStudies, $startIndex, $perPage);
        $totalPages = (int) ceil($total / $perPage);

        return view('studies.index', compact(
            'searchDate',
            'searchName',
            'searchId',
            'page',
            'studies',
            'totalPages',
            'errorMsg'
        ));
    }

    public function view(Request $request)
    {
        $id = (string) $request->query('id', '');
        $study = $id !== '' ? $this->orthanc->study($id) : null;

        if (! is_array($study)) {
            abort(500, '⚠️ فشل تحميل بيانات الدراسة من Orthanc. تحقق من الاتصال وبيانات الاعتماد.');
        }

        $patientName = $this->orthanc->cleanPatientName($study['PatientMainDicomTags']['PatientName'] ?? null);
        $instances = $this->orthanc->instances($id);

        return view('studies.view', [
            'patientName' => $patientName,
            'instances' => $instances,
            'orthanc' => $this->orthanc,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $searchDate = (string) $request->input('date', '');
        $searchName = (string) $request->input('name', '');
        $searchId = (string) $request->input('id', '');
        $today = now('Africa/Cairo')->format('Ymd');

        $query = [
            'StudyDate' => $searchDate ? str_replace('-', '', $searchDate) : $today,
        ];
        if ($searchName !== '') {
            $query['PatientName'] = '*'.$searchName.'*';
        }
        if ($searchId !== '') {
            $query['PatientID'] = '*'.$searchId.'*';
        }

        try {
            $studyIds = $this->orthanc->findStudies($query, false);
        } catch (\Throwable $e) {
            abort(500, '❌ فشل تحميل البيانات من Orthanc. تحقق من الاتصال وبيانات الاعتماد.');
        }

        $filename = 'studies_'.now('Africa/Cairo')->format('Y-m-d').'.xls';
        $toUtf16 = fn (string $text) => mb_convert_encoding($text, 'UTF-16LE', 'UTF-8');

        return response()->streamDownload(function () use ($studyIds, $toUtf16) {
            echo chr(255).chr(254);
            echo $toUtf16("👤 اسم المريض\t📅 تاريخ الفحص\t🆔 Patient ID\n");

            foreach ($studyIds as $studyId) {
                $study = is_array($studyId) ? $studyId : $this->orthanc->study((string) $studyId);
                if (! is_array($study)) {
                    continue;
                }

                $formattedDate = $this->orthanc->formatDicomDate($study['MainDicomTags']['StudyDate'] ?? '');
                $patientName = $this->orthanc->cleanPatientName($study['PatientMainDicomTags']['PatientName'] ?? null);
                $patientId = $study['PatientMainDicomTags']['PatientID'] ?? '';

                echo $toUtf16("$patientName\t$formattedDate\t$patientId\n");
            }
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-16LE',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}

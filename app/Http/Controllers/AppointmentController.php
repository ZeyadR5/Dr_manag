<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('delete_id')) {
            $redirect = url('reserv.php');
            if ($request->filled('selectedDate')) {
                $redirect .= '?selectedDate='.$request->query('selectedDate');
            }

            $appointment = Appointment::find($request->integer('delete_id'));
            if (! $appointment) {
                return redirect()->to($redirect)->with('error', 'الحجز غير موجود.');
            }

            if ($appointment->statue == 1) {
                return redirect()->to($redirect)->with('error', '⚠️ لا يمكن حذف هذا الحجز لأنه مكتمل (تم الكشف ومغلق).');
            }

            $appointment->delete();

            return redirect()->to($redirect)->with('success', 'تم حذف الحجز بنجاح.');
        }

        if ($request->isMethod('post') && $request->has('update_status')) {
            $appointment = Appointment::find($request->integer('apid'));
            if ($appointment) {
                $updateData = [
                    'statue' => $request->has('status') ? 1 : 0,
                ];

                if ($request->has('doctor_comment')) {
                    $updateData['commnet'] = trim((string) $request->input('doctor_comment'));
                }

                $appointment->update($updateData);
            }

            $redirect = url('reserv.php');
            if ($request->filled('selectedDate')) {
                $redirect .= '?selectedDate='.$request->input('selectedDate');
            }

            return redirect()->to($redirect)->with('success', 'تم تحديث حالة الحجز وملاحظات الطبيب بنجاح.');
        }

        $selectedDate = now('Africa/Cairo')->format('Y-m-d');
        if ($request->filled('appointmentDate')) {
            $selectedDate = (string) $request->input('appointmentDate');
        } elseif ($request->has('selectedDate')) {
            $selectedDate = (string) $request->input('selectedDate');
        }

        if ($request->has('show_all')) {
            $selectedDate = '';
        }

        $query = Appointment::query();
        $hasConditions = false;

        if ($selectedDate !== '') {
            $query->where('appointmentDate', $selectedDate);
            $hasConditions = true;
        }
        if ($request->filled('patientName')) {
            $query->where('patient_Name', 'like', '%'.$request->input('patientName').'%');
            $hasConditions = true;
        }
        if ($request->filled('patientNum')) {
            $query->where('patient_Num', 'like', '%'.$request->input('patientNum').'%');
            $hasConditions = true;
        }
        if ($request->filled('adrs')) {
            $query->where('adrs', 'like', '%'.$request->input('adrs').'%');
            $hasConditions = true;
        }

        if ($hasConditions) {
            $query->orderBy('postingDate')->orderBy('apid');
        } else {
            $query->orderByDesc('postingDate')->orderByDesc('apid');
        }

        $rows = $query->get();

        $appointmentsByType = [
            'كشف' => [],
            'استشاره' => [],
            'استشارة' => [],
            'أخرى' => [],
        ];
        $totalPaid = 0;

        foreach ($rows as $row) {
            $totalPaid += $row->rowTotal();
            if ($row->typ === 'كشف') {
                $appointmentsByType['كشف'][] = $row;
            } elseif (in_array($row->typ, ['استشاره', 'استشارة'], true)) {
                $appointmentsByType['استشاره'][] = $row;
            } else {
                $appointmentsByType['أخرى'][] = $row;
            }
        }

        foreach ($appointmentsByType as $key => $value) {
            $appointmentsByType[$key] = array_reverse($value);
        }

        return view('appointments.reserv', [
            'selectedDate' => $selectedDate,
            'appointmentsByType' => $appointmentsByType,
            'totalPaid' => $totalPaid,
            'hasResults' => $rows->isNotEmpty(),
            'patientName' => (string) $request->input('patientName', ''),
            'patientNum' => (string) $request->input('patientNum', ''),
            'adrs' => (string) $request->input('adrs', ''),
            'appointmentDateInput' => (string) $request->input('appointmentDate', $selectedDate),
        ]);
    }

    public function create()
    {
        return view('appointments.create');
    }

    public function store(Request $request)
    {
        if (! $request->has('Save') && ! $request->filled('name')) {
            return redirect()->to(url('new_appoint.php'));
        }

        Appointment::create([
            'patient_Name' => trim((string) $request->input('name', '')),
            'patient_Num' => trim((string) $request->input('phnumber', '')),
            'appointmentDate' => trim((string) $request->input('Date', '')),
            'appointmentTime' => trim((string) $request->input('Time', '')),
            'paid' => trim((string) $request->input('Payed', '')) ?: 0,
            'over_paid' => trim((string) $request->input('over_price_g', '')) ?: 0,
            'typ' => trim((string) $request->input('typ', '')),
            'commnet' => trim((string) $request->input('about', '')),
            'adrs' => trim((string) $request->input('adrs', '')),
        ]);

        return redirect()->to(url('new_appoint.php'))->with('success', 'تم الحجز بنجاح');
    }

    public function edit(Request $request)
    {
        $appointmentId = (int) $request->query('id', 0);
        if ($appointmentId < 1) {
            abort(400, 'معرف الحجز غير موجود');
        }

        $appointment = Appointment::find($appointmentId);
        if (! $appointment) {
            abort(404, 'الحجز غير موجود');
        }

        if ($appointment->statue == 1) {
            return redirect()->to(url('reserv.php'))->with('error', '⚠️ هذا الحجز مكتمل بالفعل (تم الكشف) ومغلق ضد التعديل.');
        }

        return view('appointments.edit', compact('appointment'));
    }

    public function update(Request $request)
    {
        $appointmentId = (int) $request->query('id', $request->input('ID', 0));
        $appointment = Appointment::find($appointmentId);
        if (! $appointment) {
            abort(404, 'الحجز غير موجود');
        }

        if ($appointment->statue == 1) {
            return redirect()->to(url('reserv.php'))->with('error', '⚠️ هذا الحجز مكتمل بالفعل ومغلق ضد التعديل.');
        }

        if (! $request->has('update') && ! $request->filled('name')) {
            return view('appointments.edit', compact('appointment'));
        }

        $appointment->update([
            'patient_Name' => trim((string) $request->input('name', '')),
            'patient_Num' => trim((string) $request->input('phnumber', '')),
            'appointmentDate' => trim((string) $request->input('Date', '')),
            'appointmentTime' => trim((string) $request->input('Time', '')),
            'paid' => trim((string) $request->input('Payed', '')) ?: 0,
            'over_paid' => trim((string) $request->input('over_price_g', '')) ?: 0,
            'typ' => trim((string) $request->input('typ', '')),
            'commnet' => trim((string) $request->input('about', '')),
            'adrs' => trim((string) $request->input('adrs', '')),
        ]);

        return redirect()->to(url('reserv.php'))->with('success', 'تم التحديث بنجاح');
    }

    public function history(Request $request)
    {
        $name = trim((string) $request->query('name', ''));
        $phone = trim((string) $request->query('phone', ''));

        if ($name === '' && $phone === '') {
            return response()->json(['history' => []]);
        }

        $query = Appointment::query();
        if ($phone !== '') {
            $query->where('patient_Num', $phone);
        } elseif ($name !== '') {
            $query->where('patient_Name', 'like', '%'.$name.'%');
        }

        $history = $query->orderByDesc('appointmentDate')->orderByDesc('apid')->get()->map(function ($row) {
            return [
                'id' => $row->apid,
                'name' => $row->patient_Name,
                'phone' => $row->patient_Num,
                'date' => $row->appointmentDate,
                'time' => $row->appointmentTime,
                'type' => $row->typ,
                'paid' => $row->paid,
                'over_paid' => $row->over_paid,
                'total' => $row->rowTotal(),
                'status' => $row->statue,
                'comment' => $row->commnet,
            ];
        });

        return response()->json(['history' => $history]);
    }

    public function exportExcel(Request $request)
    {
        $query = Appointment::query();

        if ($request->filled('patientName')) {
            $query->where('patient_Name', 'like', '%'.$request->query('patientName').'%');
        }

        $date = $request->query('appointmentDate') ?: $request->query('selectedDate');
        if (! empty($date)) {
            $query->where('appointmentDate', $date);
        }
        if ($request->filled('patientNum')) {
            $query->where('patient_Num', 'like', '%'.$request->query('patientNum').'%');
        }
        if ($request->filled('adrs')) {
            $query->where('adrs', 'like', '%'.$request->query('adrs').'%');
        }

        $rows = $query->orderByDesc('postingDate')->get();
        $filename = 'appointments_'.now('Africa/Cairo')->format('Y-m-d').'.xls';

        $html = '<table border="1"><tr><th>م</th><th>اسم المريض</th><th>العنوان</th><th>رقم الموبايل</th><th>نوع الحجز</th><th>سعر الكشف</th><th>سعر الجبس</th><th>الإجمالي</th></tr>';
        $totalPaid = 0;
        foreach ($rows as $row) {
            $rowTotal = $row->rowTotal();
            $totalPaid += $rowTotal;
            $html .= '<tr>'
                .'<td>'.$row->apid.'</td>'
                .'<td>'.e($row->patient_Name).'</td>'
                .'<td>'.e($row->adrs).'</td>'
                .'<td>'.e($row->patient_Num).'</td>'
                .'<td>'.e($row->typ).'</td>'
                .'<td>'.$row->paid.'</td>'
                .'<td>'.$row->over_paid.'</td>'
                .'<td>'.$rowTotal.'</td>'
                .'</tr>';
        }
        $html .= '<tr style="font-weight:bold; background:#d4edda"><td colspan="7">الإجمالي</td><td>'.$totalPaid.'</td></tr></table>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename='.$filename,
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}

@extends('layouts.app')

@section('title', 'أرشيف وفحوصات الأشعة PACS')
@section('page-title', 'أرشيف وفحوصات الأشعة (PACS)')
@section('page-subtitle', 'البحث في دراسات وصور الأشعة الرقمية DICOM ومراجعة صور المرضى')

@section('content')
<div class="space-y-6">

    <!-- Search Box Card -->
    <div class="glass-card p-5 md:p-6 border border-slate-200">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-sky-600 to-clinic-600 text-white flex items-center justify-center text-xl shadow-md shadow-sky-600/20">
                    <i class="bi bi-search-heart"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base sm:text-lg">البحث في دراسات الأشعة</h3>
                    <p class="text-xs text-slate-500">ابحث باسم المريض أو تاريخ الفحص أو Patient ID من خادم Orthanc PACS</p>
                </div>
            </div>

            <!-- Quick Export Excel -->
            <form method="POST" action="{{ url('export_excel.php') }}" class="m-0">
                @csrf
                <input type="hidden" name="date" value="{{ $searchDate }}">
                <input type="hidden" name="name" value="{{ $searchName }}">
                <input type="hidden" name="id" value="{{ $searchId }}">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-sm transition-all hover:scale-[1.01]">
                    <i class="bi bi-file-earmark-excel"></i>
                    <span>تصدير الحالات إلى Excel</span>
                </button>
            </form>
        </div>

        <form method="GET" action="{{ url('index.php') }}" class="mt-5">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1.5">
                        🗓️ تاريخ الفحص
                    </label>
                    <input type="date" name="date" value="{{ $searchDate }}" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-clinic-500 focus:border-clinic-500 transition-all outline-none">
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1.5">
                        👤 اسم المريض
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" name="name" value="{{ $searchName }}" placeholder="أدخل اسم المريض..." class="w-full pr-9 pl-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-clinic-500 focus:border-clinic-500 transition-all outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1.5">
                        🆔 Patient ID
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <i class="bi bi-upc-scan"></i>
                        </span>
                        <input type="text" name="id" value="{{ $searchId }}" placeholder="رقم المعرف..." class="w-full pr-9 pl-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-clinic-500 focus:border-clinic-500 transition-all outline-none font-mono">
                    </div>
                </div>
            </div>

            <input type="hidden" name="page" value="1">

            <div class="mt-4 pt-3 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-clinic-600 hover:bg-clinic-700 text-white font-extrabold text-sm shadow-md shadow-clinic-500/20 transition-all hover:scale-[1.01]">
                        <i class="bi bi-search"></i>
                        <span>بحث في السجلات</span>
                    </button>
                    <a href="{{ url('index.php') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm transition-all">
                        إعادة تعيين
                    </a>
                </div>
                <div class="text-xs text-slate-500">
                    الصفحة <strong class="text-slate-800">{{ $page }}</strong> من أصل <strong class="text-slate-800">{{ max(1, $totalPages) }}</strong>
                </div>
            </div>
        </form>
    </div>

    <!-- Studies Table Card -->
    <div class="glass-card overflow-hidden border border-slate-200 shadow-sm">
        <div class="p-4 sm:p-5 bg-gradient-to-r from-sky-50 via-white to-white border-b border-sky-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sky-600 text-white flex items-center justify-center font-bold shadow-md shadow-sky-600/20">
                    <i class="bi bi-file-medical text-xl"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base sm:text-lg">نتائج فحوصات الأشعة</h3>
                    <p class="text-xs text-slate-500">انقر على "عرض الصور" لفتح عارض DICOM الخاص بالحالة</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-black bg-sky-100 text-sky-800 border border-sky-200">
                {{ count($studies) }} حالة في هذه الصفحة
            </span>
        </div>

        @if(!empty($studies))
            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm border-collapse">
                    <thead>
                        <tr class="bg-slate-100/80 text-slate-700 font-bold border-b border-slate-200 text-xs">
                            <th class="p-3.5">اسم المريض</th>
                            <th class="p-3.5 text-center">تاريخ الفحص</th>
                            <th class="p-3.5 text-center">وقت الفحص</th>
                            <th class="p-3.5 text-center">Patient ID</th>
                            <th class="p-3.5 text-center w-36">استعراض الأشعة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($studies as $study)
                            @php
                                $id = $study['ID'] ?? '';
                                $tags = $study['MainDicomTags'] ?? [];
                                $dicomDate = $tags['StudyDate'] ?? '';
                                $formattedDate = $dicomDate ? substr($dicomDate, 0, 4).'/'.substr($dicomDate, 4, 2).'/'.substr($dicomDate, 6, 2) : '—';
                                $dicomTime = $tags['StudyTime'] ?? '';
                                $formattedTime = $dicomTime ? substr($dicomTime, 0, 2).':'.substr($dicomTime, 2, 2).':'.substr($dicomTime, 4, 2) : '—';
                                $rawName = $study['PatientMainDicomTags']['PatientName'] ?? 'غير معروف';
                                $patientName = preg_replace('/[^a-zA-Z0-9\s\p{Arabic}]/u', '', $rawName);
                                $patientId = $study['PatientMainDicomTags']['PatientID'] ?? '—';
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="p-3.5 font-bold text-slate-900">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-800 flex items-center justify-center font-bold text-sm">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-slate-900">{{ $patientName }}</p>
                                            <p class="text-[11px] text-slate-400 font-mono">{{ $id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3.5 text-center font-semibold text-slate-700">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-xs font-mono">
                                        {{ $formattedDate }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-center text-slate-500 font-mono text-xs">
                                    {{ $formattedTime }}
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="px-2.5 py-1 rounded-lg bg-clinic-50 border border-clinic-200 text-clinic-800 font-mono text-xs font-bold">
                                        {{ $patientId }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <a href="{{ url('view.php') }}?id={{ $id }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-clinic-600 to-sky-600 hover:from-clinic-700 hover:to-sky-700 text-white font-bold text-xs shadow-sm shadow-clinic-500/20 transition-all hover:scale-[1.02]">
                                        <i class="bi bi-eye-fill"></i>
                                        <span>عرض الصور</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-3xl mb-3">
                    <i class="bi bi-folder-x"></i>
                </div>
                <h4 class="text-base font-bold text-slate-800">لا توجد دراسات أشعة مطابقة</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">تأكد من تاريخ الفحص المحدد أو اتصال خادم Orthanc PACS</p>
            </div>
        @endif

        <!-- Pagination -->
        @if($totalPages > 1)
            <div class="p-4 border-t border-slate-100 flex items-center justify-center gap-2 bg-slate-50/50">
                @if($page > 1)
                    <a href="?page={{ $page - 1 }}&date={{ $searchDate }}&name={{ $searchName }}&id={{ $searchId }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-100 transition-colors">
                        <i class="bi bi-chevron-right ml-1"></i> السابق
                    </a>
                @endif

                <span class="px-3 py-1.5 text-xs font-bold text-slate-600">
                    صفحة {{ $page }} من {{ $totalPages }}
                </span>

                @if($page < $totalPages)
                    <a href="?page={{ $page + 1 }}&date={{ $searchDate }}&name={{ $searchName }}&id={{ $searchId }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-100 transition-colors">
                        التالي <i class="bi bi-chevron-left mr-1"></i>
                    </a>
                @endif
            </div>
        @endif
    </div>

</div>
@endsection

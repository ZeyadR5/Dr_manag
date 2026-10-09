@extends('layouts.app')

@section('title', 'جدول الحجوزات والمواعيد')
@section('page-title', 'جدول المواعيد والحجوزات')
@section('page-subtitle', 'إدارة ومتابعة حجوزات المرضى، الكشوفات، الاستشارات وتدوين الملاحظات الطبية')

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards -->
    @php
        $kashfList = $appointmentsByType['كشف'] ?? [];
        $estesharaList = $appointmentsByType['استشاره'] ?? [];
        $otherList = $appointmentsByType['أخرى'] ?? [];
        $totalCount = count($kashfList) + count($estesharaList) + count($otherList);
        
        $completedCount = 0;
        foreach (array_merge($kashfList, $estesharaList, $otherList) as $item) {
            if ($item->statue == 1) $completedCount++;
        }
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
        <!-- Card 1: Total Appointments -->
        <div class="glass-card p-5 relative overflow-hidden transition-all hover:translate-y-[-2px] hover:shadow-lg border-r-4 border-r-clinic-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">إجمالي الحالات المسجلة</p>
                    <h3 class="text-3xl font-black text-slate-900 mt-1">{{ $totalCount }}</h3>
                    <p class="text-[12px] text-slate-500 mt-1 flex items-center gap-1">
                        <i class="bi bi-calendar-event text-clinic-600"></i>
                        <span>{{ $selectedDate !== '' ? $selectedDate : 'جميع الفترات' }}</span>
                    </p>
                </div>
                <div class="w-13 h-13 rounded-2xl bg-clinic-100 text-clinic-700 flex items-center justify-center text-2xl shadow-inner">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                <span>المكتمل: <strong class="text-emerald-700 font-bold">{{ $completedCount }}</strong></span>
                <span>الانتظار: <strong class="text-amber-700 font-bold">{{ $totalCount - $completedCount }}</strong></span>
            </div>
        </div>

        <!-- Card 2: Kashf Count -->
        <div class="glass-card p-5 relative overflow-hidden transition-all hover:translate-y-[-2px] hover:shadow-lg border-r-4 border-r-tealmed-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">عدد الكشوفات</p>
                    <h3 class="text-3xl font-black text-slate-900 mt-1">{{ count($kashfList) }}</h3>
                    <p class="text-[12px] text-teal-700 font-semibold mt-1">كشف جديد</p>
                </div>
                <div class="w-13 h-13 rounded-2xl bg-teal-100 text-tealmed-700 flex items-center justify-center text-2xl shadow-inner">
                    <i class="bi bi-clipboard2-pulse-fill"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                <span>نسبة الكشوفات</span>
                <span class="font-bold text-tealmed-700">{{ $totalCount > 0 ? round((count($kashfList) / $totalCount) * 100) : 0 }}%</span>
            </div>
        </div>

        <!-- Card 3: Esteshara Count -->
        <div class="glass-card p-5 relative overflow-hidden transition-all hover:translate-y-[-2px] hover:shadow-lg border-r-4 border-r-indigo-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">عدد الاستشارات</p>
                    <h3 class="text-3xl font-black text-slate-900 mt-1">{{ count($estesharaList) }}</h3>
                    <p class="text-[12px] text-indigo-700 font-semibold mt-1">متابعة واستشارة</p>
                </div>
                <div class="w-13 h-13 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-2xl shadow-inner">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                <span>حالات أخرى</span>
                <span class="font-bold text-indigo-700">{{ count($otherList) }} حالة</span>
            </div>
        </div>

        <!-- Card 4: Total Revenue -->
        <div class="glass-card p-5 relative overflow-hidden transition-all hover:translate-y-[-2px] hover:shadow-lg border-r-4 border-r-amber-500 bg-gradient-to-br from-white via-white to-amber-50/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">إجمالي الإيرادات</p>
                    <h3 class="text-2xl sm:text-3xl font-black text-amber-700 mt-1">{{ number_format($totalPaid, 0) }} <span class="text-xs font-semibold text-slate-500">ج.م</span></h3>
                    <p class="text-[12px] text-slate-500 mt-1">شامل الكشف والخدمات</p>
                </div>
                <div class="w-13 h-13 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl shadow-inner">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                <span>الحالة المالية</span>
                <span class="text-emerald-700 font-bold flex items-center gap-1"><i class="bi bi-check2-circle"></i> تم التدقيق</span>
            </div>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="glass-card p-5 md:p-6 border border-slate-200">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl bg-clinic-100 text-clinic-700 flex items-center justify-center font-bold">
                    <i class="bi bi-funnel text-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-base">فلترة وبحث الحجوزات</h4>
                    <p class="text-xs text-slate-500">ابحث بالاسم، التاريخ، رقم الهاتف أو العنوان</p>
                </div>
            </div>

            <!-- Quick Date Buttons -->
            @php
                $todayDate = now('Africa/Cairo')->format('Y-m-d');
                $yesterdayDate = now('Africa/Cairo')->subDay()->format('Y-m-d');
                $tomorrowDate = now('Africa/Cairo')->addDay()->format('Y-m-d');
            @endphp
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ url('reserv.php') }}?selectedDate={{ $todayDate }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $selectedDate === $todayDate ? 'bg-clinic-600 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                    📅 مواعيد اليوم
                </a>
                <a href="{{ url('reserv.php') }}?selectedDate={{ $yesterdayDate }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $selectedDate === $yesterdayDate ? 'bg-clinic-600 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                    أمس
                </a>
                <a href="{{ url('reserv.php') }}?selectedDate={{ $tomorrowDate }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $selectedDate === $tomorrowDate ? 'bg-clinic-600 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                    غداً
                </a>
                <a href="{{ url('reserv.php') }}?show_all=1" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $selectedDate === '' ? 'bg-clinic-600 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                    عرض الكل
                </a>
            </div>
        </div>

        <!-- Form Filters -->
        <form method="POST" action="{{ url('reserv.php') }}" class="mt-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">👤 اسم المريض</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" name="patientName" value="{{ $patientName }}" placeholder="اكتب اسم المريض..." class="w-full pr-9 pl-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-clinic-500 focus:border-clinic-500 transition-all outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">🗓️ تاريخ الحجز</label>
                    <div class="relative">
                        <input type="date" name="appointmentDate" value="{{ $appointmentDateInput }}" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-clinic-500 focus:border-clinic-500 transition-all outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">📱 رقم الهاتف / الموبايل</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <i class="bi bi-telephone"></i>
                        </span>
                        <input type="text" name="patientNum" value="{{ $patientNum }}" placeholder="رقم الهاتف..." class="w-full pr-9 pl-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-clinic-500 focus:border-clinic-500 transition-all outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">📍 العنوان / المنطقة</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <i class="bi bi-geo-alt"></i>
                        </span>
                        <input type="text" name="adrs" value="{{ $adrs }}" placeholder="العنوان..." class="w-full pr-9 pl-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-clinic-500 focus:border-clinic-500 transition-all outline-none">
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-clinic-600 hover:bg-clinic-700 text-white font-bold text-sm shadow-sm transition-all hover:scale-[1.01]">
                        <i class="bi bi-search"></i>
                        <span>تطبيق البحث</span>
                    </button>
                    <a href="{{ url('reserv.php') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm transition-all">
                        إعادة ضبط
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ url('export_excel2.php') }}?patientName={{ urlencode($patientName) }}&appointmentDate={{ urlencode($appointmentDateInput) }}&patientNum={{ urlencode($patientNum) }}&adrs={{ urlencode($adrs) }}&selectedDate={{ urlencode($selectedDate) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-sm transition-all">
                        <i class="bi bi-file-earmark-excel"></i>
                        <span>تصدير الحالات إلى Excel</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Section 1: Kashf Table -->
    @if(!empty($kashfList))
        @php $kashfCount = count($kashfList); @endphp
        <div class="glass-card overflow-hidden border border-slate-200 shadow-sm">
            <div class="p-4 sm:p-5 bg-gradient-to-r from-teal-50 via-white to-white border-b border-teal-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center font-bold shadow-md shadow-teal-600/20">
                        <i class="bi bi-stethoscope text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base sm:text-lg">الكشوفات الطبية</h3>
                        <p class="text-xs text-slate-500">حالات الكشف المسجلة لهذا اليوم ({{ $kashfCount }} حالة)</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-black bg-teal-100 text-teal-800 border border-teal-200">
                    {{ $kashfCount }} كشف
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm border-collapse" id="kashfTable">
                    <thead>
                        <tr class="bg-slate-100/80 text-slate-700 font-bold border-b border-slate-200 text-xs">
                            <th class="p-3 text-center w-12 sequence-column">#</th>
                            <th class="p-3 text-center w-28">حالة الكشف</th>
                            <th class="p-3.5 sortable font-extrabold cursor-pointer hover:bg-slate-200/60" data-sort="patient_Name">اسم المريض والسجل</th>
                            <th class="p-3.5 sortable cursor-pointer hover:bg-slate-200/60" data-sort="patient_Num">رقم الهاتف</th>
                            <th class="p-3.5 sortable cursor-pointer hover:bg-slate-200/60" data-sort="adrs">العنوان</th>
                            <th class="p-3.5 text-center sortable cursor-pointer hover:bg-slate-200/60" data-sort="paid">سعر الكشف</th>
                            <th class="p-3.5 text-center sortable cursor-pointer hover:bg-slate-200/60" data-sort="over_paid">سعر الجبس / إضافي</th>
                            <th class="p-3.5 text-center sortable cursor-pointer hover:bg-slate-200/60" data-sort="total">الإجمالي</th>
                            <th class="p-3.5 min-w-[180px]">ملاحظات الطبيب</th>
                            <th class="p-3 text-center w-32">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($kashfList as $index => $row)
                            @php $rowTotal = $row->rowTotal(); @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors {{ $row->statue == 1 ? 'bg-emerald-50/40' : '' }}">
                                <td class="p-3 text-center font-bold text-slate-400 sequence-column">
                                    {{ $kashfCount - $index }}
                                </td>
                                <td class="p-3 text-center">
                                    @if($row->statue == 1)
                                        <button type="button" onclick="openDoctorNotesModal({{ $row->apid }}, '{{ addslashes($row->patient_Name) }}', '{{ addslashes($row->commnet) }}', true)" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-300 hover:bg-emerald-200 transition-colors shadow-sm" title="عرض ملاحظات الطبيب">
                                            <i class="bi bi-check-circle-fill text-emerald-600"></i>
                                            <span>تم الكشف</span>
                                        </button>
                                    @else
                                        <button type="button" onclick="openDoctorNotesModal({{ $row->apid }}, '{{ addslashes($row->patient_Name) }}', '{{ addslashes($row->commnet) }}', false)" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-50 text-amber-800 font-bold text-xs border border-amber-300 hover:bg-amber-100 hover:border-amber-400 transition-all shadow-sm group" title="كتابة الملاحظات الطبية وإتمام الكشف">
                                            <i class="bi bi-hourglass-split text-amber-600"></i>
                                            <span>انتظار</span>
                                            <span class="text-[10px] bg-white text-clinic-700 px-1.5 py-0.5 rounded border border-amber-200 font-black group-hover:bg-clinic-50">إتمام</span>
                                        </button>
                                    @endif
                                </td>
                                <td class="p-3.5 font-bold text-slate-900">
                                    <div class="flex items-start gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-800 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                                            {{ mb_substr($row->patient_Name, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="text-sm font-extrabold text-slate-900">{{ $row->patient_Name }}</span>
                                            <div class="mt-1">
                                                <button type="button" onclick="openPatientHistory('{{ addslashes($row->patient_Name) }}', '{{ $row->patient_Num }}')" class="inline-flex items-center gap-1 text-[11px] font-bold text-clinic-600 hover:text-clinic-800 bg-clinic-50 hover:bg-clinic-100 px-2 py-0.5 rounded-md border border-clinic-200/60 transition-colors" title="عرض جميع الزيارات والملاحظات السابقة">
                                                    <i class="bi bi-clock-history"></i>
                                                    <span>السجل التاريخي</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3.5 text-slate-600 font-mono text-xs">{{ $row->patient_Num }}</td>
                                <td class="p-3.5 text-slate-600">{{ $row->adrs ?: '—' }}</td>
                                <td class="p-3.5 text-center font-bold text-slate-800" data-sort-value="{{ $row->paid }}">{{ $row->paid }} ج.م</td>
                                <td class="p-3.5 text-center text-slate-600" data-sort-value="{{ $row->over_paid }}">{{ $row->over_paid }} ج.م</td>
                                <td class="p-3.5 text-center font-extrabold text-clinic-700" data-sort-value="{{ $rowTotal }}">{{ number_format($rowTotal, 2) }} ج.م</td>
                                <td class="p-3.5 text-slate-700 text-xs">
                                    @if($row->commnet)
                                        <div class="p-2 rounded-lg bg-slate-100/90 border border-slate-200/80 text-slate-800 text-xs line-clamp-2">
                                            {{ $row->commnet }}
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">لا توجد ملاحظات</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    @if($row->statue == 1)
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 text-slate-500 font-bold text-xs border border-slate-200 cursor-not-allowed" title="الحجز مكتمل - مغلق ضد التعديل أو الحذف">
                                            <i class="bi bi-lock-fill text-slate-400"></i>
                                            <span>مغلق</span>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ url('edit_appointment.php') }}?id={{ $row->apid }}" title="تعديل الحجز" class="p-1.5 rounded-lg text-clinic-600 hover:bg-clinic-50 transition-colors">
                                                <i class="bi bi-pencil-square text-base"></i>
                                            </a>
                                            <a href="javascript:void(0);" onclick="confirmDelete({{ $row->apid }})" title="حذف الحجز" class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 transition-colors">
                                                <i class="bi bi-trash3 text-base"></i>
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Section 2: Esteshara Table -->
    @if(!empty($estesharaList))
        @php $estesharaCount = count($estesharaList); @endphp
        <div class="glass-card overflow-hidden border border-slate-200 shadow-sm">
            <div class="p-4 sm:p-5 bg-gradient-to-r from-indigo-50 via-white to-white border-b border-indigo-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold shadow-md shadow-indigo-600/20">
                        <i class="bi bi-arrow-repeat text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base sm:text-lg">الاستشارات والمتابعات</h3>
                        <p class="text-xs text-slate-500">حالات الاستشارة المسجلة لهذا اليوم ({{ $estesharaCount }} استشارة)</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-black bg-indigo-100 text-indigo-800 border border-indigo-200">
                    {{ $estesharaCount }} استشارة
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm border-collapse" id="estesharaTable">
                    <thead>
                        <tr class="bg-slate-100/80 text-slate-700 font-bold border-b border-slate-200 text-xs">
                            <th class="p-3 text-center w-12 sequence-column">#</th>
                            <th class="p-3 text-center w-28">حالة الكشف</th>
                            <th class="p-3.5 sortable font-extrabold cursor-pointer hover:bg-slate-200/60" data-sort="patient_Name">اسم المريض والسجل</th>
                            <th class="p-3.5 sortable cursor-pointer hover:bg-slate-200/60" data-sort="patient_Num">رقم الهاتف</th>
                            <th class="p-3.5 sortable cursor-pointer hover:bg-slate-200/60" data-sort="adrs">العنوان</th>
                            <th class="p-3.5 text-center sortable cursor-pointer hover:bg-slate-200/60" data-sort="paid">سعر الاستشارة</th>
                            <th class="p-3.5 text-center sortable cursor-pointer hover:bg-slate-200/60" data-sort="over_paid">سعر الجبس / إضافي</th>
                            <th class="p-3.5 text-center sortable cursor-pointer hover:bg-slate-200/60" data-sort="total">الإجمالي</th>
                            <th class="p-3.5 min-w-[180px]">ملاحظات الطبيب</th>
                            <th class="p-3 text-center w-32">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($estesharaList as $index => $row)
                            @php $rowTotal = $row->rowTotal(); @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors {{ $row->statue == 1 ? 'bg-emerald-50/40' : '' }}">
                                <td class="p-3 text-center font-bold text-slate-400 sequence-column">
                                    {{ $estesharaCount - $index }}
                                </td>
                                <td class="p-3 text-center">
                                    @if($row->statue == 1)
                                        <button type="button" onclick="openDoctorNotesModal({{ $row->apid }}, '{{ addslashes($row->patient_Name) }}', '{{ addslashes($row->commnet) }}', true)" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-300 hover:bg-emerald-200 transition-colors shadow-sm" title="عرض ملاحظات الطبيب">
                                            <i class="bi bi-check-circle-fill text-emerald-600"></i>
                                            <span>تم الكشف</span>
                                        </button>
                                    @else
                                        <button type="button" onclick="openDoctorNotesModal({{ $row->apid }}, '{{ addslashes($row->patient_Name) }}', '{{ addslashes($row->commnet) }}', false)" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-50 text-amber-800 font-bold text-xs border border-amber-300 hover:bg-amber-100 hover:border-amber-400 transition-all shadow-sm group" title="كتابة الملاحظات الطبية وإتمام الكشف">
                                            <i class="bi bi-hourglass-split text-amber-600"></i>
                                            <span>انتظار</span>
                                            <span class="text-[10px] bg-white text-clinic-700 px-1.5 py-0.5 rounded border border-amber-200 font-black group-hover:bg-clinic-50">إتمام</span>
                                        </button>
                                    @endif
                                </td>
                                <td class="p-3.5 font-bold text-slate-900">
                                    <div class="flex items-start gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-800 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                                            {{ mb_substr($row->patient_Name, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="text-sm font-extrabold text-slate-900">{{ $row->patient_Name }}</span>
                                            <div class="mt-1">
                                                <button type="button" onclick="openPatientHistory('{{ addslashes($row->patient_Name) }}', '{{ $row->patient_Num }}')" class="inline-flex items-center gap-1 text-[11px] font-bold text-clinic-600 hover:text-clinic-800 bg-clinic-50 hover:bg-clinic-100 px-2 py-0.5 rounded-md border border-clinic-200/60 transition-colors" title="عرض جميع الزيارات والملاحظات السابقة">
                                                    <i class="bi bi-clock-history"></i>
                                                    <span>السجل التاريخي</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3.5 text-slate-600 font-mono text-xs">{{ $row->patient_Num }}</td>
                                <td class="p-3.5 text-slate-600">{{ $row->adrs ?: '—' }}</td>
                                <td class="p-3.5 text-center font-bold text-slate-800" data-sort-value="{{ $row->paid }}">{{ $row->paid }} ج.م</td>
                                <td class="p-3.5 text-center text-slate-600" data-sort-value="{{ $row->over_paid }}">{{ $row->over_paid }} ج.م</td>
                                <td class="p-3.5 text-center font-extrabold text-indigo-700" data-sort-value="{{ $rowTotal }}">{{ number_format($rowTotal, 2) }} ج.م</td>
                                <td class="p-3.5 text-slate-700 text-xs">
                                    @if($row->commnet)
                                        <div class="p-2 rounded-lg bg-slate-100/90 border border-slate-200/80 text-slate-800 text-xs line-clamp-2">
                                            {{ $row->commnet }}
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">لا توجد ملاحظات</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    @if($row->statue == 1)
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 text-slate-500 font-bold text-xs border border-slate-200 cursor-not-allowed" title="الحجز مكتمل - مغلق ضد التعديل أو الحذف">
                                            <i class="bi bi-lock-fill text-slate-400"></i>
                                            <span>مغلق</span>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ url('edit_appointment.php') }}?id={{ $row->apid }}" title="تعديل الحجز" class="p-1.5 rounded-lg text-clinic-600 hover:bg-clinic-50 transition-colors">
                                                <i class="bi bi-pencil-square text-base"></i>
                                            </a>
                                            <a href="javascript:void(0);" onclick="confirmDelete({{ $row->apid }})" title="حذف الحجز" class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 transition-colors">
                                                <i class="bi bi-trash3 text-base"></i>
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Section 3: Other Types Table -->
    @if(!empty($otherList))
        @php $otherCount = count($otherList); @endphp
        <div class="glass-card overflow-hidden border border-slate-200 shadow-sm">
            <div class="p-4 sm:p-5 bg-gradient-to-r from-amber-50 via-white to-white border-b border-amber-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-600 text-white flex items-center justify-center font-bold shadow-md shadow-amber-600/20">
                        <i class="bi bi-tags text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base sm:text-lg">أنواع حجوزات أخرى</h3>
                        <p class="text-xs text-slate-500">حالات خاصة أخرى ({{ $otherCount }} حالة)</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-800 border border-amber-200">
                    {{ $otherCount }} حالة
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm border-collapse" id="otherTable">
                    <thead>
                        <tr class="bg-slate-100/80 text-slate-700 font-bold border-b border-slate-200 text-xs">
                            <th class="p-3 text-center w-12 sequence-column">#</th>
                            <th class="p-3 text-center w-28">حالة الكشف</th>
                            <th class="p-3.5 sortable font-extrabold cursor-pointer" data-sort="patient_Name">اسم المريض والسجل</th>
                            <th class="p-3.5 sortable cursor-pointer" data-sort="typ">النوع</th>
                            <th class="p-3.5 sortable cursor-pointer" data-sort="patient_Num">رقم الهاتف</th>
                            <th class="p-3.5 sortable cursor-pointer" data-sort="adrs">العنوان</th>
                            <th class="p-3.5 text-center sortable cursor-pointer" data-sort="paid">سعر الكشف</th>
                            <th class="p-3.5 text-center sortable cursor-pointer" data-sort="over_paid">سعر الجبس</th>
                            <th class="p-3.5 text-center sortable cursor-pointer" data-sort="total">الإجمالي</th>
                            <th class="p-3.5 min-w-[180px]">ملاحظات الطبيب</th>
                            <th class="p-3 text-center w-32">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($otherList as $index => $row)
                            @php $rowTotal = $row->rowTotal(); @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors {{ $row->statue == 1 ? 'bg-emerald-50/40' : '' }}">
                                <td class="p-3 text-center font-bold text-slate-400 sequence-column">{{ $otherCount - $index }}</td>
                                <td class="p-3 text-center">
                                    @if($row->statue == 1)
                                        <button type="button" onclick="openDoctorNotesModal({{ $row->apid }}, '{{ addslashes($row->patient_Name) }}', '{{ addslashes($row->commnet) }}', true)" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-300 hover:bg-emerald-200 transition-colors shadow-sm">
                                            <i class="bi bi-check-circle-fill text-emerald-600"></i>
                                            <span>تم الكشف</span>
                                        </button>
                                    @else
                                        <button type="button" onclick="openDoctorNotesModal({{ $row->apid }}, '{{ addslashes($row->patient_Name) }}', '{{ addslashes($row->commnet) }}', false)" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-50 text-amber-800 font-bold text-xs border border-amber-300 hover:bg-amber-100 transition-all shadow-sm group">
                                            <i class="bi bi-hourglass-split text-amber-600"></i>
                                            <span>انتظار</span>
                                            <span class="text-[10px] bg-white text-clinic-700 px-1.5 py-0.5 rounded border border-amber-200 font-black">إتمام</span>
                                        </button>
                                    @endif
                                </td>
                                <td class="p-3.5 font-bold text-slate-900">
                                    <div>
                                        <span class="text-sm font-extrabold text-slate-900">{{ $row->patient_Name }}</span>
                                        <div class="mt-1">
                                            <button type="button" onclick="openPatientHistory('{{ addslashes($row->patient_Name) }}', '{{ $row->patient_Num }}')" class="inline-flex items-center gap-1 text-[11px] font-bold text-clinic-600 hover:text-clinic-800 bg-clinic-50 hover:bg-clinic-100 px-2 py-0.5 rounded-md border border-clinic-200/60 transition-colors">
                                                <i class="bi bi-clock-history"></i>
                                                <span>السجل التاريخي</span>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3.5"><span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">{{ $row->typ }}</span></td>
                                <td class="p-3.5 text-slate-600 font-mono text-xs">{{ $row->patient_Num }}</td>
                                <td class="p-3.5 text-slate-600">{{ $row->adrs ?: '—' }}</td>
                                <td class="p-3.5 text-center font-bold text-slate-800" data-sort-value="{{ $row->paid }}">{{ $row->paid }} ج.م</td>
                                <td class="p-3.5 text-center text-slate-600" data-sort-value="{{ $row->over_paid }}">{{ $row->over_paid }} ج.م</td>
                                <td class="p-3.5 text-center font-extrabold text-amber-700" data-sort-value="{{ $rowTotal }}">{{ number_format($rowTotal, 2) }} ج.م</td>
                                <td class="p-3.5 text-slate-700 text-xs">
                                    @if($row->commnet)
                                        <div class="p-2 rounded-lg bg-slate-100/90 border border-slate-200/80 text-slate-800 text-xs line-clamp-2">
                                            {{ $row->commnet }}
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">لا توجد ملاحظات</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    @if($row->statue == 1)
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 text-slate-500 font-bold text-xs border border-slate-200 cursor-not-allowed" title="الحجز مكتمل - مغلق ضد التعديل أو الحذف">
                                            <i class="bi bi-lock-fill text-slate-400"></i>
                                            <span>مغلق</span>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ url('edit_appointment.php') }}?id={{ $row->apid }}" class="p-1.5 rounded-lg text-clinic-600 hover:bg-clinic-50">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="javascript:void(0);" onclick="confirmDelete({{ $row->apid }})" class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50">
                                                <i class="bi bi-trash3"></i>
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Empty State -->
    @if(!$hasResults)
        <div class="glass-card p-12 text-center border border-dashed border-slate-300">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-3xl mb-4">
                <i class="bi bi-calendar-x"></i>
            </div>
            <h4 class="text-lg font-bold text-slate-800">لا توجد حجوزات مسجلة لهذا التاريخ</h4>
            <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">لم يتم العثور على أي مواعيد مطابقة لخيارات البحث المحددة. يمكنك إضافة حجز جديد الآن.</p>
            <div class="mt-6">
                <a href="{{ url('new_appoint.php') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-clinic-600 hover:bg-clinic-700 text-white font-bold text-sm shadow-md shadow-clinic-500/20">
                    <i class="bi bi-plus-circle"></i>
                    <span>إضافة حجز جديد الآن</span>
                </a>
            </div>
        </div>
    @endif

</div>

<!-- Modal 1: Doctor Notes & Complete Modal -->
<div id="doctorNotesModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200 animate-scale-up">
        <div class="p-5 bg-gradient-to-r from-clinic-700 via-clinic-800 to-tealmed-700 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-xl">
                    <i class="bi bi-clipboard2-pulse"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-base" id="doctorModalTitle">تدوين ملاحظات الطبيب وإتمام الكشف</h3>
                    <p class="text-xs text-clinic-200" id="doctorModalPatientName">المريض: --</p>
                </div>
            </div>
            <button type="button" onclick="closeDoctorNotesModal()" class="text-white/80 hover:text-white p-1 rounded-lg">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>

        <form method="POST" action="{{ url('reserv.php') }}" id="doctorNotesForm" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="update_status" value="1">
            <input type="hidden" name="apid" id="modalApid">
            <input type="hidden" name="selectedDate" value="{{ $selectedDate }}">
            <input type="hidden" name="status" id="modalStatusInput" value="1">

            <div>
                <label class="block text-xs font-black text-slate-700 mb-1.5 flex items-center justify-between">
                    <span>📝 ملاحظات الطبيب، التشخيص، والعلاج</span>
                    <span class="text-[11px] font-normal text-slate-400">سوف تُحفظ في السجل الطبي للمريض</span>
                </label>
                <textarea name="doctor_comment" id="modalDoctorNotes" rows="5" placeholder="اكتب التشخيص الطبي، خطة العلاج، تعليمات المريض، أو سبب الزيارة..." class="w-full p-3.5 text-sm bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-clinic-500 focus:border-clinic-500 transition-all outline-none leading-relaxed"></textarea>
            </div>

            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-start gap-2" id="doctorModalWarning">
                <i class="bi bi-shield-exclamation text-base shrink-0 mt-0.5 text-amber-600"></i>
                <p>تنبيه: بعد تحويل الحجز إلى <strong>مكتمل (تم)</strong>، سيتم إغلاق الحجز ولن يُسمح بتعديله أو حذفه للحفاظ على دقة السجلات.</p>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeDoctorNotesModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                    إلغاء
                </button>
                <button type="submit" id="doctorModalSubmitBtn" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-tealmed-600 hover:from-emerald-700 hover:to-tealmed-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/20 transition-all hover:scale-[1.01]">
                    <i class="bi bi-check2-circle text-base"></i>
                    <span id="doctorModalBtnText">حفظ الملاحظات وتحويل إلى (تم)</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Patient Clinical History Modal -->
<div id="patientHistoryModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[85vh] flex flex-col overflow-hidden border border-slate-200 animate-scale-up">
        <!-- Header -->
        <div class="p-5 bg-gradient-to-r from-clinic-800 via-clinic-900 to-slate-900 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-xl text-clinic-300">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-base">السجل التاريخي للمريض</h3>
                    <p class="text-xs text-clinic-300 font-bold" id="historyPatientName">--</p>
                </div>
            </div>
            <button type="button" onclick="closePatientHistoryModal()" class="text-white/80 hover:text-white p-1 rounded-lg">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>

        <!-- Body / Timeline -->
        <div class="p-6 overflow-y-auto flex-1 space-y-4" id="historyContent">
            <!-- Loading indicator -->
            <div id="historyLoading" class="py-12 text-center text-slate-500">
                <div class="w-10 h-10 border-4 border-clinic-600 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                <p class="text-xs font-bold">جاري تحميل السجل الطبي وتاريخ الزيارات...</p>
            </div>

            <!-- List will be injected here -->
            <div id="historyList" class="space-y-3 hidden"></div>

            <!-- Empty indicator -->
            <div id="historyEmpty" class="py-12 text-center text-slate-400 hidden">
                <i class="bi bi-folder-x text-5xl mb-2 block text-slate-300"></i>
                <h5 class="text-sm font-bold text-slate-700">لا توجد زيارات سابقة مسجلة</h5>
                <p class="text-xs text-slate-400 mt-1">هذه أول زيارة للمريض أو لم يتم تسجيل ملاحظات سابقة</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-200 text-left shrink-0">
            <button type="button" onclick="closePatientHistoryModal()" class="px-5 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-xs transition-colors">
                إغلاق
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete(id) {
    if (confirm('هل أنت متأكد من حذف هذا الحجز نهائياً من سجلات العيادة؟')) {
        window.location.href = @json(url('reserv.php')) + '?delete_id=' + id + '&selectedDate=' + @json($selectedDate);
    }
}

// Modal 1: Doctor Notes & Complete Modal
function openDoctorNotesModal(apid, patientName, currentNotes, isAlreadyCompleted) {
    document.getElementById('modalApid').value = apid;
    document.getElementById('doctorModalPatientName').textContent = 'المريض: ' + patientName;
    document.getElementById('modalDoctorNotes').value = currentNotes || '';
    
    const warningEl = document.getElementById('doctorModalWarning');
    const submitBtnText = document.getElementById('doctorModalBtnText');
    const statusInput = document.getElementById('modalStatusInput');
    
    if (isAlreadyCompleted) {
        document.getElementById('doctorModalTitle').textContent = 'ملاحظات الطبيب (حالة مكتملة)';
        warningEl.classList.add('hidden');
        submitBtnText.textContent = 'تحديث الملاحظات الطبية';
        statusInput.value = '1';
    } else {
        document.getElementById('doctorModalTitle').textContent = 'تدوين ملاحظات الطبيب وإتمام الكشف';
        warningEl.classList.remove('hidden');
        submitBtnText.textContent = 'حفظ الملاحظات وتحويل إلى (تم) ✅';
        statusInput.value = '1';
    }

    document.getElementById('doctorNotesModal').classList.remove('hidden');
}

function closeDoctorNotesModal() {
    document.getElementById('doctorNotesModal').classList.add('hidden');
}

// Modal 2: Patient History
function openPatientHistory(patientName, patientPhone) {
    document.getElementById('historyPatientName').textContent = patientName + (patientPhone ? ' (' + patientPhone + ')' : '');
    document.getElementById('historyLoading').classList.remove('hidden');
    document.getElementById('historyList').classList.add('hidden');
    document.getElementById('historyEmpty').classList.add('hidden');
    document.getElementById('patientHistoryModal').classList.remove('hidden');

    const url = @json(url('patient_history.php')) + '?name=' + encodeURIComponent(patientName) + '&phone=' + encodeURIComponent(patientPhone || '');
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            document.getElementById('historyLoading').classList.add('hidden');
            const listEl = document.getElementById('historyList');
            listEl.innerHTML = '';

            if (data.history && data.history.length > 0) {
                data.history.forEach((visit, idx) => {
                    const isComp = visit.status == 1;
                    const itemHtml = `
                        <div class="p-4 rounded-2xl border ${isComp ? 'bg-emerald-50/40 border-emerald-200' : 'bg-slate-50 border-slate-200'} space-y-2.5 transition-all">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full ${isComp ? 'bg-emerald-600' : 'bg-amber-500'} text-white flex items-center justify-center text-xs font-bold">
                                        ${idx + 1}
                                    </span>
                                    <span class="text-xs font-extrabold text-slate-800">
                                        🗓️ ${visit.date || 'تاريخ غير محدد'}
                                    </span>
                                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-md ${visit.type === 'كشف' ? 'bg-teal-100 text-teal-800' : 'bg-indigo-100 text-indigo-800'}">
                                        ${visit.type || 'كشف'}
                                    </span>
                                </div>
                                <span class="text-xs font-bold ${isComp ? 'text-emerald-700' : 'text-amber-700'}">
                                    ${isComp ? '✅ تم الكشف' : '⏳ قيد الانتظار'}
                                </span>
                            </div>

                            <div class="p-3 rounded-xl bg-white border border-slate-200/80 shadow-xs">
                                <p class="text-[11px] font-bold text-slate-400 mb-1 flex items-center gap-1">
                                    <i class="bi bi-file-earmark-medical text-clinic-600"></i> ملاحظات وتشخيص الطبيب:
                                </p>
                                <p class="text-xs text-slate-800 whitespace-pre-wrap leading-relaxed">
                                    ${visit.comment ? visit.comment : '<span class="text-slate-400 italic">لا توجد ملاحظات مدونة في هذه الزيارة</span>'}
                                </p>
                            </div>

                            <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1">
                                <span>الرسوم: <strong>${visit.total} ج.م</strong></span>
                                <span>رقم الحجز: #${visit.id}</span>
                            </div>
                        </div>
                    `;
                    listEl.insertAdjacentHTML('beforeend', itemHtml);
                });
                listEl.classList.remove('hidden');
            } else {
                document.getElementById('historyEmpty').classList.remove('hidden');
            }
        })
        .catch(err => {
            document.getElementById('historyLoading').classList.add('hidden');
            document.getElementById('historyEmpty').classList.remove('hidden');
            console.error(err);
        });
}

function closePatientHistoryModal() {
    document.getElementById('patientHistoryModal').classList.add('hidden');
}

// Table Sorting
document.addEventListener('DOMContentLoaded', function() {
    initTableSorting('kashfTable');
    initTableSorting('estesharaTable');
    initTableSorting('otherTable');
});

function initTableSorting(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;
    const headers = table.querySelectorAll('th.sortable');
    const tbody = table.querySelector('tbody');
    headers.forEach(header => {
        header.addEventListener('click', () => {
            const columnIndex = Array.from(header.parentElement.children).indexOf(header);
            const sortKey = header.getAttribute('data-sort');
            const currentIsAscending = header.classList.contains('asc');
            headers.forEach(h => h.classList.remove('asc', 'desc'));
            header.classList.add(currentIsAscending ? 'desc' : 'asc');
            sortTable(tbody, columnIndex, !currentIsAscending, sortKey);
        });
    });
}

function sortTable(tbody, columnIndex, ascending, sortKey) {
    const rows = Array.from(tbody.querySelectorAll('tr'));
    rows.sort((a, b) => {
        let aCell = a.children[columnIndex];
        let bCell = b.children[columnIndex];
        if (!aCell || !bCell) return 0;

        let aValue, bValue;
        if (sortKey === 'total' || sortKey === 'paid' || sortKey === 'over_paid') {
            aValue = parseFloat(aCell.getAttribute('data-sort-value') || 0);
            bValue = parseFloat(bCell.getAttribute('data-sort-value') || 0);
        } else {
            aValue = (aCell.getAttribute('data-sort-value') || aCell.textContent).trim();
            bValue = (bCell.getAttribute('data-sort-value') || bCell.textContent).trim();
            if (!isNaN(aValue) && !isNaN(bValue)) {
                aValue = parseFloat(aValue);
                bValue = parseFloat(bValue);
            }
        }
        if (aValue < bValue) return ascending ? -1 : 1;
        if (aValue > bValue) return ascending ? 1 : -1;
        return 0;
    });
    rows.forEach(row => tbody.appendChild(row));
    updateSequenceNumbers(tbody);
}

function updateSequenceNumbers(tbody) {
    const rows = tbody.querySelectorAll('tr');
    const rowCount = rows.length;
    rows.forEach((row, index) => {
        const seqCell = row.querySelector('.sequence-column');
        if (seqCell) {
            seqCell.textContent = rowCount - index;
        }
    });
}
</script>
@endpush

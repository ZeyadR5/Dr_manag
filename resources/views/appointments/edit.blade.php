@extends('layouts.app')

@section('title', 'تعديل بيانات الحجز')
@section('page-title', 'تعديل بيانات الحجز')
@section('page-subtitle', 'تعديل بيانات المريض ' . $appointment->patient_Name . ' ومتابعة التكاليف والملاحظات')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb & Back button -->
    <div class="flex items-center justify-between mb-6">
        <a href="{{ url('reserv.php') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-clinic-600 transition-colors">
            <i class="bi bi-arrow-right"></i>
            <span>العودة لجدول المواعيد</span>
        </a>
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                رقم الحجز: #{{ $appointment->apid }}
            </span>
            <span class="text-xs font-bold px-3 py-1 rounded-full {{ $appointment->statue == 1 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                {{ $appointment->statue == 1 ? 'مكتمل' : 'قيد الانتظار' }}
            </span>
        </div>
    </div>

    <!-- Main Card -->
    <div class="glass-card overflow-hidden border border-slate-200 shadow-md">
        <!-- Card Header -->
        <div class="p-6 bg-gradient-to-r from-clinic-700 via-clinic-800 to-indigo-800 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white text-2xl shadow-inner border border-white/20">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-lg text-white">تعديل حجز: {{ $appointment->patient_Name }}</h3>
                    <p class="text-xs text-clinic-200">تحديث تفاصيل المريض أو الرسوم أو تاريخ الموعد</p>
                </div>
            </div>
            <div class="hidden sm:block text-right">
                <span class="text-xs font-bold text-clinic-300">عيادة د. أحمد نفادي</span>
            </div>
        </div>

        <!-- Form Body -->
        <form method="POST" action="{{ url('edit_appointment.php') }}?id={{ $appointment->apid }}" class="p-6 sm:p-8 space-y-8">
            @csrf
            <input type="hidden" name="ID" value="{{ $appointment->apid }}">

            <!-- Section 1: Patient Basic Info -->
            <div>
                <div class="flex items-center gap-2 pb-3 mb-5 border-b border-slate-100 text-slate-800">
                    <i class="bi bi-person-vcard text-clinic-600 text-lg"></i>
                    <h4 class="font-extrabold text-sm sm:text-base">1. البيانات الشخصية للمريض</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">
                            اسم المريض بالكامل <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400">
                                <i class="bi bi-person"></i>
                            </span>
                            <input required type="text" name="name" value="{{ $appointment->patient_Name }}" class="w-full pr-10 pl-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-clinic-500 focus:border-clinic-500 transition-all outline-none font-bold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">
                            رقم الهاتف / الموبايل <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <input required type="tel" name="phnumber" value="{{ $appointment->patient_Num }}" class="w-full pr-10 pl-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-clinic-500 focus:border-clinic-500 transition-all outline-none font-mono font-bold">
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">
                            العنوان / محل الإقامة
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <input type="text" name="adrs" value="{{ $appointment->adrs }}" class="w-full pr-10 pl-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-clinic-500 focus:border-clinic-500 transition-all outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Appointment Details -->
            <div>
                <div class="flex items-center gap-2 pb-3 mb-5 border-b border-slate-100 text-slate-800">
                    <i class="bi bi-calendar2-range text-clinic-600 text-lg"></i>
                    <h4 class="font-extrabold text-sm sm:text-base">2. تفاصيل وتاريخ الحجز</h4>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">
                            نوع الحجز <span class="text-rose-500">*</span>
                        </label>
                        <select name="typ" id="typSelect" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-clinic-500 focus:border-clinic-500 transition-all outline-none font-bold text-slate-700">
                            <option value="كشف" @selected($appointment->typ == 'كشف')>🩺 كشف طبي جديد</option>
                            <option value="استشاره" @selected(in_array($appointment->typ, ['استشاره', 'استشارة']))>🔄 استشارة / متابعة</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">
                            تاريخ الحجز <span class="text-rose-500">*</span>
                        </label>
                        <input required type="date" name="Date" value="{{ $appointment->appointmentDate }}" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-clinic-500 focus:border-clinic-500 transition-all outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">
                            وقت الحجز
                        </label>
                        <input type="time" name="Time" value="{{ $appointment->appointmentTime ?: '12:00' }}" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-clinic-500 focus:border-clinic-500 transition-all outline-none">
                    </div>
                </div>
            </div>

            <!-- Section 3: Financials & Fees -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200 text-slate-800">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-cash-coin text-amber-600 text-lg"></i>
                        <h4 class="font-extrabold text-sm sm:text-base">3. الرسوم والحسابات المالية</h4>
                    </div>
                    <span class="text-xs text-slate-500 font-semibold">بالجنيه المصري (EGP)</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">
                            سعر الكشف / الاستشارة
                        </label>
                        <div class="relative">
                            <input type="number" id="payedInput" name="Payed" value="{{ $appointment->paid }}" placeholder="0" min="0" step="1" class="w-full pr-3.5 pl-12 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-clinic-500 focus:border-clinic-500 transition-all outline-none font-bold" oninput="calcTotal()">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs font-bold text-slate-400">ج.م</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">
                            سعر الجبس / خدمة إضافية
                        </label>
                        <div class="relative">
                            <input type="number" id="overPriceInput" name="over_price_g" value="{{ $appointment->over_paid }}" placeholder="0" min="0" step="1" class="w-full pr-3.5 pl-12 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-clinic-500 focus:border-clinic-500 transition-all outline-none font-bold" oninput="calcTotal()">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs font-bold text-slate-400">ج.م</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">
                            إجمالي الحساب المحسوب
                        </label>
                        <div class="px-4 py-2.5 bg-clinic-100/70 border border-clinic-200 rounded-xl flex items-center justify-between">
                            <span class="text-xs font-bold text-clinic-800">الإجمالي:</span>
                            <span id="calculatedTotalDisplay" class="text-base font-black text-clinic-900">{{ number_format($appointment->rowTotal(), 2) }} ج.م</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Notes / Comments -->
            <div>
                <label class="block text-xs font-extrabold text-slate-700 mb-1.5">
                    ملاحظات أو تعليق إضافي عن الحالة
                </label>
                <textarea name="about" rows="3" placeholder="ملاحظات الحالة..." class="w-full p-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-clinic-500 focus:border-clinic-500 transition-all outline-none">{{ $appointment->commnet }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-5 border-t border-slate-100 flex flex-wrap items-center justify-end gap-3">
                <a href="{{ url('reserv.php') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition-all">
                    إلغاء وعودة
                </a>
                <button type="submit" name="update" class="inline-flex items-center gap-2 px-7 py-2.5 rounded-xl bg-gradient-to-r from-clinic-600 to-indigo-600 hover:from-clinic-700 hover:to-indigo-700 text-white font-extrabold text-sm shadow-md shadow-clinic-500/20 transition-all hover:scale-[1.01] active:scale-[0.99]">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>حفظ وتحديث التعديلات</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function calcTotal() {
    const payed = parseFloat(document.getElementById('payedInput').value) || 0;
    const over = parseFloat(document.getElementById('overPriceInput').value) || 0;
    const total = payed + over;
    document.getElementById('calculatedTotalDisplay').textContent = total.toFixed(2) + ' ج.م';
}
</script>
@endpush

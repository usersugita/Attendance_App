 @extends(Auth::user()->role === 'admin' ? 'layouts.admin-app' : 'layouts.app')

 @section('css')
 <link rel="stylesheet" href="{{ asset('css/attendancedetail.css') }}">
 @endsection

 @section('content')

 <body>
     <main>
         <div class="title">
             <h2>勤怠詳細</h2>
         </div>
         <div class="content">
             @if ($attendanceRequest)
             <table class="content__table">
                 <tr>
                     <th>名前</th>
                     <td colspan="4" class="content__table-name">{{ $attendance->user->name }}</td>
                 </tr>
                 <tr>
                     <th>日付</th>
                     <td>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}</td>
                     <td></td>
                     <td>{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}</td>
                     <td></td>
                 </tr>
                 <tr>
                     <th>出勤・退勤</th>
                     <td>{{ $attendanceRequest->new_clock_in ?? '-' }}</td>
                     <td>～</td>
                     <td>{{ $attendanceRequest->new_clock_out ?? '-' }}</td>
                     <td></td>
                 </tr>
                 <tr>
                     @foreach ($breakRequests as $i => $br)
                 <tr>
                     <th>休憩{{ $i + 1 }}</th>
                     <td>
                         {{ $br->new_break_start ?? '-' }}
                     </td>
                     <td>～</td>
                     <td>
                         {{ $br->new_break_end ?? '-' }}
                     </td>
                     <td></td>
                 </tr>
                 @endforeach
                 </tr>
                 <tr>
                     <th>備考</th>
                     <td>
                         {{ $attendanceRequest->note ?? '-' }}
                     </td>
                 </tr>

             </table>
             <div style="text-align: right; margin-top: 20px; color:red;">
                 *承認待ちのため修正できません。
             </div>
             @else
             <form action="/attendance-request" method="post">
                 @csrf
                 <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
                 <table class="content__table">
                     <tr>
                         <th>名前</th>
                         <td colspan="4" class="content__table-name">{{ $attendance->user->name }}</td>
                     </tr>

                     <tr>
                         <th>日付</th>
                         <td>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}</td>
                         <td></td>
                         <td>{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}</td>
                         <td></td>
                     </tr>

                     <!-- 出勤・退勤 -->
                     <tr>
                         <th>出勤・退勤</th>
                         <td><input type="time" name="new_clock_in" value="{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}"></td>
                         <td>～</td>
                         <td> <input type="time" name="new_clock_out" value="{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}"></td>
                         <td></td>
                     </tr>

                     <!-- 休憩 -->
                     <tr>
                         @foreach ($attendance->breaks as $i => $break)
                     <tr>
                         <th>休憩{{ $i + 1 }}</th>
                         <td>
                             <input type="time" name="breaks[{{ $i }}][new_break_start]"
                                 value="{{ $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '' }}">
                             @error('breaks.*.new_break_start')
                             <div style="color: red;">{{ $message }}</div>
                             @enderror
                         </td>
                         <td>～</td>
                         <td>
                             <input type="time" name="breaks[{{ $i }}][new_break_end]"
                                 value="{{ $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '' }}">
                             @error('breaks.*.new_break_end')
                             <div style="color: red;">{{ $message }}</div>
                             @enderror
                         </td>
                         <td></td>
                     </tr>
                     @endforeach
                     </tr>

                     <!-- 備考 -->
                     <tr>
                         <th>備考</th>

                         <td colspan="4">
                             <textarea name="note" rows="4" style="width: 65% ;"></textarea>
                             @error('note')
                             <div style="color: red;">{{ $message }}</div>
                             @enderror
                         </td>

                     </tr>

                 </table>
                 <div class="content__button" style="text-align: right; margin-top: 20px;">
                     <button type="submit">修正</button>
                 </div>

             </form>



             @endif
         </div>

     </main>
 </body>
 @endsection
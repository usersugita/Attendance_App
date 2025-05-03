 @extends(Auth::user()->role === 'admin' ? 'layouts.admin-app' : 'layouts.app')
 @section('css')
 <link rel="stylesheet" href="{{ asset('css/stamp_correction_request.css') }}">
 @endsection

 @section('content')

 <body>
     <main>
         <div class="title">
             <h2>申請一覧</h2>
         </div>
         <div class="tab-links">
             <a href="{{ url('/stamp_correction_request/list?status=pending') }}"
                 class="{{ request('status') === 'pending' ? 'active-tab' : '' }}">
                 承認待ち
             </a>

             <a href="{{ url('/stamp_correction_request/list?status=approved') }}"
                 class="{{ request('status') === 'approved' ? 'active-tab' : '' }}">
                 承認済み
             </a>

         </div>
         <div class="content">

             <table class="content__table">

                 <tr>
                     <th>状態</th>
                     <th>名前</th>
                     <th>対象日時</th>
                     <th>申請理由</th>
                     <th>申請日時</th>
                     <th>詳細</th>
                 </tr>
                 @auth
                 @if (Auth::user()->role === 'admin')
                 @foreach ($requests as $request)
                 <tr>
                     <td>
                         @if ($request->status === 'pending')
                         <span>承認待ち</span>
                         @elseif ($request->status === 'approved')
                         <span>承認済</span>
                         @endif
                     </td>
                     <td>{{ $request->user->name ?? '-' }}</td>
                     <td>{{ \Carbon\Carbon::parse($request->attendance->date ?? null)->format('Y/m/d') }}</td>
                     <td class="note-column">{{ $request->note ?? '-' }}</td>
                     <td>{{ $request->created_at->format('Y/m/d H:i') }}</td>
                     <td>
                         <a class="detail-link" href="/stamp_correction_request/approve/{{ $request->id }}">詳細</a>
                     </td>
                 </tr>
                 @endforeach
                 @else
                 @foreach ($requests as $request)
                 <tr>
                     <td>
                         @if ($request->status === 'pending')
                         <span>承認待ち</span>
                         @elseif ($request->status === 'approved')
                         <span>承認済</span>
                         @endif
                     </td>
                     <td>{{ $request->user->name ?? '-' }}</td>
                     <td>{{ \Carbon\Carbon::parse($request->attendance->date ?? null)->format('Y/m/d') }}</td>
                     <td class="note-column">{{ $request->note ?? '-' }}</td>
                     <td>{{ $request->created_at->format('Y/m/d H:i') }}</td>
                     <td>

                         <a class="detail-link" href="/attendance/{{ $request->attendance_id }}">詳細</a>
                     </td>
                 </tr>
                 @endforeach
                 @endif
                 @endauth
             </table>
         </div>
     </main>
 </body>
 @endsection
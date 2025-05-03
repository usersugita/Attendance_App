<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ユーザーとの関連付け
            $table->date('date'); // 勤務日
            $table->time('clock_in')->nullable(); // 出勤時間
            $table->time('clock_out')->nullable(); // 退勤時間
            $table->time('total_work_time')->nullable(); // 勤怠合計時間（後で自動計算）
            $table->timestamps(); // 作成日時 & 更新日時
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}

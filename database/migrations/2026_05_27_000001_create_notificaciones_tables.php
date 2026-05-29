<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla de multas
        Schema::create('multas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->string('descripcion');
            $table->decimal('monto', 10, 2);
            $table->enum('estado', ['pendiente', 'pagada', 'cancelada'])->default('pendiente');
            $table->text('detalles')->nullable();
            $table->timestamp('fecha_vencimiento')->nullable();
            $table->timestamps();
        });

        // Tabla de asambleas
        Schema::create('asambleas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion');
            $table->dateTime('fecha');
            $table->string('lugar');
            $table->text('agenda')->nullable();
            $table->enum('estado', ['programada', 'en_curso', 'finalizada'])->default('programada');
            $table->timestamps();
        });

        // Tabla de pagos atrasados
        Schema::create('pagos_atrasados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->string('concepto');
            $table->decimal('monto', 10, 2);
            $table->date('fecha_vencimiento');
            $table->integer('dias_atraso');
            $table->text('detalles')->nullable();
            $table->timestamps();
        });

        // Tabla de registro de notificaciones
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->enum('tipo', ['mensaje', 'multa', 'asamblea', 'pago_atrasado']);
            $table->unsignedBigInteger('referencia_id');
            $table->string('titulo');
            $table->text('descripcion');
            $table->boolean('leida')->default(false);
            $table->timestamp('fecha_lectura')->nullable();
            $table->timestamps();
            
            $table->index(['usuario_id', 'leida', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
        Schema::dropIfExists('pagos_atrasados');
        Schema::dropIfExists('asambleas');
        Schema::dropIfExists('multas');
    }
};

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Usuario;
use App\Models\Multa;
use App\Models\Asamblea;
use App\Models\PagoAtrasado;
use App\Models\Notificacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuarios de prueba
        $usuario1 = Usuario::create([
            'nombre' => 'Juan García',
            'correo' => 'juan@gmail.com',
            'password' => '123',
            'rol' => 'usuario'
        ]);

        $usuario2 = Usuario::create([
            'nombre' => 'María López',
            'correo' => 'maria@gmail.com',
            'password' => '123',
            'rol' => 'usuario'
        ]);

        $admin = Usuario::create([
            'nombre' => 'Administrador',
            'correo' => 'admin@gmail.com',
            'password' => '123',
            'rol' => 'admin'
        ]);

        // Crear multas
        $multa1 = Multa::create([
            'usuario_id' => $usuario1->id,
            'descripcion' => 'Ruido excesivo después de las 22:00',
            'monto' => 50.00,
            'estado' => 'pendiente',
            'detalles' => 'Violación del reglamento de convivencia',
            'fecha_vencimiento' => Carbon::now()->addDays(10)
        ]);

        $multa2 = Multa::create([
            'usuario_id' => $usuario2->id,
            'descripcion' => 'Invasión de zona común',
            'monto' => 75.00,
            'estado' => 'pendiente',
            'detalles' => 'Parqueo en zona no permitida',
            'fecha_vencimiento' => Carbon::now()->addDays(15)
        ]);

        // Crear asambleas
        $asamblea1 = Asamblea::create([
            'titulo' => 'Asamblea General Extraordinaria',
            'descripcion' => 'Se tratarán temas importantes del condominio',
            'fecha' => Carbon::now()->addDays(7)->setHour(18)->setMinute(0),
            'lugar' => 'Salón de eventos',
            'agenda' => 'Revisión presupuesto 2026, Reforma reglamento',
            'estado' => 'programada'
        ]);

        $asamblea2 = Asamblea::create([
            'titulo' => 'Asamblea Ordinaria Mensual',
            'descripcion' => 'Discusión de asuntos mensuales',
            'fecha' => Carbon::now()->addDays(14)->setHour(19)->setMinute(0),
            'lugar' => 'Salón de eventos',
            'agenda' => 'Informes de administración',
            'estado' => 'programada'
        ]);

        // Crear pagos atrasados
        $pago1 = PagoAtrasado::create([
            'usuario_id' => $usuario1->id,
            'concepto' => 'Cuota de mantenimiento Abril',
            'monto' => 250.00,
            'fecha_vencimiento' => Carbon::now()->subDays(15),
            'dias_atraso' => 15,
            'detalles' => 'Pago vencido desde el 15 de abril'
        ]);

        $pago2 = PagoAtrasado::create([
            'usuario_id' => $usuario2->id,
            'concepto' => 'Cuota de mantenimiento Mayo',
            'monto' => 250.00,
            'fecha_vencimiento' => Carbon::now()->subDays(5),
            'dias_atraso' => 5,
            'detalles' => 'Pago vencido desde el 22 de mayo'
        ]);

        // Crear notificaciones
        Notificacion::create([
            'usuario_id' => $usuario1->id,
            'tipo' => 'multa',
            'referencia_id' => $multa1->id,
            'titulo' => 'Nueva Multa Registrada',
            'descripcion' => 'Ha recibido una multa de $50.00',
            'leida' => false
        ]);

        Notificacion::create([
            'usuario_id' => $usuario2->id,
            'tipo' => 'multa',
            'referencia_id' => $multa2->id,
            'titulo' => 'Nueva Multa Registrada',
            'descripcion' => 'Ha recibido una multa de $75.00',
            'leida' => false
        ]);

        Notificacion::create([
            'usuario_id' => $usuario1->id,
            'tipo' => 'asamblea',
            'referencia_id' => $asamblea1->id,
            'titulo' => 'Nueva Asamblea Programada',
            'descripcion' => 'Asamblea General Extraordinaria - ' . $asamblea1->fecha->format('d/m/Y H:i'),
            'leida' => false
        ]);

        Notificacion::create([
            'usuario_id' => $usuario2->id,
            'tipo' => 'asamblea',
            'referencia_id' => $asamblea1->id,
            'titulo' => 'Nueva Asamblea Programada',
            'descripcion' => 'Asamblea General Extraordinaria - ' . $asamblea1->fecha->format('d/m/Y H:i'),
            'leida' => false
        ]);

        Notificacion::create([
            'usuario_id' => $usuario1->id,
            'tipo' => 'pago_atrasado',
            'referencia_id' => $pago1->id,
            'titulo' => 'Pago Atrasado',
            'descripcion' => 'Cuota de mantenimiento Abril - 15 días de atraso',
            'leida' => false
        ]);

        Notificacion::create([
            'usuario_id' => $usuario2->id,
            'tipo' => 'pago_atrasado',
            'referencia_id' => $pago2->id,
            'titulo' => 'Pago Atrasado',
            'descripcion' => 'Cuota de mantenimiento Mayo - 5 días de atraso',
            'leida' => false
        ]);
    }
}

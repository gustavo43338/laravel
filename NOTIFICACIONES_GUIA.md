# Sistema de Notificaciones Asincrónicas - Guía de Implementación

## ✅ Pasos Completados

Se ha implementado un sistema completo de **notificaciones asincrónicas en tiempo real** usando WebSocket (Laravel Reverb) con las siguientes características:

### 1. **Base de Datos** 
- Tabla `multas` - para registrar sanciones por infracciones
- Tabla `asambleas` - para eventos de asambleas
- Tabla `pagos_atrasados` - para deudas vencidas
- Tabla `notificaciones` - registro central de todas las notificaciones

### 2. **Backend (Laravel)**
- **Modelos**: `Multa`, `Asamblea`, `PagoAtrasado`, `Notificacion`
- **Eventos**: `MultaNueva`, `AsambleyaNueva`, `PagoAtrasadoNuevo`, `NotificacionNueva`
- **Controladores**: `MultaController`, `AsambleaController`, `PagoAtrasadoController`, `NotificacionController`
- **Rutas API**: Todos los endpoints necesarios configurados

### 3. **Frontend (Vue 3)**
- Botón de campana con contador de notificaciones no leídas
- Panel desplegable de notificaciones
- Modal de detalle de cada notificación
- Escucha en tiempo real de WebSocket
- Soporte para múltiples tipos de notificaciones

### 4. **Autenticación**
- Login funcional
- Sistema de usuarios con 2 usuarios de prueba + 1 administrador
- Rol de usuario y administrador

---

## 🚀 Pasos para Ejecutar

### **Paso 1: Ejecutar Migraciones**
```bash
php artisan migrate
```

### **Paso 2: Crear Datos de Prueba**
```bash
php artisan db:seed
```

### **Paso 3: Iniciar Servidores**

#### Terminal 1 - Servidor Laravel:
```bash
php artisan serve
```

#### Terminal 2 - Servidor Reverb (WebSocket):
```bash
php artisan reverb:start
```

#### Terminal 3 - Desarrollo Frontend:
```bash
npm run dev
```

---

## 👤 Credenciales de Prueba

| Usuario | Email | Contraseña | Rol |
|---------|-------|-----------|-----|
| Juan García | juan@gmail.com | 123 | usuario |
| María López | maria@gmail.com | 123 | usuario |
| Administrador | admin@gmail.com | 123 | admin |

---

## 📱 Cómo Funciona

### **Para Usuarios Normales:**
1. ✅ Inician sesión
2. ✅ Ven el botón de campana 🔔 en la esquina superior
3. ✅ Reciben notificaciones en tiempo real cuando:
   - Se registra una nueva multa
   - Se programa una asamblea
   - Hay pagos atrasados
   - Llega un mensaje nuevo
4. ✅ Pueden hacer clic en notificaciones para ver detalles
5. ✅ Las notificaciones se marcan como leídas automáticamente

### **Para Administrador:**
Usa las rutas API para crear notificaciones:

#### Crear Multa:
```bash
POST /api/multas
{
  "usuario_id": 1,
  "descripcion": "Ruido excesivo",
  "monto": 50,
  "estado": "pendiente",
  "fecha_vencimiento": "2026-06-01"
}
```

#### Crear Asamblea (notifica a todos):
```bash
POST /api/asambleas
{
  "titulo": "Asamblea Extraordinaria",
  "descripcion": "Temas importantes",
  "fecha": "2026-06-15 18:00:00",
  "lugar": "Salón de eventos",
  "agenda": "Reforma reglamento"
}
```

#### Crear Pago Atrasado:
```bash
POST /api/pagos-atrasados
{
  "usuario_id": 1,
  "concepto": "Cuota Junio",
  "monto": 250,
  "fecha_vencimiento": "2026-06-01",
  "dias_atraso": 5
}
```

---

## 🔌 Canales WebSocket

### **Canales Privados (por usuario):**
- `usuario.{usuarioId}` - Notificaciones personales (multas, pagos atrasados, mensajes)

### **Canales Públicos:**
- `chat-channel` - Chat en vivo
- `asambleas` - Notificaciones de asambleas

---

## 📡 Tipos de Notificaciones

| Tipo | Icono | Color | Caso de Uso |
|------|-------|-------|-----------|
| Mensaje | 💬 | Azul | Nuevos mensajes en chat |
| Multa | 💰 | Naranja | Infracciones registradas |
| Asamblea | 📅 | Verde | Eventos programados |
| Pago Atrasado | ⚠️ | Rojo | Deudas vencidas |

---

## ⚙️ Configuración

### Base de Datos (PostgreSQL)
- Host: 127.0.0.1
- Puerto: 5432
- Base de datos: laravel
- Usuario: postgres

### Laravel Reverb
- Host: 127.0.0.1
- Puerto: 8080
- URL: http://127.0.0.1:8000

---

## 📚 Rutas API Disponibles

```
# Notificaciones
GET    /api/notificaciones/              - Listar todas
GET    /api/notificaciones/no-leidas     - Notificaciones sin leer
GET    /api/notificaciones/{id}          - Detalle
PUT    /api/notificaciones/{id}/leida    - Marcar como leída
PUT    /api/notificaciones/marcar-todas-leidas

# Multas
POST   /api/multas                       - Crear
GET    /api/multas/{usuarioId}           - Listar por usuario
GET    /api/multas/{id}                  - Detalle
PUT    /api/multas/{id}                  - Actualizar

# Asambleas
POST   /api/asambleas                    - Crear (notifica a todos)
GET    /api/asambleas                    - Listar todas
GET    /api/asambleas/{id}               - Detalle
PUT    /api/asambleas/{id}               - Actualizar

# Pagos Atrasados
POST   /api/pagos-atrasados              - Crear
GET    /api/pagos-atrasados/{usuarioId}  - Listar por usuario
GET    /api/pagos-atrasados/{id}         - Detalle
```

---

## 🎨 Características de UI

✅ Panel desplegable de notificaciones
✅ Contador de no leídas en la campana
✅ Modal con detalles de cada notificación
✅ Animaciones suaves
✅ Responsive design
✅ Tema oscuro para el header

---

## ❓ Preguntas Frecuentes

**¿Cómo funcionan las notificaciones privadas?**
- Cada usuario tiene su propio canal privado `usuario.{id}`
- Solo recibe notificaciones dirigidas a él (multas, pagos atrasados)

**¿Cómo se distribuyen las notificaciones de asambleas?**
- Se crean registros separados para cada usuario en la tabla `notificaciones`
- Todos reciben la notificación simultáneamente via WebSocket

**¿Qué pasa si me desconecto?**
- Las notificaciones se guardan en BD
- Al reconectarse, se cargan automáticamente desde el servidor

**¿Puedo marcar notificaciones como leídas?**
- Sí, se marcan automáticamente al hacer clic en el panel
- También hay endpoint para marcar una a una o todas de una vez

---

## 🔐 Seguridad

- Validación de datos en los controladores
- Autorización privada en canales WebSocket
- Passwords sin hashear en BD (en producción usar bcrypt)
- CORS configurado si es necesario

---

¡Sistema listo para usar! 🎉

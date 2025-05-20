package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.time.LocalDate;
import java.time.format.DateTimeFormatter;
import java.time.format.DateTimeParseException;
import java.util.List;
import java.util.Map;

@RestController
@RequestMapping(value = "/api/citas", produces = "application/json")
@CrossOrigin(origins = "*")
public class CitaController {
    private static final Logger logger = LoggerFactory.getLogger(CitaController.class);

    @Autowired
    private CitaService citaService;
    
    @GetMapping("/test")
    public ResponseEntity<String> test() {
        logger.info("Solicitud recibida en el endpoint de prueba");
        return ResponseEntity.ok("El controlador de citas está funcionando correctamente");
    }
    
    public CitaController() {
        logger.info("CitaController inicializado");
    }

    // Endpoint para agendar una cita
    @PostMapping("/agendar/{usuarioId}")
    public ResponseEntity<?> agendarCita(@RequestBody Cita cita, @PathVariable Integer usuarioId) {
        try {
            if (cita == null) {
                return ResponseEntity.badRequest().body("Los datos de la cita no pueden ser nulos");
            }
            
            // Validar campos obligatorios
            if (cita.getNombre() == null || cita.getNombre().trim().isEmpty()) {
                return ResponseEntity.badRequest().body("El nombre es obligatorio");
            }
            if (cita.getTelefono() == null || cita.getTelefono().trim().isEmpty()) {
                return ResponseEntity.badRequest().body("El teléfono es obligatorio");
            }
            if (cita.getEmail() == null || cita.getEmail().trim().isEmpty()) {
                return ResponseEntity.badRequest().body("El email es obligatorio");
            }
            if (cita.getTipo() == null || cita.getTipo().trim().isEmpty()) {
                return ResponseEntity.badRequest().body("El tipo de cita es obligatorio");
            }
            if (cita.getCiudad() == null || cita.getCiudad().trim().isEmpty()) {
                return ResponseEntity.badRequest().body("La ciudad es obligatoria");
            }
            if (cita.getCodigoPostal() == null || cita.getCodigoPostal().trim().isEmpty()) {
                return ResponseEntity.badRequest().body("El código postal es obligatorio");
            }
            if (cita.getNumeroCasa() == null || cita.getNumeroCasa().trim().isEmpty()) {
                return ResponseEntity.badRequest().body("El número de casa es obligatorio");
            }
            if (cita.getFecha() == null || cita.getFecha().trim().isEmpty()) {
                return ResponseEntity.badRequest().body("La fecha es obligatoria");
            }
            if (cita.getHora() == null || cita.getHora().trim().isEmpty()) {
                return ResponseEntity.badRequest().body("La hora es obligatoria");
            }

            Cita nuevaCita = citaService.agendarCita(cita, usuarioId);
            return ResponseEntity.ok(nuevaCita);
        } catch (RuntimeException e) {
            return ResponseEntity.badRequest().body(e.getMessage());
        } catch (Exception e) {
            return ResponseEntity.internalServerError().body("Error al agendar la cita: " + e.getMessage());
        }
    }

    // Endpoint para ver el historial de citas de un usuario (usado principalmente por empresas)
    @GetMapping("/historial/{usuarioId}")
    public ResponseEntity<?> obtenerHistorial(@PathVariable Integer usuarioId) {
        try {
            List<Cita> citas = citaService.obtenerCitasPorUsuario(usuarioId);
            return ResponseEntity.ok(citas);
        } catch (Exception e) {
            return ResponseEntity.internalServerError().body("Error al obtener el historial: " + e.getMessage());
        }
    }

    // Endpoint para obtener citas por email (usado principalmente por particulares)
    @GetMapping("/email/{email}")
    public ResponseEntity<?> obtenerCitasPorEmail(@PathVariable String email) {
        try {
            List<Cita> citas = citaService.obtenerCitasPorEmail(email);
            return ResponseEntity.ok(citas);
        } catch (Exception e) {
            return ResponseEntity.internalServerError().body("Error al obtener las citas: " + e.getMessage());
        }
    }

    // Endpoint para anular una cita
    @DeleteMapping("/anular/{citaId}")
    public ResponseEntity<?> anularCita(@PathVariable Integer citaId) {
        try {
            boolean anulada = citaService.anularCita(citaId);
            if (anulada) {
                return ResponseEntity.ok("Cita anulada correctamente");
            } else {
                return ResponseEntity.badRequest().body("No se encontró la cita");
            }
        } catch (Exception e) {
            return ResponseEntity.internalServerError().body("Error al anular la cita: " + e.getMessage());
        }
    }
    
    @PostMapping("/crear/{usuarioId}")
    public ResponseEntity<?> crearCita(@PathVariable int usuarioId, @RequestBody Cita cita) {
        try {
            logger.info("Recibiendo solicitud de creación de cita para usuario {}", usuarioId);
            logger.debug("Datos de la cita recibidos: {}", cita);

            if (cita == null) {
                return ResponseEntity.badRequest().body("Los datos de la cita no pueden ser nulos");
            }
            
            // Validar campos obligatorios
            if (cita.getNombre() == null || cita.getNombre().trim().isEmpty()) {
                return ResponseEntity.badRequest().body("El nombre es obligatorio");
            }
            if (cita.getTelefono() == null || cita.getTelefono().trim().isEmpty()) {
                return ResponseEntity.badRequest().body("El teléfono es obligatorio");
            }
            if (cita.getEmail() == null || cita.getEmail().trim().isEmpty()) {
                return ResponseEntity.badRequest().body("El email es obligatorio");
            }
            if (cita.getTipo() == null || cita.getTipo().trim().isEmpty()) {
                return ResponseEntity.badRequest().body("El tipo de cita es obligatorio");
            }
            if (cita.getCiudad() == null || cita.getCiudad().trim().isEmpty()) {
                return ResponseEntity.badRequest().body("La ciudad es obligatoria");
            }
            if (cita.getCodigoPostal() == null || cita.getCodigoPostal().trim().isEmpty()) {
                return ResponseEntity.badRequest().body("El código postal es obligatorio");
            }
            if (cita.getNumeroCasa() == null || cita.getNumeroCasa().trim().isEmpty()) {
                return ResponseEntity.badRequest().body("El número de casa es obligatorio");
            }

            // Validación específica de fecha y hora
            logger.debug("Validando fecha y hora. Fecha: {}, Hora: {}", cita.getFecha(), cita.getHora());
            
            if (cita.getFecha() == null || cita.getFecha().trim().isEmpty()) {
                logger.error("Fecha no proporcionada");
                return ResponseEntity.badRequest().body("La fecha es obligatoria");
            }
            if (cita.getHora() == null || cita.getHora().trim().isEmpty()) {
                logger.error("Hora no proporcionada");
                return ResponseEntity.badRequest().body("La hora es obligatoria");
            }

            try {
                Cita nuevaCita = citaService.crearCita(usuarioId, cita);
                logger.info("Cita creada exitosamente con ID: {}", nuevaCita.getId());
                return ResponseEntity.ok(nuevaCita);
            } catch (RuntimeException e) {
                logger.error("Error al crear la cita: {}", e.getMessage());
                return ResponseEntity.badRequest().body("Error al crear la cita: " + e.getMessage());
            }
        } catch (Exception e) {
            logger.error("Error inesperado al crear la cita: {}", e.getMessage(), e);
            return ResponseEntity.internalServerError().body("Error al crear la cita: " + e.getMessage());
        }
    }
    
    // Obtener citas por fecha
    @GetMapping(value = "/fecha/{fecha}", produces = "application/json")
    @ResponseBody
    public ResponseEntity<?> obtenerCitasPorFecha(@PathVariable String fecha) {
        logger.info("🔍 Solicitud recibida para obtenerCitasPorFecha con fecha: {}", fecha);
        
        // Validar formato de fecha
        try {
            LocalDate fechaConsulta = LocalDate.parse(fecha);
            logger.info("✅ Formato de fecha válido: {}", fecha);
        } catch (DateTimeParseException e) {
            String mensajeError = "❌ Formato de fecha inválido. Use el formato yyyy-MM-dd";
            logger.error(mensajeError);
            return ResponseEntity.badRequest().body(mensajeError);
        }
        
        try {
            logger.info("🔍 Buscando citas para la fecha: {}", fecha);
            List<Cita> citas = citaService.obtenerCitasPorFecha(fecha);
            logger.info("✅ Se encontraron {} citas para la fecha {}", citas.size(), fecha);
            return ResponseEntity.ok(citas);
        } catch (RuntimeException e) {
            logger.error("❌ Error al obtener citas por fecha: {}", e.getMessage());
            return ResponseEntity.badRequest().body(e.getMessage());
        } catch (Exception e) {
            String mensajeError = "❌ Error inesperado al obtener citas por fecha: " + e.getMessage();
            logger.error(mensajeError, e);
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                             .body(mensajeError);
        }
    }
    
    // Obtener citas por fecha y tipo
    @GetMapping("/fecha/{fecha}/tipo/{tipo}")
    public ResponseEntity<?> obtenerCitasPorFechaYTipo(
            @PathVariable String fecha, 
            @PathVariable String tipo) {
        try {
            logger.info("Obteniendo citas para la fecha: {} y tipo: {}", fecha, tipo);
            List<Cita> citas = citaService.obtenerCitasPorFechaYTipo(fecha, tipo);
            return ResponseEntity.ok(citas);
        } catch (RuntimeException e) {
            logger.error("Error al obtener citas por fecha y tipo: {}", e.getMessage());
            return ResponseEntity.badRequest().body(e.getMessage());
        } catch (Exception e) {
            logger.error("Error inesperado al obtener citas por fecha y tipo: {}", e.getMessage(), e);
            return ResponseEntity.internalServerError().body("Error al obtener las citas: " + e.getMessage());
        }
    }
    
    // Actualizar estado de una cita
    @PutMapping("/{citaId}/estado")
    public ResponseEntity<?> actualizarEstadoCita(
            @PathVariable Integer citaId, 
            @RequestBody Map<String, String> estadoMap) {
        try {
            logger.info("Actualizando estado de la cita ID: {} a {}", citaId, estadoMap);
            
            if (estadoMap == null || !estadoMap.containsKey("estado")) {
                return ResponseEntity.badRequest().body("El campo 'estado' es obligatorio");
            }
            
            String nuevoEstado = estadoMap.get("estado");
            Cita citaActualizada = citaService.actualizarEstadoCita(citaId, nuevoEstado);
            
            logger.info("Estado de la cita actualizado correctamente");
            return ResponseEntity.ok(citaActualizada);
            
        } catch (RuntimeException e) {
            logger.error("Error al actualizar el estado de la cita: {}", e.getMessage());
            return ResponseEntity.badRequest().body(e.getMessage());
        } catch (Exception e) {
            logger.error("Error inesperado al actualizar el estado de la cita: {}", e.getMessage(), e);
            return ResponseEntity.internalServerError().body("Error al actualizar el estado de la cita: " + e.getMessage());
        }
    }
}
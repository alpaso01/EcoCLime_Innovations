package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import java.time.LocalDateTime;
import java.time.LocalDate;
import java.time.LocalTime;
import java.time.format.DateTimeFormatter;
import java.util.List;
import java.util.Optional;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import java.time.format.DateTimeParseException;

@Service
public class CitaService {
    private static final Logger logger = LoggerFactory.getLogger(CitaService.class);

    @Autowired
    private CitaRepository citaRepository;

    @Autowired
    private UsuarioRepository usuarioRepository;

    // Agendar una nueva cita
    public Cita agendarCita(Cita cita, Integer usuarioId) {
        logger.info("Datos de la cita recibidos: {}", cita);
        logger.info("Código Postal: {}", cita.getCodigoPostal());
        logger.info("Número de Casa: {}", cita.getNumeroCasa());
        
        Optional<Usuario> usuarioOpt = usuarioRepository.findById(usuarioId);
        if (usuarioOpt.isPresent()) {
            Usuario usuario = usuarioOpt.get();
            cita.setUsuario(usuario);
            cita.setId(null); // Aseguramos que se cree una nueva cita
            
            // Convertir fecha y hora a LocalDateTime
            try {
                LocalDate fecha = LocalDate.parse(cita.getFecha());
                LocalTime hora = LocalTime.parse(cita.getHora());
                LocalDateTime fechaHora = LocalDateTime.of(fecha, hora);
                cita.setFechaHora(fechaHora);
            } catch (Exception e) {
                throw new RuntimeException("Error al procesar la fecha y hora: " + e.getMessage());
            }
            
            logger.info("Datos de la cita antes de guardar: {}", cita);
            Cita citaGuardada = citaRepository.save(cita);
            logger.info("Cita guardada: {}", citaGuardada);
            return citaGuardada;
        }
        throw new RuntimeException("Usuario no encontrado");
    }

    // Obtener historial de citas de un usuario
    public List<Cita> obtenerCitasPorUsuario(Integer usuarioId) {
        return citaRepository.findByUsuarioId(usuarioId);
    }

    // Obtener citas por email
    public List<Cita> obtenerCitasPorEmail(String email) {
        return citaRepository.findByEmail(email);
    }

    // Anular una cita
    public boolean anularCita(Integer citaId) {
        Optional<Cita> citaOpt = citaRepository.findById(citaId);
        if (citaOpt.isPresent()) {
            citaRepository.deleteById(citaId);
            return true;
        }
        return false;
    }
    
    public Cita crearCita(int usuarioId, Cita cita) {
        logger.info("Datos de la cita recibidos en crearCita: {}", cita);
        logger.info("Código Postal: {}", cita.getCodigoPostal());
        logger.info("Número de Casa: {}", cita.getNumeroCasa());
        
        Optional<Usuario> usuarioOpt = usuarioRepository.findById(usuarioId);
        if (usuarioOpt.isPresent()) {
            Usuario usuario = usuarioOpt.get();
            cita.setUsuario(usuario);
            cita.setId(null); // Aseguramos que se cree una nueva cita
            
            // Convertir fecha y hora a LocalDateTime
            try {
                LocalDate fecha = LocalDate.parse(cita.getFecha());
                LocalTime hora = LocalTime.parse(cita.getHora());
                LocalDateTime fechaHora = LocalDateTime.of(fecha, hora);
                cita.setFechaHora(fechaHora);
            } catch (Exception e) {
                throw new RuntimeException("Error al procesar la fecha y hora: " + e.getMessage());
            }
            
            // Establecer estado por defecto si no se especifica
            if (cita.getEstado() == null || cita.getEstado().trim().isEmpty()) {
                cita.setEstado("programada");
            }
            
            logger.info("Datos de la cita antes de guardar: {}", cita);
            Cita citaGuardada = citaRepository.save(cita);
            logger.info("Cita guardada: {}", citaGuardada);
            return citaGuardada;
        }
        throw new RuntimeException("Usuario no encontrado");
    }
    
    // Obtener citas por fecha
    public List<Cita> obtenerCitasPorFecha(String fechaStr) {
        logger.info("🔍 Buscando citas para la fecha: {}", fechaStr);
        
        if (fechaStr == null || fechaStr.trim().isEmpty()) {
            throw new IllegalArgumentException("La fecha no puede estar vacía");
        }
        
        try {
            LocalDate fecha = LocalDate.parse(fechaStr);
            logger.debug("Fecha parseada correctamente: {}", fecha);
            
            List<Cita> citas = citaRepository.findByFecha(fecha);
            logger.info("✅ Se encontraron {} citas para la fecha {}", citas.size(), fecha);
            
            return citas;
        } catch (DateTimeParseException e) {
            String mensajeError = String.format("Formato de fecha inválido: %s. Use el formato yyyy-MM-dd", fechaStr);
            logger.error("❌ {}", mensajeError, e);
            throw new IllegalArgumentException(mensajeError, e);
        } catch (Exception e) {
            String mensajeError = String.format("Error al buscar citas para la fecha %s: %s", fechaStr, e.getMessage());
            logger.error("❌ {}", mensajeError, e);
            throw new RuntimeException(mensajeError, e);
        }
    }
    
    // Obtener citas por fecha y tipo
    public List<Cita> obtenerCitasPorFechaYTipo(String fechaStr, String tipo) {
        try {
            LocalDate fecha = LocalDate.parse(fechaStr);
            return citaRepository.findByFechaAndTipo(fecha, tipo);
        } catch (Exception e) {
            throw new RuntimeException("Error al obtener citas por fecha y tipo: " + e.getMessage());
        }
    }
    
    // Actualizar estado de una cita
    public Cita actualizarEstadoCita(Integer citaId, String nuevoEstado) {
        // Validar que el estado sea uno de los permitidos
        List<String> estadosPermitidos = List.of("programada", "confirmada", "en_curso", "cancelada");
        if (!estadosPermitidos.contains(nuevoEstado.toLowerCase())) {
            throw new RuntimeException("Estado no válido. Los estados permitidos son: " + String.join(", ", estadosPermitidos));
        }
        
        // Buscar la cita
        Cita cita = citaRepository.findById(citaId)
                .orElseThrow(() -> new RuntimeException("No se encontró la cita con ID: " + citaId));
        
        // Actualizar el estado
        cita.setEstado(nuevoEstado);
        
        // Guardar los cambios
        return citaRepository.save(cita);
    }
}
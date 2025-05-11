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
            
            logger.info("Datos de la cita antes de guardar: {}", cita);
            Cita citaGuardada = citaRepository.save(cita);
            logger.info("Cita guardada: {}", citaGuardada);
            return citaGuardada;
        }
        throw new RuntimeException("Usuario no encontrado");
    }
}
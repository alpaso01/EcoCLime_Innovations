package com.example.demo;

import java.time.LocalDate;
import java.util.List;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

@Repository
public interface CitaRepository extends JpaRepository<Cita, Integer> {
    List<Cita> findByUsuarioId(Integer usuarioId); // Obtener todas las citas de un usuario
    List<Cita> findByEmail(String email); // Obtener citas por email
    
    @Query("SELECT c FROM Cita c WHERE DATE(c.fechaHora) = :fecha")
    List<Cita> findByFecha(@Param("fecha") LocalDate fecha);
    
    @Query("SELECT c FROM Cita c WHERE DATE(c.fechaHora) = :fecha AND c.tipo = :tipo")
    List<Cita> findByFechaAndTipo(@Param("fecha") LocalDate fecha, @Param("tipo") String tipo);
    
    @Query("SELECT c FROM Cita c WHERE c.estado = :estado")
    List<Cita> findByEstado(@Param("estado") String estado);
}
package com.example.demo;

import java.util.List;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface CitaRepository extends JpaRepository<Cita, Integer> {
    List<Cita> findByUsuarioId(Integer usuarioId); // Obtener todas las citas de un usuario
    List<Cita> findByEmail(String email); // Obtener citas por email
}
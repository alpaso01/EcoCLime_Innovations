package com.example.demo;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;
import java.util.Optional;

@Repository
public interface TrabajadorRepository extends JpaRepository<Trabajador, Integer> {
    boolean existsByEmail(String email);
    Optional<Trabajador> findByEmail(String email);
} 
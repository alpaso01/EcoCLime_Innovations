package com.example.demo;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.Optional;

@Repository
public interface UsuarioRepository extends JpaRepository<Usuario, Integer> {

    // Buscar usuarios por tipo
    java.util.List<Usuario> findByTipo(String tipo);


    // Buscar un usuario por email
	boolean existsByEmail(String email);
	
	Optional<Usuario> findByEmail(String email);

}
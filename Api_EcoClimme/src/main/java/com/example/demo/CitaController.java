package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/citas")
public class CitaController {

    @Autowired
    private CitaService citaService;

    // Endpoint para agendar una cita
    @PostMapping("/agendar/{usuarioId}")
    public Cita agendarCita(@RequestBody Cita cita, @PathVariable Long usuarioId) {
        return citaService.agendarCita(cita, usuarioId);
    }

    // Endpoint para ver el historial de citas de un usuario
    @GetMapping("/historial/{usuarioId}")
    public List<Cita> obtenerHistorial(@PathVariable Long usuarioId) {
        return citaService.obtenerCitasPorUsuario(usuarioId);
    }

    // Endpoint para anular una cita
    @DeleteMapping("/anular/{citaId}")
    public String anularCita(@PathVariable Long citaId) {
        boolean anulada = citaService.anularCita(citaId);
        return anulada ? "Cita anulada correctamente" : "Error: Cita no encontrada";
    }
}
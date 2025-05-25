package com.example.demo;

import jakarta.mail.MessagingException;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.Map;

@RestController
@RequestMapping("/api/auth")
public class RecuperacionController {
    @Autowired
    private CodigoRecuperacionService codigoRecuperacionService;
    @Autowired
    private EmailService emailService;
    @Autowired
    private UsuarioService usuarioService;

    @PostMapping("/enviar-codigo")
    public ResponseEntity<?> enviarCodigo(@RequestBody Map<String, String> body) {
        String email = body.get("email");
        System.out.println("[RecuperacionController] Email recibido: '" + email + "'");
        if (!usuarioService.existePorEmail(email)) {
            System.out.println("[RecuperacionController] NO existe el usuario: '" + email + "'");
            return ResponseEntity.status(HttpStatus.NOT_FOUND).body(Map.of("mensaje", "El correo no está registrado."));
        }
        String codigo = codigoRecuperacionService.generarYGuardarCodigo(email);
        try {
            emailService.enviarConfirmacionCita(email, "Código de recuperación", "Tu código es: <b>" + codigo + "</b>.<br>Este código expirará en 10 minutos.");
        } catch (MessagingException e) {
            System.out.println("[RecuperacionController] Error enviando correo a '" + email + "': " + e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(Map.of("mensaje", "Error enviando el correo."));
        }
        System.out.println("[RecuperacionController] Código enviado correctamente a: '" + email + "'");
        return ResponseEntity.ok(Map.of("mensaje", "Código enviado al correo."));
    }

    @PostMapping("/verificar-codigo")
    public ResponseEntity<?> verificarCodigo(@RequestBody Map<String, String> body) {
        String email = body.get("email");
        String codigo = body.get("codigo");
        if (codigoRecuperacionService.validarCodigo(email, codigo)) {
            return ResponseEntity.ok(Map.of("mensaje", "Código válido."));
        } else {
            return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(Map.of("mensaje", "Código inválido o expirado."));
        }
    }

    @PostMapping("/restablecer-contrasena")
    public ResponseEntity<?> restablecerContrasena(@RequestBody Map<String, String> body) {
        String email = body.get("email");
        String codigo = body.get("codigo");
        String nuevaContrasena = body.get("nuevaContrasena");
        if (codigoRecuperacionService.validarCodigo(email, codigo)) {
            usuarioService.cambiarPassword(email, nuevaContrasena);
            codigoRecuperacionService.eliminarCodigo(email);
            return ResponseEntity.ok(Map.of("mensaje", "Contraseña restablecida correctamente."));
        } else {
            return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(Map.of("mensaje", "Código inválido o expirado."));
        }
    }
}

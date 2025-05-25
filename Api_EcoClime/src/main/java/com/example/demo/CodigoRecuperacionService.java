package com.example.demo;

import org.springframework.stereotype.Service;
import java.util.Map;
import java.util.Random;
import java.util.concurrent.ConcurrentHashMap;

@Service
public class CodigoRecuperacionService {
    private static class CodigoInfo {
        String codigo;
        long expiracion;
        CodigoInfo(String codigo, long expiracion) {
            this.codigo = codigo;
            this.expiracion = expiracion;
        }
    }
    private final Map<String, CodigoInfo> codigos = new ConcurrentHashMap<>();

    public String generarYGuardarCodigo(String email) {
        String codigo = String.format("%04d", new Random().nextInt(10000));
        long expiracion = System.currentTimeMillis() + 10 * 60 * 1000; // 10 minutos
        codigos.put(email, new CodigoInfo(codigo, expiracion));
        return codigo;
    }
    public boolean validarCodigo(String email, String codigo) {
        CodigoInfo info = codigos.get(email);
        if (info == null) return false;
        if (System.currentTimeMillis() > info.expiracion) {
            codigos.remove(email);
            return false;
        }
        return info.codigo.equals(codigo);
    }
    public void eliminarCodigo(String email) {
        codigos.remove(email);
    }
}

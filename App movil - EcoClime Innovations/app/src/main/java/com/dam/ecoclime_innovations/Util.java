package com.dam.ecoclime_innovations;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;

public class Util {

    // Método para encriptar la contraseña con MD5
    public static String encriptarMD5(String input) {
        try {
            MessageDigest md = MessageDigest.getInstance("MD5");
            byte[] messageDigest = md.digest(input.getBytes());

            StringBuilder hexString = new StringBuilder();
            for (byte b : messageDigest) {
                hexString.append(String.format("%02x", b));
            }
            return hexString.toString();
        } catch (NoSuchAlgorithmException e) {
            e.printStackTrace();
            return null;
        }
    }
}

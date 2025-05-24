package com.dam.ecoclime_innovations;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import java.util.Properties;

import javax.mail.Authenticator;
import javax.mail.Message;
import javax.mail.MessagingException;
import javax.mail.PasswordAuthentication;
import javax.mail.Session;
import javax.mail.Transport;
import javax.mail.internet.InternetAddress;
import javax.mail.internet.MimeMessage;

/**
 * Clase utilitaria para enviar correos electrónicos desde la aplicación.
 */
public class EmailSender {
    private static final String TAG = "EmailSender";
    
    // Configuración del servidor SMTP (estos valores deberían estar en un archivo de configuración seguro)
    private static final String SMTP_HOST = "smtp.gmail.com";
    private static final String SMTP_PORT = "587";
    private static final String EMAIL_FROM = "EcoClime.Innovations@gmail.com"; // Reemplazar con el correo real de la empresa
    private static final String EMAIL_PASSWORD = "kolaggojaucwhlsn"; // Reemplazar con tu contraseña de aplicación de 16 caracteres
    private static final String EMAIL_SUBJECT_CITA = "Confirmación de Cita - EcoClime Innovations";

    /**
     * Envía un correo electrónico de confirmación de cita al usuario.
     *
     * @param context Contexto de la aplicación
     * @param cita Objeto Cita con la información de la cita agendada
     * @param callback Callback para notificar el resultado del envío
     */
    public static void enviarConfirmacionCita(Context context, Cita cita, EmailCallback callback) {
        new SendEmailTask(context, callback).execute(cita);
    }

    /**
     * Tarea asíncrona para enviar correos electrónicos en segundo plano.
     */
    private static class SendEmailTask extends AsyncTask<Cita, Void, Boolean> {
        private Context context;
        private EmailCallback callback;
        private Exception exception;

        public SendEmailTask(Context context, EmailCallback callback) {
            this.context = context;
            this.callback = callback;
        }

        @Override
        protected Boolean doInBackground(Cita... citas) {
            if (citas.length == 0) return false;
            
            Cita cita = citas[0];
            try {
                Properties props = new Properties();
                props.put("mail.smtp.host", SMTP_HOST);
                props.put("mail.smtp.socketFactory.port", SMTP_PORT);
                props.put("mail.smtp.auth", "true");
                props.put("mail.smtp.port", SMTP_PORT);
                props.put("mail.smtp.starttls.enable", "true");

                Session session = Session.getDefaultInstance(props, new Authenticator() {
                    @Override
                    protected PasswordAuthentication getPasswordAuthentication() {
                        return new PasswordAuthentication(EMAIL_FROM, EMAIL_PASSWORD);
                    }
                });

                MimeMessage message = new MimeMessage(session);
                message.setFrom(new InternetAddress(EMAIL_FROM));
                message.addRecipient(Message.RecipientType.TO, new InternetAddress(cita.getEmail()));
                message.setSubject(EMAIL_SUBJECT_CITA);
                
                // Crear el contenido del correo
                String emailContent = crearContenidoCorreo(cita);
                message.setContent(emailContent, "text/html; charset=utf-8");

                Transport.send(message);
                return true;
            } catch (MessagingException e) {
                Log.e(TAG, "Error al enviar correo: " + e.getMessage());
                exception = e;
                return false;
            }
        }

        @Override
        protected void onPostExecute(Boolean result) {
            if (result) {
                callback.onSuccess();
            } else {
                callback.onError(exception != null ? exception.getMessage() : "Error desconocido");
            }
        }
    }

    /**
     * Crea el contenido HTML del correo electrónico de confirmación.
     *
     * @param cita Objeto Cita con la información de la cita
     * @return Contenido HTML del correo
     */
    private static String crearContenidoCorreo(Cita cita) {
        String tipoCita = cita.getTipo().equals("Particular") ? "particular" : "para su empresa";
        
        StringBuilder html = new StringBuilder();
        html.append("<!DOCTYPE html>")
            .append("<html><head><meta charset='UTF-8'>")
            .append("<style>")
            .append("body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }")
            .append(".header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }")
            .append(".content { padding: 20px; }")
            .append(".footer { background-color: #f1f1f1; padding: 10px; text-align: center; font-size: 12px; }")
            .append("h1 { color: #2E7D32; }")
            .append("table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }")
            .append("th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }")
            .append("th { background-color: #f2f2f2; }")
            .append(".btn { display: inline-block; background-color: #4CAF50; color: white; padding: 10px 20px; ")
            .append("text-decoration: none; border-radius: 4px; margin-top: 20px; }")
            .append("</style></head>")
            .append("<body>")
            .append("<div class='header'>")
            .append("<h1>EcoClime Innovations</h1>")
            .append("</div>")
            .append("<div class='content'>")
            .append("<h2>¡Gracias por agendar su cita ").append(tipoCita).append("!</h2>")
            .append("<p>Estimado/a ").append(cita.getNombre()).append(" ").append(cita.getApellidos()).append(",</p>")
            .append("<p>Hemos recibido su solicitud de cita para la instalación de paneles solares. ")
            .append("A continuación, le mostramos los detalles de su cita:</p>")
            .append("<table>")
            .append("<tr><th>Fecha</th><td>").append(cita.getFecha()).append("</td></tr>")
            .append("<tr><th>Hora</th><td>").append(cita.getHora()).append("</td></tr>")
            .append("<tr><th>Tipo de Cita</th><td>").append(cita.getTipo()).append("</td></tr>")
            .append("<tr><th>Dirección</th><td>").append(cita.getCalle()).append(" ").append(cita.getNumeroCasa())
            .append(", ").append(cita.getCiudad()).append(", CP ").append(cita.getCodigoPostal()).append("</td></tr>");
        
        if (cita.getMensaje() != null && !cita.getMensaje().isEmpty()) {
            html.append("<tr><th>Mensaje</th><td>").append(cita.getMensaje()).append("</td></tr>");
        }
        
        html.append("</table>")
            .append("<p>Un técnico de EcoClime Innovations se presentará en la dirección indicada en la fecha y hora programadas.</p>")
            .append("<p>Si necesita modificar o cancelar su cita, puede hacerlo a través de la aplicación en la sección 'Historial de Citas'.</p>")
            .append("<p>Para cualquier consulta adicional, no dude en contactarnos:</p>")
            .append("<ul>")
            .append("<li>Teléfono: +34 91 123 45 67</li>")
            .append("<li>Email: soporte@ecoclime.es</li>")
            .append("</ul>")
            .append("<p>¡Gracias por confiar en EcoClime Innovations!</p>")
            .append("</div>")
            .append("<div class='footer'>")
            .append("<p>© 2025 EcoClime Innovations. Todos los derechos reservados.</p>")
            .append("<p>Este correo electrónico fue enviado automáticamente. Por favor, no responda a este mensaje.</p>")
            .append("</div>")
            .append("</body></html>");
        
        return html.toString();
    }

    /**
     * Interfaz de callback para notificar el resultado del envío de correo.
     */
    public interface EmailCallback {
        void onSuccess();
        void onError(String error);
    }
}

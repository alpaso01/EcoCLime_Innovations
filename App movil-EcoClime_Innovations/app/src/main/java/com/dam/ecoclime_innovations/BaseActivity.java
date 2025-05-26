package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;

import androidx.appcompat.app.AppCompatActivity;
import com.google.android.material.bottomnavigation.BottomNavigationView;

public abstract class BaseActivity extends AppCompatActivity {

    protected String userEmail;
    protected BottomNavigationView bottomNavigationView;
    
    // Vistas para la navegación personalizada
    protected LinearLayout bottomNavigationContainer;
    protected LinearLayout navHome;
    protected LinearLayout navWeb;
    protected LinearLayout navAccount;
    protected View indicatorHome;
    protected View indicatorWeb;
    protected View indicatorAccount;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        userEmail = getIntent().getStringExtra("userEmail");
    }

    protected void setupBottomNavigation() {
        // Soporte para el antiguo BottomNavigationView (por compatibilidad)
        if (bottomNavigationView != null) {
            bottomNavigationView.setSelectedItemId(getSelectedNavigationItemId());
            bottomNavigationView.setOnItemSelectedListener(item -> {
                int itemId = item.getItemId();
                if (itemId == R.id.navigation_home && !isCurrentActivity(pantalla_principal.class)) {
                    Intent intent = new Intent(this, pantalla_principal.class);
                    intent.putExtra("userEmail", userEmail);
                    startActivity(intent);
                    if (!isCurrentActivity(VistaWebActivity.class)) {
                        finish();
                    }
                    return true;
                } else if (itemId == R.id.navigation_web && !isCurrentActivity(VistaWebActivity.class)) {
                    Intent intent = new Intent(this, VistaWebActivity.class);
                    intent.putExtra("userEmail", userEmail);
                    startActivity(intent);
                    if (!isCurrentActivity(pantalla_principal.class)) {
                        finish();
                    }
                    return true;
                } else if (itemId == R.id.navigation_perfil && !isCurrentActivity(MiPerfilActivity.class)) {
                    Intent intent = new Intent(this, MiPerfilActivity.class);
                    intent.putExtra("userEmail", userEmail);
                    startActivity(intent);
                    if (!isCurrentActivity(pantalla_principal.class) && !isCurrentActivity(VistaWebActivity.class)) {
                        finish();
                    }
                    return true;
                }
                return false;
            });
        }
        
        // Soporte para el nuevo menú de navegación personalizado
        setupCustomNavigation();
    }

    protected abstract int getSelectedNavigationItemId();

    protected void pasarDatosUsuario(Intent intent) {
        intent.putExtra("userEmail", userEmail);
    }

    /**
     * Configura la navegación personalizada con LinearLayouts
     */
    protected void setupCustomNavigation() {
        // Inicializar vistas de navegación personalizada
        bottomNavigationContainer = findViewById(R.id.bottom_navigation_container);
        navHome = findViewById(R.id.nav_home);
        navWeb = findViewById(R.id.nav_web);
        navAccount = findViewById(R.id.nav_account);
        
        // Si no se encuentra el contenedor de navegación, salir
        if (bottomNavigationContainer == null) {
            return;
        }
        
        // Añadir los ImageView para los puntitos si no existen
        addDotsIndicators();
        
        // Configurar listeners
        if (navHome != null) {
            navHome.setOnClickListener(v -> {
                if (!isCurrentActivity(pantalla_principal.class)) {
                    Intent intent = new Intent(this, pantalla_principal.class);
                    pasarDatosUsuario(intent);
                    startActivity(intent);
                    finish();
                }
            });
        }
        
        if (navWeb != null) {
            navWeb.setOnClickListener(v -> {
                if (!isCurrentActivity(VistaWebActivity.class)) {
                    Intent intent = new Intent(this, VistaWebActivity.class);
                    pasarDatosUsuario(intent);
                    startActivity(intent);
                    finish();
                }
            });
        }
        
        if (navAccount != null) {
            navAccount.setOnClickListener(v -> {
                if (!isCurrentActivity(MiPerfilActivity.class)) {
                    Intent intent = new Intent(this, MiPerfilActivity.class);
                    pasarDatosUsuario(intent);
                    startActivity(intent);
                    finish();
                }
            });
        }
        
        // Mostrar los puntitos en el elemento activo
        highlightActiveNavItem();
    }
    
    /**
     * Añade las vistas para los indicadores de navegación
     */
    private void addDotsIndicators() {
        if (navHome == null || navWeb == null || navAccount == null) {
            return;
        }
        
        // Verificar si ya existen los indicadores
        indicatorHome = navHome.findViewById(R.id.indicator_home);
        if (indicatorHome == null) {
            indicatorHome = new View(this);
            indicatorHome.setId(R.id.indicator_home);
            indicatorHome.setBackgroundResource(R.drawable.nav_indicator);
            LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(
                    LinearLayout.LayoutParams.WRAP_CONTENT,
                    (int) getResources().getDimension(android.R.dimen.notification_large_icon_height) / 12);
            params.width = (int) (getResources().getDisplayMetrics().density * 48);
            params.topMargin = (int) (getResources().getDisplayMetrics().density * 4);
            params.bottomMargin = (int) (getResources().getDisplayMetrics().density * 4);
            indicatorHome.setLayoutParams(params);
            indicatorHome.setVisibility(View.INVISIBLE);
            navHome.addView(indicatorHome); // Añadir al final del LinearLayout
        }
        
        indicatorWeb = navWeb.findViewById(R.id.indicator_web);
        if (indicatorWeb == null) {
            indicatorWeb = new View(this);
            indicatorWeb.setId(R.id.indicator_web);
            indicatorWeb.setBackgroundResource(R.drawable.nav_indicator);
            LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(
                    LinearLayout.LayoutParams.WRAP_CONTENT,
                    (int) getResources().getDimension(android.R.dimen.notification_large_icon_height) / 12);
            params.width = (int) (getResources().getDisplayMetrics().density * 48);
            params.topMargin = (int) (getResources().getDisplayMetrics().density * 4);
            params.bottomMargin = (int) (getResources().getDisplayMetrics().density * 4);
            indicatorWeb.setLayoutParams(params);
            indicatorWeb.setVisibility(View.INVISIBLE);
            navWeb.addView(indicatorWeb);
        }
        
        indicatorAccount = navAccount.findViewById(R.id.indicator_account);
        if (indicatorAccount == null) {
            indicatorAccount = new View(this);
            indicatorAccount.setId(R.id.indicator_account);
            indicatorAccount.setBackgroundResource(R.drawable.nav_indicator);
            LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(
                    LinearLayout.LayoutParams.WRAP_CONTENT,
                    (int) getResources().getDimension(android.R.dimen.notification_large_icon_height) / 12);
            params.width = (int) (getResources().getDisplayMetrics().density * 48);
            params.topMargin = (int) (getResources().getDisplayMetrics().density * 4);
            params.bottomMargin = (int) (getResources().getDisplayMetrics().density * 4);
            indicatorAccount.setLayoutParams(params);
            indicatorAccount.setVisibility(View.INVISIBLE);
            navAccount.addView(indicatorAccount);
        }
    }
    
    /**
     * Muestra el indicador en el elemento de navegación activo
     */
    protected void highlightActiveNavItem() {
        // Si no tenemos los indicadores, salir
        if (indicatorHome == null || indicatorWeb == null || indicatorAccount == null) {
            return;
        }
        
        // Ocultar todos los indicadores primero
        indicatorHome.setVisibility(View.INVISIBLE);
        indicatorWeb.setVisibility(View.INVISIBLE);
        indicatorAccount.setVisibility(View.INVISIBLE);
        
        // Mostrar el indicador en el elemento activo
        if (isCurrentActivity(pantalla_principal.class)) {
            indicatorHome.setVisibility(View.VISIBLE);
        } else if (isCurrentActivity(VistaWebActivity.class)) {
            indicatorWeb.setVisibility(View.VISIBLE);
        } else if (isCurrentActivity(MiPerfilActivity.class)) {
            indicatorAccount.setVisibility(View.VISIBLE);
        }
    }
    
    private boolean isCurrentActivity(Class<?> activityClass) {
        return this.getClass().equals(activityClass);
    }
}
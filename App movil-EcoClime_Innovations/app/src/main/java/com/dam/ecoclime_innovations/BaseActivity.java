package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import androidx.appcompat.app.AppCompatActivity;
import com.google.android.material.bottomnavigation.BottomNavigationView;

public abstract class BaseActivity extends AppCompatActivity {

    protected String userEmail;
    protected BottomNavigationView bottomNavigationView;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        userEmail = getIntent().getStringExtra("userEmail");
    }

    protected void setupBottomNavigation() {
        bottomNavigationView = findViewById(R.id.bottom_navigation);
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
    }

    protected abstract int getSelectedNavigationItemId();

    protected void pasarDatosUsuario(Intent intent) {
        intent.putExtra("userEmail", userEmail);
    }

    private boolean isCurrentActivity(Class<?> activityClass) {
        return this.getClass().equals(activityClass);
    }
}
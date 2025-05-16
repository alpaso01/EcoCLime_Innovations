package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.view.MenuItem;
import android.view.View;
import android.webkit.WebView;
import android.webkit.WebViewClient;

import androidx.annotation.NonNull;

import com.google.android.material.bottomnavigation.BottomNavigationView;

public class VistaWebActivity extends BaseActivity {

    private WebView webView;
    private String userEmail;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_vista_web);

        userEmail = getIntent().getStringExtra("userEmail");

        setupWebView();
        setupBottomNavigation();
    }

    protected void setupBottomNavigation() {
        // Configurar los botones de navegación personalizados
        View navHome = findViewById(R.id.nav_home);
        View navWeb = findViewById(R.id.nav_web);
        View navAccount = findViewById(R.id.nav_account);
        
        // Botón Inicio
        navHome.setOnClickListener(v -> {
            Intent intent = new Intent(this, pantalla_principal.class);
            intent.putExtra("userEmail", userEmail);
            startActivity(intent);
            finish();
        });
        
        // Botón Web (ya estamos en la vista web)
        navWeb.setOnClickListener(v -> {
            // Ya estamos en la vista web, no hacemos nada
        });
        
        // Botón Cuenta
        navAccount.setOnClickListener(v -> {
            Intent intent = new Intent(this, MiPerfilActivity.class);
            intent.putExtra("userEmail", userEmail);
            startActivity(intent);
        });
    }

    private void setupWebView() {
        webView = findViewById(R.id.webView);
        webView.getSettings().setJavaScriptEnabled(true);
        webView.setWebViewClient(new WebViewClient());
        webView.loadUrl("https://www.ecoclime.com"); // Reemplaza con tu URL real
    }

    @Override
    protected int getSelectedNavigationItemId() {
        return R.id.navigation_web;
    }

    @Override
    public void onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack();
        } else {
            super.onBackPressed();
        }
    }
}
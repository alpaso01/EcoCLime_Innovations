package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.view.MenuItem;
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
        BottomNavigationView bottomNavigationView = findViewById(R.id.bottom_navigation);
        bottomNavigationView.setSelectedItemId(R.id.navigation_web);
        bottomNavigationView.setOnItemSelectedListener(item -> {
            int itemId = item.getItemId();
            if (itemId == R.id.navigation_home) {
                Intent intent = new Intent(this, pantalla_principal.class);
                intent.putExtra("userEmail", userEmail);
                startActivity(intent);
                finish();
                return true;
            } else if (itemId == R.id.navigation_web) {
                // Ya estamos en la vista web
                return true;
            } else if (itemId == R.id.navigation_perfil) {
                Intent intent = new Intent(this, MiPerfilActivity.class);
                intent.putExtra("userEmail", userEmail);
                startActivity(intent);
                return true;
            }
            return false;
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
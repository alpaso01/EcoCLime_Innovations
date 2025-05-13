package com.dam.ecoclime_innovations;

import android.os.Bundle;
import android.webkit.WebView;
import android.webkit.WebViewClient;

public class vista_web extends BaseActivity {

    private WebView webView;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_vista_web);

        // Inicializar WebView
        webView = findViewById(R.id.webView);
        webView.setWebViewClient(new WebViewClient());
        webView.getSettings().setJavaScriptEnabled(true);
        webView.loadUrl("http://ecoclimeinnovations.free.nf"); // Reemplaza con tu URL

        // Configurar navegación inferior
        setupBottomNavigation();
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
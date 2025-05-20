package com.dam.ecoclime_innovations;

import android.animation.ValueAnimator;
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.view.View;
import android.view.ViewTreeObserver;
import android.view.WindowManager;
import android.view.animation.AccelerateDecelerateInterpolator;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.TextView;

public class SplashCustomActivity extends Activity {
    private static final int SPLASH_DURATION = 2500; // 2.5 segundos
    private static final int ANIMATION_DURATION = 1200; // 1.2 segundos por vuelta
    private ValueAnimator orbitAnimator;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, 
                           WindowManager.LayoutParams.FLAG_FULLSCREEN);
        setContentView(R.layout.activity_splash_custom);

        // Initialize views
        final ImageView splashLogo = findViewById(R.id.splashLogo);
        final TextView splashText = findViewById(R.id.splashText);
        final TextView loadingText = findViewById(R.id.loadingText);
        final ImageView dotLoader = findViewById(R.id.dotLoader);
        final FrameLayout loaderContainer = findViewById(R.id.loaderContainer);

        // Set initial states
        splashLogo.setAlpha(0f);
        splashText.setAlpha(0f);
        splashText.setScaleX(0.85f);
        splashText.setScaleY(0.85f);
        loadingText.setAlpha(0f);
        dotLoader.setAlpha(0f);

        // Animate logo
        splashLogo.animate()
                .alpha(1f)
                .setDuration(800)
                .setStartDelay(200)
                .start();

        // Animate text with zoom and fade
        splashText.animate()
                .alpha(1f)
                .scaleX(1f)
                .scaleY(1f)
                .setDuration(1000)
                .setStartDelay(400)
                .start();

        // Animate loading text
        loadingText.animate()
                .alpha(1f)
                .setDuration(800)
                .setStartDelay(800)
                .start();

        // Setup dot loader animation after layout is complete
        loaderContainer.getViewTreeObserver().addOnGlobalLayoutListener(
                new ViewTreeObserver.OnGlobalLayoutListener() {
                    @Override
                    public void onGlobalLayout() {
                        // Remove the listener to avoid multiple calls
                        loaderContainer.getViewTreeObserver().removeOnGlobalLayoutListener(this);
                        
                        // Make dot visible
                        dotLoader.animate()
                                .alpha(1f)
                                .setDuration(600)
                                .setStartDelay(600)
                                .start();

                        // Get container dimensions
                        int containerWidth = loaderContainer.getWidth();
                        int containerHeight = loaderContainer.getHeight();
                        
                        // Calculate center points (accounting for padding)
                        int centerX = containerWidth / 2;
                        int centerY = containerHeight / 2;
                        
                        // Calculate radius (slightly smaller than half the container size)
                        int radius = (int) (Math.min(containerWidth, containerHeight) * 0.42f);
                        
                        // Create the rotation animation
                        orbitAnimator = ValueAnimator.ofFloat(0f, 360f);
                        orbitAnimator.setDuration(ANIMATION_DURATION);
                        orbitAnimator.setRepeatCount(ValueAnimator.INFINITE);
                        orbitAnimator.setInterpolator(new AccelerateDecelerateInterpolator());
                        
                        orbitAnimator.addUpdateListener(animation -> {
                            float angle = (float) animation.getAnimatedValue();
                            float radians = (float) Math.toRadians(angle);
                            
                            // Calculate position on the circle
                            float x = centerX + (radius * (float) Math.cos(radians)) - (dotLoader.getWidth() / 2f);
                            float y = centerY + (radius * (float) Math.sin(radians)) - (dotLoader.getHeight() / 2f);
                            
                            // Update dot position
                            dotLoader.setX(x);
                            dotLoader.setY(y);
                            
                            // Optional: Add a slight scale effect at the bottom of the circle
                            float scale = 1.0f + 0.2f * (float) Math.abs(Math.sin(radians));
                            dotLoader.setScaleX(scale);
                            dotLoader.setScaleY(scale);
                        });
                        
                        // Start the animation
                        orbitAnimator.start();
                    }
                });

        // Start main activity after delay
        new Handler().postDelayed(() -> {
            if (orbitAnimator != null) {
                orbitAnimator.cancel();
            }
            startActivity(new Intent(SplashCustomActivity.this, LoginActivity.class));
            finish();
        }, SPLASH_DURATION);
    }

    @Override
    protected void onDestroy() {
        if (orbitAnimator != null) {
            orbitAnimator.cancel();
        }
        super.onDestroy();
    }
}

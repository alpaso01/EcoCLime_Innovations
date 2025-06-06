package com.example.demo;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.boot.autoconfigure.domain.EntityScan;
import org.springframework.context.annotation.ComponentScan;
import org.springframework.data.jpa.repository.config.EnableJpaRepositories;

@SpringBootApplication
@EntityScan("com.example.demo")
@EnableJpaRepositories("com.example.demo")
@ComponentScan("com.example.demo")
@org.springframework.scheduling.annotation.EnableAsync
public class ApiEcoClimmeApplication {

    public static void main(String[] args) {
        SpringApplication.run(ApiEcoClimmeApplication.class, args);
    }
}

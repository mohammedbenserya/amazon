# Use an official PHP runtime as the base image
FROM php:8.0-apache

# Set the working directory in the container
WORKDIR /var/www/html

# Copy your PHP files into the container
COPY . .

# Expose port 80 for the Apache server
EXPOSE 80

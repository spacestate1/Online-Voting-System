#!/bin/bash

read -p "Enter password: " password

hash=$(php -r "echo password_hash('$password', PASSWORD_DEFAULT);")

echo "Hashed password: $hash"


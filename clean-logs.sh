#!/bin/bash

cd /home/master/applications/agfauhtwyt/public_html

# Limpiar logs TELEGRAM de más de 30 días
find ./storage/logs/telegram \
  -mindepth 1 -maxdepth 1 -type f \
  -name 'telegram_*.log' -mtime +30 -delete

# Limpiar logs AUTOEMIT de más de 30 días
find ./storage/logs/autoemit \
  -mindepth 1 -maxdepth 1 -type f \
  -name 'autoemit_*.log' -mtime +30 -delete


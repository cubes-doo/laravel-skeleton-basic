#!/bin/bash
while true; do php /opt/artisan schedule:run; sleep 2; done
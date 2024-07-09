#!/bin/bash

STATUS=$(supervisorctl status horizon | grep 'RUNNING')

if [ -z "$STATUS" ]; then
  echo "Horizon is not running. Restarting..."
  supervisorctl restart horizon
else
  echo "Horizon is running."
fi

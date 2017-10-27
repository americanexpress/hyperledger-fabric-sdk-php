#!/usr/bin/env bash
docker rm -f $(docker ps -aq)
docker-compose up -d
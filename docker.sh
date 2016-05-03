#!/bin/bash

function start() {
  # create networks
  for network in sharengo-front-tier sharengo-back-tier
  do
    count=$(docker network ls -f name=$network|wc -l)
    if [ $count -eq 1 ]
    then
      docker network create --driver bridge $network
    fi
  done

  if [ $(container_exists sharengo-postgres) -eq 0 ]
  then
    # container does not exist, create it
    docker run --name sharengo-postgres \
      -e "POSTGRES_USER=sharengo" \
      -e "POSTGRES_DB=sharengo" \
      -e "POSTGRES_PASSWORD: qazwsx100." \
      --net="sharengo-back-tier" \
      -p 5432:5432 \
      -d \
      docker.mvlabs.it/sharengo-postgres
  
  else
    if [ $(container_running sharengo-postgres) -eq 0 ]
    then
      # container is not running
      docker start sharengo-postgres
    fi
  fi

  if [ $(container_exists sharengo-redis) -eq 0 ]
  then
    # container does not exist, create it
    docker run --name sharengo-redis \
      --net="sharengo-back-tier" \
      -p 6379:6379 \
      -d \
      redis:3.0-alpine
  
  else
    if [ $(container_running sharengo-redis) -eq 0 ]
    then
      # container is not running
      docker start sharengo-redis
    fi
  fi
  
  docker-compose up -d >/dev/null

  # destroy any running sharengo-frontend container
  if [ $(container_exists sharengo-frontend) -eq 1 ]
  then
     docker stop sharengo-frontend
     docker rm sharengo-frontend
  fi

  admin_container_name=sharengoadmin_sharengo-admin-nginx_1
  public_container_name=sharengopublicsite_sharengo-public-nginx_1
  if [ $(container_running sharengopublicsite_sharengo-public-nginx_1) -eq 0 ]
  then
    # no public container found, fallback to admin container
    public_container_name=$admin_container_name
  fi

  docker run -d --name sharengo-frontend \
    -p 80:80 \
    --link $admin_container_name:admin \
    --link $public_container_name:public \
    --net sharengo-front-tier \
    docker.mvlabs.it/sharengo-frontend
  
  echo ""
  echo "To display application output please execute: "
  echo "$0 logs"
}

function stop() {
  docker-compose stop
  docker stop sharengo-postgres
  docker stop sharengo-redis
}

function container_running() {
  count=$(docker ps -f name=$1|wc -l)
  if [ $count -eq 1 ]
  then
    echo 0
  else
    echo 1
  fi
}

function container_exists() {
  count=$(docker ps -a -f name=$1|wc -l)
  if [ $count -eq 1 ]
  then
    echo 0
  else
    echo 1
  fi
}


case "$1" in
  start)
    start
  ;;
  stop)
    stop
  ;;
  logs)
    docker-compose logs
  ;;
  *)
    echo "Usage: $0 start|stop|logs"
  ;;
esac

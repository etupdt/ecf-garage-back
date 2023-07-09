#!/bin/bash

log=/var/log/deploy/ecf-garage-back.log

echo 'exec restartapache.sh' |& sudo tee $log

aws ecr get-login-password --region eu-west-3 | sudo docker login --username AWS --password-stdin 498746666064.dkr.ecr.eu-west-3.amazonaws.com |& tee $log

sudo docker stop ecf-garage-back |& tee -a $log
sudo docker rm ecf-garage-back |& tee -a $log

sudo docker image rm 498746666064.dkr.ecr.eu-west-3.amazonaws.com/ecf-garage-back:latest

sudo docker pull 498746666064.dkr.ecr.eu-west-3.amazonaws.com/ecf-garage-back:latest |& tee -a $log
sudo docker run -p 9443:9443 -d -v symfony-images:/var/www/html/ecf-garage-back/public/images --name ecf-garage-back 498746666064.dkr.ecr.eu-west-3.amazonaws.com/ecf-garage-back |& tee -a $log

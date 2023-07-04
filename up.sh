path=$(pwd)

source ~/.bashrc

sudo service postgresql stop
sudo service apache2 stop

docker-compose up -d --build

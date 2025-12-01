# Update the package lists.
apt update
#apt install make
apt install docker.io
apt install zip
apt install npm
# Install PHP.
apt install -y php
usermod -aG docker $USER
newgrp docker
touch ${PWD}/resources/config/.dev_env_build

#make start

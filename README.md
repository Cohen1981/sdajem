# Survivants d'Acres Event Extension for Joomla!

A extension for Managing Event participation as a group.

As of now, the extension is not published on the Joomla Extension Directory.

# How to use:

Prerequisites:
- GIT
- An IDE of your choice (I use PHPStorm)
- Docker installed on your machine

On Windows, you can use WSL2 to run the development environment.

1) Clone this repository
2) check the .env file and edit as seen fit
3) Run "sudo ./build_dev_env.sh", which will install the dependencies call make start.
4) Go to your local Joomla installation and install the extension via discover.
5) Enjoy!

If you want to stop the development environment, run make stop".

# Make commands:

- start: Starts the development environment and shows the url to access the site.
- up: Starts the development environment without showing the url.
- stop: Stops the development environment.
- down: Stops the development environment.
- log: Shows the logs of the running docker containers.
- config: Shows the docker compose configuration.
- reset: Stops the development environment and removes all data (/src excluded).
- build: Builds the extension and places it in the target folder.

# Tips and tricks:

- phpstorm configuration:
    - PHP -> server: Path mapping for use with xdebug:
        - "joomla_data" -> "/var/www/html"
        - "src/Sda/Component/Sdajem/Administrator" -> "var/www/src/Sda/Component/Sdajem/Administrator"
        - "src/Sda/Component/Sdajem/Site" -> "var/www/src/Sda/Component/Sdajem/Site"

# Survivants d'Acres Event Extension for Joomla!

A extension for Managing Event participation as a group.

As of now, the extension is not published on the Joomla Extension Directory.

## Prerequisites

* Docker
* Git
* when on Windows: WSL2

## Usage

* Clone this repository
* Configure your environment variables in the `.env` file
    * The first block is for Docker and the defaults should be fine for most cases.
* Run `make build` in the root directory of this repository
    * this will install additional tools like npm and zip, build the Docker images and start the containers
    * this will also symlink your extensions into the joomla installation
    * if you want to add more extensions, just add them to the corresponding variable in the `.env` file and run
      `make build` again
* After this initial build, you can run `make start` to start the containers and `make stop` to stop them again
* to reset the environment, run `make reset`. This will delete the joomla and mysql data folders and stop and remove the
  containers as well as remove the Docker volumes.
* to package your extensions, run `make package`. This will create a zip file for each extension.

# Tips and tricks:

- phpstorm configuration:
    - PHP -> server: Path mapping for use with xdebug:
        - "joomla_data" -> "/var/www/html"
        - "src/Sda/Component/Sdajem/Administrator" -> "var/www/src/Sda/Component/Sdajem/Administrator"
        - "src/Sda/Component/Sdajem/Site" -> "var/www/src/Sda/Component/Sdajem/Site"

# Yoda
Let the little green guy do the tedious docker work for you!

## What is it? 
Yoda is a command line tool used to interact with docker projects/containers based on Yaml configuration files. There are however some features that yoda does not have that Fig does have, and vice versa.

## Features
- Ability to pull, build and run containers and _all_ of their dependencies. 
- A repository of .yoda files that yoda can grab(and contribute to too!) to get you up and running even quicker!
- Wraps the CLI interface and relies more heavily on shell, thus making it more customizable. 
- Functionality for different environments built in(no need for additional config files based on env)
- A few more!!!

## Commands ##
```yoda lift [--loudly, --force]```

Within the folder that contains your .yoda configuration file, will run the .yoda file. Will perform builds, pulls, removes starts runs for you. Automagically.

```yoda lift <env> [--loudly, --force]```

Exactly the same as ```yoda lift [--loudly, --force]``` except, <env> is not part of a special configuration. See the configuration section for more details.

```yoda seek <env> [--loudly, --force]```

Seek will go through, recursively, the folders from the directory you ran the seek in and perform a ```yoda lift [--loudly, --force]``` on each of the .yoda files it finds. This is especially useful if you killed all the containers, or if your machine rebooted or you just need to bring up all of your enviornments at once. Of course, feel free to go into each folder and manually run a ```yoda lift [--loudly, --force]``` individually.

```yoda summon <name_of_yoda_file> [--loudly, --force]```

This will prompt you to create a folder in the current working directory. It's done this way, so you can use this with yoda seek! http://yoda.kcmerrill.com/share/ will show all of the available .yoda files to summon. Summon is a feature that allows someone to pull down a .yoda file that someone has worked on. Perhaps it's getting a new dev enviornment setup, or perhaps it's an optimized docker image for mysql or _insert some image here_. The sky is the limit. _use --force to force overwrite the folder if that specific folder already exists_

```yoda control [--loudly]```

From within the folder, yoda control will simply take your command and perform a ```docker exec -t -i <name_of_container_running>``` There is a "control" keyword inside the .yoda file. You can specify commands by a yaml list. So for example, let say you needed to "echo hello world" each time before launching to the container, you can do that here. This will run for all containers within the configuration file. If no control keys are found, it will simply run 'bash' within the last docker container found in the .yoda config file.

```yoda control <name_of_config_for_container> [--loudly]```
Same as ```yoda control [--loudly]``` except going through only the container configuration specified. 

```yoda kill [--loudly]```
Will __kill all__ of the containers currently running

## Installation ##
1. [Install Composer](http://getcomposer.org)
2. `cd yoda_folder/ && composer.phar install`
2. Add yoda to your path. You can do this a number of ways. 
  1. Create a symoblic link to a path that already exists: `cd /usr/bin && ln -s _path_to_yoda/yoda yoda`
  2. Append the yoda installation folder to the $PATH variable. [See this StackExchange Q&A for additional help](http://unix.stackexchange.com/questions/26047/how-to-correctly-add-a-path-to-path)

## Screencast ##
I suppose it's a little long for a "brief" overview, but a brief overview of what Yoda is. How you might be able to leverage it in certain ways and going over a few of it's key features.

[![Yoda Overview](https://raw.githubusercontent.com/kcmerrill/yoda/master/screenshots/yoda_lift_config.png)](https://www.youtube.com/watch?v=jBvG8wOmAdU)

## Screenshots ##
![Yoda Screenshot #1](https://raw.githubusercontent.com/kcmerrill/yoda/master/screenshots/yoda_lift_config.png)
![Yoda Screenshot #2](https://raw.githubusercontent.com/kcmerrill/yoda/master/screenshots/3__tmux.png)
![Yoda Screenshot #3](https://raw.githubusercontent.com/kcmerrill/yoda/master/screenshots/3__tmux_and_screenshots.png)


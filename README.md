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
![Yoda Screenshot #1](https://raw.githubusercontent.com/kcmerrill/yoda/master/screenshots/3__tmux.png)
![Yoda Screenshot #2](https://raw.githubusercontent.com/kcmerrill/yoda/master/screenshots/3__tmux_and_screenshots.png)
![Yoda Screenshot #3](https://raw.githubusercontent.com/kcmerrill/yoda/master/screenshots/yoda_lift_config.png)

# Yoda

[![Join the chat at https://gitter.im/kcmerrill/yoda](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/kcmerrill/yoda?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Let the little green guy do the tedious docker work for you!

## What is it?
With all of the amazing tools the docker community has created, one of which is incredibly lacking, which is where yoda aims to help fix. That area? The dev enviornment. There are orchestration tools that are amazing for prod, but what if you just want a tool that will volume mount and setup your dev enviornment lickety split? That's where yoda comes in.

## Features
- Ability to pull, build and run containers and _all_ of their dependencies.
- A repository of .yoda files that yoda can grab(and contribute to too!) to get you up and running even quicker!
- Wraps the CLI interface and relies more heavily on shell, thus making it more customizable.
- Functionality for different environments built in(no need for additional config files based on env)
- A few more!!!

## Experimental Installation ##
Instead of downloading and installing a php application, yoda is now in a container(and seems to be working?! Need help testing!)
Create an alias and you should be up and running:

```bash
curl https://raw.githubusercontent.com/kcmerrill/yoda/master/install | sh #ONLY IF YOU ARE NOT THE ROOT USER
```

If you are the root user OR you would like to have yoda persist simply alias it in your ~/.bash_profile:

```bash
alias yoda='docker run --rm -ti --name yodaapp -v $HOME/.ssh:$HOME/.ssh -v $HOME/.yoda/shares:/yoda/www/share -v $HOME/.docker:$HOME/.docker -v $HOME/.yoda:$HOME/.yoda -e containerized=true -h=$HOSTNAME -v /var/run/docker.sock:/var/run/docker.sock -v $(if [ $(dirname $PWD) == "/" ]; then echo $PWD; else dirname $PWD; fi):$(if [ $(dirname $PWD) == "/" ]; then echo $PWD; else dirname $PWD; fi) -w $PWD -u $(id -u $USER) kcmerrill/yoda'
```

## Installation ##
1. Clone the yoda project from github
2. Run composer to install the php dependencies
3. Add yoda to your $PATH
```bash
git clone git@github.com:kcmerrill/yoda.git
cd yoda/ && ./composer install
sudo ln -s $PWD/yoda /usr/bin/yoda
```

Yoda should now be ready to go. Give it a try
```bash
yoda
```

## Screencast (Video) ##
I suppose it's a little long for a "brief" overview, but a brief overview of what Yoda is. How you might be able to leverage it in certain ways and going over a few of it's key features.

[![Yoda Overview](http://images.kcmerrill.com/yoda/intro.png)](https://www.youtube.com/watch?v=xY65f2gOTJs)

## Screenshots ##

#### Example of a `$ yoda lift`
Use `yoda lift` to build and run a yoda project and all its dependencies.

Compare the command output to that project's `.yoda` config file.

![Yoda Screenshot #1](https://raw.githubusercontent.com/kcmerrill/yoda/master/screenshots/yoda_lift_config.png)

#### Example of a `$ yoda kill`
If you're looking for a clean start, use `yoda kill` to stop all running docker containers.

![Yoda Screenshot #2](https://raw.githubusercontent.com/kcmerrill/yoda/master/screenshots/3__tmux.png)

#### Example of a `$ yoda control`
Once Yoda has lifted a project's docker container you can use `yoda control` to shell into that container.

The functionality of `yoda control` can be extended by adding a `control:` section to the `.yoda` file.

![Yoda Screenshot #3](https://raw.githubusercontent.com/kcmerrill/yoda/master/screenshots/3__tmux_and_screenshots.png)

